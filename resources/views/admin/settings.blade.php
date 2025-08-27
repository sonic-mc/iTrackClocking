@extends('layouts.app')

@section('title', 'Admin Settings')

@section('page-header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Configure and manage all aspects of the attendance system</p>
    </div>
    <div class="flex gap-4">
        <button class="btn-secondary" onclick="resetToDefaults()">
            Reset to Defaults
        </button>
        <button class="btn-primary" onclick="saveAllSettings()">
            Save All Settings
        </button>
    </div>
</div>
@endsection

@section('content')
<style>
    .settings-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .settings-nav {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 32px;
        overflow-x: auto;
    }
    
    .settings-nav-item {
        padding: 16px 24px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s ease;
    }
    
    .settings-nav-item:hover {
        color: var(--primary-color);
        background: #f8fafc;
    }
    
    .settings-nav-item.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        background: #f8fafc;
    }
    
    .settings-section {
        display: none;
    }
    
    .settings-section.active {
        display: block;
    }
    
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .setting-group {
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        padding: 24px;
    }
    
    .setting-group-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .setting-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f8fafc;
    }
    
    .setting-item:last-child {
        border-bottom: none;
    }
    
    .setting-label {
        flex: 1;
    }
    
    .setting-title {
        font-weight: 500;
        color: #374151;
        margin-bottom: 4px;
    }
    
    .setting-description {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.4;
    }
    
    .setting-control {
        min-width: 120px;
        text-align: right;
    }
    
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }
    
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 24px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background-color: var(--primary-color);
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .color-picker {
        width: 40px;
        height: 32px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .time-input-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .time-input {
        width: 80px;
    }
    
    .danger-zone {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 20px;
        margin-top: 24px;
    }
    
    .danger-zone h3 {
        color: #dc2626;
        margin-bottom: 12px;
        font-size: 16px;
        font-weight: 600;
    }
    
    .btn-danger {
        background: #dc2626;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-danger:hover {
        background: #b91c1c;
    }
    
    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 8px;
    }
    
    .geofence-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-top: 12px;
    }
    
    .geofence-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .geofence-item:last-child {
        border-bottom: none;
    }
    
    .notification-test {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 8px;
    }
    
    .test-btn {
        padding: 6px 12px;
        font-size: 12px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .test-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        
        .setting-item {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        
        .setting-control {
            text-align: left;
            min-width: auto;
        }
        
        .settings-nav {
            flex-wrap: wrap;
        }
        
        .settings-nav-item {
            flex: 1;
            text-align: center;
            min-width: 120px;
        }
    }
</style>

<div class="settings-container">
    <!-- Settings Navigation -->
    <div class="settings-nav">
        <div class="settings-nav-item active" onclick="showSection('general')">General</div>
        <div class="settings-nav-item" onclick="showSection('attendance')">Attendance</div>
        <div class="settings-nav-item" onclick="showSection('biometric')">Biometric</div>
        <div class="settings-nav-item" onclick="showSection('geofence')">Geofencing</div>
        <div class="settings-nav-item" onclick="showSection('notifications')">Notifications</div>
        <div class="settings-nav-item" onclick="showSection('security')">Security</div>
        <div class="settings-nav-item" onclick="showSection('integrations')">Integrations</div>
        <div class="settings-nav-item" onclick="showSection('maintenance')">Maintenance</div>
    </div>

    <!-- General Settings -->
    <div id="general" class="settings-section active">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">System Information</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Company Name</div>
                        <div class="setting-description">Organization name displayed throughout the system</div>
                    </div>
                    <div class="setting-control">
                        <input type="text" class="form-input" value="{{ $settings['company_name'] ?? 'Your Company' }}" name="company_name">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">System Timezone</div>
                        <div class="setting-description">Default timezone for all time calculations</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="timezone">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles" selected>Pacific Time</option>
                            <option value="Africa/Harare">CAT (Africa/Harare)</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Date Format</div>
                        <div class="setting-description">How dates are displayed across the system</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="date_format">
                            <option value="Y-m-d">2024-01-15</option>
                            <option value="m/d/Y" selected>01/15/2024</option>
                            <option value="d/m/Y">15/01/2024</option>
                            <option value="F j, Y">January 15, 2024</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Time Format</div>
                        <div class="setting-description">12-hour or 24-hour time display</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="time_format">
                            <option value="12" selected>12 Hour (2:30 PM)</option>
                            <option value="24">24 Hour (14:30)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Interface Settings</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Primary Color</div>
                        <div class="setting-description">Main brand color for the interface</div>
                    </div>
                    <div class="setting-control">
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker" value="#3b82f6" name="primary_color">
                            <input type="text" class="form-input" value="#3b82f6" name="primary_color_hex" style="width: 80px;">
                        </div>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Dark Mode</div>
                        <div class="setting-description">Enable dark theme for the admin interface</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="dark_mode">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Auto Refresh</div>
                        <div class="setting-description">Automatically refresh dashboard data</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="auto_refresh">
                            <option value="0">Disabled</option>
                            <option value="15">15 seconds</option>
                            <option value="30" selected>30 seconds</option>
                            <option value="60">1 minute</option>
                            <option value="300">5 minutes</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Records Per Page</div>
                        <div class="setting-description">Default number of records shown in tables</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="records_per_page">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Settings -->
    <div id="attendance" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">Work Schedule</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Default Work Start Time</div>
                        <div class="setting-description">Standard work day start time</div>
                    </div>
                    <div class="setting-control">
                        <input type="time" class="form-input time-input" value="09:00" name="work_start_time">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Default Work End Time</div>
                        <div class="setting-description">Standard work day end time</div>
                    </div>
                    <div class="setting-control">
                        <input type="time" class="form-input time-input" value="17:00" name="work_end_time">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Lunch Break Duration</div>
                        <div class="setting-description">Default lunch break time in minutes</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="60" name="lunch_break_duration" min="0" max="180">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Work Days</div>
                        <div class="setting-description">Standard working days of the week</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="work_days">
                            <option value="1,2,3,4,5" selected>Monday to Friday</option>
                            <option value="1,2,3,4,5,6">Monday to Saturday</option>
                            <option value="0,1,2,3,4,5,6">All Days</option>
                            <option value="custom">Custom Schedule</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Attendance Rules</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Late Arrival Grace Period</div>
                        <div class="setting-description">Minutes after start time before marked as late</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="15" name="late_grace_period" min="0" max="60">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Early Clock-in Limit</div>
                        <div class="setting-description">How early employees can clock in (minutes)</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="30" name="early_clockin_limit" min="0" max="120">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Minimum Work Hours</div>
                        <div class="setting-description">Minimum hours required per day</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="8" name="minimum_work_hours" min="1" max="24" step="0.5">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Overtime Threshold</div>
                        <div class="setting-description">Hours after which overtime applies</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="8" name="overtime_threshold" min="1" max="24" step="0.5">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Auto Clock-out</div>
                        <div class="setting-description">Automatically clock out employees after hours</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="auto_clockout" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Auto Clock-out Time</div>
                        <div class="setting-description">Time to automatically clock out employees</div>
                    </div>
                    <div class="setting-control">
                        <input type="time" class="form-input time-input" value="23:59" name="auto_clockout_time">
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Break Management</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Break Tracking</div>
                        <div class="setting-description">Enable break time tracking</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="break_tracking" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Maximum Break Time</div>
                        <div class="setting-description">Maximum allowed break time per day (minutes)</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="90" name="max_break_time" min="0" max="480">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Break Reminders</div>
                        <div class="setting-description">Remind employees to take breaks</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="break_reminders">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biometric Settings -->
    <div id="biometric" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">Authentication Methods</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Fingerprint Authentication</div>
                        <div class="setting-description">Enable fingerprint-based clock in/out</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="fingerprint_enabled" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Face Recognition</div>
                        <div class="setting-description">Enable facial recognition authentication</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="face_recognition_enabled" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">PIN Authentication</div>
                        <div class="setting-description">Allow PIN-based authentication as backup</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="pin_enabled">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Multi-Factor Authentication</div>
                        <div class="setting-description">Require multiple authentication methods</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="mfa_enabled">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Biometric Security</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Fingerprint Quality Threshold</div>
                        <div class="setting-description">Minimum quality score for fingerprint acceptance (1-100)</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="60" name="fingerprint_quality" min="1" max="100">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Face Match Confidence</div>
                        <div class="setting-description">Minimum confidence level for face recognition (1-100)</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="85" name="face_confidence" min="1" max="100">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Anti-Spoofing</div>
                        <div class="setting-description">Enable liveness detection for face recognition</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="anti_spoofing" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Failed Attempts Limit</div>
                        <div class="setting-description">Lock user after this many failed attempts</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="3" name="failed_attempts_limit" min="1" max="10">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Lockout Duration</div>
                        <div class="setting-description">Minutes to lock user after failed attempts</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="30" name="lockout_duration" min="1" max="1440">
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Device Management</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Device Registration</div>
                        <div class="setting-description">Allow new biometric device registration</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="device_registration" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Device Sync Interval</div>
                        <div class="setting-description">How often to sync with biometric devices</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="device_sync_interval">
                            <option value="60">1 minute</option>
                            <option value="300" selected>5 minutes</option>
                            <option value="900">15 minutes</option>
                            <option value="3600">1 hour</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Geofencing Settings -->
    <div id="geofence" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">Location Tracking</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Geofencing Enabled</div>
                        <div class="setting-description">Enable location-based attendance tracking</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="geofencing_enabled" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">GPS Accuracy Required</div>
                        <div class="setting-description">Minimum GPS accuracy in meters</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="10" name="gps_accuracy" min="1" max="100">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Location Update Frequency</div>
                        <div class="setting-description">How often to update employee locations</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="location_update_frequency">
                            <option value="30">30 seconds</option>
                            <option value="60" selected>1 minute</option>
                            <option value="300">5 minutes</option>
                            <option value="600">10 minutes</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Allow Outside Zone Clock-in</div>
                        <div class="setting-description">Permit clocking in outside defined geofences</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="allow_outside_clockin">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Violation Alerts</div>
                        <div class="setting-description">Send alerts for geofence violations</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="violation_alerts" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Geofence Zones</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Active Zones</div>
                        <div class="setting-description">Currently configured geofence areas</div>
                    </div>
                    <div class="setting-control">
                        <button class="btn-primary" onclick="manageGeofences()">Manage Zones</button>
                    </div>
                </div>
                
                <div class="geofence-list">
                    <div class="geofence-item">
                        <div>
                            <strong>Main Office</strong><br>
                            <small>Radius: 100m | Active: Yes</small>
                        </div>
                        <div>
                            <button class="test-btn" onclick="editGeofence('main')">Edit</button>
                            <button class="test-btn" onclick="deleteGeofence('main')">Delete</button>
                        </div>
                    </div>
                    <div class="geofence-item">
                        <div>
                            <strong>Warehouse</strong><br>
                            <small>Radius: 200m | Active: Yes</small>
                        </div>
                        <div>
                            <button class="test-btn" onclick="editGeofence('warehouse')">Edit</button>
                            <button class="test-btn" onclick="deleteGeofence('warehouse')">Delete</button>
                        </div>
                    </div>
                    <div class="geofence-item">
                        <div>
                            <strong>Remote Site A</strong><br>
                            <small>Radius: 50m | Active: No</small>
                        </div>
                        <div>
                            <button class="test-btn" onclick="editGeofence('remote-a')">Edit</button>
                            <button class="test-btn" onclick="deleteGeofence('remote-a')">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Settings -->
    <div id="notifications" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">Email Notifications</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Email Notifications Enabled</div>
                        <div class="setting-description">Send email notifications for system events</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="email_notifications" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">SMTP Server</div>
                        <div class="setting-description">Email server hostname</div>
                    </div>
                    <div class="setting-control">
                        <input type="text" class="form-input" value="smtp.gmail.com" name="smtp_server">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">SMTP Port</div>
                        <div class="setting-description">Email server port (usually 587 or 465)</div>
                    </div>
                    <div class="setting-control">
                        <input type="number" class="form-input time-input" value="587" name="smtp_port">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Email Username</div>
                        <div class="setting-description">SMTP authentication username</div>
                    </div>
                    <div class="setting-control">
                        <input type="email" class="form-input" name="smtp_username" placeholder="your-email@domain.com">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Test Email Configuration</div>
                        <div class="setting-description">Send a test email to verify settings</div>
                    </div>
                    <div class="setting-control">
                        <div class="notification-test">
                            <input type="email" class="form-input" placeholder="test@example.com" id="testEmail">
                            <button class="test-btn" onclick="sendTestEmail()">Send Test</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Push Notifications</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Browser Notifications</div>
                        <div class="setting-description">Enable browser push notifications</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="browser_notifications" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Mobile Push Notifications</div>
                        <div class="setting-description">Send notifications to mobile apps</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="mobile_notifications" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Firebase Server Key</div>
                        <div class="setting-description">Firebase Cloud Messaging server key</div>
                    </div>
                    <div class="setting-control">
                        <input type="password" class="form-input" name="firebase_key" placeholder="Enter Firebase key">
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Notification Rules</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Late Arrival Alerts</div>
                        <div class="setting-description">Notify admins when employees are late</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="late_alerts" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Overtime Alerts</div>
                        <div class="setting-description">Alert when employees exceed overtime threshold</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="overtime_alerts" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Absence Notifications</div>
                        <div class="setting-description">Notify about employee absences</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="absence_alerts" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Leave Request Notifications</div>
                        <div class="setting-description">Alert admins about new leave requests</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="leave_request_alerts" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">System Alert Recipients</div>
                        <div class="setting-description">Email addresses to receive system alerts</div>
                    </div>
                    <div class="setting-control">
                        <textarea class="form-textarea" name="alert_recipients" placeholder="admin@company.com, hr@company.com"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div id="security" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">Access Control</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Session Timeout</div>
                        <div class="setting-description">Automatically log out inactive users (minutes)</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="session_timeout">
                            <option value="30">30 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="120" selected>2 hours</option>
                            <option value="480">8 hours</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Password Requirements</div>
                        <div class="setting-description">Minimum password complexity</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="password_requirements">
                            <option value="basic">Basic (8+ characters)</option>
                            <option value="medium" selected>Medium (8+ chars, numbers)</option>
                            <option value="strong">Strong (8+ chars, numbers, symbols)</option>
                            <option value="very_strong">Very Strong (12+ chars, mixed case, numbers, symbols)</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Two-Factor Authentication</div>
                        <div class="setting-description">Require 2FA for admin accounts</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="require_2fa" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">IP Whitelist</div>
                        <div class="setting-description">Restrict admin access to specific IP addresses</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="ip_whitelist">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Allowed IP Addresses</div>
                        <div class="setting-description">Comma-separated list of allowed IPs</div>
                    </div>
                    <div class="setting-control">
                        <textarea class="form-textarea" name="allowed_ips" placeholder="192.168.1.100, 10.0.0.50"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Data Protection</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Data Encryption</div>
                        <div class="setting-description">Encrypt sensitive data at rest</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="data_encryption" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Backup Frequency</div>
                        <div class="setting-description">How often to backup system data</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="backup_frequency">
                            <option value="hourly">Every Hour</option>
                            <option value="daily" selected>Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Backup Retention</div>
                        <div class="setting-description">How long to keep backup files</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="backup_retention">
                            <option value="7">7 days</option>
                            <option value="30" selected>30 days</option>
                            <option value="90">90 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Data Anonymization</div>
                        <div class="setting-description">Remove personal identifiers from reports</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="data_anonymization">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Audit & Compliance</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Activity Logging</div>
                        <div class="setting-description">Log all user actions for audit purposes</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="activity_logging" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Log Retention Period</div>
                        <div class="setting-description">How long to keep audit logs</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="log_retention">
                            <option value="30">30 days</option>
                            <option value="90">90 days</option>
                            <option value="365" selected>1 year</option>
                            <option value="1825">5 years</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">GDPR Compliance Mode</div>
                        <div class="setting-description">Enable GDPR compliance features</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="gdpr_compliance">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Integrations Settings -->
    <div id="integrations" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">Payroll Integration</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Payroll System</div>
                        <div class="setting-description">Connect to external payroll system</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="payroll_system">
                            <option value="">None</option>
                            <option value="quickbooks">QuickBooks</option>
                            <option value="adp">ADP</option>
                            <option value="sage">Sage Payroll</option>
                            <option value="paychex">Paychex</option>
                            <option value="custom">Custom API</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">API Endpoint</div>
                        <div class="setting-description">Payroll system API URL</div>
                    </div>
                    <div class="setting-control">
                        <input type="url" class="form-input" name="payroll_api_url" placeholder="https://api.payrollsystem.com">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">API Key</div>
                        <div class="setting-description">Authentication key for payroll API</div>
                    </div>
                    <div class="setting-control">
                        <input type="password" class="form-input" name="payroll_api_key" placeholder="Enter API key">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Auto Sync</div>
                        <div class="setting-description">Automatically sync attendance data</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="payroll_auto_sync">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Test Connection</div>
                        <div class="setting-description">Test payroll system integration</div>
                    </div>
                    <div class="setting-control">
                        <button class="test-btn" onclick="testPayrollConnection()">Test Connection</button>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">HR Information System</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">HRIS Integration</div>
                        <div class="setting-description">Connect to HR management system</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="hris_system">
                            <option value="">None</option>
                            <option value="workday">Workday</option>
                            <option value="successfactors">SuccessFactors</option>
                            <option value="bamboohr">BambooHR</option>
                            <option value="namely">Namely</option>
                            <option value="custom">Custom API</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Employee Sync</div>
                        <div class="setting-description">Automatically sync employee data</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="hris_employee_sync">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Sync Frequency</div>
                        <div class="setting-description">How often to sync with HRIS</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="hris_sync_frequency">
                            <option value="hourly">Every Hour</option>
                            <option value="daily" selected>Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="manual">Manual Only</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Third-Party Services</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">SMS Service</div>
                        <div class="setting-description">SMS provider for notifications</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="sms_provider">
                            <option value="">Disabled</option>
                            <option value="twilio" selected>Twilio</option>
                            <option value="nexmo">Nexmo</option>
                            <option value="aws_sns">AWS SNS</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">SMS API Key</div>
                        <div class="setting-description">Authentication key for SMS service</div>
                    </div>
                    <div class="setting-control">
                        <input type="password" class="form-input" name="sms_api_key" placeholder="Enter SMS API key">
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Calendar Integration</div>
                        <div class="setting-description">Sync with calendar applications</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="calendar_integration">
                            <option value="">None</option>
                            <option value="google">Google Calendar</option>
                            <option value="outlook">Outlook Calendar</option>
                            <option value="exchange">Exchange Server</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Settings -->
    <div id="maintenance" class="settings-section">
        <div class="settings-grid">
            <div class="setting-group">
                <h3 class="setting-group-title">System Maintenance</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Maintenance Mode</div>
                        <div class="setting-description">Enable maintenance mode to prevent user access</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="maintenance_mode">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Maintenance Message</div>
                        <div class="setting-description">Message shown to users during maintenance</div>
                    </div>
                    <div class="setting-control">
                        <textarea class="form-textarea" name="maintenance_message" placeholder="System is under maintenance. Please try again later."></textarea>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Auto Cleanup</div>
                        <div class="setting-description">Automatically clean old data and logs</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="auto_cleanup" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Cleanup Schedule</div>
                        <div class="setting-description">When to run automatic cleanup</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="cleanup_schedule">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Performance</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Cache Duration</div>
                        <div class="setting-description">How long to cache dashboard data (minutes)</div>
                    </div>
                    <div class="setting-control">
                        <select class="form-select" name="cache_duration">
                            <option value="1">1 minute</option>
                            <option value="5" selected>5 minutes</option>
                            <option value="15">15 minutes</option>
                            <option value="60">1 hour</option>
                        </select>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Database Optimization</div>
                        <div class="setting-description">Regularly optimize database tables</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="db_optimization" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Image Compression</div>
                        <div class="setting-description">Compress uploaded images to save space</div>
                    </div>
                    <div class="setting-control">
                        <label class="toggle-switch">
                            <input type="checkbox" name="image_compression" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="setting-group">
                <h3 class="setting-group-title">Advanced Actions</h3>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Clear All Cache</div>
                        <div class="setting-description">Remove all cached data (may slow system temporarily)</div>
                    </div>
                    <div class="setting-control">
                        <button class="btn-secondary" onclick="clearCache()">Clear Cache</button>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Rebuild Search Index</div>
                        <div class="setting-description">Rebuild search index for better performance</div>
                    </div>
                    <div class="setting-control">
                        <button class="btn-secondary" onclick="rebuildIndex()">Rebuild Index</button>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Export System Logs</div>
                        <div class="setting-description">Download system logs for debugging</div>
                    </div>
                    <div class="setting-control">
                        <button class="btn-secondary" onclick="exportLogs()">Export Logs</button>
                    </div>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="danger-zone">
                <h3>Danger Zone</h3>
                <p style="color: #6b7280; margin-bottom: 16px;">
                    These actions are irreversible and should be used with extreme caution.
                </p>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Reset All Settings</div>
                        <div class="setting-description">Restore all system settings to factory defaults</div>
                    </div>
                    <div class="setting-control">
                        <button class="btn-danger" onclick="resetAllSettings()">Reset to Defaults</button>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-label">
                        <div class="setting-title">Clear All Data</div>
                        <div class="setting-description">Remove all attendance records and employee data (IRREVERSIBLE)</div>
                    </div>
                    <div class="setting-control">
                        <button class="btn-danger" onclick="clearAllData()">Clear All Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Settings Functionality -->
<script>
// Settings Navigation
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.settings-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active class from all nav items
    document.querySelectorAll('.settings-nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(sectionId).classList.add('active');
    
    // Activate corresponding nav item
    event.target.classList.add('active');
}

// Save Settings Functions
function saveAllSettings() {
    const formData = new FormData();
    
    // Collect all form inputs
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            formData.append(input.name, input.checked);
        } else if (input.name) {
            formData.append(input.name, input.value);
        }
    });
    
    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    fetch('/admin/settings/save', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings saved successfully!', 'success');
            // Update any dynamic elements based on saved settings
            updateUIFromSettings(data.settings);
        } else {
            showNotification('Failed to save settings: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error saving settings:', error);
        showNotification('Network error occurred while saving settings', 'error');
    })
    .finally(() => {
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

function resetToDefaults() {
    if (!confirm('Are you sure you want to reset all settings to their default values? This action cannot be undone.')) {
        return;
    }
    
    fetch('/admin/settings/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings reset to defaults successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification('Failed to reset settings: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error resetting settings:', error);
        showNotification('Network error occurred while resetting settings', 'error');
    });
}

// Color picker synchronization
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.querySelector('input[name="primary_color"]');
    const colorInput = document.querySelector('input[name="primary_color_hex"]');
    
    if (colorPicker && colorInput) {
        colorPicker.addEventListener('change', function() {
            colorInput.value = this.value;
            updatePrimaryColor(this.value);
        });
        
        colorInput.addEventListener('input', function() {
            if (this.value.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) {
                colorPicker.value = this.value;
                updatePrimaryColor(this.value);
            }
        });
    }
});

