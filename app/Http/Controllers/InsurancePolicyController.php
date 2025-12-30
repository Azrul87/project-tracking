<?php

namespace App\Http\Controllers;

use App\Models\InsurancePolicy;
use App\Models\Project;
use Illuminate\Http\Request;

class InsurancePolicyController extends Controller
{
    /**
     * Display a listing of insurance policies.
     */
    public function index(Request $request)
    {
        $query = InsurancePolicy::with('project.client');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('policy_number', 'like', "%{$search}%")
                  ->orWhere('provider_name', 'like', "%{$search}%")
                  ->orWhereHas('project', function($projectQuery) use ($search) {
                      $projectQuery->where('project_id', 'like', "%{$search}%")
                                   ->orWhereHas('client', function($clientQuery) use ($search) {
                                       $clientQuery->where('client_name', 'like', "%{$search}%");
                                   });
                  });
            });
        }

        $policies = $query->latest('created_at')->paginate(20);
        $projects = Project::with('client')->orderBy('project_id')->get();

        return view('insurance-tracker', compact('policies', 'projects'));
    }

    /**
     * Store a newly created insurance policy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|string|exists:projects,project_id',
            'provider_name' => 'required|string|max:255',
            'policy_number' => 'nullable|string|max:255',
            'policy_date' => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['policy_id'] = InsurancePolicy::generatePolicyId();

        InsurancePolicy::create($validated);

        return redirect()->route('insurance-tracker.index')
            ->with('success', 'Insurance policy created successfully.');
    }

    /**
     * Update the specified insurance policy.
     */
    public function update(Request $request, InsurancePolicy $insurancePolicy)
    {
        $validated = $request->validate([
            'project_id' => 'required|string|exists:projects,project_id',
            'provider_name' => 'required|string|max:255',
            'policy_number' => 'nullable|string|max:255',
            'policy_date' => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ]);

        $insurancePolicy->update($validated);

        return redirect()->route('insurance-tracker.index')
            ->with('success', 'Insurance policy updated successfully.');
    }

    /**
     * Remove the specified insurance policy.
     */
    public function destroy(InsurancePolicy $insurancePolicy)
    {
        $insurancePolicy->delete();

        return redirect()->route('insurance-tracker.index')
            ->with('success', 'Insurance policy deleted successfully.');
    }
}

