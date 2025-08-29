@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Attendance Overview</h2>

    @foreach($employees as $employee)
        <div class="mb-6 border p-4 rounded shadow">
            <h3 class="text-lg font-semibold">
                {{ $employee->user->name ?? 'N/A' }}
            </h3>

            @if($employee->attendanceLogs->isEmpty())
                <p class="text-gray-500">No attendance logs.</p>
            @else
                <table class="table-auto w-full border-collapse border border-gray-300 mt-2">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Clock In</th>
                            <th class="border px-4 py-2">Clock Out</th>
                            <th class="border px-4 py-2">Geofence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->attendanceLogs as $log)
                            <tr>
                                <td class="border px-4 py-2">{{ $log->clock_in_time ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $log->clock_out_time ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    {{ $log->geofence_status ? '✅ Inside' : '❌ Outside' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
</div>
@endsection