function updatePrimaryColor(color) {
    document.documentElement.style.setProperty('--primary-color', color);
}

// Geofence Management
function manageGeofences() {
    const modal = createModal('Manage Geofence Zones', `
        <div style="margin-bottom: 24px;">
            <button class="btn-primary" onclick="addNewGeofence()" style="margin-bottom: 16px;">
                Add New Zone
            </button>
        </div>
        
        <div class="geofence-list">
            <div class="geofence-item">
                <div>
                    <strong>Main Office</strong><br>
                    <small>Lat: -17.8292, Lng: 31.0522 | Radius: 100m</small>
                </div>
                <div>
                    <button class="test-btn" onclick="editGeofence('main')">Edit</button>
                    <button class="test-btn" onclick="toggleGeofence('main')">Toggle</button>
                    <button class="test-btn" onclick="deleteGeofence('main')">Delete</button>
                </div>
            </div>
            <div class="geofence-item">
                <div>
                    <strong>Warehouse</strong><br>
                    <small>Lat: -17.8350, Lng: 31.0600 | Radius: 200m</small>
                </div>
                <div>
                    <button class="test-btn" onclick="editGeofence('warehouse')">Edit</button>
                    <button class="test-btn" onclick="toggleGeofence('warehouse')">Toggle</button>
                    <button class="test-btn" onclick="deleteGeofence('warehouse')">Delete</button>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 24px; padding: 16px; background: #f8fafc; border-radius: 8px;">
            <h4 style="margin: 0 0 8px 0;">Instructions:</h4>
            <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #6b7280;">
                <li>Click "Add New Zone" to create a geofence area</li>
                <li>Use Edit to modify zone parameters</li>
                <li>Toggle to enable/disable zones temporarily</li>
                <li>Delete removes zones permanently</li>
            </ul>
        </div>
    `);
}

