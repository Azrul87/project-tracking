<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Payment;
use App\Models\InsurancePolicy;
use App\Models\Item;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataImportController extends Controller
{
	public function index()
	{
		return view('data-import');
	}

    /**
     * Analyze Excel file and show all sheets with previews
     */
	public function analyze(Request $request)
	{
        // Increase execution time for large files
        set_time_limit(0); // No time limit
        ini_set('memory_limit', '2048M'); // 2GB memory
        ini_set('max_execution_time', 0); // No execution time limit

		$request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
		]);

		$path = $request->file('file')->store('imports');
		$absolute = Storage::path($path);

        try {
            // Load spreadsheet - read cached calculated values (faster than recalculating)
            $reader = IOFactory::createReader(IOFactory::identify($absolute));
            // Read data only first (faster), then we'll get calculated values selectively
            $reader->setReadDataOnly(false); // Need to read formulas to get calculated values
            $reader->setReadEmptyCells(false); // Skip empty cells
            $spreadsheet = $reader->load($absolute);
            
            // Enable calculation cache for performance
            $calculation = \PhpOffice\PhpSpreadsheet\Calculation\Calculation::getInstance($spreadsheet);
            $calculation->setCalculationCacheEnabled(true);
            
            $sheetNames = $spreadsheet->getSheetNames();
            $sheetsData = [];

            foreach ($sheetNames as $sheetName) {
                try {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    if (!$sheet) {
                        continue;
                    }
                    
                    // Use toArray with calculated values - limit rows for preview to save time
                    $maxRows = 500; // Only read first 500 rows for preview (reduced for speed)
		$rows = [];
                    
                    // Use toArray with calculation - it's faster than iterating cells
                    try {
                        $allData = $sheet->toArray(null, true, true, true); // Calculate formulas
                        $rows = array_slice($allData, 0, $maxRows + 1); // +1 for header
                    } catch (\Exception $e) {
                        // If toArray fails, fall back to cell iteration (slower)
                        $rowCount = 0;
                        foreach ($sheet->getRowIterator() as $row) {
                            if ($rowCount >= $maxRows) {
                                break;
                            }
                            
                            $rowData = [];
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(true);
                            
                            foreach ($cellIterator as $cell) {
                                try {
                                    // Try to get calculated value
                                    $value = $cell->getCalculatedValue();
                                    
                                    // If it's a formula error, try formatted value
                                    if (is_string($value) && strpos($value, '#') === 0) {
                                        $value = $cell->getFormattedValue();
                                    }
                                    
                                    // If still an error, get raw value
                                    if (is_string($value) && strpos($value, '#') === 0) {
                                        $value = $cell->getValue();
                                    }
                                    
                                    $rowData[] = $value;
                                } catch (\Exception $e2) {
                                    // Fallback to raw value
                                    try {
                                        $rowData[] = $cell->getValue();
                                    } catch (\Exception $e3) {
                                        $rowData[] = '';
                                    }
                                }
                            }
                            
                            if (!empty(array_filter($rowData, function($v) { return $v !== null && $v !== ''; }))) {
                                $rows[] = $rowData;
                                $rowCount++;
                            }
                        }
                    }
                    
                    // Get total row count (approximate)
                    $totalRows = $sheet->getHighestRow();
                    
                    if (empty($rows)) {
                        continue;
                    }

                    $headers = $rows[0] ?? [];
		$preview = array_slice($rows, 1, 10);
                    
                    $sheetsData[$sheetName] = [
                        'rows' => max($totalRows - 1, 0), // Use actual row count
			'columns' => count($headers),
			'headers' => $headers,
			'preview' => $preview,
                    ];
                } catch (\Exception $e) {
                    // Skip sheets that cause errors
                    continue;
                }
            }

            // Free memory
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            return back()->with('import_sheets', $sheetsData)->with('import_path', $path);
        } catch (\Exception $e) {
            return back()->with('import_error', 'Error reading Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Import data from all sheets into database
     */
	public function commit(Request $request)
	{
        // Increase execution time for large imports
        set_time_limit(0); // No time limit
        ini_set('memory_limit', '2048M'); // 2GB memory
        ini_set('max_execution_time', 0); // No execution time limit

		$request->validate([
			'path' => ['required', 'string'],
            'sheets' => ['required', 'array'],
        ]);

        $absolute = Storage::path($request->path);
        
        // Load spreadsheet - read cached calculated values
        $reader = IOFactory::createReader(IOFactory::identify($absolute));
        $reader->setReadDataOnly(false); // Need formulas to calculate
        $reader->setReadEmptyCells(false); // Skip empty cells
        $spreadsheet = $reader->load($absolute);
        
        // Enable calculation cache for performance
        $calculation = \PhpOffice\PhpSpreadsheet\Calculation\Calculation::getInstance($spreadsheet);
        $calculation->setCalculationCacheEnabled(true);
        
        $results = [
            'projects' => ['imported' => 0, 'errors' => []],
            'payments' => ['imported' => 0, 'errors' => []],
            'insurance' => ['imported' => 0, 'errors' => []],
            'items' => ['imported' => 0, 'errors' => []],
            'project_items' => ['imported' => 0, 'errors' => []],
        ];

        DB::beginTransaction();
        try {
            // Step 1: Import Projects first (required for other tables)
            // Check all possible sheet name variations - be more flexible
            $projectSheetName = null;
            $allSheetNames = $spreadsheet->getSheetNames();
            
            // First, check selected sheets
            foreach ($request->sheets as $selectedSheet) {
                if (stripos($selectedSheet, 'project tracker') !== false || 
                    stripos($selectedSheet, 'master') !== false ||
                    stripos($selectedSheet, 'ecn') !== false) {
                    $projectSheetName = $selectedSheet;
                    break;
                }
            }
            
            // If not found in selected, check all sheets
            if (!$projectSheetName) {
                foreach ($allSheetNames as $sheet) {
                    if (stripos($sheet, 'project tracker') !== false || 
                        stripos($sheet, 'master') !== false ||
                        stripos($sheet, 'ecn') !== false) {
                        $projectSheetName = $sheet;
                        break;
                    }
                }
            }
            
            if ($projectSheetName) {
                $results['projects'] = $this->importProjects($spreadsheet, $projectSheetName);
            } else {
                $results['projects'] = ['imported' => 0, 'errors' => ['No project tracker sheet found. Available sheets: ' . implode(', ', $allSheetNames)]];
            }

            // Step 2: Import Items (required for project_items)
            if (in_array('Material Input', $request->sheets)) {
                $results['items'] = $this->importItems($spreadsheet, 'Material Input');
            }

            // Step 3: Import related data (depends on projects)
            if (in_array('Finance Tracker', $request->sheets)) {
                $results['payments'] = $this->importPayments($spreadsheet, 'Finance Tracker');
            }

            if (in_array('Insurance Tracker', $request->sheets)) {
                $results['insurance'] = $this->importInsurance($spreadsheet, 'Insurance Tracker');
            }

            // Step 4: Import Project Items (depends on projects and items)
            if (in_array('Material List', $request->sheets)) {
                $results['project_items'] = $this->importProjectItems($spreadsheet, 'Material List');
                
                // Sync inventory items after importing project materials
                \Illuminate\Support\Facades\Artisan::call('inventory:sync');
            }

            DB::commit();
            
            // Free memory
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            $message = "Import completed successfully!\n";
            $message .= "Projects: {$results['projects']['imported']}\n";
            $message .= "Payments: {$results['payments']['imported']}\n";
            $message .= "Insurance: {$results['insurance']['imported']}\n";
            $message .= "Items: {$results['items']['imported']}\n";
            $message .= "Project Items: {$results['project_items']['imported']}";
            
            // Add error details if any
            $allErrors = [];
            foreach ($results as $type => $result) {
                if (!empty($result['errors'])) {
                    $allErrors = array_merge($allErrors, array_slice($result['errors'], 0, 10)); // Limit to first 10 errors
                }
            }
            
            if (!empty($allErrors)) {
                $message .= "\n\nErrors encountered:\n" . implode("\n", array_slice($allErrors, 0, 10));
            }

            return back()->with('import_success', $message)->with('import_results', $results);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: ' . $e->getMessage());
            return back()->with('import_error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import projects from Excel sheet
     */
    private function importProjects($spreadsheet, $sheetName)
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            return ['imported' => 0, 'errors' => ['Sheet not found']];
        }

        $rows = $this->getSheetData($sheet);
        if (empty($rows)) {
            return ['imported' => 0, 'errors' => ['Sheet is empty']];
        }

        // Try to find header row (could be row 0 or row 1)
        $headerRowIndex = 0;
        $headers = [];
        
        // Check row 0 first
        if (!empty($rows[0])) {
            $testHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[0]))));
            if (!empty($testHeaders) && $this->isHeaderRow($testHeaders)) {
                $headerRowIndex = 0;
                $headers = $testHeaders;
            }
        }
        
        // If row 0 doesn't look like headers, try row 1
        if (empty($headers) && !empty($rows[1])) {
            $testHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[1]))));
            if (!empty($testHeaders) && $this->isHeaderRow($testHeaders)) {
                $headerRowIndex = 1;
                $headers = $testHeaders;
            }
        }
        
        // If still no headers, use row 0 as default
        if (empty($headers) && !empty($rows[0])) {
            $headers = array_map('strtolower', array_filter(array_map('trim', array_values($rows[0]))));
            $headerRowIndex = 0;
        }
        
        // Data starts after header row
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        
        $imported = 0;
        $errors = [];
        
        // Log for debugging
        if (empty($headers)) {
            $errors[] = 'Could not find header row. First row: ' . json_encode($rows[0] ?? []);
        }

        foreach ($dataRows as $rowNum => $row) {
            $row = array_values($row);
            if (empty(array_filter($row, function($v) { return $v !== null && $v !== '' && $v !== 0; }))) {
                continue; // Skip empty rows
            }

            try {
                $data = $this->mapRowToArray($headers, $row, [
                    // Basic Info
                    'project_id' => ['project no', 'project id', 'project_id', 'project_no', 'project number', 'project', 'no', 'id'],
                    'client_name' => ['client', 'client name', 'client_name', 'clientname'],
                    'sales_pic' => ['sales pic', 'sales_pic', 'sales person', 'salespic', 'sales person in charge', 'sales', 'pic'],
                    'name' => ['project name', 'name', 'project_name', 'projectname'],
                    'category' => ['category'],
                    'scheme' => ['scheme'],
                    'location' => ['location'],
                    'status' => ['status', 'general status', 'project status', 'current status', 'stage'],
                    
                    // Technical Specifications
                    'pv_system_capacity_kwp' => ['pv system capacity', 'capacity', 'pv_capacity', 'system capacity', 'pv capacity (kwp)', 'kwp'],
                    'ev_charger_capacity' => ['ev charger capacity', 'ev charger', 'ev capacity'],
                    'bess_capacity' => ['bess capacity', 'bess', 'battery capacity'],
                    'module' => ['module', 'solar module', 'pv module', 'panel'],
                    'module_quantity' => ['module quantity', 'module qty', 'qty', 'quantity', 'no of module'],
                    'inverter' => ['inverter', 'inverter model'],
                    'installer' => ['installer', 'subcon'],
                    'site_survey_date' => ['site survey date', 'site survey', 'survey date'],
                    'installation_date' => ['installation date', 'install date'],
                    
                    // Financial
                    'project_value_rm' => ['project value', 'project_value', 'value', 'project value (rm)', 'value (rm)', 'contract value'],
                    'vo_rm' => ['vo', 'variation order', 'vo_rm', 'vo (rm)', 'vo amount'],
                    'payment_method' => ['payment method', 'payment type'],
                    'contract_type' => ['contract type', 'contract'],
                    // Legacy fields - mapped from Excel but no longer stored in projects table
                    'invoice_status' => ['invoice status'],
                    'payment_status' => ['payment status'],
                    'procurement_status' => ['procurement status', 'procurement'],
                    'closed_date' => ['closed date', 'project closed date'],
                    
                    // Operations & O&M
                    'insurance_warranty' => ['insurance/warranty', 'insurance', 'warranty'],
                    'dlp_period' => ['dlp period', 'dlp', 'defect liability period'],
                    'partner' => ['partner', 'partnership'],
                    'om_status' => ['o&m status', 'om status', 'o&m'],
                    'om_details' => ['om details', 'o&m details'],
                    'services_exclusion' => ['services exclusion', 'exclusion'],
                    'additional_remark' => ['additional remark', 'remark', 'remarks', 'notes'],
                    
                    // NEM Application Workflow
                    'nem_quota_submission_date' => ['nem quota submission', 'nem submission', 'quota submission', 'nem application submission'],
                    'nem_quota_approval_date' => ['nem quota approval', 'quota approval', 'nem approval'],
                    'nem_quota_status' => ['nem quota status', 'quota status', 'nem status'],
                    'st_license_application_date' => ['st license application', 'st application'],
                    'st_license_approval_date' => ['st license approval', 'st approval'],
                    'nemcd_obtained_date' => ['nemcd obtained', 'nemcd', 'nemcd date'],
                    'nem_meter_change_date' => ['nem meter change', 'meter change', 'nem meter'],
                    
                    // Workflow - EPCC Stages
                    'client_enquiry_date' => ['client enquiry', 'enquiry date', 'enquiry'],
                    'proposal_preparation_date' => ['proposal preparation', 'proposal prep'],
                    'proposal_submission_date' => ['proposal submission', 'proposal submit'],
                    'proposal_acceptance_date' => ['proposal acceptance', 'proposal accept'],
                    'letter_of_award_date' => ['letter of award', 'loa', 'award date'],
                    'first_invoice_date' => ['1st invoice', '1st invoice date', 'first invoice'],
                    'first_invoice_payment_date' => ['1st invoice payment', '1st payment', 'first payment'],
                    'site_study_date' => ['site study', 'site study date'],
                    'project_planning_date' => ['project planning', 'planning date'],
                    'second_invoice_date' => ['2nd invoice', '2nd invoice date', 'second invoice'],
                    'second_invoice_payment_date' => ['2nd invoice payment', '2nd payment', 'second payment'],
                    'material_procurement_date' => ['material procurement', 'procurement date'],
                    'subcon_appointment_date' => ['subcon appointment', 'subcon date'],
                    'material_delivery_date' => ['material delivery', 'delivery date'],
                    'site_mobilization_date' => ['site mobilization', 'mobilization'],
                    'system_testing_date' => ['system testing', 'testing date'],
                    'system_commissioning_date' => ['system commissioning', 'commissioning'],
                    'last_invoice_date' => ['last invoice', 'last invoice date', 'final invoice'],
                    'last_invoice_payment_date' => ['last invoice payment', 'last payment', 'final payment'],
                    'system_energize_date' => ['system energize', 'energize date', 'energized'],
                    'system_training_date' => ['system training', 'training date'],
                    'project_handover_to_client_date' => ['project handover to client', 'handover to client', 'client handover'],
                    'project_closure_date' => ['project closure', 'closure date'],
                    'handover_to_om_date' => ['handover to o&m', 'handover to om', 'om handover'],
                    
                    // O&M Workflow
                    'om_site_study_date' => ['o&m site study', 'om site study'],
                    'om_schedule_prepared_date' => ['o&m schedule prepared', 'om schedule prepared'],
                    'om_start_date' => ['o&m start date', 'om start date', 'o&m start'],
                    'om_end_date' => ['o&m end date', 'om end date', 'o&m end'],
                ]);
                
                // Debug: Log first row mapping
                if ($rowNum === 0 && empty($data['project_id'])) {
                    $errors[] = 'DEBUG Row 1: Headers found: ' . json_encode(array_slice($headers, 0, 15));
                    $errors[] = 'DEBUG Row 1: First row data: ' . json_encode(array_slice($row, 0, 15));
                    $errors[] = 'DEBUG Row 1: Mapped data: ' . json_encode($data);
                }

                // Clean project_id - remove any whitespace and convert to string
                $projectId = trim((string)($data['project_id'] ?? ''));
                
                // If project_id not found in mapping, try to get it from first column (common case)
                if (empty($projectId) && !empty($row[0])) {
                    $projectId = trim((string)$row[0]);
                    // Check if it looks like a project ID (numeric or alphanumeric)
                    // Skip Excel errors like #REF!, #N/A, etc.
                    if (!empty($projectId) && 
                        !preg_match('/^#(REF|N\/A|VALUE|NAME|DIV\/0|NULL)/i', $projectId) &&
                        (is_numeric($projectId) || preg_match('/^[A-Z0-9-]+$/i', $projectId))) {
                        $data['project_id'] = $projectId;
                    }
                }
                
                // Skip Excel formula errors
                if (!empty($projectId) && preg_match('/^#(REF|N\/A|VALUE|NAME|DIV\/0|NULL)/i', $projectId)) {
                    continue; // Skip rows with formula errors
                }
                
                if (empty($projectId)) {
                    // Only log first few errors to avoid spam
                    if ($rowNum < 3) {
                        $errors[] = "Row " . ($rowNum + $headerRowIndex + 2) . ": No project ID found. Headers: " . json_encode(array_slice($headers, 0, 10)) . " | First row data: " . json_encode(array_slice($row, 0, 10));
                    }
                    continue; // Skip if no project ID
                }
                
                // Use cleaned project ID
                $data['project_id'] = $projectId;

                // Extract client-specific data from the row
                $clientData = $this->mapRowToArray($headers, $row, [
                    'client_phone' => ['client phone', 'phone', 'phone number', 'client contact', 'contact number'],
                    'client_email' => ['client email', 'email', 'email address'],
                    'client_ic' => ['ic number', 'ic', 'nric', 'passport', 'ic/passport'],
                    'client_address' => ['installation address', 'address', 'site address', 'location address'],
                    'client_payment_method' => ['payment method', 'payment type', 'payment'],
                    'client_contract_type' => ['contract type', 'outright/ppa', 'ppa/outright', 'contract'],
                ]);
                
                // Determine contract type from payment method or dedicated column
                $contractType = null;
                if (!empty($clientData['client_contract_type'])) {
                    $contractType = $clientData['client_contract_type'];
                    // Normalize the value
                    if (stripos($contractType, 'outright') !== false) {
                        $contractType = 'Outright';
                    } elseif (stripos($contractType, 'ppa') !== false) {
                        $contractType = 'PPA';
                    }
                }
                
                // Find or create client with all available data
                $clientId = $this->generateIdFromName($data['client_name'] ?? 'Unknown', 'CLI');
                $client = Client::firstOrNew(['client_id' => $clientId]);
                
                // Update client information (only if not empty, to preserve existing data)
                $client->client_name = $data['client_name'] ?? $client->client_name ?? 'Unknown';
                if (!empty($clientData['client_ic'])) {
                    $client->ic_number = $clientData['client_ic'];
                }
                if (!empty($clientData['client_address'])) {
                    $client->installation_address = $clientData['client_address'];
                }
                if (!empty($clientData['client_phone'])) {
                    $client->phone_number = $clientData['client_phone'];
                }
                if (!empty($clientData['client_email'])) {
                    $client->email_address = $clientData['client_email'];
                }
                if (!empty($clientData['client_payment_method'])) {
                    $client->payment_method = $clientData['client_payment_method'];
                }
                if (!empty($contractType)) {
                    $client->contract_type = $contractType;
                }
                
                $client->save();

                // Find or create sales PIC (user)
                $salesPicName = $data['sales_pic'] ?? 'Unknown';
                $salesPicId = $this->generateIdFromName($salesPicName, 'USR');
                $salesPicEmail = strtolower(str_replace([' ', '-'], '', $salesPicName)) . '@example.com';
                
                // Check if user exists by ID first
                $salesPic = User::find($salesPicId);
                
                if (!$salesPic) {
                    // Check if email already exists
                    $existingUser = User::where('email', $salesPicEmail)->first();
                    if ($existingUser) {
                        $salesPic = $existingUser;
                    } else {
                        // Create new user
                        try {
                            $salesPic = User::create([
                                'user_id' => $salesPicId,
                                'name' => $salesPicName,
                                'email' => $salesPicEmail,
                                'password' => bcrypt('password'),
                                'role' => 'Sales',
                            ]);
                        } catch (\Exception $e) {
                            // If email conflict, try with unique suffix
                            $salesPicEmail = strtolower(str_replace([' ', '-'], '', $salesPicName)) . rand(100, 999) . '@example.com';
                            $salesPic = User::create([
                                'user_id' => $salesPicId,
                                'name' => $salesPicName,
                                'email' => $salesPicEmail,
                                'password' => bcrypt('password'),
                                'role' => 'Sales',
                            ]);
                        }
                    }
                }

                // Create or update project (handles duplicates - updates if exists)
                try {
                    // Find existing project or create new one
                    $project = Project::firstOrNew(['project_id' => $data['project_id']]);
                    
                    // Only update fields that have actual values (not null/empty)
                    // This preserves existing data when importing partial updates
                    
                    // Basic Info - Always update these core fields
                    $project->client_id = $client->client_id;
                    $project->sales_pic_id = $salesPic->user_id;
                    
                    // Only update if new value exists
                    if (!empty($data['name'])) {
                        $project->name = $data['name'];
                    }
                    if (!empty($data['category'])) {
                        $project->category = $data['category'];
                    }
                    if (!empty($data['scheme'])) {
                        $project->scheme = $data['scheme'];
                    }
                    if (!empty($data['location'])) {
                        $project->location = $data['location'];
                    }
                    if (!empty($data['status'])) {
                        $project->status = $data['status'];
                    } elseif (!$project->exists) {
                        // Only set default 'Planning' for new projects
                        $project->status = 'Planning';
                    }
                    
                    // Technical Specifications - Only update if value exists
                    $pvCapacity = $this->parseDecimal($data['pv_system_capacity_kwp'] ?? null);
                    if ($pvCapacity !== null) {
                        $project->pv_system_capacity_kwp = $pvCapacity;
                    }
                    if (!empty($data['ev_charger_capacity'])) {
                        $project->ev_charger_capacity = $data['ev_charger_capacity'];
                    }
                    if (!empty($data['bess_capacity'])) {
                        $project->bess_capacity = $data['bess_capacity'];
                    }
                    if (!empty($data['module'])) {
                        $project->module = $data['module'];
                    }
                    $moduleQty = $this->parseInteger($data['module_quantity'] ?? null);
                    if ($moduleQty !== null) {
                        $project->module_quantity = $moduleQty;
                    }
                    if (!empty($data['inverter'])) {
                        $project->inverter = $data['inverter'];
                    }
                    if (!empty($data['installer'])) {
                        $project->installer = $data['installer'];
                    }
                    
                    // Financial - Only update if value exists
                    $projectValue = $this->parseDecimal($data['project_value_rm'] ?? null);
                    if ($projectValue !== null) {
                        $project->project_value_rm = $projectValue;
                    }
                    $voValue = $this->parseDecimal($data['vo_rm'] ?? null);
                    if ($voValue !== null) {
                        $project->vo_rm = $voValue;
                    }
                    if (!empty($data['payment_method'])) {
                        $project->payment_method = $data['payment_method'];
                    }
                    if (!empty($data['contract_type'])) {
                        $project->contract_type = $data['contract_type'];
                    }
                    // Note: invoice_status and payment_status are no longer stored in projects table
                    // They are calculated from the payments table
                    if (!empty($data['procurement_status'])) {
                        $project->procurement_status = $data['procurement_status'];
                    }
                    
                    // Operations & O&M - Only update if value exists
                    if (!empty($data['insurance_warranty'])) {
                        $project->insurance_warranty = $data['insurance_warranty'];
                    }
                    if (!empty($data['dlp_period'])) {
                        $project->dlp_period = $data['dlp_period'];
                    }
                    if (!empty($data['partner'])) {
                        $project->partner = $data['partner'];
                    }
                    // om_status is now handled in workflow stages table (see below)
                    if (!empty($data['om_details'])) {
                        $project->om_details = $data['om_details'];
                    }
                    if (!empty($data['services_exclusion'])) {
                        $project->services_exclusion = $data['services_exclusion'];
                    }
                    if (!empty($data['additional_remark'])) {
                        $project->additional_remark = $data['additional_remark'];
                    }
                    
                    // Dates - Only update if value exists
                    $this->updateDateField($project, 'site_survey_date', $data['site_survey_date'] ?? null);
                    $this->updateDateField($project, 'installation_date', $data['installation_date'] ?? null);
                    $this->updateDateField($project, 'closed_date', $data['closed_date'] ?? null);
                    
                    // Workflow - EPCC Stages - Only update if value exists
                    $this->updateDateField($project, 'client_enquiry_date', $data['client_enquiry_date'] ?? null);
                    $this->updateDateField($project, 'proposal_preparation_date', $data['proposal_preparation_date'] ?? null);
                    $this->updateDateField($project, 'proposal_submission_date', $data['proposal_submission_date'] ?? null);
                    $this->updateDateField($project, 'proposal_acceptance_date', $data['proposal_acceptance_date'] ?? null);
                    $this->updateDateField($project, 'letter_of_award_date', $data['letter_of_award_date'] ?? null);
                    $this->updateDateField($project, 'first_invoice_date', $data['first_invoice_date'] ?? null);
                    $this->updateDateField($project, 'first_invoice_payment_date', $data['first_invoice_payment_date'] ?? null);
                    $this->updateDateField($project, 'site_study_date', $data['site_study_date'] ?? null);
                    $this->updateDateField($project, 'nem_application_submission_date', $data['nem_quota_submission_date'] ?? null);
                    $this->updateDateField($project, 'project_planning_date', $data['project_planning_date'] ?? null);
                    $this->updateDateField($project, 'nem_approval_date', $data['nem_quota_approval_date'] ?? null);
                    $this->updateDateField($project, 'st_license_application_date', $data['st_license_application_date'] ?? null);
                    $this->updateDateField($project, 'second_invoice_date', $data['second_invoice_date'] ?? null);
                    $this->updateDateField($project, 'second_invoice_payment_date', $data['second_invoice_payment_date'] ?? null);
                    $this->updateDateField($project, 'material_procurement_date', $data['material_procurement_date'] ?? null);
                    $this->updateDateField($project, 'subcon_appointment_date', $data['subcon_appointment_date'] ?? null);
                    $this->updateDateField($project, 'material_delivery_date', $data['material_delivery_date'] ?? null);
                    $this->updateDateField($project, 'site_mobilization_date', $data['site_mobilization_date'] ?? null);
                    $this->updateDateField($project, 'st_license_approval_date', $data['st_license_approval_date'] ?? null);
                    $this->updateDateField($project, 'system_testing_date', $data['system_testing_date'] ?? null);
                    $this->updateDateField($project, 'system_commissioning_date', $data['system_commissioning_date'] ?? null);
                    $this->updateDateField($project, 'nem_meter_change_date', $data['nem_meter_change_date'] ?? null);
                    $this->updateDateField($project, 'last_invoice_date', $data['last_invoice_date'] ?? null);
                    $this->updateDateField($project, 'last_invoice_payment_date', $data['last_invoice_payment_date'] ?? null);
                    $this->updateDateField($project, 'system_energize_date', $data['system_energize_date'] ?? null);
                    $this->updateDateField($project, 'nemcd_obtained_date', $data['nemcd_obtained_date'] ?? null);
                    $this->updateDateField($project, 'system_training_date', $data['system_training_date'] ?? null);
                    $this->updateDateField($project, 'project_handover_to_client_date', $data['project_handover_to_client_date'] ?? null);
                    $this->updateDateField($project, 'project_closure_date', $data['project_closure_date'] ?? null);
                    $this->updateDateField($project, 'handover_to_om_date', $data['handover_to_om_date'] ?? null);
                    
                    // O&M Workflow - Only update if value exists
                    $this->updateDateField($project, 'om_site_study_date', $data['om_site_study_date'] ?? null);
                    $this->updateDateField($project, 'om_schedule_prepared_date', $data['om_schedule_prepared_date'] ?? null);
                    $this->updateDateField($project, 'om_start_date', $data['om_start_date'] ?? null);
                    $this->updateDateField($project, 'om_end_date', $data['om_end_date'] ?? null);
                    
                    // Handle workflow_stage and om_status
                    $workflowData = [];
                    if (!empty($data['workflow_stage'])) {
                        $workflowData['workflow_stage'] = $data['workflow_stage'];
                    }
                    if (!empty($data['om_status'])) {
                        $workflowData['om_status'] = $data['om_status'];
                    }
                    
                    // Update or create workflow stage record
                    if (!empty($workflowData)) {
                        \App\Models\ProjectWorkflowStage::updateOrCreate(
                            ['project_id' => $project->project_id],
                            $workflowData
                        );
                    }
                    
                    // Save the project
                    $project->save();

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowNum + $headerRowIndex + 2) . ": Failed to create/update project {$data['project_id']}: " . $e->getMessage();
                }
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Import payments from Finance Tracker sheet
     * Finance Tracker has a special structure:
     * - Row 2: Payment phase labels (1st Payment, 2nd Payment, etc.)
     * - Row 3: Column headers (Project No, Client Name, Invoice Status, etc.)
     * - Row 4+: Data rows
     */
    private function importPayments($spreadsheet, $sheetName)
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            return ['imported' => 0, 'errors' => ['Sheet not found']];
        }

        $rows = $this->getSheetData($sheet);
        if (empty($rows) || count($rows) < 2) {
            return ['imported' => 0, 'errors' => ['Sheet has insufficient data']];
        }

        // Finance Tracker structure (after filtering empty rows):
        // Index 0: Headers (row 3 in Excel, but row 2 is filtered out as empty cells)
        // Index 1+: Data rows
        // Note: Phase labels (row 2 in Excel) would be at index 0, but since row 1 is empty, it shifts
        
        // Check if first row looks like headers
        $firstRow = array_values($rows[0] ?? []);
        $firstRowLower = array_map('strtolower', array_map('trim', $firstRow));
        
        // Determine which row is headers
        $headerRowIndex = 0;
        if (in_array('project no', $firstRowLower) || in_array('project id', $firstRowLower)) {
            // First row is headers
            $headerRow = $firstRow;
        } else {
            // First row might be phase labels, second row is headers
            $headerRowIndex = 1;
            $headerRow = array_values($rows[1] ?? []);
        }
        
        $headerRow = array_values($headerRow);
        
        // Build column mapping - store ALL occurrences of each column name
        $colMap = [];
        $colMapMulti = []; // Store multiple occurrences
        foreach ($headerRow as $index => $header) {
            $key = strtolower(trim($header));
            if (!isset($colMap[$key])) {
                $colMap[$key] = $index;
            }
            // Store all occurrences for repeated column names
            if (!isset($colMapMulti[$key])) {
                $colMapMulti[$key] = [];
            }
            $colMapMulti[$key][] = $index;
        }
        
        $imported = 0;
        $errors = [];
        $paymentCount = 0;

        // Process data rows (starting after header row)
        for ($rowIdx = $headerRowIndex + 1; $rowIdx < count($rows); $rowIdx++) {
            $row = array_values($rows[$rowIdx]);
            
            // Skip empty rows
            if (empty(array_filter($row, function($v) { return $v !== null && $v !== ''; }))) {
                continue;
            }

            try {
                // Get project-level data
                $projectId = trim((string)($row[$colMap['project no'] ?? -1] ?? ''));
                
                // Skip Excel formula errors or empty
                if (empty($projectId) || preg_match('/^#(REF|N\/A|VALUE|NAME|DIV\/0|NULL)/i', $projectId)) {
                    continue;
                }
                
                // Check if project exists
                $project = Project::find($projectId);
                if (!$project) {
                    if (count($errors) < 20) {
                        $errors[] = "Row " . ($rowIdx + 1) . ": Project {$projectId} not found";
                    }
                    continue;
                }

                // Update project-level finance data
                // Note: invoice_status, payment_status, total_invoiced, and total_paid are now calculated from payments table
                $project->update([
                    'payment_type' => $row[$colMap['payment type'] ?? -1] ?? null,
                ]);

                // Process individual payment phases
                // Define exact column indices based on Finance Tracker structure
                // For repeated column names, get the nth occurrence
                $invoiceAmounts = $colMapMulti['invoice amount'] ?? [];
                $paymentReceiveds = $colMapMulti['payment received'] ?? [];
                
                $paymentPhases = [
                    '1st Payment' => [
                        'invoice_date_col' => $colMap['1st invoice date'] ?? null,
                        'invoice_amount_col' => $colMap['invoiced amount'] ?? null,
                        'payment_date_col' => $colMap['1st payment date'] ?? null,
                        'payment_amount_col' => $colMap['1st payment'] ?? null,
                    ],
                    '2nd Payment' => [
                        'invoice_date_col' => $colMap['2nd invoice date'] ?? null,
                        'invoice_amount_col' => $invoiceAmounts[0] ?? null, // 1st occurrence
                        'payment_date_col' => $colMap['2nd payment date'] ?? null,
                        'payment_amount_col' => $paymentReceiveds[0] ?? null, // 1st occurrence
                    ],
                    '3rd Payment' => [
                        'invoice_date_col' => $colMap['3rd invoice date'] ?? null,
                        'invoice_amount_col' => $invoiceAmounts[1] ?? null, // 2nd occurrence
                        'payment_date_col' => $colMap['3rd payment date'] ?? null,
                        'payment_amount_col' => $paymentReceiveds[1] ?? null, // 2nd occurrence
                    ],
                    '4th Payment' => [
                        'invoice_date_col' => $colMap['4th invoice date'] ?? null,
                        'invoice_amount_col' => $invoiceAmounts[2] ?? null, // 3rd occurrence
                        'payment_date_col' => $colMap['4th payment date'] ?? null,
                        'payment_amount_col' => $paymentReceiveds[2] ?? null, // 3rd occurrence
                    ],
                    '5th Payment' => [
                        'invoice_date_col' => $colMap['5th invoice date'] ?? null,
                        'invoice_amount_col' => $invoiceAmounts[3] ?? null, // 4th occurrence
                        'payment_date_col' => $colMap['5th payment date'] ?? null,
                        'payment_amount_col' => $paymentReceiveds[3] ?? null, // 4th occurrence
                    ],
                    'VO Payment' => [
                        'invoice_date_col' => $colMap['vo invoiced date'] ?? null,
                        'invoice_amount_col' => $colMap['vo invoice amount'] ?? null,
                        'payment_date_col' => $colMap['vo payment date'] ?? null,
                        'payment_amount_col' => $colMap['vo payment'] ?? null,
                    ],
                ];

                foreach ($paymentPhases as $phaseName => $columns) {
                    // Check if this phase has any data
                    $hasData = false;
                    $invoiceDate = null;
                    $invoiceAmount = null;
                    $paymentDate = null;
                    $paymentAmount = null;

                    // Get values from specific columns
                    if ($columns['invoice_date_col'] !== null && isset($row[$columns['invoice_date_col']]) && $row[$columns['invoice_date_col']] !== null && $row[$columns['invoice_date_col']] !== '') {
                        $invoiceDate = $this->parseDate($row[$columns['invoice_date_col']]);
                        $hasData = true;
                    }
                    
                    if ($columns['invoice_amount_col'] !== null && isset($row[$columns['invoice_amount_col']]) && $row[$columns['invoice_amount_col']] !== null && $row[$columns['invoice_amount_col']] !== '') {
                        $invoiceAmount = $this->parseDecimal($row[$columns['invoice_amount_col']]);
                        $hasData = true;
                    }
                    
                    if ($columns['payment_date_col'] !== null && isset($row[$columns['payment_date_col']]) && $row[$columns['payment_date_col']] !== null && $row[$columns['payment_date_col']] !== '') {
                        $paymentDate = $this->parseDate($row[$columns['payment_date_col']]);
                        $hasData = true;
                    }
                    
                    if ($columns['payment_amount_col'] !== null && isset($row[$columns['payment_amount_col']]) && $row[$columns['payment_amount_col']] !== null && $row[$columns['payment_amount_col']] !== '') {
                        $paymentAmount = $this->parseDecimal($row[$columns['payment_amount_col']]);
                        $hasData = true;
                    }

                    // Create/update payment record if we have data
                    if ($hasData) {
                        $existingPayment = Payment::where('project_id', $projectId)
                            ->where('description', $phaseName)
                            ->first();

                        $paymentData = [
                            'invoice_date' => $invoiceDate,
                            'invoice_amount' => $invoiceAmount,
                            'payment_date' => $paymentDate,
                            'payment_amount' => $paymentAmount,
                        ];

                        if ($existingPayment) {
                            $existingPayment->update($paymentData);
                        } else {
                            $paymentData['payment_id'] = Payment::generatePaymentId();
                            $paymentData['project_id'] = $projectId;
                            $paymentData['description'] = $phaseName;
                            Payment::create($paymentData);
                        }

                        $paymentCount++;
                    }
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($rowIdx + 1) . ": " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'errors' => $errors, 'message' => "{$imported} projects updated, {$paymentCount} payment records processed"];
    }

    /**
     * Import insurance policies
     */
    private function importInsurance($spreadsheet, $sheetName)
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            return ['imported' => 0, 'errors' => ['Sheet not found']];
        }

        $rows = $this->getSheetData($sheet);
        if (empty($rows)) {
            return ['imported' => 0, 'errors' => ['Sheet is empty']];
        }

        // Try to find header row
        $headerRowIndex = 0;
        $headers = [];
        
        if (!empty($rows[0])) {
            $testHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[0]))));
            if (!empty($testHeaders) && $this->isHeaderRow($testHeaders)) {
                $headerRowIndex = 0;
                $headers = $testHeaders;
            }
        }
        
        if (empty($headers) && !empty($rows[1])) {
            $testHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[1]))));
            if (!empty($testHeaders) && $this->isHeaderRow($testHeaders)) {
                $headerRowIndex = 1;
                $headers = $testHeaders;
            }
        }
        
        if (empty($headers) && !empty($rows[0])) {
            $headers = array_map('strtolower', array_filter(array_map('trim', array_values($rows[0]))));
            $headerRowIndex = 0;
        }
        
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        
        $imported = 0;
        $errors = [];

        foreach ($dataRows as $rowNum => $row) {
            $row = array_values($row);
            if (empty(array_filter($row, function($v) { return $v !== null && $v !== '' && $v !== 0; }))) {
                continue;
            }

            try {
                $data = $this->mapRowToArray($headers, $row, [
                    'project_id' => ['project no', 'project id', 'project_id', 'project number'],
                    'provider_name' => ['provider', 'provider name', 'provider_name'],
                    'policy_number' => ['policy number', 'policy_number', 'policy no'],
                    'policy_date' => ['policy date', 'policy_date', 'date'],
                    'description' => ['description'],
                ]);

                // Clean and validate project_id
                $projectId = trim((string)($data['project_id'] ?? ''));
                
                // Skip Excel formula errors
                if (empty($projectId) || preg_match('/^#(REF|N\/A|VALUE|NAME|DIV\/0|NULL)/i', $projectId)) {
                    continue; // Skip rows with formula errors or empty
                }
                
                $project = Project::find($projectId);
                if (!$project) {
                    // Only log first 20 errors to avoid spam
                    if (count($errors) < 20) {
                        $errors[] = "Row " . ($rowNum + $headerRowIndex + 2) . ": Project {$projectId} not found (Projects must be imported first)";
                    }
                    continue;
                }
                
                // Update data with cleaned project_id
                $data['project_id'] = $projectId;

                // Use updateOrCreate to handle duplicates (updates if exists, creates if new)
                // Match by project_id and policy_number if provided, otherwise by project_id and provider_name
                $matchFields = ['project_id' => $projectId];
                if (!empty($data['policy_number'])) {
                    $matchFields['policy_number'] = $data['policy_number'];
                } elseif (!empty($data['provider_name'])) {
                    $matchFields['provider_name'] = $data['provider_name'];
                }
                
                // Check if policy already exists to preserve policy_id
                $existingPolicy = InsurancePolicy::where($matchFields)->first();
                
                $policyData = [
                    'project_id' => $projectId,
                    'provider_name' => $data['provider_name'] ?? null,
                    'policy_number' => $data['policy_number'] ?? null,
                    'policy_date' => $this->parseDate($data['policy_date'] ?? null),
                    'description' => $data['description'] ?? null,
                ];
                
                if ($existingPolicy) {
                    // Update existing policy (preserve policy_id)
                    $existingPolicy->update($policyData);
                } else {
                    // Create new policy
                    $policyData['policy_id'] = InsurancePolicy::generatePolicyId();
                    InsurancePolicy::create($policyData);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Import items from Material Input sheet
     */
    private function importItems($spreadsheet, $sheetName)
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            return ['imported' => 0, 'errors' => ['Sheet not found']];
        }

        $rows = $this->getSheetData($sheet);
        if (empty($rows)) {
            return ['imported' => 0, 'errors' => ['Sheet is empty']];
        }

        // Try to find header row
        $headerRowIndex = 0;
        $headers = [];
        
        if (!empty($rows[0])) {
            $testHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[0]))));
            if (!empty($testHeaders) && $this->isHeaderRow($testHeaders)) {
                $headerRowIndex = 0;
                $headers = $testHeaders;
            }
        }
        
        if (empty($headers) && !empty($rows[1])) {
            $testHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[1]))));
            if (!empty($testHeaders) && $this->isHeaderRow($testHeaders)) {
                $headerRowIndex = 1;
                $headers = $testHeaders;
            }
        }
        
        if (empty($headers) && !empty($rows[0])) {
            $headers = array_map('strtolower', array_filter(array_map('trim', array_values($rows[0]))));
            $headerRowIndex = 0;
        }
        
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        
        $imported = 0;
        $errors = [];

        foreach ($dataRows as $rowNum => $row) {
            $row = array_values($row);
            if (empty(array_filter($row, function($v) { return $v !== null && $v !== '' && $v !== 0; }))) {
                continue;
            }

            try {
                $data = $this->mapRowToArray($headers, $row, [
                    'item_id' => ['item id', 'item_id', 'item no'],
                    'name' => ['name', 'item name'],
                    'type' => ['type', 'item type'],
                    'brand' => ['brand'],
                    'model' => ['model'],
                    'unit' => ['unit'],
                    'warranty_details' => ['warranty', 'warranty details'],
                    'stock_total_amount' => ['stock total', 'total stock'],
                    'stock_delivered' => ['stock delivered', 'delivered'],
                    'stock_current_need' => ['current need', 'need'],
                ]);

                if (empty($data['name'])) {
                    $errors[] = "Row " . ($rowNum + $headerRowIndex + 2) . ": No item name found";
                    continue;
                }

                $itemId = $data['item_id'] ?? Item::generateItemId();

                Item::updateOrCreate(
                    ['item_id' => $itemId],
                    [
                        'name' => $data['name'],
                        'type' => $data['type'] ?? 'Other',
                        'brand' => $data['brand'] ?? null,
                        'model' => $data['model'] ?? null,
                        'unit' => $data['unit'] ?? 'pcs',
                        'warranty_details' => $data['warranty_details'] ?? null,
                        'stock_total_amount' => $this->parseInteger($data['stock_total_amount'] ?? 0),
                        'stock_delivered' => $this->parseInteger($data['stock_delivered'] ?? 0),
                        'stock_current_need' => $this->parseInteger($data['stock_current_need'] ?? 0),
                    ]
                );

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Import project material list from 'Material List' sheet
     * This updates:
     * 1. Project fields (Module, Inverter, Roof Type) in 'projects' table
     * 2. Material quantities in 'project_materials' table
     */
    private function importProjectItems($spreadsheet, $sheetName)
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            return ['imported' => 0, 'errors' => ['Sheet not found']];
        }

        $rows = $this->getSheetData($sheet);
        if (empty($rows)) {
            return ['imported' => 0, 'errors' => ['Sheet is empty']];
        }

        // Try to find header row (looking for Client Name, System Capacity etc)
        $headerRowIndex = 0;
        $headers = [];
        
        // Scan first 15 rows for headers
        for ($i = 0; $i < min(15, count($rows)); $i++) {
            $rowHeaders = array_map('strtolower', array_filter(array_map('trim', array_values($rows[$i]))));
            
            // Check for key columns that identify the Material List header
            $matches = 0;
            $keywords = ['client name', 'system capacity', 'module', 'inverter', 'roof type', 'mid clamp'];
            
            foreach ($keywords as $keyword) {
                if (in_array($keyword, $rowHeaders)) $matches++;
            }
            
            if ($matches >= 3) {
                $headerRowIndex = $i;
                $headers = $rowHeaders;
                break;
            }
        }
        
        if (empty($headers)) {
            // Fallback to row 10 (index 10, which is row 11 in Excel) which usually contains headers in this file format
            if (isset($rows[10])) {
                $headers = array_map('strtolower', array_filter(array_map('trim', array_values($rows[10]))));
                $headerRowIndex = 10;
            } else {
                return ['imported' => 0, 'errors' => ['Could not find header row']];
            }
        }
        
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        $imported = 0;
        $errors = [];

        foreach ($dataRows as $rowNum => $row) {
            $row = array_values($row);
            if (empty(array_filter($row, function($v) { return $v !== null && $v !== '' && $v !== 0; }))) {
                continue;
            }

            try {
                // Map columns to fields
                $data = $this->mapRowToArray($headers, $row, [
                    // Identification
                    'client_name' => ['client name', 'client'],
                    
                    // Project Technical Specs (Update projects table)
                    'procurement_status' => ['procurement status'],
                    'pv_system_capacity_kwp' => ['system capacity', 'capacity'],
                    'category' => ['category'],
                    'module' => ['module'],
                    'module_quantity' => ['module quantity', 'qty'],
                    'inverter' => ['inverter'],
                    'roof_type' => ['roof type'],
                    
                    // Materials (project_materials table)
                    'klip_lok_clamp' => ['klip lok clamp', 'klip lok'],
                    'l_foot' => ['l-foot', 'l foot'],
                    'tile_hook' => ['tile hook'],
                    'rail_2_6m' => ['2.6m rail'],
                    'rail_5_3m' => ['5.3m rail'],
                    'rail_4_7m' => ['4.7m rail'],
                    'rail_3_6m' => ['3.6m rail'],
                    'splicer' => ['splicer'],
                    'mid_clamp' => ['mid clamp'],
                    'end_clamp' => ['end clamp'],
                    'grounding_clip' => ['grounding clip'],
                    'grounding_lug' => ['grounding lug'],
                    'dongle' => ['dongle'],
                    'precast_concrete_block' => ['precast concrete block', 'concrete block'],
                    'dc_cable_4mmsq' => ['dc cable (4mmsq)', 'dc cable 4mm'],
                    'dc_cable_6mmsq' => ['dc cable (6mmsq)', 'dc cable 6mm'],
                    'pv_connector_male' => ['pv connctor (male)', 'pv connector (male)', 'male connector'],
                    'pv_connector_female' => ['pv connctor (female)', 'pv connector (female)', 'female connector'],
                    'isolator_switch_3p' => ['3p isolator switch', 'isolator switch'],
                    'kwh_meter_1phase' => ['1-phase kwh meter', '1 phase meter'],
                    'kwh_meter_3phase' => ['3-phase kwh meter', '3 phase meter'],
                    'pv_ac_db' => ['pv ac db', 'ac db'],
                    'data_logger' => ['data logger'],
                    'weather_station' => ['weather station'],
                    'bess' => ['bess'],
                    'ev_charger' => ['ev charger'],
                    'optimiser' => ['optimiser'],
                    'remark' => ['remark'],
                ]);

                $clientName = $data['client_name'] ?? null;
                if (empty($clientName)) {
                    continue; // Skip if no client name
                }

                // Find project by client name (since we might not have project ID in this sheet)
                // We typically need to associate this with an existing project.
                // Strategy: Find project where client name matches (via client relationship)
                
                $project = Project::whereHas('client', function($q) use ($clientName) {
                    $q->where('client_name', $clientName)
                      ->orWhere('client_name', 'like', '%' . $clientName . '%');
                })->first();

                // If not found by client name only, check if there's a project capacity match too for better accuracy
                if (!$project && !empty($data['pv_system_capacity_kwp'])) {
                    $capacity = $this->parseDecimal($data['pv_system_capacity_kwp']);
                    $project = Project::where('pv_system_capacity_kwp', $capacity)->first();
                }

                if (!$project) {
                    $errors[] = "Row " . ($rowNum + $headerRowIndex + 2) . ": Project for client '{$clientName}' not found";
                    continue;
                }

                // 1. Update Project fields
                $projectUpdateData = [];
                if (!empty($data['procurement_status'])) $projectUpdateData['procurement_status'] = $data['procurement_status'];
                if (!empty($data['module'])) $projectUpdateData['module'] = $data['module'];
                if (!empty($data['module_quantity'])) $projectUpdateData['module_quantity'] = $this->parseInteger($data['module_quantity']);
                if (!empty($data['inverter'])) $projectUpdateData['inverter'] = $data['inverter'];
                if (!empty($data['roof_type'])) $projectUpdateData['roof_type'] = $data['roof_type'];
                if (!empty($data['category'])) $projectUpdateData['category'] = $data['category'];
                
                if (!empty($projectUpdateData)) {
                    $project->update($projectUpdateData);
                }

                // 2. Update/Create Project Materials (Normalized Structure)
                // Get all materials from database with their codes
                $materials = \App\Models\Material::pluck('id', 'code');
                
                // Delete existing materials for this project (clean slate approach)
                \App\Models\ProjectMaterial::where('project_id', $project->project_id)->delete();
                
                // Store remark separately if it exists
                $remarkText = $data['remark'] ?? null;
                $firstMaterialCreated = false;
                
                // Insert new material quantities dynamically
                foreach ($materials as $code => $materialId) {
                    $quantity = $this->parseInteger($data[$code] ?? 0);
                    
                    if ($quantity > 0) {
                        \App\Models\ProjectMaterial::create([
                            'project_id' => $project->project_id,
                            'material_id' => $materialId,
                            'quantity' => $quantity,
                            // Attach remark to first material only (to avoid duplication)
                            'remark' => (!$firstMaterialCreated && $remarkText) ? $remarkText : null,
                        ]);
                        
                        $firstMaterialCreated = true;
                    }
                }


                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Helper: Map row data to array based on header mapping
     * (Existing helper methods continue here...)
     */
    private function mapRowToArray($headers, $row, $mapping)
    {
        $result = [];
        foreach ($mapping as $key => $possibleHeaders) {
            foreach ($possibleHeaders as $possibleHeader) {
                $index = array_search(strtolower($possibleHeader), $headers);
                if ($index !== false && isset($row[$index])) {
                    $result[$key] = $row[$index];
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Helper: Parse decimal value
     */
    private function parseDecimal($value)
    {
        if (empty($value)) return null;
        $value = str_replace([',', 'RM', '$'], '', (string)$value);
        return is_numeric($value) ? (float)$value : null;
    }

    /**
     * Helper: Parse integer value
     */
    private function parseInteger($value)
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (int)$value;
        // Extract numbers from string
        $cleaned = preg_replace('/[^0-9-]/', '', (string)$value);
        return $cleaned !== '' ? (int)$cleaned : null;
    }

    /**
     * Helper: Parse date value
     */
    private function parseDate($value)
    {
        if (empty($value)) return null;
        
        // Handle Excel date serial numbers
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Not an Excel date, try regular parsing
            }
        }
        
        // Try to parse as regular date
        try {
            $timestamp = strtotime($value);
            return $timestamp ? date('Y-m-d', $timestamp) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Helper: Update date field only if new value exists
     */
    private function updateDateField($project, $fieldName, $value)
    {
        $parsedDate = $this->parseDate($value);
        if ($parsedDate !== null) {
            // Workflow date fields are now in project_workflow_stages table
            // The __set() magic method will handle saving them to the workflow_stages table
            $project->{$fieldName} = $parsedDate;
        }
    }

    /**
     * Helper: Generate ID from name
     */
    private function generateIdFromName($name, $prefix)
    {
        $clean = strtoupper(preg_replace('/[^A-Z0-9]/', '', $name));
        return $prefix . '-' . substr($clean, 0, 8);
    }

    /**
     * Helper: Check if a row looks like a header row
     */
    private function isHeaderRow($headers)
    {
        $headerKeywords = ['project', 'client', 'name', 'category', 'scheme', 'location', 'value', 'status', 'date', 'amount'];
        $matches = 0;
        foreach ($headers as $header) {
            foreach ($headerKeywords as $keyword) {
                if (stripos($header, $keyword) !== false) {
                    $matches++;
                    break;
                }
            }
        }
        // If at least 3 header keywords match, it's likely a header row
        return $matches >= 3;
    }

    /**
     * Helper: Get sheet data safely, handling formula errors (optimized for large files)
     * Reads calculated values, not formulas
     */
    private function getSheetData($sheet)
    {
        $rows = [];
        
        try {
            // Use toArray with calculated values (true = calculate formulas)
            $data = $sheet->toArray(null, true, true, true); // true = calculate formulas to get values
            
            foreach ($data as $row) {
                // Filter out completely empty rows
                if (!empty(array_filter($row, function($v) { 
                    return $v !== null && $v !== '' && $v !== 0; 
                }))) {
                    $rows[] = array_values($row);
                }
            }
        } catch (\Exception $e) {
            // Fallback to row iterator if toArray fails
            try {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true); // Only existing cells for speed
                    
                    foreach ($cellIterator as $cell) {
                        try {
                            // Get calculated value (what Excel displays), not the formula
                            $value = $cell->getCalculatedValue();
                            
                            // If it's a formula error (starts with #), try formatted value
                            if (is_string($value) && strpos($value, '#') === 0) {
                                $value = $cell->getFormattedValue();
                            }
                            
                            // If still an error, get raw value
                            if (is_string($value) && strpos($value, '#') === 0) {
                                $value = $cell->getValue();
                            }
                            
                            $rowData[] = $value;
                        } catch (\Exception $e2) {
                            // If calculation fails, try formatted value
                            try {
                                $rowData[] = $cell->getFormattedValue();
                            } catch (\Exception $e3) {
                                // Last resort: get raw value
                                try {
                                    $rowData[] = $cell->getValue();
                                } catch (\Exception $e4) {
                                    $rowData[] = '';
                                }
                            }
                        }
                    }
                    
                    if (!empty(array_filter($rowData, function($v) { 
                        return $v !== null && $v !== '' && $v !== 0; 
                    }))) {
                        $rows[] = $rowData;
                    }
                }
            } catch (\Exception $e2) {
                return [];
            }
        }
        
        return $rows;
    }
}
