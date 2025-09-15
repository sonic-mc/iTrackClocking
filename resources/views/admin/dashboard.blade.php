@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('page-header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">Complete system overview and management controls</p>
    </div>
    <div class="flex gap-4">
        <button class="btn-secondary" onclick="refreshDashboard()">
            <span id="refreshIcon">🔄</span> Refresh
        </button>
        <button class="btn-primary" onclick="exportReports()">
            📊 Export Reports
        </button>
    </div>
</div>
@endsection

@section('content')
<style>
    /* Dashboard Specific Styles */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .stat-icon {
        font-size: 24px;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f8fafc;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 14px;
        color: var(--secondary-color);
        font-weight: 500;
    }
    
    .stat-change {
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 8px;
    }
    
    .stat-change.positive {
        color: var(--success-color);
    }
    
    .stat-change.negative {
        color: var(--danger-color);
    }
    
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 20px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: var(--border-radius);
        text-decoration: none;
        color: #475569;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .quick-action-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .quick-action-icon {
        font-size: 32px;
    }
    
    .quick-action-label {
        font-weight: 600;
        text-align: center;
    }
    
    .alert-card {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .alert-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }
    
    .alert-item:hover {
        background: #f8fafc;
    }
    
    .alert-item:last-child {
        border-bottom: none;
    }
    
    .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .alert-icon.warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .alert-icon.danger {
        background: #fecaca;
        color: #991b1b;
    }
    
    .alert-icon.info {
        background: #dbeafe;
        color: #1d4ed8;
    }
    
    .alert-content {
        flex: 1;
    }
    
    .alert-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 4px;
    }
    
    .alert-desc {
        font-size: 14px;
        color: var(--secondary-color);
    }
    
    .alert-time {
        font-size: 12px;
        color: #94a3b8;
        white-space: nowrap;
    }
    
    .chart-container {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        padding: 24px;
        height: 400px;
    }
    
    .table-container {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background: #f8fafc;
        padding: 12px 24px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .data-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: white;
        color: var(--secondary-color);
        border: 1px solid #e2e8f0;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .geofence-map {
        height: 300px;
        background: #f8fafc;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
        border: 2px dashed #e2e8f0;
    }
    
    .form-select {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #475569;
        font-size: 14px;
        cursor: pointer;
    }
    
    .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
    }
    
    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .badge.success {
        background: #dcfce7;
        color: #166534;
    }
    
    .badge.warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .badge.danger {
        background: #fecaca;
        color: #991b1b;
    }
    
    .badge.info {
        background: #dbeafe;
        color: #1d4ed8;
    }
    
    .text-success { color: var(--success-color); }
    .text-danger { color: var(--danger-color); }
    .text-secondary { color: var(--secondary-color); }
    
    .w-8 { width: 2rem; }
    .h-8 { height: 2rem; }
    .bg-blue-500 { background-color: #3b82f6; }
    .bg-green-500 { background-color: #10b981; }
    .bg-purple-500 { background-color: #8b5cf6; }
    .bg-red-500 { background-color: #ef4444; }
    .rounded-full { border-radius: 9999px; }
    .text-white { color: white; }
    .flex { display: flex; }
    .items-center { align-items: center; }
    .gap-3 { gap: 0.75rem; }
    .gap-4 { gap: 1rem; }
    .text-sm { font-size: 0.875rem; }
    .font-semibold { font-weight: 600; }
    .text-right { text-align: right; }
    .justify-between { justify-content: space-between; }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions-grid {
            grid-template-columns: 1fr;
        }
        
        .data-table {
            font-size: 12px;
        }
        
        .data-table th,
        .data-table td {
            padding: 8px 12px;
        }
        
        .flex {
            flex-direction: column;
            align-items: stretch;
        }
        
        .justify-between {
            justify-content: flex-start;
        }
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<!-- Key Metrics Overview -->
<div class="stats-grid">
    <!-- Total Employees -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #eff6ff; color: var(--primary-color);">
                👥
            </div>
            <div class="text-right">
                <div class="stat-change positive">
                    ↗ +{{ $newEmployeesThisMonth ?? 0 }} this month
                </div>
            </div>
        </div>
        <div class="stat-value">{{ $totalEmployees ?? 0 }}</div>
        <div class="stat-label">Total Employees</div>
    </div>
    

   <!-- Currently Clocked In -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #dcfce7; color: var(--success-color);">
                ✅
            </div>
            <div class="text-right">
                <div class="stat-change positive">
                    ↗ {{ $attendanceRate }}% attendance rate
                </div>
            </div>
        </div>
        <div class="stat-value">{{ $currentlyClocked ?? 0 }}</div>
        <div class="stat-label">Currently Clocked In</div>
    </div>


   <!-- Late Arrivals Today -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #fef3c7; color: var(--warning-color);">
                ⚠️
            </div>
            <div class="text-right">
                <div class="stat-change negative">
                    ↘ -3 from yesterday
                </div>
            </div>
        </div>
        <div class="stat-value">{{ $lateArrivals ?? 0 }}</div>
        <div class="stat-label">Late Arrivals Today</div>
    </div>


   <!-- Overtime Hours -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #fecaca; color: var(--danger-color);">
                ⏳
            </div>
            <div class="text-right">
                <div class="stat-change positive">
                    ↗ +1% this week
                </div>
            </div>
        </div>
        <div class="stat-value">{{ $overtimeHours ?? 0 }}h</div>
        <div class="stat-label">Weekly Overtime</div>
    </div>


   <!-- Pending Leave Requests -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;">
                📝
            </div>
        </div>
        <div class="stat-value">{{ $pendingLeaves ?? 0 }}</div>
        <div class="stat-label">Pending Leave Requests</div>
    </div>


    <!-- Geofence Violations -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon" style="background: #fecaca; color: var(--danger-color);">
                📍
            </div>
            <div class="text-right">
                <div class="stat-change negative">
                    ↘ -2 from yesterday
                </div>
            </div>
        </div>
        <div class="stat-value">{{ $geofenceViolations ?? 0 }}</div>
        <div class="stat-label">Geofence Violations</div>
    </div>
</div>

<!-- Quick Actions -->
<!-- Quick Actions -->
<div class="card mb-4"> <!-- Added mb-4 for spacing -->
    <div class="card-header">
        <h3 class="card-title">Quick Actions</h3>
    </div>
    <div class="card-body">
        <div class="quick-actions-grid">
            <a href="{{ route('admin.employees.create') }}" class="quick-action-btn">
                <div class="quick-action-icon">👤➕</div>
                <div class="quick-action-label">Add Employee</div>
            </a>
            
            <a href="{{ route('branches.create') }}" class="quick-action-btn">
                <div class="quick-action-icon">🏢</div>
                <div class="quick-action-label">Manage Branches</div>
            </a>
            
            
            <a href="{{ route('geofence.manage') }}" class="quick-action-btn">
                <div class="quick-action-icon">🗺️</div>
                <div class="quick-action-label">Manage Geofence</div>
            </a>
            
            <button class="quick-action-btn" onclick="exportPayrollData()">
                <div class="quick-action-icon">💰</div>
                <div class="quick-action-label">Export Payroll</div>
            </button>
            
            <a href="{{ route('shifts.manage') }}" class="quick-action-btn">
                <div class="quick-action-icon">🔄</div>
                <div class="quick-action-label">Manage Shifts</div>
            </a>
            
            <button class="quick-action-btn" onclick="sendBulkNotification()">
                <div class="quick-action-icon">📢</div>
                <div class="quick-action-label">Send Notification</div>
            </button>
        </div>
    </div>
</div>


<div class="dashboard-grid">
    <!-- Real-time Alerts & Notifications -->
    <div class="alert-card">
        <div class="card-header">
            <h3 class="card-title">Real-time Alerts</h3>
            <button class="btn-secondary" onclick="refreshAlerts()">
                🔄 Refresh
            </button>
        </div>
        <div id="alertsList">
            @forelse($notifications as $notification)
                <div class="alert-item">
                    <div class="alert-icon 
                        {{ $notification->type === 'geofence' ? 'danger' :
                           ($notification->type === 'late' ? 'warning' :
                           ($notification->type === 'leave' ? 'info' : 'warning')) }}">
                        {{ $notification->type === 'geofence' ? '⚠️' :
                           ($notification->type === 'late' ? '🕐' :
                           ($notification->type === 'leave' ? '📝' : '⏳')) }}
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">{{ ucfirst($notification->type) }}</div>
                        <div class="alert-desc">{{ $notification->message }}</div>
                    </div>
                    <div class="alert-time">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <p class="text-gray-500">No alerts right now 🚀</p>
            @endforelse
        </div>
    </div>
    

    <!-- Live Employee Status -->
    <div class="table-container">
        <div class="table-header">
            <h3 class="card-title">Live Employee Status</h3>
            <div class="flex gap-4">
                <select class="form-select" onchange="filterEmployees(this.value)">
                    <option value="all">All Employees</option>
                    <option value="clocked-in">Clocked In</option>
                    <option value="clocked-out">Clocked Out</option>
                    <option value="late">Late</option>
                    <option value="overtime">Overtime</option>
                </select>
            </div>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Clock In</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="employeeStatusTable">
                    @foreach($todayAttendance as $log)
                    @php
                        $status = 'Clocked Out';
                        $badge = 'primary';
                        $icon = '⚪';
                
                        $shiftStart = \Carbon\Carbon::parse('08:00:00');
                        $overtimeThreshold = 9;
                
                        $clockIn = \Carbon\Carbon::parse($log->clock_in_time);
                
                        if (!$log->clock_out_time) {
                            $workedHours = now()->diffInHours($clockIn);
                
                            if ($clockIn->gt($shiftStart)) {
                                $status = 'Late';
                                $badge = 'danger';
                                $icon = '🔴';
                            } elseif ($workedHours > $overtimeThreshold) {
                                $status = 'Overtime';
                                $badge = 'warning';
                                $icon = '⏳';
                            } else {
                                $status = 'Clocked In';
                                $badge = 'success';
                                $icon = '🟢';
                            }
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($log->employee->user->name,0,1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $log->employee->user->name }}</div>
                                    <div class="text-sm text-secondary">{{ $log->employee->position ?? 'Employee' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge {{ $badge }}">{{ $icon }} {{ $status }}</span></td>
                        <td>{{ $clockIn->format('h:i A') }}</td>
                        <td><span class="text-success">📍 In Zone</span></td>
                        <td>
                            <button class="btn-secondary" onclick="viewEmployee('{{ $log->employee->id }}')">View</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="dashboard-grid" style="display: flex; gap: 24px; flex-wrap: wrap;">
    
    <!-- Weekly Attendance Trends -->
    <div class="card" style="flex: 1; min-width: 400px;">
        <div class="card-header">
            <h3 class="card-title">Weekly Attendance Trends</h3>
        </div>
        <div class="card-body">
            <canvas id="attendanceChart" style="width: 100%; height: 400px;"></canvas>
        </div>
    </div>

    <!-- Geofence Status -->
    <div class="card" style="flex: 1; min-width: 400px;">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title">Geofence Status</h3>
            <a href="{{ route('geofence.manage') }}" class="btn btn-sm btn-primary">
                ⚙️ Manage Zones
            </a>
        </div>
        <div class="card-body">
            <div id="geofenceMap" style="height: 400px; border-radius: 6px; overflow: hidden;">
                <small>🗺️ Interactive map loading... Click "Manage Zones" to configure geofencing.</small>
            </div>
            <div class="mt-4 flex justify-between items-center text-sm">
                <span>Active Zones: <strong>{{ $activeZones ?? 0 }}</strong></span>
                <span>Employees in Zone: 
                    <strong>{{ $employeesInZone ?? 0 }}/{{ $totalEmployees ?? 0 }}</strong>
                </span>
            </div>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dates),
            datasets: [{
                label: 'Clocked In Employees',
                data: @json($attendanceCounts),
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    
        // Blade → JS variable injection
        const inZone = {{ $inZone ?? 0 }};
        const outOfZone = {{ $outOfZone ?? 0 }};
        const violations = {{ $violations ?? 0 }};
        const firstZoneRadius = {{ $zones->first()->radius ?? 0 }};
        const zoneActive = {{ $zones->count() > 0 ? 'true' : 'false' }};
    
        const map = L.map('geofenceMap').setView([-17.8252, 31.0335], 13);
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    
        const zones = @json($zones ?? []);
        const employees = @json($employeesInZoneData ?? []);
    
        zones.forEach(zone => {
            L.circle([zone.lat, zone.lng], {
                radius: zone.radius,
                color: 'blue',
                fillColor: '#2196f3',
                fillOpacity: 0.4
            }).addTo(map).bindPopup(`📍 ${zone.name}`);
        });
    
        employees.forEach(emp => {
            L.marker([emp.lat, emp.lng])
                .addTo(map)
                .bindPopup(`👤 ${emp.name}`);
        });
    
        const summaryControl = L.control({ position: 'topright' });
        summaryControl.onAdd = function () {
            const div = L.DomUtil.create('div', 'geofence-summary');
            div.innerHTML = `
                <div style="background: white; padding: 12px; border-radius: 8px; 
                            box-shadow: 0 2px 6px rgba(0,0,0,0.2); font-size: 14px;">
                    <div style="margin-bottom: 8px;">
                        🗺️ <strong>Main Office Zone</strong><br>
                        <small>Radius: ${firstZoneRadius}m | Active: ${zoneActive === 'true' ? 'Yes' : 'No'}</small>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>🟢 In Zone: ${inZone}</div>
                        <div>🔴 Out of Zone: ${outOfZone}</div>
                        <div>⚠️ Violations: ${violations}</div>
                    </div>
                </div>
            `;
            return div;
        };
        summaryControl.addTo(map);
    
    });
    </script>
     
</div>

<!-- Biometric Authentication Overview -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Biometric Authentication Status</h3>
        <button class="btn-primary" onclick="openBiometricDashboard()">
            🔐 Manage Biometrics
        </button>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #dcfce7; color: var(--success-color);">
                        👆
                    </div>
                </div>
                <div class="stat-value">{{ $fingerprintUsers }}</div>
                <div class="stat-label">Fingerprint Enrolled</div>
            </div>
            
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #dbeafe; color: var(--primary-color);">
                        👤
                    </div>
                </div>
                <div class="stat-value">{{ $faceIdUsers }}</div>
                <div class="stat-label">Face Recognition Enrolled</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #fef3c7; color: var(--warning-color);">
                        ⚠️
                    </div>
                </div>
                <div class="stat-value">{{ $pendingEnrollment }}</div>
                <div class="stat-label">Pending Enrollment</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Log -->
<div class="table-container">
    <div class="table-header">
        <h3 class="card-title">Recent System Activity</h3>
        <div class="flex gap-4">
            <select class="form-select" onchange="filterActivityLog(this.value)">
                <option value="all">All Activities</option>
                <option value="clock">Clock In/Out</option>
                <option value="leave">Leave Requests</option>
                <option value="admin">Admin Actions</option>
                <option value="violations">Violations</option>
            </select>
            <a href="{{ route('admin.audit') }}" class="btn-secondary">
                📊 Full Audit Log
            </a>
        </div>
    </div>
    <div style="max-height: 300px; overflow-y: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Employee</th>
                    <th>Activity</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="activityLogTable">
                @foreach($recentActivities as $log)
                    <tr>
                        <td>{{ $log->created_at->format('h:i A') }}</td>
                        <td>{{ $log->user->employee->name ?? $log->user->name ?? 'System' }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->ip_address ?? '🌐 System' }}</td>
                        <td><span class="badge info">Logged</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript for Dashboard Functionality -->
<script>
// Auto-refresh dashboard every 30 seconds
let autoRefreshInterval;

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        refreshDashboardData();
    }, 30000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Refresh dashboard data
function refreshDashboard() {
    const refreshIcon = document.getElementById('refreshIcon');
    refreshIcon.style.animation = 'spin 1s linear infinite';
    
    refreshDashboardData().finally(() => {
        refreshIcon.style.animation = '';
    });
}

async function refreshDashboardData() {
    try {
        const response = await fetch('/api/admin/dashboard-data', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateDashboardUI(data);
        }
    } catch (error) {
        console.error('Failed to refresh dashboard:', error);
        showNotification('Failed to refresh dashboard data', 'error');
    }
}

function updateDashboardUI(data) {
    // Update stat cards
    if (data.totalEmployees) {
        document.querySelector('.stat-value').textContent = data.totalEmployees;
    }
    
    // Refresh alerts
    refreshAlerts();
    
    // Update employee status table
    if (data.employees) {
        updateEmployeeStatusTable(data.employees);
    }
}

// Quick Actions
function openBiometricSetup() {
    window.open('/admin/biometric-setup', '_blank');
}

function exportPayrollData() {
    if (confirm('Export payroll data for current month?')) {
        window.location.href = '/admin/export/payroll';
    }
}

function sendBulkNotification() {
    const message = prompt('Enter notification message:');
    if (message) {
        fetch('/api/admin/send-notification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Notification sent to all employees', 'success');
            } else {
                showNotification('Failed to send notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error occurred', 'error');
        });
    }
}

function exportReports() {
    const reportType = prompt('Enter report type (attendance/payroll/analytics):');
    if (reportType) {
        window.location.href = `/admin/export/${reportType}`;
    }
}

// Alerts management
async function refreshAlerts() {
    try {
        const response = await fetch('/api/admin/alerts', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const alerts = await response.json();
            updateAlertsUI(alerts);
        }
    } catch (error) {
        console.error('Failed to refresh alerts:', error);
    }
}

function updateAlertsUI(alerts) {
    const alertsList = document.getElementById('alertsList');
    alertsList.innerHTML = alerts.map(alert => `
        <div class="alert-item">
            <div class="alert-icon ${alert.type}">
                ${getAlertIcon(alert.type)}
            </div>
            <div class="alert-content">
                <div class="alert-title">${alert.title}</div>
                <div class="alert-desc">${alert.description}</div>
            </div>
            <div class="alert-time">${alert.timeAgo}</div>
        </div>
    `).join('');
}

function getAlertIcon(type) {
    const icons = {
        'danger': '⚠️',
        'warning': '🕐',
        'info': '📝',
        'success': '✅'
    };
    return icons[type] || '📢';
}

// Employee management
function filterEmployees(filter) {
    const rows = document.querySelectorAll('#employeeStatusTable tr');
    
    rows.forEach(row => {
        const statusBadge = row.querySelector('.badge');
        if (!statusBadge) return;
        
        const statusText = statusBadge.textContent.toLowerCase();
        let shouldShow = filter === 'all';
        
        switch (filter) {
            case 'clocked-in':
                shouldShow = statusText.includes('clocked in');
                break;
            case 'clocked-out':
                shouldShow = statusText.includes('clocked out');
                break;
            case 'late':
                shouldShow = statusText.includes('late');
                break;
            case 'overtime':
                shouldShow = statusText.includes('overtime');
                break;
        }
        
        row.style.display = shouldShow ? '' : 'none';
    });
}

function viewEmployee(employeeSlug) {
    window.location.href = `/admin/employees/${employeeSlug}`;
}

function updateEmployeeStatusTable(employees) {
    const tableBody = document.getElementById('employeeStatusTable');
    
    tableBody.innerHTML = employees.map(emp => `
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-${emp.avatarColor}-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        ${emp.initials}
                    </div>
                    <div>
                        <div class="font-semibold">${emp.name}</div>
                        <div class="text-sm text-secondary">${emp.position}</div>
                    </div>
                </div>
            </td>
            <td><span class="badge ${emp.statusClass}">${emp.statusIcon} ${emp.status}</span></td>
            <td>${emp.clockIn || 'Not clocked in'}</td>
            <td><span class="text-${emp.locationStatus === 'in-zone' ? 'success' : 'danger'}">📍 ${emp.locationText}</span></td>
            <td>
                <button class="btn-secondary" onclick="viewEmployee('${emp.slug}')">View</button>
            </td>
        </tr>
    `).join('');
}

// Activity Log
function filterActivityLog(filter) {
    const rows = document.querySelectorAll('#activityLogTable tr');
    
    rows.forEach(row => {
        const activityCell = row.cells[2];
        if (!activityCell) return;
        
        const activityText = activityCell.textContent.toLowerCase();
        let shouldShow = filter === 'all';
        
        switch (filter) {
            case 'clock':
                shouldShow = activityText.includes('clock');
                break;
            case 'leave':
                shouldShow = activityText.includes('leave');
                break;
            case 'admin':
                shouldShow = activityText.includes('admin') || activityText.includes('geofence') || activityText.includes('updated');
                break;
            case 'violations':
                shouldShow = activityText.includes('violation') || activityText.includes('late');
                break;
        }
        
        row.style.display = shouldShow ? '' : 'none';
    });
}

// Biometric Dashboard
function openBiometricDashboard() {
    // Create modal for biometric management
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 2000; display: flex; align-items: center; justify-content: center;" onclick="this.remove()">
            <div style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;" onclick="event.stopPropagation()">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="margin: 0; font-size: 1.5rem; font-weight: 600;">Biometric Management</h2>
                    <button onclick="this.closest('div').parentElement.remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">✕</button>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #dcfce7; color: var(--success-color);">👆</div>
                        <div class="stat-value">{{ $fingerprintUsers }}</div>
                        <div class="stat-label">Fingerprint Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #dbeafe; color: var(--primary-color);">👤</div>
                        <div class="stat-value">{{ $faceIdUsers }}</div>
                        <div class="stat-label">Face Recognition Users</div>
                    </div>
                </div>
                
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-bottom: 16px;">Quick Actions</h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        <button class="btn-primary" onclick="enrollBiometric()">
                            ➕ Enroll New User
                        </button>
                        <button class="btn-secondary" onclick="resetBiometric()">
                            🔄 Reset Biometric Data
                        </button>
                        <button class="btn-secondary" onclick="exportBiometricReport()">
                            📊 Export Report
                        </button>
                        <button class="btn-secondary" onclick="configureBiometric()">
                            ⚙️ Configure Settings
                        </button>
                    </div>
                </div>
                
                <div>
                    <h3 style="margin-bottom: 16px;">Recent Biometric Activity</h3>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table class="data-table" style="font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($recentActivities as $log)
                                <tr>
                                    <td>{{ $log->employee->user->name }}</td>
                                    <td>
                                        @if($log->type === 'fingerprint')
                                            Fingerprint
                                        @elseif($log->type === 'face')
                                            Face Recognition
                                        @else
                                            Both
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Biometric functions
function enrollBiometric() {
    alert('Biometric enrollment wizard would open here');
}

function resetBiometric() {
    if (confirm('Are you sure you want to reset all biometric data? This action cannot be undone.')) {
        alert('Biometric data reset functionality would be implemented here');
    }
}

function exportBiometricReport() {
    window.location.href = '/admin/export/biometric-report';
}

function configureBiometric() {
    window.location.href = '/admin/biometric/settings';
}

// Chart initialization
function initializeCharts() {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;
    
    // Sample attendance chart - would use Chart.js library in real implementation
    ctx.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: center; height: 300px; color: var(--secondary-color); text-align: center;">
            📊 Interactive attendance chart would render here<br>
            <small>(Chart.js integration required)</small>
        </div>
    `;
}



// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        min-width: 300px;
        max-width: 400px;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div>${getNotificationIcon(type)}</div>
            <div style="flex: 1;">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; padding: 0;">✕</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function getNotificationIcon(type) {
    const icons = {
        'success': '✅',
        'error': '❌',
        'warning': '⚠️',
        'info': 'ℹ️'
    };
    return icons[type] || 'ℹ️';
}

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeGeofenceMap();
    startAutoRefresh();
    
    // Initial data load
    refreshDashboardData();
});

// Cleanup when leaving page
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});

// Real-time updates placeholder
function initializeWebSocket() {
    // WebSocket connection would be established here for real-time updates
    console.log('WebSocket connection would be established here for real-time updates');
}

initializeWebSocket();
</script>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endpush