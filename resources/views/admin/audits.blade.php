@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">System Activity Logs</h2>
    <span class="text-muted">Audit trail of user actions across the platform</span>
</div>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        {{-- Filters --}}
        <form method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="action" class="form-label">Action</label>
                    <input type="text" name="action" id="action" class="form-control" placeholder="Search action..." value="{{ request('action') }}">
                </div>

                <div class="col-md-2">
                    <label for="from" class="form-label">From</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                </div>

                <div class="col-md-2">
                    <label for="to" class="form-label">To</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.logs.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-outline-success me-2">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>
            <a href="{{ route('admin.logs.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>

        {{-- Logs Table --}}
        @if($logs->count())
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>
                            @if($log->user)
                                <span class="fw-semibold text-dark">{{ $log->user->name }}</span><br>
                                <small class="text-muted">{{ $log->user->email }}</small>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td><span class="badge bg-info text-dark">{{ ucfirst($log->action) }}</span></td>
                        <td>{{ $log->description ?? '—' }}</td>
                        <td>{{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '—' }}</td>
                        <td>{{ $log->updated_at ? $log->updated_at->format('d M Y, H:i') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @else
            <div class="alert alert-warning mt-4">
                <i class="bi bi-exclamation-circle me-2"></i> No logs found for the selected filters.
            </div>
        @endif
    </div>
</div>
@endsection