function addNewGeofence() {
    const name = prompt('Enter geofence zone name:');
    if (!name) return;
    
    const lat = prompt('Enter latitude:');
    if (!lat) return;
    
    const lng = prompt('Enter longitude:');
    if (!lng) return;
    
    const radius = prompt('Enter radius in meters:');
    if (!radius) return;
    
    fetch('/admin/geofence/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: name,
            latitude: lat,
            longitude: lng,
            radius: radius
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Geofence zone created successfully!', 'success');
            refreshGeofenceList();
        } else {
            showNotification('Failed to create geofence zone', 'error');
        }
    })
    .catch(error => {
        console.error('Error creating geofence:', error);
        showNotification('Network error occurred', 'error');
    });
}

function editGeofence(zoneId) {
    showNotification('Geofence editing interface would open here', 'info');
}

function deleteGeofence(zoneId) {
    if (!confirm('Are you sure you want to delete this geofence zone?')) {
        return;
    }
    
    fetch(`/admin/geofence/${zoneId}/delete`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Geofence zone deleted successfully!', 'success');
            refreshGeofenceList();
        } else {
            showNotification('Failed to delete geofence zone', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting geofence:', error);
        showNotification('Network error occurred', 'error');
    });
}

// Email Testing
function sendTestEmail() {
    const testEmail = document.getElementById('testEmail').value;
    if (!testEmail || !testEmail.includes('@')) {
        showNotification('Please enter a valid email address', 'error');
        return;
    }
    
    const testBtn = event.target;
    const originalText = testBtn.textContent;
    testBtn.textContent = 'Sending...';
    testBtn.disabled = true;
    
    fetch('/admin/settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email: testEmail })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Test email sent successfully!', 'success');
        } else {
            showNotification('Failed to send test email: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error sending test email:', error);
        showNotification('Network error occurred while sending test email', 'error');
    })
    .finally(() => {
        testBtn.textContent = originalText;
        testBtn.disabled = false;
    });
}

