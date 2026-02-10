<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $query = Client::withCount('projects');
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_id', 'like', "%{$search}%")
                  ->orWhere('email_address', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        // Filter by contract type
        if ($request->has('contract_type') && $request->contract_type != '') {
            $query->where('contract_type', $request->contract_type);
        }
        
        $clients = $query->orderBy('client_name')->paginate(20);
        
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'ic_number' => 'nullable|string|max:255',
            'installation_address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:255',
            'email_address' => 'nullable|email|max:255',
            'payment_method' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
        ]);
        
        // Generate client ID
        $validated['client_id'] = Client::generateClientId();
        
        Client::create($validated);
        
        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully!');
    }

    /**
     * Display the specified client
     */
    public function show($id)
    {
        $client = Client::with('projects')->findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Updating client', ['client_id' => $id, 'method' => $request->method()]);
            
            $client = Client::findOrFail($id);
            
            $validated = $request->validate([
                'client_name' => 'required|string|max:255',
                'ic_number' => 'nullable|string|max:255',
                'installation_address' => 'nullable|string',
                'phone_number' => 'nullable|string|max:255',
                'email_address' => 'nullable|email|max:255',
                'payment_method' => 'nullable|string|max:255',
                'contract_type' => 'nullable|string|max:255',
            ]);
            
            // Ensure client_id is never updated (it's the primary key)
            // Also remove it from request if it somehow got in there
            unset($validated['client_id']);
            $request->merge(['client_id' => null]);
            
            // Update only the allowed fields
            $client->fill($validated);
            $client->save();
            
            Log::info('Client updated successfully', ['client_id' => $id]);
            
            return redirect()->route('clients.index')
                ->with('success', 'Client updated successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error updating client', [
                'client_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Handle database constraint errors
            if (str_contains($e->getMessage(), 'foreign key constraint') || 
                str_contains($e->getMessage(), 'Cannot delete') ||
                str_contains($e->getMessage(), '1451') || // MySQL foreign key constraint error code
                str_contains($e->getMessage(), '1452')) { // MySQL foreign key constraint error code
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cannot update client due to database constraints. The client may have associated projects that prevent this update.');
            }
            
            // Re-throw if it's a different database error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error updating client', [
                'client_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update client: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified client from storage
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        
        // Check if client has projects
        if ($client->projects()->count() > 0) {
            return redirect()->route('clients.index')
                ->with('error', 'Cannot delete client with existing projects. Please delete or reassign projects first.');
        }
        
        $client->delete();
        
        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully!');
    }
}
