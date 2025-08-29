@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Add Employee</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf

        <div>
            <label for="user_id" class="block font-medium">Select User</label>
            <select name="user_id" id="user_id" class="w-full border rounded p-2">
                <option value="">-- Select User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="employee_number" class="block font-medium">Employee Number</label>
            <input type="text" name="employee_number" id="employee_number" class="w-full border rounded p-2" value="{{ old('employee_number') }}">
            @error('employee_number') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="branch_name" class="block font-medium">Branch</label>
            <input type="text" name="branch_name" id="branch_name" class="w-full border rounded p-2" value="{{ old('branch_name') }}">
            @error('branch_name') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="department_name" class="block font-medium">Department</label>
            <input type="text" name="department_name" id="department_name" class="w-full border rounded p-2" value="{{ old('department_name') }}">
            @error('department_name') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="position" class="block font-medium">Position</label>
            <input type="text" name="position" id="position" class="w-full border rounded p-2" value="{{ old('position') }}">
            @error('position') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="status" class="block font-medium">Status</label>
            <select name="status" id="status" class="w-full border rounded p-2">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
            @error('status') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Employee</button>
        </div>
    </form>
</div>
@endsection
