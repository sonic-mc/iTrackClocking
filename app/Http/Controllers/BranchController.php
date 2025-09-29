<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Traits\AuditLogger;

class BranchController extends Controller
{
    use AuditLogger;

    public function index()
    {
        $branches = Branch::latest()->get();
        $this->logAudit('view_branches', 'Viewed branch list');
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $branches = Branch::latest()->get();
        $this->logAudit('create_branch_page', 'Opened create branch page');
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'geofence_coordinates' => 'nullable|json',
        ]);

        $branch = Branch::create($request->all());

        $this->logAudit('create_branch', "Created branch #{$branch->id} ({$branch->name})");

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        $this->logAudit('view_branch', "Viewed branch #{$branch->id} ({$branch->name})");
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $this->logAudit('edit_branch_page', "Opened edit page for branch #{$branch->id} ({$branch->name})");
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'geofence_coordinates' => 'nullable|json',
        ]);

        $branch->update($request->all());

        $this->logAudit('update_branch', "Updated branch #{$branch->id} ({$branch->name})");

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branchId = $branch->id;
        $branchName = $branch->name;
        $branch->delete();

        $this->logAudit('delete_branch', "Deleted branch #{$branchId} ({$branchName})");

        return redirect()->route('branches.index')->with('success', 'Branch deleted.');
    }
}
