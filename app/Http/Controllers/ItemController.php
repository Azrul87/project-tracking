<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMaterial;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display the Project Material Matrix
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'materials']);
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($c) use ($search) {
                      $c->where('client_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $projects = $query->paginate(20);
        
        // Load material columns dynamically from database
        $materialColumns = \App\Models\Material::orderBy('category')
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        // Get unique categories for filter dropdown
        $categories = Project::distinct()->pluck('category')->filter()->sort()->values();
        
        return view('inventory', compact('projects', 'categories', 'materialColumns'));
    }
}