// Integration Testing
function testPayrollConnection() {
    const testBtn = event.target;
    const originalText = testBtn.textContent;
    testBtn.textContent = 'Testing...';
    testBtn.disabled = true;
    
    fetch('/admin/integrations/test-payroll', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Payroll connection test successful!', 'success');
        } else {
            showNotification('Payroll connection test failed: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error testing payroll connection:', error);
        showNotification('Network error occurred during connection test', 'error');
    })
    .finally(() => {
        testBtn.textContent = originalText;
        testBtn.disabled = false;
    });
}

// Maintenance Functions
function clearCache() {
    if (!confirm('Are you sure you want to clear all cache? This may temporarily slow down the system.')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Clearing...';
    btn.disabled = true;
    
    fetch('/admin/maintenance/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cache cleared successfully!', 'success');
        } else {
            showNotification('Failed to clear cache', 'error');
        }
    })
    .catch(error => {
        console.error('Error clearing cache:', error);
        showNotification('Network error occurred', 'error');
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

function rebuildIndex() {
    if (!confirm('Are you sure you want to rebuild the search index? This may take several minutes.')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Rebuilding...';
    btn.disabled = true;
    
    fetch('/admin/maintenance/rebuild-index', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Search index rebuilt successfully!', 'success');
        } else {
            showNotification('Failed to rebuild search index', 'error');
        }
    })
    .catch(error => {
        console.error('Error rebuilding index:', error);
        showNotification('Network error occurred', 'error');
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

function exportLogs() {
    showNotification('Preparing system logs for download...', 'info');
    window.location.href = '/admin/maintenance/export-logs';
}

// Danger Zone Functions
function resetAllSettings() {
    if (!confirm('Are you ABSOLUTELY sure you want to reset ALL settings to factory defaults?\n\nThis will:\n- Reset all configuration settings\n- Clear all customizations\n- Restore default values\n\nThis action CANNOT be undone!')) {
        return;
    }
    
    if (!confirm('Final confirmation: Type "RESET" to proceed with resetting all settings.')) {
        return;
    }
    
    const confirmText = prompt('Please type "RESET" in capital letters to confirm:');
    if (confirmText !== 'RESET') {
        showNotification('Reset cancelled - confirmation text did not match', 'info');
        return;
    }
    
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Resetting...';
    btn.disabled = true;
    
    fetch('/admin/settings/reset-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('All settings reset successfully! Reloading page...', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showNotification('Failed to reset settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error resetting settings:', error);
        showNotification('Network error occurred', 'error');
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

function clearAllData() {
    if (!confirm('⚠️ EXTREME CAUTION ⚠️\n\nThis will PERMANENTLY DELETE:\n- All employee records\n- All attendance data\n- All leave requests\n- All system logs\n- All biometric data\n\nTHIS CANNOT BE UNDONE!\n\nAre you absolutely certain?')) {
        return;
    }
    
    if (!confirm('This is your final warning!\n\nClicking OK will immediately and permanently delete ALL data from the system.\n\nDo you want to proceed?')) {
        return;
    }
    
    const confirmText = prompt('To proceed, type "DELETE EVERYTHING" exactly as shown (case sensitive):');
    if (confirmText !== 'DELETE EVERYTHING') {
        showNotification('Data deletion cancelled - confirmation text did not match', 'info');
        return;
    }
    
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Deleting...';
    btn.disabled = true;
    
    fetch('/admin/maintenance/clear-all-data', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('All data cleared successfully! System will reset...', 'success');
            setTimeout(() => {
                window.location.href = '/admin/setup';
            }, 3000);
        } else {
            showNotification('Failed to clear data', 'error');
        }
    })
    .catch(error => {
        console.error('Error clearing data:', error);
        showNotification('Network error occurred', 'error');
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

// Utility Functions
function createModal(title, content) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;" onclick="event.stopPropagation()">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 600;">${title}</h2>
                <button onclick="this.closest('div').parentElement.remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">✕</button>
            </div>
            ${content}
        </div>
    `;
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    document.body.appendChild(modal);
    return modal;
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
        z-index: 3000;
        min-width: 300px;
        max-width: 400px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    const colors = {
        success: { border: '#10b981', bg: '#dcfce7', text: '#065f46', icon: '✅' },
        error: { border: '#ef4444', bg: '#fecaca', text: '#991b1b', icon: '❌' },
        warning: { border: '#f59e0b', bg: '#fef3c7', text: '#92400e', icon: '⚠️' },
        info: { border: '#3b82f6', bg: '#dbeafe', text: '#1e40af', icon: 'ℹ️' }
    };
    
    const color = colors[type] || colors.info;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px; background: ${color.bg}; border: 1px solid ${color.border}; border-radius: 6px; padding: 12px;">
            <div style="color: ${color.text}; font-size: 18px;">${color.icon}</div>
            <div style="flex: 1; color: ${color.text}; font-weight: 500;">${message}</div>
            <button onclick="this.closest('div').parentElement.remove()" style="background: none; border: none; font-size: 16px; cursor: pointer; color: ${color.text}; opacity: 0.7;">✕</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    requestAnimationFrame(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    });
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function updateUIFromSettings(settings) {
    // Update primary color
    if (settings.primary_color) {
        updatePrimaryColor(settings.primary_color);
    }
    
    // Update dark mode
    if (settings.dark_mode) {
        document.body.classList.toggle('dark-mode', settings.dark_mode);
    }
    
    // Update any other dynamic UI elements based on settings
}

function refreshGeofenceList() {
    // Refresh the geofence list display
    showNotification('Geofence list refreshed', 'info');
}

// Auto-save functionality for certain settings
let autoSaveTimeout;
document.addEventListener('input', function(e) {
    if (e.target.matches('input, select, textarea')) {
        // Clear existing timeout
        if (autoSaveTimeout) {
            clearTimeout(autoSaveTimeout);
        }
        
        // Set new timeout for auto-save
        autoSaveTimeout = setTimeout(() => {
            // Only auto-save certain non-critical settings
            if (e.target.name && ['auto_refresh', 'records_per_page', 'date_format', 'time_format'].includes(e.target.name)) {
                saveSettingField(e.target.name, e.target.type === 'checkbox' ? e.target.checked : e.target.value);
            }
        }, 2000);
    }
});

function saveSettingField(fieldName, value) {
    fetch('/admin/settings/save-field', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            field: fieldName,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Subtle indication that setting was saved
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.style.borderColor = '#10b981';
                setTimeout(() => {
                    field.style.borderColor = '';
                }, 1000);
            }
        }
    })
    .catch(error => {
        console.error('Error auto-saving setting:', error);
    });
}

// Initialize settings page
document.addEventListener('DOMContentLoaded', function() {
    // Set up form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAllSettings();
        });
    });
    
    // Initialize tooltips and help text
    initializeHelpTooltips();
});

function initializeHelpTooltips() {
    // Add help tooltips to complex settings
    const helpTexts = {
        'fingerprint_quality': 'Lower values accept more fingerprints but may be less secure. Higher values are more secure but may reject valid fingerprints.',
        'face_confidence': 'Higher values mean more accurate face matching but may reject valid users in poor lighting conditions.',
        'gps_accuracy': 'Lower values require more precise GPS but may cause issues in urban areas. Higher values are more forgiving but less precise.',
        'backup_retention': 'Longer retention periods use more storage space but provide better data recovery options.'
    };
    
    Object.entries(helpTexts).forEach(([field, text]) => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            const helpIcon = document.createElement('span');
            helpIcon.innerHTML = ' ?';
            helpIcon.style.cssText = 'cursor: help; color: #6b7280; font-weight: bold; margin-left: 4px;';
            helpIcon.title = text;
            input.parentElement.appendChild(helpIcon);
        }
    });
}
</script>

@endsection