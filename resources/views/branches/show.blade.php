@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">👁️ Branch Details</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $branch->name }}</dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $branch->address }}</dd>
            

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $branch->created_at->format('d M Y H:i') }}</dd>
            </dl>

            <div class="mt-4">
                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-primary">✏️ Edit Branch</a>
                <a href="{{ route('branches.index') }}" class="btn btn-secondary ms-2">↩️ Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
