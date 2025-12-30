<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    /**
     * Check if user has finance access (Finance or Project Manager)
     */
    private function hasFinanceAccess(): bool
    {
        $role = auth()->user()->role ?? '';
        return in_array($role, ['Finance', 'Project Manager']);
    }

    /**
     * Check if user can perform CRUD operations
     */
    private function canPerformCRUD(): bool
    {
        return $this->hasFinanceAccess();
    }

    /**
     * Display finance overview with payment data from database
     */
    public function overview(Request $request)
    {
        // All authenticated users can view finance overview

        $query = Project::with(['client', 'salesPic', 'payments']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            // This will be handled in the view based on payment calculations
        }

        $projects = $query->orderBy('project_id')->get();

        // Transform projects data for JavaScript
        $projectsData = $projects->map(function($project) {
            $invoices = [];
            $payments = [];
            $voInvoiceAmount = 0;
            $voPaymentAmount = 0;
            
            foreach($project->payments as $payment) {
                if (stripos($payment->description ?? '', 'VO') !== false) {
                    $voInvoiceAmount += $payment->invoice_amount ?? 0;
                    $voPaymentAmount += $payment->payment_amount ?? 0;
                } else {
                    if ($payment->invoice_amount) {
                        $invoices[] = [
                            'amount' => (float)$payment->invoice_amount,
                            'date' => $payment->invoice_date ? $payment->invoice_date->format('Y-m-d') : null
                        ];
                    }
                    if ($payment->payment_amount) {
                        $payments[] = [
                            'amount' => (float)$payment->payment_amount,
                            'date' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : null
                        ];
                    }
                }
            }
            
            return [
                'projectNo' => $project->project_id,
                'client' => $project->client->client_name ?? '',
                'salesPic' => $project->salesPic->name ?? '',
                'projectValue' => (float)($project->project_value_rm ?? 0),
                'vo' => (float)($project->vo_rm ?? 0),
                'invoices' => $invoices,
                'payments' => $payments,
                'voInvoiceAmount' => $voInvoiceAmount,
                'voPaymentAmount' => $voPaymentAmount,
            ];
        });

        return view('finance-overview', compact('projects', 'projectsData'));
    }

    /**
     * Display finance tracker (for creating/editing finance data)
     */
    public function tracker(Request $request, $projectId = null)
    {
        // All authenticated users can view, but only Finance and PM can edit (CRUD)
        $canEdit = $this->canPerformCRUD();

        $project = null;
        $payments = collect([]);
        $paymentsData = [];

        if ($projectId) {
            $project = Project::with(['client', 'salesPic', 'payments'])
                ->findOrFail($projectId);
            $payments = $project->payments()->orderBy('description')->get();
            
            // Transform payments data for JavaScript
            $paymentsData = $payments->map(function($payment) {
                return [
                    'payment_id' => $payment->payment_id,
                    'description' => $payment->description,
                    'invoice_date' => $payment->invoice_date ? $payment->invoice_date->format('Y-m-d') : null,
                    'invoice_amount' => $payment->invoice_amount,
                    'payment_date' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : null,
                    'payment_amount' => $payment->payment_amount,
                ];
            })->toArray();
        }

        $projects = Project::with('client')->orderBy('project_id')->get();

        return view('finance-tracker', compact('project', 'payments', 'paymentsData', 'projects', 'canEdit'));
    }

    /**
     * Store or update finance data for a project
     */
    public function store(Request $request)
    {
        if (!$this->canPerformCRUD()) {
            abort(403, 'Only Finance and Project Manager can perform this action.');
        }

        $validated = $request->validate([
            'project_id' => 'required|string|exists:projects,project_id',
            'project_value_rm' => 'nullable|numeric|min:0',
            'vo_rm' => 'nullable|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.payment_id' => 'nullable|string',
            'payments.*.description' => 'required_with:payments|string|max:255',
            'payments.*.invoice_date' => 'nullable|date',
            'payments.*.invoice_amount' => 'nullable|numeric|min:0',
            'payments.*.payment_date' => 'nullable|date',
            'payments.*.payment_amount' => 'nullable|numeric|min:0',
            'vo_invoice_date' => 'nullable|date',
            'vo_invoice_amount' => 'nullable|numeric|min:0',
            'vo_payment_date' => 'nullable|date',
            'vo_payment_amount' => 'nullable|numeric|min:0',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        // Update project financial data
        if (isset($validated['project_value_rm'])) {
            $project->project_value_rm = $validated['project_value_rm'];
        }
        if (isset($validated['vo_rm'])) {
            $project->vo_rm = $validated['vo_rm'];
        }
        $project->save();

        // Handle regular payments
        if (isset($validated['payments']) && is_array($validated['payments'])) {
            foreach ($validated['payments'] as $paymentData) {
                // Skip if no data provided
                if (empty($paymentData['description']) && 
                    empty($paymentData['invoice_amount']) && 
                    empty($paymentData['payment_amount'])) {
                    continue;
                }
                
                if (isset($paymentData['payment_id']) && $paymentData['payment_id']) {
                    // Update existing payment
                    $payment = Payment::findOrFail($paymentData['payment_id']);
                    $payment->update([
                        'description' => $paymentData['description'] ?? $payment->description,
                        'invoice_date' => $paymentData['invoice_date'] ?? null,
                        'invoice_amount' => $paymentData['invoice_amount'] ?? null,
                        'payment_date' => $paymentData['payment_date'] ?? null,
                        'payment_amount' => $paymentData['payment_amount'] ?? null,
                    ]);
                } else {
                    // Create new payment only if there's actual data
                    if (!empty($paymentData['description']) || 
                        !empty($paymentData['invoice_amount']) || 
                        !empty($paymentData['payment_amount'])) {
                        Payment::create([
                            'payment_id' => Payment::generatePaymentId(),
                            'project_id' => $project->project_id,
                            'description' => $paymentData['description'] ?? 'Payment',
                            'invoice_date' => $paymentData['invoice_date'] ?? null,
                            'invoice_amount' => $paymentData['invoice_amount'] ?? null,
                            'payment_date' => $paymentData['payment_date'] ?? null,
                            'payment_amount' => $paymentData['payment_amount'] ?? null,
                        ]);
                    }
                }
            }
        }

        // Handle VO payment if provided
        if (isset($validated['vo_invoice_amount']) || isset($validated['vo_payment_amount'])) {
            $voPayment = Payment::where('project_id', $project->project_id)
                ->where('description', 'like', '%VO%')
                ->first();

            if ($voPayment) {
                $voPayment->update([
                    'invoice_date' => $validated['vo_invoice_date'] ?? null,
                    'invoice_amount' => $validated['vo_invoice_amount'] ?? null,
                    'payment_date' => $validated['vo_payment_date'] ?? null,
                    'payment_amount' => $validated['vo_payment_amount'] ?? null,
                ]);
            } else {
                Payment::create([
                    'payment_id' => Payment::generatePaymentId(),
                    'project_id' => $project->project_id,
                    'description' => 'VO Payment',
                    'invoice_date' => $validated['vo_invoice_date'] ?? null,
                    'invoice_amount' => $validated['vo_invoice_amount'] ?? null,
                    'payment_date' => $validated['vo_payment_date'] ?? null,
                    'payment_amount' => $validated['vo_payment_amount'] ?? null,
                ]);
            }
        }

        return redirect()->route('finance.tracker.project', $project->project_id)
            ->with('success', 'Finance data saved successfully.');
    }

    /**
     * Delete a payment
     */
    public function destroyPayment($paymentId)
    {
        if (!$this->canPerformCRUD()) {
            abort(403, 'Only Finance and Project Manager can perform this action.');
        }

        $payment = Payment::findOrFail($paymentId);
        $projectId = $payment->project_id;
        $payment->delete();

        return redirect()->route('finance.tracker.project', $projectId)
            ->with('success', 'Payment deleted successfully.');
    }
}

