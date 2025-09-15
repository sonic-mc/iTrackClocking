@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">✏️ Edit Branch</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Update Branch Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('branches.update', $branch) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Branch Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $branch->address) }}" required>
                    </div>
                  
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">💾 Update Branch</button>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary ms-2">↩️ Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
