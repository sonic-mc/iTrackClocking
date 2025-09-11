<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - iTrack Clocking</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⏰</text></svg>">
    
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Primary Colors - Modern Blue Theme */
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            
            /* Turquoise/Teal Accents */
            --accent-color: #06b6d4;
            --accent-light: #22d3ee;
            --accent-dark: #0891b2;
            --accent-gradient: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            
            /* Neutral Colors */
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            /* Status Colors */
            --success-color: #059669;
            --success-light: #10b981;
            --warning-color: #d97706;
            --warning-light: #f59e0b;
            --danger-color: #dc2626;
            --danger-light: #ef4444;
            --info-color: #0284c7;
            --info-light: #0ea5e9;
            
            /* Layout */
            --sidebar-width: 280px;
            --header-height: 70px;
            --border-radius: 16px;
            --border-radius-sm: 8px;
            --border-radius-lg: 24px;
            
            /* Shadows */
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            font-weight: 400;
        }

        /* Header Navigation */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: var(--white);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gray-200);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-600);
            padding: 8px;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-toggle:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .logo:hover {
            transform: translateY(-1px);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
            box-shadow: var(--shadow-md);
        }

        /* Header Right */
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .clock-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .clock-status.clocked-in {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: var(--success-color);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        .clock-status.clocked-out {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: var(--danger-color);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .status-dot.active {
            background: var(--success-color);
        }

        .status-dot.inactive {
            background: var(--danger-color);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .location-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--gray-600);
            background: var(--gray-100);
            padding: 6px 12px;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
        }

        .location-status.in-zone {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: var(--success-color);
            border-color: rgba(5, 150, 105, 0.2);
        }

        .location-status.out-zone {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: var(--danger-color);
            border-color: rgba(220, 38, 38, 0.2);
        }

        .user-menu {
            position: relative;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
        }

        .user-avatar:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Sidebar */
        .sidebar {
            --transition-timing: cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: linear-gradient(180deg, var(--white) 0%, var(--gray-50) 100%);
            border-right: 1px solid var(--gray-200);
            overflow-y: auto;
            transition: transform 0.3s var(--transition-timing);
            z-index: 999;
            scrollbar-width: thin;
            scrollbar-color: var(--gray-300) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }

        .sidebar-content {
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Quick Actions */
        .quick-actions {
            padding: 0 20px;
            margin-bottom: 24px;
        }

        .quick-clock-btn {
            width: 100%;
            padding: 16px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }

        .clock-in-btn {
            background: var(--accent-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .clock-in-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .clock-out-btn {
            background: linear-gradient(135deg, var(--danger-color), var(--danger-light));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .clock-out-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Navigation */
        .nav-section {
            margin-bottom: 24px;
        }

        .nav-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 20px;
            margin-bottom: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 3px solid transparent;
            position: relative;
            cursor: pointer;
            font-weight: 500;
            margin: 2px 12px;
            border-radius: 12px;
        }

        .nav-item:hover {
            background: linear-gradient(135deg, var(--primary-color)10, var(--primary-color)05);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .nav-item.active {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            box-shadow: var(--shadow-md);
            border-right-color: transparent;
        }

        .nav-item.active .nav-icon {
            filter: brightness(1.2);
        }

        .nav-icon {
            font-size: 18px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 32px;
            min-height: calc(100vh - var(--header-height));
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 16px;
            font-weight: 400;
        }

        /* Breadcrumbs */
        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .breadcrumb-item {
            color: var(--gray-500);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .breadcrumb-item:hover {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--gray-900);
            font-weight: 600;
        }

        .breadcrumb-separator {
            color: var(--gray-300);
        }

        /* Cards and Components */
        .card {
            background: var(--white);
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--gray-50), var(--white));
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 400;
        }

        .card-body {
            padding: 28px;
        }

        /* Stats Cards */
        .stats-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent-gradient);
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 4px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Tables */
        .table-container {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table thead {
            background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
        }

        .table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            color: var(--gray-800);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--gray-200);
        }

        .table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-700);
            font-weight: 500;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, var(--primary-color)05, var(--accent-color)03);
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid transparent;
        }

        .badge.success {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: var(--success-color);
            border-color: rgba(5, 150, 105, 0.2);
        }

        .badge.warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: var(--warning-color);
            border-color: rgba(217, 119, 6, 0.2);
        }

        .badge.danger {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: var(--danger-color);
            border-color: rgba(220, 38, 38, 0.2);
        }

        .badge.info {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: var(--info-color);
            border-color: rgba(2, 132, 199, 0.2);
        }

        .badge.primary {
            background: var(--primary-gradient);
            color: white;
        }

        .badge.accent {
            background: var(--accent-gradient);
            color: white;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-accent {
            background: var(--accent-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-accent:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        /* User Dropdown */
        .user-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            min-width: 240px;
            overflow: hidden;
            animation: fadeInDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .user-dropdown .user-info {
            padding: 20px;
            background: linear-gradient(135deg, var(--gray-50), var(--white));
            border-bottom: 1px solid var(--gray-200);
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .user-dropdown a:hover {
            background: linear-gradient(135deg, var(--primary-color)10, var(--accent-color)05);
            color: var(--primary-color);
        }

        .user-dropdown hr {
            border: none;
            border-top: 1px solid var(--gray-200);
            margin: 0;
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 90px;
            right: 32px;
            padding: 16px 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            z-index: 1001;
            animation: slideInRight 0.3s ease-out;
            max-width: 400px;
            border-left: 4px solid;
            backdrop-filter: blur(10px);
        }

        .notification.success {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(220, 252, 231, 0.95));
            border-left-color: var(--success-color);
            color: var(--success-color);
        }

        .notification.error {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(254, 242, 242, 0.95));
            border-left-color: var(--danger-color);
            color: var(--danger-color);
        }

        .notification.warning {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(254, 243, 199, 0.95));
            border-left-color: var(--warning-color);
            color: var(--warning-color);
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(100%) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-200);
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: var(--shadow-xl);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 20px;
            }

            .main-content {
                padding: 20px;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .header-right {
                gap: 12px;
            }

            .clock-status {
                display: none;
            }

            .card-body, .card-header {
                padding: 20px;
            }
        }

        /* Utilities */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .gap-2 { gap: 8px; }
        .gap-4 { gap: 16px; }
        .gap-6 { gap: 24px; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .text-lg { font-size: 18px; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-primary { color: var(--primary-color); }
        .text-secondary { color: var(--gray-600); }
        .text-white { color: var(--white); }
        .bg-gradient { background: var(--primary-gradient); }
        .bg-accent-gradient { background: var(--accent-gradient); }
        .hidden { display: none; }
        .block { display: block; }
        .rounded { border-radius: var(--border-radius-sm); }
        .rounded-lg { border-radius: var(--border-radius); }
        .shadow { box-shadow: var(--shadow-md); }
        .shadow-lg { box-shadow: var(--shadow-lg); }

        /* Grid System */
        .grid {
            display: grid;
            gap: 24px;
        }

        .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

        @media (max-width: 768px) {
            .grid-cols-2, .grid-cols-3, .grid-cols-4 {
                grid-template-columns: 1fr;
            }
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--gray-800);
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            transition: all 0.2s ease;
            background: var(--white);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            background: var(--white);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        /* Modern Tabs */
        .tabs {
            border-bottom: 2px solid var(--gray-200);
            margin-bottom: 24px;
        }

        .tab-nav {
            display: flex;
            gap: 0;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .tab-item {
            padding: 16px 24px;
            color: var(--gray-600);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            border-bottom: 2px solid transparent;
            background: transparent;
            border: none;
        }

        .tab-item:hover {
            color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color)05, transparent);
        }

        .tab-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color)10, var(--accent-color)05);
        }

        .tab-content {
            padding: 24px 0;
        }

        /* Progress Bars */
        .progress {
            width: 100%;
            height: 8px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
            margin: 8px 0;
        }

        .progress-bar {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 10px;
            transition: width 0.6s ease;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                transparent 35%, 
                rgba(255,255,255,0.3) 50%, 
                transparent 65%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: var(--border-radius-sm);
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-success {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: var(--success-color);
            border-left-color: var(--success-color);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: var(--warning-color);
            border-left-color: var(--warning-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: var(--danger-color);
            border-left-color: var(--danger-color);
        }

        .alert-info {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: var(--info-color);
            border-left-color: var(--info-color);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
        }

        .modal-overlay.show .modal {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 24px 28px 0;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 24px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .modal-body {
            padding: 0 28px 24px;
        }

        .modal-footer {
            padding: 0 28px 28px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid var(--gray-200);
            margin-top: 24px;
            padding-top: 24px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 24px 0;
        }

        .pagination-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            color: var(--gray-600);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .pagination-item:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color)10, var(--accent-color)05);
        }

        .pagination-item.active {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
        }

        .pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Time Display */
        .time-display {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 14px;
            color: var(--gray-600);
            background: linear-gradient(135deg, var(--gray-100), var(--gray-50));
            padding: 8px 12px;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--gray-200);
        }

        /* Dashboard Widgets */
        .widget {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .widget::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent-gradient);
        }

        .widget:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .widget-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .widget-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .widget-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            :root {
                --white: #0f172a;
                --gray-50: #1e293b;
                --gray-100: #334155;
                --gray-200: #475569;
                --gray-800: #f1f5f9;
                --gray-900: #ffffff;
            }
        }

        /* Print Styles */
        @media print {
            .header, .sidebar, .menu-toggle {
                display: none !important;
            }

            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }

            * {
                background: white !important;
                color: black !important;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus visible for keyboard navigation */
        *:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* High contrast mode */
        @media (prefers-contrast: high) {
            :root {
                --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.3);
                --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.3);
                --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.3);
            }

            .card, .widget, .stats-card {
                border-width: 2px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <span id="menu-icon">☰</span>
            </button>
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-icon">⏰</div>
                <span>iTrack Clocking</span>
            </a>
        </div>

        <div class="header-right">
           

           <!-- Location Status -->
        <div class="location-status" id="locationStatus">
            <span>📍</span>
            <span id="locationText">Checking location...</span>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const locationText = document.getElementById("locationText");

            if ("geolocation" in navigator) {
                // Watch position for realtime updates
                navigator.geolocation.watchPosition(
                    (position) => {
                        const lat = position.coords.latitude.toFixed(6);
                        const lng = position.coords.longitude.toFixed(6);

                        locationText.textContent = `Lat: ${lat}, Lng: ${lng}`;
                    },
                    (error) => {
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                locationText.textContent = "Permission denied";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                locationText.textContent = "Location unavailable";
                                break;
                            case error.TIMEOUT:
                                locationText.textContent = "Location request timed out";
                                break;
                            default:
                                locationText.textContent = "Unknown error";
                                break;
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0
                    }
                );
            } else {
                locationText.textContent = "Geolocation not supported";
            }
        });
        </script>


            <!-- Current Time -->
            <div class="time-display" id="currentTime"></div>

           <!-- User Menu -->
                <div class="user-menu">
                    @auth
                        <div class="user-avatar" onclick="toggleUserMenu()">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-name">
                            {{ auth()->user()->name }}
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    @endauth
                </div>

                
                
                <!-- User Dropdown (only show when logged in) -->
                @auth
                <div class="user-dropdown hidden" id="userDropdown">
                    <div class="user-info">
                        <div class="font-bold">{{ auth()->user()->name }}</div>
                        <div class="text-sm text-secondary">{{ auth()->user()->email }}</div>
                        <div class="text-xs text-secondary">{{ auth()->user()->role ?? 'Employee' }}</div>
                    </div>
                    <hr>
                    <a href="{{ route('profile.index') }}">👤 Profile Settings</a>
                    <a href="{{ route('attendance.history') }}">📅 My Attendance</a>
                
                    @if(auth()->user()->isManager())
                        <a href="{{ route('reports') }}">📊 Reports</a>
                    @endif
                
                    <hr>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        🚪 Logout
                    </a>
                </div>
                @endauth
        </div>
    </header>

    <!-- Sidebar -->
    @auth
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-content">
          <!-- Quick Clock Action -->
          <div class="quick-actions">
            <form id="clock-form" method="POST" action="{{ route('employee.clock') }}">
                @csrf
                <input type="hidden" name="action" value="{{ auth()->user()->isClockedIn() ? 'out' : 'in' }}">
                <input type="hidden" name="device_info" value="{{ request()->header('User-Agent') }}">
                <input type="hidden" name="location_lat" id="location_lat">
                <input type="hidden" name="location_lng" id="location_lng">
        
                <button type="submit" class="quick-clock-btn" id="clock-button">
                    <span>🕐</span> <span id="clock-label">{{ auth()->user()->isClockedIn() ? 'Clock Out' : 'Clock In' }}</span>
                </button>
            </form>
        </div>
        

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("clock-form");
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? "{{ csrf_token() }}";
            const actionInput = form.querySelector("input[name='action']");
            const label = document.getElementById("clock-label");
        
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
        
                const action = actionInput.value; // ✅ Use value, not element
        
                // Confirm only if clocking out
                if (action === 'out') {
                    const confirmed = await Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to clock out?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Clock Out",
                    }).then(r => r.isConfirmed);
        
                    if (!confirmed) return;
                }
        
                await submitClockForm(action);
            });
        
            async function submitClockForm(action) {
                const formData = new FormData(form);
                formData.set('action', action); // ✅ Explicitly set action value
        
                Swal.fire({ title: "Processing...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
                try {
                    const res = await fetch(form.getAttribute('action'), { // ✅ Use getAttribute to avoid DOM confusion
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: formData
                    });
        
                    let data;
                    try {
                        data = await res.json();
                    } catch {
                        const text = await res.text().catch(() => '');
                        console.error('Non-JSON response:', res.status, text.slice(0, 1000));
                        Swal.close();
                        Swal.fire("Server error", "Unexpected server response. Check DevTools -> Network.", "error");
                        return;
                    }
        
                    Swal.close();
        
                    if (data.status === 'confirm') {
                        const ans = await Swal.fire({
                            title: "Already clocked in",
                            text: data.message || "Do you want to clock out instead?",
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonText: "Yes, Clock Out",
                        });
        
                        if (ans.isConfirmed) {
                            return await submitClockForm('out'); // ✅ Re-submit with correct action
                        }
                        return;
                    }
        
                    const icon = data.status === 'success' ? 'success'
                               : data.status === 'warning' ? 'warning'
                               : data.status === 'error' ? 'error' : 'info';
        
                    await Swal.fire(data.message || "Done", "", icon);
        
                    if (data.status === 'success') toggleButtonState();
                } catch (err) {
                    console.error('Fetch error:', err);
                    Swal.close();
                    Swal.fire("Network error", "Could not reach server. Check your connection.", "error");
                }
            }
        
            function toggleButtonState() {
                if (!label || !actionInput) return;
        
                if (actionInput.value === 'in') {
                    label.textContent = 'Clock Out';
                    actionInput.value = 'out';
                } else {
                    label.textContent = 'Clock In';
                    actionInput.value = 'in';
                }
            }
        
            // Capture geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById("location_lat").value = position.coords.latitude;
                    document.getElementById("location_lng").value = position.coords.longitude;
                }, function (err) {
                    console.warn('Geolocation denied or failed:', err);
                }, { timeout: 8000 });
            }
        });
        </script>
        


<script>
    document.getElementById("clock-label").textContent = "{{ auth()->user()->isClockedIn() ? 'Clock Out' : 'Clock In' }}";
    document.querySelector("input[name='action']").value = "{{ auth()->user()->isClockedIn() ? 'out' : 'in' }}";
</script>

    

            <!-- Navigation Menu -->
            <nav>
                <!-- Main Menu -->
                <div class="nav-section">
                    <div class="nav-title">Main</div>
                    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>

                    <a href="{{ route('attendance.history') }}" class="nav-item {{ request()->routeIs('attendance.history') ? 'active' : '' }}">
                        <span class="nav-icon">📅</span>
                        My Attendance
                    </a>
                </div>

                <!-- Time Management -->
                <div class="nav-section">
                    <div class="nav-title">Time Management</div>
                    <a href="{{ route('shifts.index') }}" class="nav-item {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
                        <span class="nav-icon">🔄</span>
                        Upcoming Shifts
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('breaks.*') ? 'active' : '' }}">
                        <span class="nav-icon">☕</span>
                        Breaks
                    </a>
                    <a href="{{ route('overtime.index') }}" class="nav-item {{ request()->routeIs('overtime.*') ? 'active' : '' }}">
                        <span class="nav-icon">⏳</span>
                        Overtime
                    </a>
                </div>

                <!-- Leave Management -->
                <div class="nav-section">
                    <div class="nav-title">Leave</div>
                    <a href="{{ route('leave.request') }}" class="nav-item {{ request()->routeIs('leaves.request') ? 'active' : '' }}">
                        <span class="nav-icon">📝</span>
                        Request Leave
                    </a>
                    <a href="{{ route('leave.history') }}" class="nav-item {{ request()->routeIs('leaves.history') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        Leave History
                    </a>
                </div>
                 
                @auth
                @if(auth()->user()->isManager() || auth()->user()->isManager())
                <!-- Management -->
                <div class="nav-section">
                    <div class="nav-title">Management</div>
                    <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        Employees
                    </a>
                    <a href="{{ route('employees.attendance') }}" class="nav-item {{ request()->routeIs('attendance.manage') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        Attendance Overview
                    </a>
                    <a href="{{ route('leave.approve') }}" class="nav-item {{ request()->routeIs('leaves.approve') ? 'active' : '' }}">
                        <span class="nav-icon">✅</span>
                        Approve Leaves
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('geofence.*') ? 'active' : '' }}">
                        <span class="nav-icon">🗺️</span>
                        Geofencing
                    </a>
                </div>

                <!-- Reports -->
                <div class="nav-section">
                    <div class="nav-title">Reports</div>
                    <a href="#" class="nav-item {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">
                        <span class="nav-icon">📈</span>
                        Attendance Reports
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('reports.payroll') ? 'active' : '' }}">
                        <span class="nav-icon">💰</span>
                        Payroll Reports
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('reports.analytics') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        Analytics
                    </a>
                </div>
                @endif
                @endauth

                @auth
                @if(auth()->user()->isAdmin())
                <!-- System -->
                <div class="nav-section">
                    <div class="nav-title">System</div>
                    <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <span class="nav-icon">⚙️</span>
                        Settings
                    </a>
                    <a href="{{ route('admin.biometric') }}" class="nav-item {{ request()->routeIs('admin.biometric') ? 'active' : '' }}">
                        <span class="nav-icon">🔐</span>
                        Biometric Setup
                    </a>
                    <a href="{{ route('admin.audit') }}" class="nav-item {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
                        <span class="nav-icon">🔍</span>
                        Audit Logs
                    </a>
                </div>
                @endif
                @endauth
            </nav>
        </div>
    </aside>
    @endauth

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs))
        <nav class="breadcrumbs">
            @foreach($breadcrumbs as $breadcrumb)
                @if($loop->last)
                    <span class="breadcrumb-item active">{{ $breadcrumb['title'] }}</span>
                @else
                    <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-item">{{ $breadcrumb['title'] }}</a>
                    <span class="breadcrumb-separator">›</span>
                @endif
            @endforeach
        </nav>
        @endif

        <!-- Page Header -->
        @hasSection('page-header')
        <div class="page-header">
            @yield('page-header')
        </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script>
        let sidebarOpen = window.innerWidth > 1024;
        let userMenuOpen = false;

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const menuIcon = document.getElementById('menu-icon');
            
            sidebarOpen = !sidebarOpen;
            sidebar.classList.toggle('show', sidebarOpen);
            menuIcon.textContent = sidebarOpen ? '✕' : '☰';
        }

        // Toggle User Menu
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            userMenuOpen = !userMenuOpen;
            dropdown.classList.toggle('hidden', !userMenuOpen);
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(e) {
            const userMenu = document.querySelector('.user-menu');
            if (!userMenu.contains(e.target) && userMenuOpen) {
                toggleUserMenu();
            }
        });

        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: true,
                hour: 'numeric',
                minute: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }

        // Geolocation tracking
        function checkGeolocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        // Send to server to check if in geofence
                        fetch('/api/check-geofence', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ latitude: lat, longitude: lon })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const locationStatus = document.getElementById('locationStatus');
                            const locationText = document.getElementById('locationText');
                            
                            if (data.inGeofence) {
                                locationStatus.className = 'location-status in-zone';
                                locationText.textContent = 'In Work Zone';
                            } else {
                                locationStatus.className = 'location-status out-zone';
                                locationText.textContent = 'Outside Work Zone';
                            }
                        })
                        .catch(() => {
                            document.getElementById('locationText').textContent = 'Location unavailable';
                        });
                    },
                    function() {
                        document.getElementById('locationText').textContent = 'Location disabled';
                    }
                );
            }
        }

        

        // Notification System
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Init on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateTime();
            setInterval(updateTime, 60000); // update every minute
            checkGeolocation();
        });

    </script>

    @stack('scripts')