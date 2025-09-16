@extends('layouts.app')

@section('content')
<style>
    /* Core Styles */
    :root {
        --primary-blue: #1a73e8;
        --secondary-blue: #4285f4;
        --success-green: #34a853;
        --warning-yellow: #fbbc04;
        --error-red: #ea4335;
        --bg-white: #ffffff;
        --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        --card-shadow-hover: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

   /* Enhanced Card Animations – Rectangular Horizontal Layout */
.metric-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
    position: relative;
    overflow: hidden;
    width: 100%;
    max-width: 300px;
    height: 160px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Gradient shimmer effect */
.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(26, 115, 232, 0.03), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

/* Hover elevation and shimmer */
.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.metric-card:hover::before {
    transform: translateX(100%);
}

/* Metric value styling */
.metric-value {
    font-size: 48px;
    font-weight: 600;
    color: #1a73e8;
    line-height: 1;
    margin: 16px 0;
    transition: all 0.3s ease;
}

.metric-card:hover .metric-value {
    transform: scale(1.05);
}

/* Label and trend styling */
.metric-label {
    font-size: 16px;
    color: #5f6368;
    font-weight: 500;
}

.metric-trend {
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 500;
    background: #e8f0fe;
    color: #1a73e8;
    display: inline-block;
    transition: all 0.3s ease;
}

.metric-card:hover .metric-trend {
    transform: translateX(5px);
}

    /* Tab System Styles */
    .tab-container {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
    }

    .tab-container:hover {
        box-shadow: var(--card-shadow-hover);
    }

    .tab-header {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        border-bottom: 2px solid #f1f3f4;
        padding-bottom: 16px;
    }

    .tab-button {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        color: #5f6368;
        transition: all 0.3s ease;
        position: relative;
    }

    .tab-button.active {
        color: var(--primary-blue);
    }

    .tab-button::after {
        content: '';
        position: absolute;
        bottom: -18px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--primary-blue);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .tab-button.active::after {
        transform: scaleX(1);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease forwards;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Table Enhancements */
    .dashboard-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .dashboard-table tr {
        transition: all 0.3s ease;
    }

    .dashboard-table tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }

    .status-badge {
        transition: all 0.3s ease;
    }

    .status-badge:hover {
        transform: translateY(-2px);
    }
</style>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard Overview</h1>
            <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}</p>
        </div>

        <!-- Metrics Cards -->
        <!-- Horizontal Metrics Row -->
        <div class="flex flex-wrap gap-6 mb-8 justify-start">
            <!-- Total Employees -->
            <div class="metric-card w-full sm:w-[300px] h-[160px] flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="metric-label">Total Employees</span>
                    <span class="metric-trend">
                        <span class="mr-1">+2</span>
                        <span class="text-success-green">↑</span>
                    </span>
                </div>
                <div class="metric-value">{{ $employeeCount }}</div>
                <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-blue" style="width: 75%"></div>
                </div>
            </div>

            <!-- Present Today -->
            <div class="metric-card w-full sm:w-[300px] h-[160px] flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="metric-label">Present Today</span>
                    <span class="metric-trend">
                        {{ $employeeCount > 0 ? round(($presentCount / $employeeCount) * 100) : 0 }}%
                    </span>
                </div>
                <div class="metric-value">{{ $presentCount }}</div>
                <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-success-green"
                        style="width: {{ $employeeCount > 0 ? round(($presentCount / $employeeCount) * 100) : 0 }}%">
                    </div>
                </div>
            </div>

            <!-- Pending Leaves -->
            <div class="metric-card w-full sm:w-[300px] h-[160px] flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="metric-label">Pending Leaves</span>
                    @if($pendingLeaves > 0)
                        <span class="metric-trend" style="background: #fff3e0; color: #f57c00;">
                            Needs Action
                        </span>
                    @endif
                </div>
                <div class="metric-value" style="color: #f57c00;">{{ $pendingLeaves }}</div>
                <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-warning-yellow" style="width: 45%"></div>
                </div>
            </div>
        </div>

        <!-- Tabbed Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Attendance Tab -->
            <div class="tab-container">
                <div class="tab-header">
                    <button class="tab-button active" onclick="switchTab('attendance')">
                        Today's Attendance
                    </button>
                    <a href="{{ route('employees.attendance') }}"
                       class="ml-auto text-primary-blue hover:text-secondary-blue font-medium">
                        View All
                    </a>
                </div>
                <div id="attendance" class="tab-content active">
                    <div class="overflow-x-auto">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Employee</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Clock In</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Clock Out</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayAttendance as $log)
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-primary-blue bg-opacity-10 flex items-center justify-center text-primary-blue font-medium">
                                                {{ substr($log->employee->user->name, 0, 1) }}
                                            </div>
                                            <span class="ml-3">{{ $log->employee->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $log->clock_in_time ? \Carbon\Carbon::parse($log->clock_in_time)->format('g:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $log->clock_out_time ? \Carbon\Carbon::parse($log->clock_out_time)->format('g:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->clock_in_time)
                                            <span class="status-badge bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                                Present
                                            </span>
                                        @else
                                            <span class="status-badge bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
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

            <!-- Leave Requests Tab -->
            <div class="tab-container">
                <div class="tab-header">
                    <button class="tab-button active" onclick="switchTab('leaves')">
                        Leave Requests
                    </button>
                    <a href="{{ route('leave.approve') }}"
                       class="ml-auto text-primary-blue hover:text-secondary-blue font-medium">
                        View All
                    </a>
                </div>
                <div id="leaves" class="tab-content active">
                    <div class="space-y-4">
                        @foreach($recentLeaves->take(5) as $leave)
                        <div class="p-4 rounded-lg border border-gray-100 hover:border-primary-blue transition-all hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        {{ substr($leave->employee->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $leave->employee->user->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ ucfirst($leave->leave_type) }} Leave
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    @if($leave->status == 'approved')
                                        <span class="status-badge bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                            Approved
                                        </span>
                                    @elseif($leave->status == 'rejected')
                                        <span class="status-badge bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="status-badge bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M j') }} -
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('M j, Y') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    // Get all tab buttons and content
    const buttons = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    // Remove active class from all buttons and content
    buttons.forEach(button => button.classList.remove('active'));
    contents.forEach(content => content.classList.remove('active'));

    // Add active class to clicked button and corresponding content
    document.querySelector(`button[onclick="switchTab('${tabId}')"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

// Keep the time update functionality
document.addEventListener('DOMContentLoaded', function() {
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        const dateString = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const timeElement = document.querySelector('[id$="current-time"]');
        const dateElement = document.querySelector('[id$="current-date"]');
        
        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
    }
    
    updateTime();
    setInterval(updateTime, 1000);
});
</script>
@endsection
