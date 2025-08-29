@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Employees</h2>

    <a href="{{ route('employees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Add Employee
    </a>

    @if($employees->isEmpty())
        <p>No employees found.</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Employee Name</th>
                    <th class="border px-4 py-2">Employee Number</th>
                    <th class="border px-4 py-2">Branch</th>
                    <th class="border px-4 py-2">Department</th>
                    <th class="border px-4 py-2">Position</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td class="border px-4 py-2">{{ $employee->user->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $employee->employee_number }}</td>
                    <td class="border px-4 py-2">{{ $employee->branch->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $employee->department->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $employee->position }}</td>
                    <td class="border px-4 py-2">{{ ucfirst($employee->status) }}</td>
                    <td class="border px-4 py-2 text-center">
                        <a href="{{ route('employees.show', $employee->id) }}" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">View</a>
                        <a href="{{ route('employees.edit', $employee->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
