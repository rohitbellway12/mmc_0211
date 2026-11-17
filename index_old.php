<?php
/**
 * Hostinger Server Expired / Account Suspended Handler
 * Production-ready, zero-dependency, works on all servers (HTTP & HTTPS)
 */

http_response_code(503);

$uri = $_SERVER['REQUEST_URI'] ?? '';
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';

// API requests handler
if (str_contains($uri, '/api/') || str_contains($accept, 'application/json')) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'status' => 503,
        'response_code' => 'account_suspended_503',
        'message' => 'This account has been suspended. Hosting server subscription expired.',
    ], JSON_PRETTY_PRINT);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="en" style="height: 100%; width: 100%;">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Account Suspended - Hostinger</title>
    <style>
        /* CSS Reset & System Typography */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        html, body {
            height: 100%;
            width: 100%;
            background-color: #F8F9FD;
            color: #1F2232;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
        }

        /* Top Navigation Bar */
        .header-bar {
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #ECEEF3;
            padding: 16px 28px;
            display: flex;
            align-items: center;
        }

        .brand-container {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
            min-height: 28px;
            color: #673DE6;
            display: block;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #1F2232;
            text-transform: uppercase;
            line-height: 1;
        }

        /* Center Content Area */
        .content-area {
            flex: 1 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            width: 100%;
        }

        .card {
            background: #ffffff;
            width: 100%;
            max-width: 560px;
            padding: 44px 36px 36px 36px;
            border-radius: 16px;
            border: 1px solid #E6E8EE;
            box-shadow: 0 10px 30px rgba(103, 61, 230, 0.06), 0 2px 10px rgba(0, 0, 0, 0.03);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Top Hostinger Purple Accent Line */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background-color: #673DE6;
        }

        /* Alert Warning Icon Circle */
        .icon-circle {
            width: 68px;
            height: 68px;
            min-width: 68px;
            min-height: 68px;
            background-color: #FFF0F0;
            border: 2px solid #FFE0E0;
            color: #EB4848;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px auto;
        }

        .icon-circle svg {
            width: 32px;
            height: 32px;
            display: block;
        }

        /* Headings & Text */
        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #1F2232;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            line-height: 1.25;
        }

        p.description {
            font-size: 14px;
            line-height: 1.6;
            color: #63687D;
            margin-bottom: 26px;
        }

        /* Status Details Table Box */
        .status-box {
            background-color: #F8F9FE;
            border: 1px solid #E8EAF5;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 24px;
            text-align: left;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
            font-size: 13.5px;
            border-bottom: 1px dashed #E2E4EF;
        }

        .status-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .status-row:first-child {
            padding-top: 0;
        }

        .label {
            color: #63687D;
            font-weight: 500;
        }

        .value {
            font-weight: 600;
            color: #1F2232;
            text-align: right;
        }

        .value.badge-danger {
            color: #EB4848;
            background-color: #FFF0F0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
        }

        /* Important Notice Banner */
        .notice-banner {
            background-color: #FDF9EC;
            border-left: 3px solid #E59F00;
            border-radius: 6px;
            padding: 14px 16px;
            text-align: left;
            margin-bottom: 10px;
        }

        .notice-banner p {
            font-size: 13px;
            line-height: 1.55;
            color: #7A5700;
            font-weight: 500;
        }

        .notice-banner strong {
            color: #5C4100;
            font-weight: 700;
        }

        /* Bottom Footer Bar */
        .footer-bar {
            width: 100%;
            text-align: center;
            padding: 20px 16px;
            color: #8C90A4;
            font-size: 12.5px;
            border-top: 1px solid #ECEEF3;
            background: #ffffff;
            flex-shrink: 0;
        }

        .footer-bar span {
            font-weight: 700;
            color: #673DE6;
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 576px) {
            .header-bar {
                padding: 14px 20px;
            }
            .card {
                padding: 32px 20px 24px 20px;
            }
            h1 {
                font-size: 21px;
            }
            .status-row {
                font-size: 12.5px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <header class="header-bar">
        <div class="brand-container">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.415 0v7.16l5.785 3.384V2.949zM1.8 0v11.237h18.815L14.89 8.09l-7.457-.003V3.024zm14.615 20.894v-5.019l-7.514-.005c.007.033-5.82-3.197-5.82-3.197l19.119.091V24zM1.8 13.551v7.343l5.633 2.949v-6.988z"/>
            </svg>
            <span class="brand-title">HOSTINGER</span>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="content-area">
        <div class="card">
            <!-- Warning Icon -->
            <div class="icon-circle">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>

            <!-- Title & Subtitle -->
            <h1>This Account Has Been Suspended</h1>
            <p class="description">
                The hosting server account for this domain has expired or is temporarily suspended. All web services and database access have been put on hold.
            </p>

            <!-- Details Block -->
            <div class="status-box">
                <div class="status-row">
                    <span class="label">Service Status</span>
                    <span class="value badge-danger">Suspended / Expired</span>
                </div>
                <div class="status-row">
                    <span class="label">Suspension Reason</span>
                    <span class="value">Hosting Plan Subscription Expired</span>
                </div>
                <div class="status-row">
                    <span class="label">Hosting Environment</span>
                    <span class="value">Hostinger Cloud Server</span>
                </div>
            </div>

            <!-- Administrator Information Banner -->
            <div class="notice-banner">
                <p>
                    <strong>Notice for Account Administrator:</strong> If you are the owner of this account, please access your billing control panel to renew your hosting subscription and reactivate your website.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer Bar -->
    <footer class="footer-bar">
        Powered by <span>Hostinger</span> Web Hosting Platform &bull; All rights reserved.
    </footer>

</body>
</html>
