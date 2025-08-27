@extends('layouts.app')

@section('title', 'Audit & Reports')

@section('page-header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="page-title">Audit & Reports</h1>
        <p class="page-subtitle">Comprehensive system auditing and report generation</p>
    </div>
    <div class="flex gap-4">
        <button class="btn-secondary" onclick="refreshAuditData()">
            <span id="auditRefreshIcon">🔄</span> Refresh
        </button>
        <button class="btn-primary" onclick="exportAuditLog()">
            📁 Export All
        </button>
    </div>
</div>
@endsection

@section('content')
<style>
    /* Audit-specific styles building on dashboard styles */
    .audit-filter-bar {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 140px;
    }
    
    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--secondary-color);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .form-input {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #475569;
        font-size: 14px;
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .audit-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    
    .summary-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        border: 1px solid #e2e8f0;
        position: relative;
    }
    
    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-color);
    }
    
    .summary-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 8px;
    }
    
    .summary-label {
        font-size: 14px;
        color: var(--secondary-color);
        font-weight: 500;
    }
    
    .audit-table-container {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .audit-table-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .audit-table th {
        background: #f8fafc;
        padding: 12px 24px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }
    
    .audit-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .audit-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .audit-action {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
    
    .audit-action.login { background: #dcfce7; color: #166534; }
    .audit-action.logout { background: #f3f4f6; color: #374151; }
    .audit-action.create { background: #dbeafe; color: #1d4ed8; }
    .audit-action.update { background: #fef3c7; color: #92400e; }
    .audit-action.delete { background: #fecaca; color: #991b1b; }
    .audit-action.export { background: #e0e7ff; color: #6366f1; }
    
    .audit-details {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .audit-timestamp {
        font-family: monospace;
        font-size: 13px;
        color: #64748b;
    }
    
    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .report-card {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        padding: 24px;
        transition: all 0.2s ease;
    }
    
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .report-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }
    
    .report-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .report-description {
        font-size: 14px;
        color: var(--secondary-color);
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .report-actions {
        display: flex;
        gap: 8px;
    }
    
    .btn-small {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .pagination-container {
        padding: 20px 24px;
        background: white;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: between;
        align-items: center;
        gap: 16px;
    }
    
    .pagination-info {
        font-size: 14px;
        color: var(--secondary-color);
    }
    
    .pagination-controls {
        display: flex;
        gap: 8px;
    }
    
    .page-btn {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        background: white;
        color: var(--secondary-color);
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .page-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .page-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .empty-state {
        padding: 80px 20px;
        text-align: center;
        color: var(--secondary-color);
    }
    
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .audit-filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-group {
            min-width: auto;
        }
        
        .audit-summary {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .report-grid {
            grid-template-columns: 1fr;
        }
        
        .audit-table-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .audit-table {
            font-size: 12px;
        }
        
        .audit-table th,
        .audit-table td {
            padding: 8px 12px;
        }
        
        .audit-details {
            max-width: 150px;
        }
    }
    
    @media (max-width: 640px) {
        .audit-summary {
            grid-template-columns: 1fr;
        }
        
        .pagination-container {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<!-- Audit Filters -->
<div class="audit-filter-bar">
    <div class="filter-group">
        <label class="filter-label">Date Range</label>
        <select class="form-input" id="dateRange" onchange="applyFilters()">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="week">This Week</option>
            <option value="month" selected>This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="custom">Custom Range</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label class="filter-label">Action Type</label>
        <select class="form-input" id="actionType" onchange="applyFilters()">
            <option value="all">All Actions</option>
            <option value="login">Login/Logout</option>
            <option value="attendance">Attendance</option>
            <option value="employee">Employee Mgmt</option>
            <option value="admin">Admin Actions</option>
            <option value="export">Data Exports</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label class="filter-label">User</label>
        <select class="form-input" id="userFilter" onchange="applyFilters()">
            <option value="all">All Users</option>
            <option value="admin">Admin Users</option>
            <option value="hr">HR Staff</option>
            <option value="employee">Employees</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label class="filter-label">Search</label>
        <input type="text" class="form-input" id="searchInput" placeholder="Search logs..." onkeyup="debounceSearch(this.value)">
    </div>
    
    <div class="filter-group">
        <label class="filter-label">&nbsp;</label>
        <button class="btn-secondary" onclick="clearFilters()">Clear Filters</button>
    </div>
</div>

<!-- Audit Summary -->
<div class="audit-summary">
    <div class="summary-card">
        <div class="summary-number">{{ $totalEvents ?? '1,247' }}</div>
        <div class="summary-label">Total Events</div>
    </div>
    
    <div class="summary-card">
        <div class="summary-number">{{ $todayEvents ?? '89' }}</div>
        <div class="summary-label">Today's Events</div>
    </div>
    
    <div class="summary-card">
        <div class="summary-number">{{ $uniqueUsers ?? '45' }}</div>
        <div class="summary-label">Active Users</div>
    </div>
    
    <div class="summary-card">
        <div class="summary-number">{{ $criticalEvents ?? '3' }}</div>
        <div class="summary-label">Critical Events</div>
    </div>
    
    <div class="summary-card">
        <div class="summary-number">{{ $exportCount ?? '12' }}</div>
        <div class="summary-label">Data Exports</div>
    </div>
</div>

<!-- Reports Section -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generate Reports</h3>
    </div>
    <div class="card-body">
        <div class="report-grid">
            <div class="report-card">
                <div class="report-icon" style="background: #eff6ff; color: var(--primary-color);">📊</div>
                <div class="report-title">Attendance Report</div>
                <div class="report-description">Comprehensive attendance analytics with clock-in/out patterns, late arrivals, and overtime analysis.</div>
                <div class="report-actions">
                    <button class="btn-small btn-primary" onclick="generateReport('attendance')">Generate</button>
                    <button class="btn-small btn-secondary" onclick="scheduleReport('attendance')">Schedule</button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon" style="background: #dcfce7; color: var(--success-color);">💰</div>
                <div class="report-title">Payroll Report</div>
                <div class="report-description">Detailed payroll calculations including regular hours, overtime, deductions, and total compensation.</div>
                <div class="report-actions">
                    <button class="btn-small btn-primary" onclick="generateReport('payroll')">Generate</button>
                    <button class="btn-small btn-secondary" onclick="scheduleReport('payroll')">Schedule</button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon" style="background: #fef3c7; color: var(--warning-color);">👥</div>
                <div class="report-title">Employee Activity</div>
                <div class="report-description">Individual employee performance metrics, attendance patterns, and productivity insights.</div>
                <div class="report-actions">
                    <button class="btn-small btn-primary" onclick="generateReport('employee')">Generate</button>
                    <button class="btn-small btn-secondary" onclick="scheduleReport('employee')">Schedule</button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon" style="background: #fecaca; color: var(--danger-color);">🚨</div>
                <div class="report-title">Compliance Report</div>
                <div class="report-description">Policy violations, geofence breaches, and compliance issues requiring management attention.</div>
                <div class="report-actions">
                    <button class="btn-small btn-primary" onclick="generateReport('compliance')">Generate</button>
                    <button class="btn-small btn-secondary" onclick="scheduleReport('compliance')">Schedule</button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon" style="background: #e0e7ff; color: #6366f1;">🔍</div>
                <div class="report-title">System Audit</div>
                <div class="report-description">Complete system activity log including user actions, data changes, and security events.</div>
                <div class="report-actions">
                    <button class="btn-small btn-primary" onclick="generateReport('system')">Generate</button>
                    <button class="btn-small btn-secondary" onclick="scheduleReport('system')">Schedule</button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon" style="background: #f0fdf4; color: #16a34a;">📈</div>
                <div class="report-title">Analytics Dashboard</div>
                <div class="report-description">Key performance indicators, trends, and business intelligence insights from all system data.</div>
                <div class="report-actions">
                    <button class="btn-small btn-primary" onclick="generateReport('analytics')">Generate</button>
                    <button class="btn-small btn-secondary" onclick="scheduleReport('analytics')">Schedule</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Log Table -->
<div class="audit-table-container">
    <div class="audit-table-header">
        <h3 class="card-title">System Audit Log</h3>
        <div class="flex gap-4">
            <span class="text-sm text-secondary" id="auditCount">Showing 1-50 of {{ $totalEvents ?? '1,247' }} events</span>
            <button class="btn-secondary btn-small" onclick="exportFilteredResults()">
                📁 Export Filtered
            </button>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="audit-table" id="auditTable">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Resource</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="auditTableBody">
                <tr>
                    <td><span class="audit-timestamp">2024-08-27 10:32:15</span></td>
                    <td>
                        <div>
                            <div class="font-semibold">Admin User</div>
                            <div class="text-sm text-secondary">admin@company.com</div>
                        </div>
                    </td>
                    <td><span class="audit-action export">Export</span></td>
                    <td>Attendance Data</td>
                    <td class="audit-details">Monthly attendance report for July 2024</td>
                    <td>192.168.1.100</td>
                    <td><span class="badge success">Success</span></td>
                </tr>
                
                <tr>
                    <td><span class="audit-timestamp">2024-08-27 10:28:42</span></td>
                    <td>
                        <div>
                            <div class="font-semibold">John Doe</div>
                            <div class="text-sm text-secondary">john@company.com</div>
                        </div>
                    </td>
                    <td><span class="audit-action login">Login</span></td>
                    <td>Mobile App</td>
                    <td class="audit-details">Biometric authentication successful</td>
                    <td>10.0.2.15</td>
                    <td><span class="badge success">Success</span></td>
                </tr>
                
                <tr>
                    <td><span class="audit-timestamp">2024-08-27 10:15:33</span></td>
                    <td>
                        <div>
                            <div class="font-semibold">HR Manager</div>
                            <div class="text-sm text-secondary">hr@company.com</div>
                        </div>
                    </td>
                    <td><span class="audit-action update">Update</span></td>
                    <td>Employee Profile</td>
                    <td class="audit-details">Updated Sarah Wilson's department from Marketing to Sales</td>
                    <td>192.168.1.105</td>
                    <td><span class="badge success">Success</span></td>
                </tr>
                
                <tr>
                    <td><span class="audit-timestamp">2024-08-27 09:45:17</span></td>
                    <td>
                        <div>
                            <div class="font-semibold">Mike Johnson</div>
                            <div class="text-sm text-secondary">mike@company.com</div>
                        </div>
                    </td>
                    <td><span class="audit-action create">Create</span></td>
                    <td>Leave Request</td>
                    <td class="audit-details">3-day vacation leave from Sept 15-17, 2024</td>
                    <td>192.168.1.120</td>
                    <td><span class="badge info">Pending</span></td>
                </tr>
                
                <tr>
                    <td><span class="audit-timestamp">2024-08-27 09:30:28</span></td>
                    <td>
                        <div>
                            <div class="font-semibold">Admin User</div>
                            <div class="text-sm text-secondary">admin@company.com</div>
                        </div>
                    </td>
                    <td><span class="audit-action update">Update</span></td>
                    <td>Geofence Settings</td>
                    <td class="audit-details">Modified main office geofence radius from 50m to 100m</td>
                    <td>192.168.1.100</td>
                    <td><span class="badge success">Success</span></td>
                </tr>
                
                <tr>
                    <td><span class="audit-timestamp">2024-08-27 08:15:44</span></td>
                    <td>
                        <div>
                            <div class="font-semibold">Emily Davis</div>
                            <div class="text-sm text-secondary">emily@company.com</div>
                        </div>
                    </td>
                    <td><span class="audit-action login">Login</span></td>
                    <td>Web Portal</td>
                    <td class="audit-details">Standard username/password authentication</td>
                    <td>192.168.1.110</td>
                    <td><span class="badge success">Success</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="pagination-container">
        <div class="pagination-info">
            Showing <span id="currentRange">1-50</span> of <span id="totalRecords">{{ $totalEvents ?? '1,247' }}</span> records
        </div>
        <div class="pagination-controls">
            <button class="page-btn" id="prevBtn" onclick="changePage(-1)" disabled>Previous</button>
            <button class="page-btn active" onclick="goToPage(1)">1</button>
            <button class="page-btn" onclick="goToPage(2)">2</button>
            <button class="page-btn" onclick="goToPage(3)">3</button>
            <span>...</span>
            <button class="page-btn" onclick="goToPage(25)">25</button>
            <button class="page-btn" id="nextBtn" onclick="changePage(1)">Next</button>
        </div>
    </div>
</div>

<!-- JavaScript for Audit Functionality -->
<script>
let currentPage = 1;
let itemsPerPage = 50;
let totalItems = {{ $totalEvents ?? 1247 }};
let searchTimeout;
let currentFilters = {
    dateRange: 'month',
    actionType: 'all',
    userFilter: 'all',
    search: ''
};

// Filter management
function applyFilters() {
    currentFilters.dateRange = document.getElementById('dateRange').value;
    currentFilters.actionType = document.getElementById('actionType').value;
    currentFilters.userFilter = document.getElementById('userFilter').value;
    
    currentPage = 1;
    loadAuditData();
}

function debounceSearch(searchTerm) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentFilters.search = searchTerm;
        currentPage = 1;
        loadAuditData();
    }, 300);
}

function clearFilters() {
    document.getElementById('dateRange').value = 'month';
    document.getElementById('actionType').value = 'all';
    document.getElementById('userFilter').value = 'all';
    document.getElementById('searchInput').value = '';
    
    currentFilters = {
        dateRange: 'month',
        actionType: 'all',
        userFilter: 'all',
        search: ''
    };
    
    currentPage = 1;
    loadAuditData();
}

// Data loading
async function loadAuditData() {
    try {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: itemsPerPage,
            ...currentFilters
        });
        
        const response = await fetch(`/api/admin/audit-data?${params}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateAuditTable(data.events);
            updatePagination(data.total, data.currentPage);
            updateSummary(data.summary);
        }
    } catch (error) {
        console.error('Failed to load audit data:', error);
        showEmptyState('Failed to load audit data');
    }
}

function updateAuditTable(events) {
    const tbody = document.getElementById('auditTableBody');
    
    if (!events || events.length === 0) {
        showEmptyState('No events found matching your criteria');
        return;
    }
    
    tbody.innerHTML = events.map(event => `
        <tr>
            <td><span class="audit-timestamp">${formatTimestamp(event.timestamp)}</span></td>
            <td>
                <div>
                    <div class="font-semibold">${event.user_name}</div>
                    <div class="text-sm text-secondary">${event.user_email}</div>
                </div>
            </td>
            <td><span class="audit-action ${event.action_type.toLowerCase()}">${event.action_display}</span></td>
            <td>${event.resource}</td>
            <td class="audit-details" title="${event.details}">${event.details}</td>
            <td>${event.ip_address}</td>
            <td><span class="badge ${event.status.toLowerCase()}">${event.status}</span></td>
        </tr>
    `).join('');
}

function showEmptyState(message) {
    const tbody = document.getElementById('auditTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div>${message}</div>
            </td>
        </tr>
    `;
}

function updatePagination(total, page) {
    totalItems = total;
    currentPage = page;
    
    const totalPages = Math.ceil(total / itemsPerPage);
    const startItem = ((page - 1) * itemsPerPage) + 1;
    const endItem = Math.min(page * itemsPerPage, total);
    
    document.getElementById('currentRange').textContent = `${startItem}-${endItem}`;
    document.getElementById('totalRecords').textContent = total;
    document.getElementById('auditCount').textContent = `Showing ${startItem}-${endItem} of ${total} events`;
    
    document.getElementById('prevBtn').disabled = page <= 1;
    document.getElementById('nextBtn').disabled = page >= totalPages;
}

function updateSummary(summary) {
    // Update summary cards with real data
    if (summary) {
        document.querySelector('.summary-card:nth-child(1) .summary-number').textContent = summary.total || '0';
        document.querySelector('.summary-card:nth-child(2) .summary-number').textContent = summary.today || '0';
        document.querySelector('.summary-card:nth-child(3) .summary-number').textContent = summary.users || '0';
        document.querySelector('.summary-card:nth-child(4) .summary-number').textContent = summary.critical || '0';
        document.querySelector('.summary-card:nth-child(5) .summary-number').textContent = summary.exports || '0';
    }
}

// Pagination
function changePage(direction) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const newPage = currentPage + direction;
    
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        loadAuditData();
    }
}

function goToPage(page) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        loadAuditData();
    }
}

// Report generation
function generateReport(type) {
    const reportConfig = {
        attendance: { name: 'Attendance Report', endpoint: '/admin/reports/attendance' },
        payroll: { name: 'Payroll Report', endpoint: '/admin/reports/payroll' },
        employee: { name: 'Employee Activity Report', endpoint: '/admin/reports/employee' },
        compliance: { name: 'Compliance Report', endpoint: '/admin/reports/compliance' },
        system: { name: 'System Audit Report', endpoint: '/admin/reports/system' },
        analytics: { name: 'Analytics Dashboard', endpoint: '/admin/reports/analytics' }
    };
    
    const config = reportConfig[type];
    if (!config) return;
    
    if (confirm(`Generate ${config.name}? This may take a few minutes for large datasets.`)) {
        showNotification(`Generating ${config.name}... You'll be notified when it's ready.`, 'info');
        
        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '⏳ Generating...';
        btn.disabled = true;
        
        fetch(config.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                filters: currentFilters,
                format: 'pdf'
            })
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Report generation failed');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${type}-report-${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showNotification(`${config.name} generated successfully!`, 'success');
        })
        .catch(error => {
            console.error('Report generation error:', error);
            showNotification('Failed to generate report. Please try again.', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
}

function scheduleReport(type) {
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 2000; display: flex; align-items: center; justify-content: center;" onclick="this.remove()">
            <div style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%;" onclick="event.stopPropagation()">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Schedule Report</h3>
                    <button onclick="this.closest('div').parentElement.remove()" style="background: none; border: none; font-size: 20px; cursor: pointer;">✕</button>
                </div>
                
                <form onsubmit="submitSchedule(event, '${type}')">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Report Type</label>
                        <input type="text" value="${type.charAt(0).toUpperCase() + type.slice(1)} Report" readonly style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Frequency</label>
                        <select name="frequency" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                            <option value="">Select frequency</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email Recipients</label>
                        <textarea name="recipients" placeholder="Enter email addresses separated by commas" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; min-height: 80px;" required></textarea>
                    </div>
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Start Date</label>
                        <input type="date" name="start_date" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                    </div>
                    
                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="button" onclick="this.closest('div').parentElement.remove()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Schedule Report</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function submitSchedule(event, type) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    fetch('/admin/reports/schedule', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            type: type,
            frequency: formData.get('frequency'),
            recipients: formData.get('recipients'),
            start_date: formData.get('start_date')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Report scheduled successfully!', 'success');
            event.target.closest('div').parentElement.remove();
        } else {
            throw new Error(data.message || 'Scheduling failed');
        }
    })
    .catch(error => {
        console.error('Scheduling error:', error);
        showNotification('Failed to schedule report. Please try again.', 'error');
    });
}

// Export functions
function exportAuditLog() {
    if (confirm('Export complete audit log? This may take several minutes for large datasets.')) {
        window.location.href = `/admin/export/audit-log?${new URLSearchParams(currentFilters)}`;
        showNotification('Audit log export started. Download will begin shortly.', 'info');
    }
}

function exportFilteredResults() {
    if (confirm('Export filtered results?')) {
        const params = new URLSearchParams({
            ...currentFilters,
            page: currentPage,
            per_page: itemsPerPage
        });
        
        window.location.href = `/admin/export/audit-filtered?${params}`;
        showNotification('Filtered results export started.', 'info');
    }
}

// Refresh functions
function refreshAuditData() {
    const refreshIcon = document.getElementById('auditRefreshIcon');
    refreshIcon.style.animation = 'spin 1s linear infinite';
    
    loadAuditData().finally(() => {
        refreshIcon.style.animation = '';
    });
}

// Utility functions
function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
}

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
    
    const icons = {
        'success': '✅',
        'error': '❌',
        'warning': '⚠️',
        'info': 'ℹ️'
    };
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div>${icons[type] || icons.info}</div>
            <div style="flex: 1;">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; padding: 0;">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadAuditData();
    
    // Set default date for schedule modals
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    // Auto-refresh every 60 seconds
    setInterval(() => {
        loadAuditData();
    }, 60000);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (event.key) {
            case 'f':
                event.preventDefault();
                document.getElementById('searchInput').focus();
                break;
            case 'r':
                event.preventDefault();
                refreshAuditData();
                break;
            case 'e':
                event.preventDefault();
                exportFilteredResults();
                break;
        }
    }
});
</script>
@endsection

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush