@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🏢 Manage Branches</h2>

    <ul class="nav nav-tabs mb-3" id="branchTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
                ➕ Create Branch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab">
                📋 Available Branches
            </button>
        </li>
    </ul>

    <div class="tab-content" id="branchTabsContent">
        <!-- Create Branch Tab -->
        <div class="tab-pane fade show active" id="create" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Create New Branch</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('branches.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Branch Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" id="address" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">💾 Save Branch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Available Branches Tab -->
        <div class="tab-pane fade" id="available" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Available Branches</h5>
                </div>
                <div class="card-body">
                    @if($branches->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Created</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branches as $branch)
                                    <tr>
                                        <td>{{ $branch->name }}</td>
                                        <td>{{ $branch->address }}</td>
                                        <td>{{ $branch->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- View -->
                                                <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-secondary" title="View Branch">
                                                    👁️
                                                </a>
                                
                                                <!-- Edit -->
                                                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary" title="Edit Branch">
                                                    ✏️
                                                </a>
                                
                                                <!-- Delete -->
                                                <form action="{{ route('branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this branch?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Branch">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No branches available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
