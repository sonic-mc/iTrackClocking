@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-6 py-8">
        
        <!-- Header with Greeting -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                        Manager Dashboard
                    </h1>
                    <p class="text-gray-600">Welcome back! Here's what's happening today.</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ date('l, F j, Y') }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ date('g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Cards with Modern Design -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <!-- Total Employees -->
            <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Employees</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $employeeCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            </div>

            <!-- Present Today -->
            <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Present Today</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $presentCount }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $employeeCount > 0 ? round(($presentCount / $employeeCount) * 100) : 0 }}% attendance rate
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
            </div>

            <!-- Pending Leave Requests -->
            <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Requests</p>
                        <p class="text-3xl font-bold text-amber-600 mt-2">{{ $pendingLeaves }}</p>
                        @if($pendingLeaves > 0)
                            <p class="text-xs text-amber-600 mt-1 font-medium">Requires attention</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">All caught up!</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500 to-amber-600"></div>
            </div>

            <!-- Notifications -->
            <div class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Notifications</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $notifications }}</p>
                        @if($newNotifications > 0)
                            <p class="text-xs text-purple-600 mt-1 font-medium">New updates</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">No new alerts</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 4h7a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-purple-600"></div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Attendance Overview - Takes 2 columns -->
            <div class="xl:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Today's Attendance</h2>
                        </div>
                        <a href="{{ route('employees.attendance') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium">
                            View All
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Employee</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Clock In</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Clock Out</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($todayAttendance as $log)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr($log->employee->user->name, 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $log->employee->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600">
                                        {{ $log->clock_in_time ? \Carbon\Carbon::parse($log->clock_in_time)->format('g:i A') : 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-600">
                                        {{ $log->clock_out_time ? \Carbon\Carbon::parse($log->clock_out_time)->format('g:i A') : 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($log->clock_in_time)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-2"></div>
                                                Present
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-2"></div>
                                                Absent
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Leave Requests Sidebar -->
            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Leave Requests</h2>
                        </div>
                        <a href="{{ route('leave.approve') }}" 
                           class="text-amber-600 hover:text-amber-700 text-sm font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($recentLeaves->take(5) as $leave)
                        <div class="border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                        {{ substr($leave->employee->user->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900 text-sm">{{ $leave->employee->user->name }}</span>
                                </div>
                                @if($leave->status == 'approved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Approved
                                    </span>
                                @elseif($leave->status == 'rejected')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-1">{{ ucfirst($leave->leave_type) }} Leave</p>
                            <p class="text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M j') }} - 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('M j, Y') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action Cards -->
        <div class="mt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('employees.index') }}" 
                   class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Manage Employees</h4>
                            <p class="text-blue-100 text-sm">View and manage your team</p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('employees.attendance') }}" 
                   class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Attendance Reports</h4>
                            <p class="text-emerald-100 text-sm">Track team attendance</p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('leave.approve') }}" 
                   class="group relative overflow-hidden bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white hover:from-amber-600 hover:to-amber-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Approve Leaves</h4>
                            <p class="text-amber-100 text-sm">Review pending requests</p>
                            @if($pendingLeaves > 0)
                                <span class="inline-block mt-2 px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                                    {{ $pendingLeaves }} pending
                                </span>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection