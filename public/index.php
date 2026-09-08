<?php
/*
|--------------------------------------------------------------------------
| GreenPulse AI - Energy Intelligence Dashboard
|--------------------------------------------------------------------------
| Frontend dashboard
|
| Backend:
|   api/history.php
|   api/latest.php
|   api/summary.php
|
| Database:
|   Supabase PostgreSQL
|
| Hardware:
|   ESP32
|   Solar PV
|   Voltage Sensor
|   ACS712
|   DHT11
|
| IMPORTANT:
| ESP32 ONLINE/OFFLINE is determined from the timestamp of the
| latest telemetry record, not from PHP/API availability alone.
|--------------------------------------------------------------------------
*/
?>

<!DOCTYPE html>

<html lang="en">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<meta
    name="description"
    content="GreenPulse AI - AI/ML Based Green Energy Management Dashboard"
>

<title>GreenPulse AI | Energy Intelligence</title>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>

    /* =========================================================
       ROOT VARIABLES
    ========================================================= */

    :root {

        --bg: #07111f;
        --bg2: #0b1728;

        --sidebar: rgba(7,17,31,0.96);

        --card: rgba(16,31,50,0.78);
        --card2: rgba(21,40,62,0.72);

        --border: rgba(255,255,255,0.08);

        --text: #f4f8ff;
        --muted: #8ea4bd;

        --primary: #42e8a4;
        --primary2: #24c982;

        --blue: #54a7ff;
        --yellow: #ffd166;
        --orange: #ff9f43;
        --red: #ff5c70;
        --purple: #9b7cff;

        --shadow:
            0 20px 60px rgba(0,0,0,0.28);

        --radius: 20px;
    }


    [data-theme="light"] {

        --bg: #eef4f8;
        --bg2: #ffffff;

        --sidebar: rgba(255,255,255,0.96);

        --card: rgba(255,255,255,0.90);
        --card2: rgba(247,250,253,0.95);

        --border: rgba(0,0,0,0.08);

        --text: #132238;
        --muted: #65758b;

        --shadow:
            0 20px 50px rgba(38,62,87,0.12);
    }


    * {

        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }


    html {

        scroll-behavior: smooth;
    }


    body {

        font-family:
            Inter,
            ui-sans-serif,
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            sans-serif;

        background:

            radial-gradient(
                circle at 10% 10%,
                rgba(66,232,164,0.08),
                transparent 30%
            ),

            radial-gradient(
                circle at 90% 20%,
                rgba(84,167,255,0.08),
                transparent 30%
            ),

            linear-gradient(
                135deg,
                var(--bg),
                var(--bg2)
            );

        color: var(--text);

        min-height: 100vh;

        overflow-x: hidden;
    }


    /* =========================================================
       ANIMATED GRID
    ========================================================= */

    body::before {

        content: "";

        position: fixed;

        inset: 0;

        pointer-events: none;

        opacity: 0.12;

        background-image:

            linear-gradient(
                rgba(255,255,255,0.04) 1px,
                transparent 1px
            ),

            linear-gradient(
                90deg,
                rgba(255,255,255,0.04) 1px,
                transparent 1px
            );

        background-size: 45px 45px;

        mask-image:
            linear-gradient(
                to bottom,
                black,
                transparent
            );
    }


    /* =========================================================
       PARTICLES
    ========================================================= */

    #particles {

        position: fixed;

        inset: 0;

        pointer-events: none;

        overflow: hidden;

        z-index: 0;
    }


    .particle {

        position: absolute;

        width: 3px;

        height: 3px;

        border-radius: 50%;

        background: var(--primary);

        opacity: 0.35;

        animation:
            floatParticle linear infinite;
    }


    @keyframes floatParticle {

        from {

            transform: translateY(110vh);

            opacity: 0;
        }

        20% {

            opacity: 0.35;
        }

        80% {

            opacity: 0.25;
        }

        to {

            transform: translateY(-20vh);

            opacity: 0;
        }
    }


    /* =========================================================
       LAYOUT
    ========================================================= */

    .app {

        display: flex;

        min-height: 100vh;

        position: relative;

        z-index: 1;
    }


    /* =========================================================
       SIDEBAR
    ========================================================= */

    .sidebar {

        width: 250px;

        min-width: 250px;

        position: fixed;

        top: 0;
        left: 0;
        bottom: 0;

        padding: 24px 16px;

        background: var(--sidebar);

        border-right: 1px solid var(--border);

        backdrop-filter: blur(20px);

        z-index: 100;
    }


    .logo {

        display: flex;

        align-items: center;

        gap: 12px;

        padding: 10px 12px 25px;
    }


    .logo-icon {

        width: 42px;
        height: 42px;

        display: grid;

        place-items: center;

        border-radius: 14px;

        background:
            linear-gradient(
                135deg,
                var(--primary),
                var(--blue)
            );

        color: #06101b;

        font-size: 22px;

        box-shadow:
            0 10px 30px rgba(66,232,164,0.22);
    }


    .logo-title {

        font-size: 18px;

        font-weight: 800;

        letter-spacing: 0.5px;
    }


    .logo-sub {

        font-size: 10px;

        color: var(--muted);

        margin-top: 2px;

        letter-spacing: 1px;
    }


    .nav {

        display: flex;

        flex-direction: column;

        gap: 6px;
    }


    .nav a {

        display: flex;

        align-items: center;

        gap: 12px;

        text-decoration: none;

        color: var(--muted);

        padding: 12px 14px;

        border-radius: 13px;

        font-size: 13px;

        transition: 0.2s;
    }


    .nav a:hover,
    .nav a.active {

        color: var(--text);

        background:
            linear-gradient(
                90deg,
                rgba(66,232,164,0.12),
                rgba(84,167,255,0.05)
            );

        border-left: 3px solid var(--primary);
    }


    .nav-icon {

        width: 24px;

        text-align: center;

        font-size: 16px;
    }


    .sidebar-bottom {

        position: absolute;

        left: 16px;
        right: 16px;

        bottom: 20px;
    }


    .theme-btn {

        width: 100%;

        border: 1px solid var(--border);

        background: var(--card);

        color: var(--text);

        padding: 12px;

        border-radius: 12px;

        cursor: pointer;

        font-size: 12px;
    }


    /* =========================================================
       MAIN
    ========================================================= */

    .main {

        margin-left: 250px;

        width: calc(100% - 250px);

        padding: 25px;

        position: relative;
    }


    /* =========================================================
       TOPBAR
    ========================================================= */

    .topbar {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 20px;

        margin-bottom: 22px;
    }


    .eyebrow {

        color: var(--primary);

        font-size: 11px;

        font-weight: 700;

        letter-spacing: 2px;

        text-transform: uppercase;

        margin-bottom: 7px;
    }


    .page-title {

        font-size: clamp(24px,3vw,38px);

        font-weight: 800;

        letter-spacing: -1px;
    }


    .page-sub {

        color: var(--muted);

        margin-top: 7px;

        font-size: 13px;
    }


    .top-actions {

        display: flex;

        align-items: center;

        gap: 10px;
    }


    /* =========================================================
       DEVICE STATUS
    ========================================================= */

    .online {

        display: flex;

        align-items: center;

        gap: 8px;

        border: 1px solid var(--border);

        background: var(--card);

        padding: 10px 14px;

        border-radius: 12px;

        font-size: 12px;

        min-width: 155px;

        justify-content: center;
    }


    .online-dot {

        width: 8px;
        height: 8px;

        border-radius: 50%;

        background: var(--primary);

        box-shadow:
            0 0 12px var(--primary);
    }


    .online-dot.offline {

        background: var(--red);

        box-shadow:
            0 0 12px var(--red);
    }


    .online-dot.warning {

        background: var(--yellow);

        box-shadow:
            0 0 12px var(--yellow);
    }


    .refresh-btn {

        border: 1px solid var(--border);

        background: var(--card);

        color: var(--text);

        padding: 10px 14px;

        border-radius: 12px;

        cursor: pointer;

        transition: 0.2s;
    }


    .refresh-btn:hover {

        transform: translateY(-2px);

        border-color:
            rgba(66,232,164,0.3);
    }


    /* =========================================================
       SYSTEM BAR
    ========================================================= */

    .system-bar {

        display: grid;

        grid-template-columns:
            repeat(4,1fr);

        gap: 10px;

        margin-bottom: 20px;
    }


    .system-item {

        background: var(--card);

        border: 1px solid var(--border);

        padding: 12px 15px;

        border-radius: 14px;

        display: flex;

        justify-content: space-between;

        gap: 10px;

        font-size: 11px;
    }


    .system-item span:first-child {

        color: var(--muted);

        text-transform: uppercase;

        letter-spacing: 0.7px;
    }


    .system-item strong {

        color: var(--text);

        text-align: right;
    }


    /* =========================================================
       CARDS
    ========================================================= */

    .card {

        background:
            linear-gradient(
                145deg,
                var(--card),
                var(--card2)
            );

        border: 1px solid var(--border);

        border-radius: var(--radius);

        box-shadow: var(--shadow);

        backdrop-filter: blur(20px);
    }


    /* =========================================================
       HERO
    ========================================================= */

    .hero-grid {

        display: grid;

        grid-template-columns:
            1.5fr
            1fr;

        gap: 18px;

        margin-bottom: 18px;
    }


    .hero {

        padding: 25px;

        min-height: 235px;

        position: relative;

        overflow: hidden;
    }


    .hero::after {

        content: "☀";

        position: absolute;

        right: 35px;

        top: 25px;

        font-size: 100px;

        opacity: 0.06;
    }


    .hero-label {

        color: var(--muted);

        font-size: 12px;

        text-transform: uppercase;

        letter-spacing: 1.5px;
    }


    .hero-power {

        display: flex;

        align-items: baseline;

        gap: 8px;

        margin-top: 12px;
    }


    .hero-power strong {

        font-size: clamp(45px,7vw,75px);

        line-height: 1;

        letter-spacing: -4px;

        color: var(--primary);
    }


    .hero-power span {

        font-size: 20px;

        color: var(--muted);
    }


    .hero-info {

        display: flex;

        justify-content: space-between;

        gap: 15px;

        margin-top: 25px;

        color: var(--muted);

        font-size: 12px;
    }


    .progress {

        margin-top: 20px;

        height: 8px;

        background:
            rgba(255,255,255,0.06);

        border-radius: 20px;

        overflow: hidden;
    }


    .progress-bar {

        height: 100%;

        width: 0%;

        background:
            linear-gradient(
                90deg,
                var(--primary),
                var(--blue)
            );

        border-radius: inherit;

        transition: width 0.6s ease;
    }


    /* =========================================================
       AI HEALTH
    ========================================================= */

    .ai-card {

        padding: 25px;

        min-height: 235px;
    }


    .section-label {

        font-size: 11px;

        color: var(--muted);

        text-transform: uppercase;

        letter-spacing: 1.5px;

        margin-bottom: 15px;
    }


    .ai-status {

        display: flex;

        align-items: center;

        gap: 15px;
    }


    .ai-icon {

        width: 58px;
        height: 58px;

        border-radius: 18px;

        display: grid;

        place-items: center;

        background:
            rgba(66,232,164,0.12);

        border:
            1px solid rgba(66,232,164,0.2);

        font-size: 27px;
    }


    .status-badge {

        display: inline-flex;

        padding: 6px 10px;

        border-radius: 20px;

        background:
            rgba(66,232,164,0.12);

        color: var(--primary);

        font-size: 11px;

        font-weight: 700;
    }


    .status-badge.fault {

        color: var(--red);

        background:
            rgba(255,92,112,0.12);
    }


    .status-badge.idle {

        color: var(--yellow);

        background:
            rgba(255,209,102,0.12);
    }


    .status-badge.medium {

        color: var(--yellow);

        background:
            rgba(255,209,102,0.12);
    }


    .ai-message {

        margin-top: 22px;

        color: var(--muted);

        line-height: 1.6;

        font-size: 13px;
    }


    .confidence {

        margin-top: 15px;

        display: flex;

        justify-content: space-between;

        color: var(--muted);

        font-size: 11px;
    }


    /* =========================================================
       METRICS
    ========================================================= */

    .metrics {

        display: grid;

        grid-template-columns:
            repeat(6,1fr);

        gap: 14px;

        margin-bottom: 18px;
    }


    .metric {

        padding: 18px;

        min-height: 125px;
    }


    .metric-top {

        display: flex;

        justify-content: space-between;

        align-items: center;
    }


    .metric-name {

        font-size: 11px;

        color: var(--muted);

        text-transform: uppercase;

        letter-spacing: 0.8px;
    }


    .metric-icon {

        font-size: 19px;
    }


    .metric-value {

        margin-top: 15px;

        font-size: 25px;

        font-weight: 800;
    }


    .metric-unit {

        color: var(--muted);

        font-size: 12px;

        margin-left: 3px;
    }


    /* =========================================================
       ALERT
    ========================================================= */

    .alert {

        padding: 14px 18px;

        margin-bottom: 18px;

        border-radius: 15px;

        border:
            1px solid rgba(66,232,164,0.18);

        background:
            rgba(66,232,164,0.06);

        display: flex;

        align-items: center;

        gap: 12px;

        font-size: 12px;

        color: var(--muted);

        transition: 0.25s;
    }


    .alert.fault {

        border-color:
            rgba(255,92,112,0.35);

        background:
            rgba(255,92,112,0.08);

        color: var(--text);
    }


    .alert.medium {

        border-color:
            rgba(255,209,102,0.35);

        background:
            rgba(255,209,102,0.07);

        color: var(--text);
    }


    .alert.offline {

        border-color:
            rgba(255,92,112,0.35);

        background:
            rgba(255,92,112,0.08);

        color: var(--text);
    }


    /* =========================================================
       FAULT PANEL
    ========================================================= */

    .fault-panel {

        display: none;

        padding: 15px 18px;

        margin-bottom: 18px;

        border-radius: 15px;

        border:
            1px solid rgba(255,92,112,0.28);

        background:
            linear-gradient(
                135deg,
                rgba(255,92,112,0.10),
                rgba(255,92,112,0.04)
            );
    }


    .fault-panel.show {

        display: block;
    }


    .fault-panel-title {

        font-size: 12px;

        font-weight: 800;

        color: var(--red);

        margin-bottom: 10px;

        text-transform: uppercase;

        letter-spacing: 1px;
    }


    .fault-list {

        display: flex;

        flex-wrap: wrap;

        gap: 8px;
    }


    .fault-chip {

        padding: 7px 10px;

        border-radius: 20px;

        background:
            rgba(255,92,112,0.10);

        border:
            1px solid rgba(255,92,112,0.18);

        color: var(--red);

        font-size: 10px;

        font-weight: 700;
    }


    /* =========================================================
       SECTION
    ========================================================= */

    .section {

        margin-bottom: 18px;
    }


    .section-title {

        font-size: 18px;

        font-weight: 750;

        margin-bottom: 12px;
    }


    .section-description {

        color: var(--muted);

        font-size: 12px;

        margin-top: -5px;

        margin-bottom: 14px;
    }


    /* =========================================================
       CHART GRID
    ========================================================= */

    .chart-grid {

        display: grid;

        grid-template-columns:
            repeat(3,1fr);

        gap: 16px;
    }


    .chart-card {

        padding: 18px;

        min-height: 310px;
    }


    .chart-card.large {

        grid-column: span 2;
    }


    .chart-title {

        font-size: 13px;

        font-weight: 700;

        margin-bottom: 15px;
    }


    .chart-container {

        position: relative;

        height: 245px;
    }


    /* =========================================================
       CURRENT GRAPH + SMALL DATA SUMMARY
    ========================================================= */

    .current-summary-grid {

        display: grid;

        grid-template-columns:
            minmax(0,2fr)
            minmax(230px,0.75fr);

        gap: 16px;

        align-items: stretch;
    }


    .current-chart-card {

        min-width: 0;
    }


    .data-summary-side {

        padding: 18px;

        min-height: 310px;
    }


    .data-summary-title {

        font-size: 12px;

        font-weight: 800;

        margin-bottom: 14px;

        text-transform: uppercase;

        letter-spacing: 1px;
    }


    .summary-mini-grid {

        display: grid;

        grid-template-columns: 1fr 1fr;

        gap: 9px;
    }


    .summary-mini {

        padding: 11px;

        border-radius: 12px;

        background:
            rgba(255,255,255,0.025);

        border:
            1px solid var(--border);
    }


    .summary-mini-label {

        color: var(--muted);

        font-size: 9px;

        text-transform: uppercase;

        letter-spacing: 0.6px;
    }


    .summary-mini-value {

        margin-top: 5px;

        font-size: 15px;

        font-weight: 800;
    }


    .summary-mini.full {

        grid-column: span 2;
    }


    .device-last-seen {

        margin-top: 13px;

        padding: 11px;

        border-radius: 12px;

        background:
            rgba(84,167,255,0.06);

        border:
            1px solid rgba(84,167,255,0.12);
    }


    .device-last-seen-title {

        color: var(--muted);

        font-size: 9px;

        text-transform: uppercase;

        letter-spacing: 0.7px;
    }


    .device-last-seen-value {

        margin-top: 5px;

        font-size: 11px;

        font-weight: 700;
    }


    /* =========================================================
       SUMMARY
    ========================================================= */

    .summary-grid {

        display: grid;

        grid-template-columns:
            repeat(4,1fr);

        gap: 15px;
    }


    .summary-card {

        padding: 20px;
    }


    .summary-label {

        color: var(--muted);

        font-size: 11px;

        text-transform: uppercase;

        letter-spacing: 1px;
    }


    .summary-value {

        margin-top: 10px;

        font-size: 25px;

        font-weight: 800;
    }


    /* =========================================================
       FORECAST
    ========================================================= */

    .forecast-grid {

        display: grid;

        grid-template-columns:
            repeat(4,1fr);

        gap: 14px;

        margin-bottom: 16px;
    }


    .forecast-card {

        padding: 17px;
    }


    .forecast-label {

        color: var(--muted);

        font-size: 11px;

        text-transform: uppercase;
    }


    .forecast-value {

        font-size: 22px;

        font-weight: 800;

        margin-top: 8px;
    }


    .risk {

        display: inline-flex;

        padding: 6px 10px;

        border-radius: 20px;

        font-size: 11px;

        font-weight: 700;

        margin-top: 7px;

        background:
            rgba(66,232,164,0.1);

        color: var(--primary);
    }


    .risk.medium {

        background:
            rgba(255,209,102,0.12);

        color: var(--yellow);
    }


    .risk.high {

        background:
            rgba(255,92,112,0.12);

        color: var(--red);
    }


    .forecast-analysis {

        padding: 20px;

        margin-top: 15px;

        color: var(--muted);

        font-size: 13px;

        line-height: 1.7;
    }


    /* =========================================================
       TELEMETRY
    ========================================================= */

    .table-card {

        padding: 18px;

        overflow: hidden;
    }


    .table-wrapper {

        overflow-x: auto;
    }


    table {

        width: 100%;

        border-collapse: collapse;

        min-width: 900px;
    }


    th {

        text-align: left;

        color: var(--muted);

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: 1px;

        padding: 13px 10px;

        border-bottom:
            1px solid var(--border);
    }


    td {

        padding: 13px 10px;

        font-size: 12px;

        border-bottom:
            1px solid var(--border);

        white-space: nowrap;
    }


    tr:hover td {

        background:
            rgba(255,255,255,0.02);
    }


    .table-status {

        display: inline-flex;

        padding: 5px 9px;

        border-radius: 20px;

        font-size: 10px;

        font-weight: 700;
    }


    .table-status.normal {

        color: var(--primary);

        background:
            rgba(66,232,164,0.1);
    }


    .table-status.fault {

        color: var(--red);

        background:
            rgba(255,92,112,0.1);
    }


    .table-status.idle {

        color: var(--yellow);

        background:
            rgba(255,209,102,0.1);
    }


    /* =========================================================
       FOOTER
    ========================================================= */

    footer {

        margin-top: 35px;

        padding: 20px 5px;

        border-top:
            1px solid var(--border);

        color: var(--muted);

        font-size: 11px;

        display: flex;

        justify-content: space-between;

        gap: 15px;
    }


    /* =========================================================
       MOBILE
    ========================================================= */

    .mobile-menu {

        display: none;

        border: 1px solid var(--border);

        background: var(--card);

        color: var(--text);

        padding: 10px;

        border-radius: 10px;

        cursor: pointer;
    }


    @media(max-width:1200px) {

        .metrics {

            grid-template-columns:
                repeat(3,1fr);
        }


        .system-bar {

            grid-template-columns:
                repeat(2,1fr);
        }


        .chart-grid {

            grid-template-columns:
                repeat(2,1fr);
        }


        .summary-grid {

            grid-template-columns:
                repeat(2,1fr);
        }


        .forecast-grid {

            grid-template-columns:
                repeat(2,1fr);
        }


        .current-summary-grid {

            grid-template-columns: 1fr;
        }


        .data-summary-side {

            min-height: auto;
        }
    }


    @media(max-width:850px) {

        .sidebar {

            transform:
                translateX(-100%);

            transition: 0.25s;
        }


        .sidebar.open {

            transform:
                translateX(0);
        }


        .main {

            margin-left: 0;

            width: 100%;

            padding: 17px;
        }


        .mobile-menu {

            display: block;
        }


        .topbar {

            align-items: flex-start;
        }


        .hero-grid {

            grid-template-columns: 1fr;
        }


        .metrics {

            grid-template-columns:
                repeat(2,1fr);
        }


        .chart-grid {

            grid-template-columns: 1fr;
        }


        .chart-card.large {

            grid-column: span 1;
        }
    }


    @media(max-width:550px) {

        .topbar {

            flex-direction: column;
        }


        .top-actions {

            width: 100%;

            justify-content: space-between;
        }


        .system-bar {

            grid-template-columns: 1fr;
        }


        .metrics {

            grid-template-columns: 1fr;
        }


        .summary-grid {

            grid-template-columns: 1fr;
        }


        .forecast-grid {

            grid-template-columns: 1fr;
        }


        .summary-mini-grid {

            grid-template-columns: 1fr 1fr;
        }


        footer {

            flex-direction: column;
        }
    }

</style>
```

</head>

<body data-theme="dark">

<div id="particles"></div>

<div class="app">

```
<!-- =========================================================
     SIDEBAR
========================================================== -->

<aside class="sidebar" id="sidebar">

    <div class="logo">

        <div class="logo-icon">
            ⚡
        </div>

        <div>

            <div class="logo-title">
                GreenPulse AI
            </div>

            <div class="logo-sub">
                ENERGY INTELLIGENCE
            </div>

        </div>

    </div>


    <nav class="nav">

        <a href="#dashboard" class="active">

            <span class="nav-icon">⌂</span>

            Dashboard

        </a>


        <a href="#live">

            <span class="nav-icon">◉</span>

            Live Analytics

        </a>


        <a href="#power">

            <span class="nav-icon">⚡</span>

            Power & Energy

        </a>


        <a href="#forecast">

            <span class="nav-icon">⌁</span>

            AI Forecast

        </a>


        <a href="#monthly">

            <span class="nav-icon">▥</span>

            Monthly Forecast

        </a>


        <a href="#telemetry">

            <span class="nav-icon">▤</span>

            Telemetry

        </a>

    </nav>


    <div class="sidebar-bottom">

        <button
            class="theme-btn"
            id="themeBtn"
            onclick="toggleTheme()"
        >
            ☾ Dark Theme
        </button>

    </div>

</aside>


<!-- =========================================================
     MAIN
========================================================== -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div>

            <div class="eyebrow">
                AI / ML GREEN ENERGY MANAGEMENT SYSTEM
            </div>

            <h1 class="page-title">
                Energy Intelligence Center
            </h1>

            <p class="page-sub">
                Real-time monitoring • Fault detection • Predictive energy analytics
            </p>

        </div>


        <div class="top-actions">

            <button
                class="mobile-menu"
                onclick="toggleSidebar()"
            >
                ☰
            </button>


            <div class="online">

                <span
                    class="online-dot"
                    id="onlineDot"
                ></span>

                <span id="deviceState">
                    CHECKING ESP32...
                </span>

            </div>


            <button
                class="refresh-btn"
                onclick="loadData()"
                title="Refresh"
            >
                ↻ Refresh
            </button>

        </div>

    </header>


    <!-- SYSTEM BAR -->

    <section class="system-bar">

        <div class="system-item">

            <span>
                Energy Source
            </span>

            <strong>
                ☀ Solar PV System
            </strong>

        </div>


        <div class="system-item">

            <span>
                Controller
            </span>

            <strong>
                ESP32
            </strong>

        </div>


        <div class="system-item">

            <span>
                API / Database
            </span>

            <strong id="apiState">
                CHECKING...
            </strong>

        </div>


        <div class="system-item">

            <span>
                AI Engine
            </span>

            <strong>
                Fault + Forecast
            </strong>

        </div>

    </section>


    <!-- HERO -->

    <section
        class="hero-grid"
        id="dashboard"
    >

        <div class="card hero">

            <div class="hero-label">
                Live Solar Output
            </div>


            <div class="hero-power">

                <strong id="heroPower">
                    0.00
                </strong>

                <span>
                    W
                </span>

            </div>


            <div class="hero-info">

                <span>
                    System Capacity
                    <b>30 W</b>
                </span>

                <span>
                    Efficiency
                    <b id="efficiency">
                        0%
                    </b>
                </span>

                <span>
                    Updated
                    <b id="lastUpdate">
                        --
                    </b>
                </span>

            </div>


            <div class="progress">

                <div
                    class="progress-bar"
                    id="powerProgress"
                ></div>

            </div>

        </div>


        <!-- AI HEALTH -->

        <div class="card ai-card">

            <div class="section-label">
                AI System Health
            </div>


            <div class="ai-status">

                <div class="ai-icon">
                    🧠
                </div>


                <div>

                    <div
                        class="status-badge"
                        id="aiStatusBadge"
                    >
                        NORMAL
                    </div>

                    <div
                        style="
                        margin-top:7px;
                        font-size:14px;
                        font-weight:700;
                        "
                        id="aiFault"
                    >
                        Nominal operation
                    </div>

                </div>

            </div>


            <div
                class="ai-message"
                id="aiMessage"
            >
                AI monitoring system is waiting for live telemetry.
            </div>


            <div class="confidence">

                <span>
                    Model Confidence
                </span>

                <strong id="aiConfidence">
                    0%
                </strong>

            </div>

        </div>

    </section>


    <!-- MAIN ALERT -->

    <div
        class="alert"
        id="alertBox"
    >

        <span id="alertIcon">
            ●
        </span>

        <span id="alertText">
            GreenPulse AI is checking ESP32 telemetry...
        </span>

    </div>


    <!-- DETAILED FAULT PANEL -->

    <div
        class="fault-panel"
        id="faultPanel"
    >

        <div class="fault-panel-title">
            ⚠ Active Fault / Risk Conditions
        </div>

        <div
            class="fault-list"
            id="faultList"
        ></div>

    </div>


    <!-- SENSOR METRICS -->

    <section>

        <div class="metrics">

            <div class="card metric">

                <div class="metric-top">

                    <span class="metric-name">
                        Voltage
                    </span>

                    <span class="metric-icon">
                        🔋
                    </span>

                </div>

                <div class="metric-value">

                    <span id="voltageValue">
                        0.00
                    </span>

                    <span class="metric-unit">
                        V
                    </span>

                </div>

            </div>


            <div class="card metric">

                <div class="metric-top">

                    <span class="metric-name">
                        Current
                    </span>

                    <span class="metric-icon">
                        ⎓
                    </span>

                </div>

                <div class="metric-value">

                    <span id="currentValue">
                        0.00
                    </span>

                    <span class="metric-unit">
                        A
                    </span>

                </div>

            </div>


            <div class="card metric">

                <div class="metric-top">

                    <span class="metric-name">
                        Temperature
                    </span>

                    <span class="metric-icon">
                        🌡
                    </span>

                </div>

                <div class="metric-value">

                    <span id="temperatureValue">
                        0.00
                    </span>

                    <span class="metric-unit">
                        °C
                    </span>

                </div>

            </div>


            <div class="card metric">

                <div class="metric-top">

                    <span class="metric-name">
                        Power
                    </span>

                    <span class="metric-icon">
                        ⚡
                    </span>

                </div>

                <div class="metric-value">

                    <span id="powerValue">
                        0.00
                    </span>

                    <span class="metric-unit">
                        W
                    </span>

                </div>

            </div>


            <div class="card metric">

                <div class="metric-top">

                    <span class="metric-name">
                        Daily Energy
                    </span>

                    <span class="metric-icon">
                        ☀
                    </span>

                </div>

                <div class="metric-value">

                    <span id="dailyEnergyValue">
                        0.000
                    </span>

                    <span class="metric-unit">
                        kWh
                    </span>

                </div>

            </div>


            <div class="card metric">

                <div class="metric-top">

                    <span class="metric-name">
                        Peak Power
                    </span>

                    <span class="metric-icon">
                        📈
                    </span>

                </div>

                <div class="metric-value">

                    <span id="peakPowerValue">
                        0.00
                    </span>

                    <span class="metric-unit">
                        W
                    </span>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         LIVE ANALYTICS
    ====================================================== -->

    <section
        class="section"
        id="live"
    >

        <div class="section-title">
            Live Sensor Analytics
        </div>

        <div class="section-description">
            Real-time telemetry received from the ESP32 and stored in Supabase PostgreSQL.
        </div>


        <div class="chart-grid">


            <!-- VOLTAGE -->

            <div class="card chart-card">

                <div class="chart-title">
                    Voltage Trend
                </div>

                <div class="chart-container">

                    <canvas id="voltageChart"></canvas>

                </div>

            </div>


            <!-- CURRENT + SMALL DATA SUMMARY -->

            <div class="current-summary-grid">

                <div class="card chart-card current-chart-card">

                    <div class="chart-title">
                        Current Trend
                    </div>

                    <div class="chart-container">

                        <canvas id="currentChart"></canvas>

                    </div>

                </div>


                <div class="card data-summary-side">

                    <div class="data-summary-title">
                        Data Summary
                    </div>


                    <div class="summary-mini-grid">

                        <div class="summary-mini">

                            <div class="summary-mini-label">
                                Records
                            </div>

                            <div
                                class="summary-mini-value"
                                id="miniRecords"
                            >
                                0
                            </div>

                        </div>


                        <div class="summary-mini">

                            <div class="summary-mini-label">
                                Faults
                            </div>

                            <div
                                class="summary-mini-value"
                                id="miniFaults"
                            >
                                0
                            </div>

                        </div>


                        <div class="summary-mini">

                            <div class="summary-mini-label">
                                Peak
                            </div>

                            <div
                                class="summary-mini-value"
                                id="miniPeak"
                            >
                                0 W
                            </div>

                        </div>


                        <div class="summary-mini">

                            <div class="summary-mini-label">
                                Energy
                            </div>

                            <div
                                class="summary-mini-value"
                                id="miniEnergy"
                            >
                                0 kWh
                            </div>

                        </div>


                        <div class="summary-mini full">

                            <div class="summary-mini-label">
                                Risk
                            </div>

                            <div
                                class="summary-mini-value"
                                id="miniRisk"
                            >
                                LOW
                            </div>

                        </div>

                    </div>


                    <div class="device-last-seen">

                        <div class="device-last-seen-title">
                            ESP32 Last Telemetry
                        </div>

                        <div
                            class="device-last-seen-value"
                            id="miniLastSeen"
                        >
                            --
                        </div>

                    </div>

                </div>

            </div>


            <!-- TEMPERATURE -->

            <div class="card chart-card">

                <div class="chart-title">
                    Temperature Trend
                </div>

                <div class="chart-container">

                    <canvas id="temperatureChart"></canvas>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ENERGY SUMMARY
    ====================================================== -->

    <section class="section">

        <div class="section-title">
            Energy Summary
        </div>


        <div class="summary-grid">


            <div class="card summary-card">

                <div class="summary-label">
                    Daily Energy
                </div>

                <div
                    class="summary-value"
                    id="summaryEnergy"
                >
                    0.000 kWh
                </div>

            </div>


            <div class="card summary-card">

                <div class="summary-label">
                    Peak Power
                </div>

                <div
                    class="summary-value"
                    id="summaryPeak"
                >
                    0.00 W
                </div>

            </div>


            <div class="card summary-card">

                <div class="summary-label">
                    Database Records
                </div>

                <div
                    class="summary-value"
                    id="summaryRecords"
                >
                    0
                </div>

            </div>


            <div class="card summary-card">

                <div class="summary-label">
                    Faults
                </div>

                <div
                    class="summary-value"
                    id="summaryFaults"
                >
                    0
                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         POWER & ENERGY
    ====================================================== -->

    <section
        class="section"
        id="power"
    >

        <div class="section-title">
            Power & Energy Analytics
        </div>


        <div class="card chart-card large">

            <div class="chart-title">
                Power Output & Cumulative Energy
            </div>

            <div class="chart-container">

                <canvas id="powerEnergyChart"></canvas>

            </div>

        </div>

    </section>


    <!-- =====================================================
         AI FORECAST
    ====================================================== -->

    <section
        class="section"
        id="forecast"
    >

        <div class="section-title">
            AI / ML Predictive Analysis
        </div>

        <div class="section-description">
            Predictive estimation based on recent telemetry trends.
        </div>


        <div class="forecast-grid">


            <div class="card forecast-card">

                <div class="forecast-label">
                    Forecast Voltage
                </div>

                <div
                    class="forecast-value"
                    id="forecastVoltage"
                >
                    -- V
                </div>

            </div>


            <div class="card forecast-card">

                <div class="forecast-label">
                    Forecast Current
                </div>

                <div
                    class="forecast-value"
                    id="forecastCurrent"
                >
                    -- A
                </div>

            </div>


            <div class="card forecast-card">

                <div class="forecast-label">
                    Forecast Temperature
                </div>

                <div
                    class="forecast-value"
                    id="forecastTemperature"
                >
                    -- °C
                </div>

            </div>


            <div class="card forecast-card">

                <div class="forecast-label">
                    Forecast Power
                </div>

                <div
                    class="forecast-value"
                    id="forecastPower"
                >
                    -- W
                </div>

                <div
                    class="risk"
                    id="forecastRisk"
                >
                    LOW RISK
                </div>

            </div>

        </div>


        <div class="card chart-card large">

            <div class="chart-title">
                Actual Power vs Forecast Power
            </div>

            <div class="chart-container">

                <canvas id="forecastPowerChart"></canvas>

            </div>

        </div>


        <div class="card forecast-analysis">

            <strong style="color:var(--text)">
                AI Forecast Analysis
            </strong>

            <p
                id="forecastAnalysis"
                style="margin-top:8px"
            >
                Waiting for enough telemetry to calculate a prediction.
            </p>

        </div>

    </section>


    <!-- =====================================================
         MONTHLY FORECAST
    ====================================================== -->

    <section
        class="section"
        id="monthly"
    >

        <div class="section-title">
            Monthly Power Forecast
        </div>

        <div class="section-description">
            Estimated monthly generation based on current measured power and seasonal factors.
        </div>


        <div class="card chart-card large">

            <div class="chart-title">
                Monthly Forecast
            </div>

            <div class="chart-container">

                <canvas id="monthlyForecastChart"></canvas>

            </div>

        </div>


        <div class="card forecast-analysis">

            <strong style="color:var(--text)">
                Forecast Method
            </strong>

            <p style="margin-top:8px">

                Monthly values are estimates generated from the
                current measured power, recent telemetry and
                predefined seasonal factors. They are intended
                for project-level predictive analysis and should
                not be interpreted as certified solar-energy
                forecasts.

            </p>

        </div>

    </section>


    <!-- =====================================================
         TELEMETRY
    ====================================================== -->

    <section
        class="section"
        id="telemetry"
    >

        <div class="section-title">
            Live Telemetry
        </div>


        <div class="card table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Timestamp
                            </th>

                            <th>
                                Voltage
                            </th>

                            <th>
                                Current
                            </th>

                            <th>
                                Temperature
                            </th>

                            <th>
                                Power
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Fault
                            </th>

                            <th>
                                AI Confidence
                            </th>

                        </tr>

                    </thead>


                    <tbody id="telemetryBody">

                        <tr>

                            <td
                                colspan="9"
                                style="
                                text-align:center;
                                color:var(--muted);
                                "
                            >
                                Loading telemetry...

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </section>


    <!-- FOOTER -->

    <footer>

        <span>
            © 2026 GreenPulse AI
        </span>

        <span>
            ESP32 • Supabase PostgreSQL • Chart.js • AI/ML
        </span>

    </footer>


</main>
```

</div>

<script>

/* ============================================================
   GREENPULSE AI JAVASCRIPT
============================================================ */


/* ============================================================
   API CONFIGURATION
============================================================ */

const API = {

    history: "api/history.php?limit=40",

    latest: "api/latest.php",

    summary: "api/summary.php"

};


/* ============================================================
   SYSTEM CONFIGURATION
============================================================ */

const SYSTEM_CAPACITY_W = 30;


/*
|--------------------------------------------------------------------------
| ESP32 HEARTBEAT / OFFLINE DETECTION
|--------------------------------------------------------------------------
|
| If the newest database reading is older than this amount,
| the dashboard considers the ESP32 disconnected.
|
| Example:
| ESP32 sends data every 5 seconds.
| 30 seconds gives enough tolerance for temporary network delay.
|
*/

const ESP32_OFFLINE_TIMEOUT_SECONDS = 30;


/*
|--------------------------------------------------------------------------
| FAULT THRESHOLDS
|--------------------------------------------------------------------------
|
| These are project-level thresholds.
| Calibrate them according to your actual panel/sensor setup.
|
*/

const LIMITS = {

    /*
    | Solar panel nominal operating voltage:
    | 2 x 18 V panels = approximately 36 V.
    |
    | These thresholds can be changed according to actual setup.
    */

    UNDER_VOLTAGE: 15,

    OVER_VOLTAGE: 42,

    WARNING_VOLTAGE: 40,


    /*
    | Current limit.
    |
    | Your panel current rating is approximately 0.84 A per panel.
    | The exact allowable current depends on your connection/load.
    */

    OVER_CURRENT: 2.8,

    WARNING_CURRENT: 2.0,


    /*
    | Short-circuit / abnormal current.
    |
    | This is NOT a certified electrical short-circuit detector.
    | It is a software anomaly indication.
    */

    SHORT_CIRCUIT_CURRENT: 4.0,


    /*
    | Temperature.
    */

    HIGH_TEMPERATURE: 60,

    WARNING_TEMPERATURE: 50,


    /*
    | Power.
    */

    OVER_POWER: 30,

    HIGH_POWER: 27,


    /*
    | Very low power while voltage is present.
    |
    | This can indicate sensor/load/wiring abnormality.
    */

    LOW_POWER_VOLTAGE_PRESENT: 1.0

};


/* ============================================================
   GLOBAL STATE
============================================================ */

let voltageChart = null;

let currentChart = null;

let temperatureChart = null;

let powerEnergyChart = null;

let forecastPowerChart = null;

let monthlyForecastChart = null;

let lastRows = [];

let lastLatest = null;

let lastSummary = null;

let lastESP32Online = false;

let lastAPIOnline = false;


/* ============================================================
   PARTICLES
============================================================ */

function createParticles() {

    const container =
        document.getElementById("particles");

    if (!container) return;


    for (let i = 0; i < 35; i++) {

        const particle =
            document.createElement("div");

        particle.className =
            "particle";

        particle.style.left =
            Math.random() * 100 + "%";

        particle.style.animationDuration =
            (8 + Math.random() * 15) + "s";

        particle.style.animationDelay =
            (-Math.random() * 20) + "s";

        particle.style.opacity =
            0.1 + Math.random() * 0.4;

        container.appendChild(particle);
    }

}


/* ============================================================
   THEME
============================================================ */

function loadTheme() {

    const saved =
        localStorage.getItem(
            "greenpulse-theme"
        );


    if (saved === "light") {

        document.body.dataset.theme =
            "light";

        document.getElementById(
            "themeBtn"
        ).textContent =
            "☀ Light Theme";

    } else {

        document.body.dataset.theme =
            "dark";

        document.getElementById(
            "themeBtn"
        ).textContent =
            "☾ Dark Theme";
    }

}


function toggleTheme() {

    const current =
        document.body.dataset.theme;

    const next =
        current === "dark"
            ? "light"
            : "dark";


    document.body.dataset.theme =
        next;


    localStorage.setItem(
        "greenpulse-theme",
        next
    );


    document.getElementById(
        "themeBtn"
    ).textContent =
        next === "dark"
            ? "☾ Dark Theme"
            : "☀ Light Theme";


    refreshCharts();
}


/* ============================================================
   SIDEBAR
============================================================ */

function toggleSidebar() {

    const sidebar =
        document.getElementById(
            "sidebar"
        );

    sidebar.classList.toggle(
        "open"
    );

}


/* ============================================================
   NUMBER HELPERS
============================================================ */

function safeNumber(value, fallback = 0) {

    const number =
        Number(value);

    return Number.isFinite(number)
        ? number
        : fallback;
}


function round(value, decimals = 2) {

    return Number(
        safeNumber(value).toFixed(
            decimals
        )
    );

}


/* ============================================================
   TIME HELPERS
============================================================ */

function formatTime(timestamp) {

    if (!timestamp) {

        return "--";
    }


    const date =
        new Date(timestamp);


    if (Number.isNaN(date.getTime())) {

        return String(timestamp);
    }


    return date.toLocaleTimeString(
        [],
        {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        }
    );

}


function formatDateTime(timestamp) {

    if (!timestamp) {

        return "--";
    }


    const date =
        new Date(timestamp);


    if (Number.isNaN(date.getTime())) {

        return String(timestamp);
    }


    return date.toLocaleString(
        [],
        {
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        }
    );

}


/*
|--------------------------------------------------------------------------
| Calculate how old the latest ESP32 reading is.
|--------------------------------------------------------------------------
*/

function getReadingAgeSeconds(timestamp) {

    if (!timestamp) {

        return Infinity;
    }


    const time =
        new Date(timestamp).getTime();


    if (!Number.isFinite(time)) {

        return Infinity;
    }


    return Math.max(
        0,
        (Date.now() - time) / 1000
    );

}


/*
|--------------------------------------------------------------------------
| ESP32 freshness.
|--------------------------------------------------------------------------
*/

function isESP32Online(latest) {

    if (!latest) {

        return false;
    }


    const age =
        getReadingAgeSeconds(
            latest.created_at
        );


    return age <=
        ESP32_OFFLINE_TIMEOUT_SECONDS;
}


/*
|--------------------------------------------------------------------------
| Human-readable age.
|--------------------------------------------------------------------------
*/

function formatAge(seconds) {

    if (!Number.isFinite(seconds)) {

        return "No telemetry";
    }


    if (seconds < 60) {

        return Math.round(seconds) + " sec ago";
    }


    if (seconds < 3600) {

        return Math.round(seconds / 60) + " min ago";
    }


    return Math.round(seconds / 3600) + " hr ago";
}


/* ============================================================
   FETCH
============================================================ */

async function fetchJSON(url) {

    const response =
        await fetch(
            url,
            {
                cache: "no-store"
            }
        );


    if (!response.ok) {

        throw new Error(
            "HTTP " + response.status
        );
    }


    const data =
        await response.json();


    return data;
}


/* ============================================================
   CHART COLORS
============================================================ */

function chartTextColor() {

    return getComputedStyle(
        document.body
    ).getPropertyValue(
        "--muted"
    ).trim();

}


function chartGridColor() {

    return document.body.dataset.theme === "light"

        ? "rgba(0,0,0,0.08)"

        : "rgba(255,255,255,0.07)";
}


function chartOptions() {

    return {

        responsive: true,

        maintainAspectRatio: false,

        interaction: {

            mode: "index",

            intersect: false

        },

        plugins: {

            legend: {

                labels: {

                    color:
                        chartTextColor(),

                    font: {

                        size: 10

                    }

                }

            },

            tooltip: {

                mode: "index",

                intersect: false

            }

        },

        scales: {

            x: {

                ticks: {

                    color:
                        chartTextColor(),

                    maxTicksLimit: 8

                },

                grid: {

                    color:
                        chartGridColor()

                }

            },

            y: {

                ticks: {

                    color:
                        chartTextColor()

                },

                grid: {

                    color:
                        chartGridColor()

                }

            }

        }

    };

}


/* ============================================================
   CREATE SENSOR CHART
============================================================ */

function createSensorChart(
    canvasId,
    label,
    borderColor
) {

    const canvas =
        document.getElementById(
            canvasId
        );


    if (!canvas) return null;


    return new Chart(

        canvas.getContext("2d"),

        {

            type: "line",

            data: {

                labels: [],

                datasets: [

                    {

                        label: label,

                        data: [],

                        borderColor:
                            borderColor,

                        backgroundColor:
                            "rgba(66,232,164,0.08)",

                        borderWidth: 2,

                        tension: 0.35,

                        fill: true,

                        pointRadius: 2,

                        pointHoverRadius: 5

                    }

                ]

            },

            options: chartOptions()

        }

    );

}


/* ============================================================
   INITIALIZE CHARTS
============================================================ */

function initializeCharts() {

    voltageChart =
        createSensorChart(
            "voltageChart",
            "Voltage (V)",
            "#54a7ff"
        );


    currentChart =
        createSensorChart(
            "currentChart",
            "Current (A)",
            "#42e8a4"
        );


    temperatureChart =
        createSensorChart(
            "temperatureChart",
            "Temperature (°C)",
            "#ff9f43"
        );


    /* =========================================================
       POWER + ENERGY
    ========================================================= */

    powerEnergyChart =
        new Chart(

            document
                .getElementById(
                    "powerEnergyChart"
                )
                .getContext("2d"),

            {

                type: "line",

                data: {

                    labels: [],

                    datasets: [

                        {

                            label:
                                "Power (W)",

                            data: [],

                            borderColor:
                                "#42e8a4",

                            backgroundColor:
                                "rgba(66,232,164,0.08)",

                            borderWidth: 2,

                            tension: 0.35,

                            fill: true,

                            yAxisID:
                                "y"

                        },

                        {

                            label:
                                "Cumulative Energy (kWh)",

                            data: [],

                            borderColor:
                                "#9b7cff",

                            backgroundColor:
                                "rgba(155,124,255,0.05)",

                            borderWidth: 2,

                            tension: 0.35,

                            fill: false,

                            yAxisID:
                                "y1"

                        }

                    ]

                },

                options: {

                    ...chartOptions(),

                    scales: {

                        x: {

                            ticks: {

                                color:
                                    chartTextColor(),

                                maxTicksLimit: 8

                            },

                            grid: {

                                color:
                                    chartGridColor()

                            }

                        },

                        y: {

                            position: "left",

                            title: {

                                display: true,

                                text: "Power (W)",

                                color:
                                    chartTextColor()

                            },

                            ticks: {

                                color:
                                    chartTextColor()

                            },

                            grid: {

                                color:
                                    chartGridColor()

                            }

                        },

                        y1: {

                            position: "right",

                            title: {

                                display: true,

                                text: "Energy (kWh)",

                                color:
                                    chartTextColor()

                            },

                            ticks: {

                                color:
                                    chartTextColor()

                            },

                            grid: {

                                drawOnChartArea: false

                            }

                        }

                    }

                }

            }

        );


    /* =========================================================
       FORECAST
    ========================================================= */

    forecastPowerChart =
        new Chart(

            document
                .getElementById(
                    "forecastPowerChart"
                )
                .getContext("2d"),

            {

                type: "line",

                data: {

                    labels: [],

                    datasets: [

                        {

                            label:
                                "Actual Power",

                            data: [],

                            borderColor:
                                "#54a7ff",

                            borderWidth: 2,

                            tension: 0.35,

                            pointRadius: 2

                        },

                        {

                            label:
                                "Forecast Power",

                            data: [],

                            borderColor:
                                "#42e8a4",

                            borderWidth: 2,

                            borderDash:
                                [6,5],

                            tension: 0.35,

                            pointRadius: 2

                        }

                    ]

                },

                options:
                    chartOptions()

            }

        );


    /* =========================================================
       MONTHLY FORECAST
    ========================================================= */

    monthlyForecastChart =
        new Chart(

            document
                .getElementById(
                    "monthlyForecastChart"
                )
                .getContext("2d"),

            {

                type: "bar",

                data: {

                    labels: [

                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec"

                    ],

                    datasets: [

                        {

                            label:
                                "Forecast Energy (kWh)",

                            data: [],

                            backgroundColor:
                                "rgba(66,232,164,0.45)",

                            borderColor:
                                "#42e8a4",

                            borderWidth: 1,

                            borderRadius: 6

                        }

                    ]

                },

                options:
                    chartOptions()

            }

        );

}


/* ============================================================
   FAULT DETECTION
============================================================ */

function detectFaults(
    voltage,
    current,
    temperature,
    power
) {

    const faults = [];


    /*
    |--------------------------------------------------------------------------
    | Under Voltage
    |--------------------------------------------------------------------------
    */

    if (
        voltage > 0 &&
        voltage < LIMITS.UNDER_VOLTAGE
    ) {

        faults.push(
            "UNDER-VOLTAGE"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Over Voltage
    |--------------------------------------------------------------------------
    */

    if (
        voltage > LIMITS.OVER_VOLTAGE
    ) {

        faults.push(
            "OVER-VOLTAGE"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Over Current
    |--------------------------------------------------------------------------
    */

    if (
        current > LIMITS.OVER_CURRENT
    ) {

        faults.push(
            "OVER-CURRENT"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Software current anomaly / possible short circuit
    |--------------------------------------------------------------------------
    */

    if (
        current >= LIMITS.SHORT_CIRCUIT_CURRENT
    ) {

        faults.push(
            "POSSIBLE SHORT-CIRCUIT / CURRENT ANOMALY"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | High Temperature
    |--------------------------------------------------------------------------
    */

    if (
        temperature > LIMITS.HIGH_TEMPERATURE
    ) {

        faults.push(
            "OVER-TEMPERATURE"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Power overload
    |--------------------------------------------------------------------------
    */

    if (
        power > LIMITS.OVER_POWER
    ) {

        faults.push(
            "POWER OVERLOAD"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Voltage present but almost no power.
    |--------------------------------------------------------------------------
    */

    if (
        voltage >= LIMITS.UNDER_VOLTAGE &&
        voltage > 10 &&
        power < LIMITS.LOW_POWER_VOLTAGE_PRESENT &&
        current < 0.05
    ) {

        faults.push(
            "LOW POWER / GENERATION ANOMALY"
        );

    }


    return faults;

}


/* ============================================================
   RISK LEVEL
============================================================ */

function calculateRisk(
    voltage,
    current,
    temperature,
    power
) {

    const faults =
        detectFaults(
            voltage,
            current,
            temperature,
            power
        );


    /*
    |--------------------------------------------------------------------------
    | HIGH RISK
    |--------------------------------------------------------------------------
    */

    if (
        faults.includes("OVER-VOLTAGE") ||
        faults.includes("OVER-CURRENT") ||
        faults.includes("POSSIBLE SHORT-CIRCUIT / CURRENT ANOMALY") ||
        faults.includes("OVER-TEMPERATURE") ||
        faults.includes("POWER OVERLOAD")
    ) {

        return "HIGH";

    }


    /*
    |--------------------------------------------------------------------------
    | MEDIUM RISK
    |--------------------------------------------------------------------------
    */

    if (
        faults.length > 0 ||
        voltage > LIMITS.WARNING_VOLTAGE ||
        current > LIMITS.WARNING_CURRENT ||
        temperature > LIMITS.WARNING_TEMPERATURE ||
        power > LIMITS.HIGH_POWER
    ) {

        return "MEDIUM";

    }


    return "LOW";

}


/* ============================================================
   UPDATE FAULT PANEL
============================================================ */

function updateFaultPanel(
    faults
) {

    const panel =
        document.getElementById(
            "faultPanel"
        );

    const list =
        document.getElementById(
            "faultList"
        );


    if (!faults.length) {

        panel.classList.remove(
            "show"
        );

        list.innerHTML = "";

        return;

    }


    panel.classList.add(
        "show"
    );


    list.innerHTML = "";


    faults.forEach(
        fault => {

            const chip =
                document.createElement(
                    "div"
                );

            chip.className =
                "fault-chip";

            chip.textContent =
                "⚠ " + fault;

            list.appendChild(
                chip
            );

        }
    );

}


/* ============================================================
   UPDATE SENSOR CHARTS
============================================================ */

function updateSensorCharts(rows) {

    if (!rows.length) return;


    const labels =
        rows.map(
            row =>
                formatTime(
                    row.created_at
                )
        );


    const voltage =
        rows.map(
            row =>
                safeNumber(
                    row.voltage
                )
        );


    const current =
        rows.map(
            row =>
                safeNumber(
                    row.current
                )
        );


    const temperature =
        rows.map(
            row =>
                safeNumber(
                    row.temperature
                )
        );


    /*
    |--------------------------------------------------------------------------
    | Voltage
    |--------------------------------------------------------------------------
    */

    if (voltageChart) {

        voltageChart.data.labels =
            labels;

        voltageChart.data.datasets[0].data =
            voltage;

        voltageChart.update("none");

    }


    /*
    |--------------------------------------------------------------------------
    | Current
    |--------------------------------------------------------------------------
    */

    if (currentChart) {

        currentChart.data.labels =
            labels;

        currentChart.data.datasets[0].data =
            current;

        currentChart.update("none");

    }


    /*
    |--------------------------------------------------------------------------
    | Temperature
    |--------------------------------------------------------------------------
    */

    if (temperatureChart) {

        temperatureChart.data.labels =
            labels;

        temperatureChart.data.datasets[0].data =
            temperature;

        temperatureChart.update("none");

    }

}


/* ============================================================
   ENERGY CALCULATION
============================================================ */

function calculateEnergy(rows) {

    if (!rows.length) {

        return 0;
    }


    let energyWh = 0;


    for (
        let i = 1;
        i < rows.length;
        i++
    ) {

        const previous =
            rows[i - 1];

        const current =
            rows[i];


        const previousPower =
            safeNumber(
                previous.power
            );


        const currentPower =
            safeNumber(
                current.power
            );


        const previousTime =
            new Date(
                previous.created_at
            ).getTime();


        const currentTime =
            new Date(
                current.created_at
            ).getTime();


        let seconds =
            (currentTime -
                previousTime) / 1000;


        if (
            !Number.isFinite(seconds) ||
            seconds <= 0 ||
            seconds > 3600
        ) {

            continue;
        }


        const averagePower =
            (
                previousPower +
                currentPower
            ) / 2;


        energyWh +=
            averagePower *
            seconds /
            3600;

    }


    return energyWh / 1000;

}


/* ============================================================
   POWER / ENERGY CHART
============================================================ */

function updatePowerEnergy(rows) {

    if (!rows.length) return;


    let cumulativeKWh = 0;


    const labels = [];

    const powers = [];

    const energies = [];


    for (
        let i = 0;
        i < rows.length;
        i++
    ) {

        const row =
            rows[i];


        if (i > 0) {

            const previous =
                rows[i - 1];


            const t1 =
                new Date(
                    previous.created_at
                ).getTime();


            const t2 =
                new Date(
                    row.created_at
                ).getTime();


            const seconds =
                (t2 - t1) / 1000;


            if (
                Number.isFinite(seconds) &&
                seconds > 0 &&
                seconds <= 3600
            ) {

                const p1 =
                    safeNumber(
                        previous.power
                    );


                const p2 =
                    safeNumber(
                        row.power
                    );


                cumulativeKWh +=

                    (
                        (p1 + p2) / 2
                    ) *

                    seconds /

                    3600 /

                    1000;

            }

        }


        labels.push(
            formatTime(
                row.created_at
            )
        );


        powers.push(
            safeNumber(
                row.power
            )
        );


        energies.push(
            round(
                cumulativeKWh,
                6
            )
        );

    }


    if (!powerEnergyChart) return;


    powerEnergyChart.data.labels =
        labels;


    powerEnergyChart.data.datasets[0].data =
        powers;


    powerEnergyChart.data.datasets[1].data =
        energies;


    powerEnergyChart.update("none");

}


/* ============================================================
   AI STATUS
============================================================ */

function updateAIStatus(latest) {

    if (!latest) return;


    const voltage =
        safeNumber(
            latest.voltage
        );


    const current =
        safeNumber(
            latest.current
        );


    const temperature =
        safeNumber(
            latest.temperature
        );


    const power =
        safeNumber(
            latest.power
        );


    const originalStatus =
        String(
            latest.status || "NORMAL"
        ).toUpperCase();


    const originalFault =
        String(
            latest.fault_type ||
            "NOMINAL"
        );


    const confidence =
        safeNumber(
            latest.ai_confidence
        );


    const faults =
        detectFaults(
            voltage,
            current,
            temperature,
            power
        );


    const risk =
        calculateRisk(
            voltage,
            current,
            temperature,
            power
        );


    const badge =
        document.getElementById(
            "aiStatusBadge"
        );


    const faultText =
        document.getElementById(
            "aiFault"
        );


    const message =
        document.getElementById(
            "aiMessage"
        );


    const confidenceElement =
        document.getElementById(
            "aiConfidence"
        );


    /*
    |--------------------------------------------------------------------------
    | Determine displayed status.
    |--------------------------------------------------------------------------
    */

    let displayStatus =
        originalStatus;


    if (faults.length > 0) {

        displayStatus =
            "FAULT";

    }
    else if (risk === "MEDIUM") {

        displayStatus =
            "WARNING";

    }


    badge.className =
        "status-badge";


    if (
        displayStatus === "FAULT"
    ) {

        badge.classList.add(
            "fault"
        );

    }
    else if (
        displayStatus === "WARNING"
    ) {

        badge.classList.add(
            "medium"
        );

    }
    else if (
        displayStatus === "IDLE"
    ) {

        badge.classList.add(
            "idle"
        );

    }


    badge.textContent =
        displayStatus;


    /*
    |--------------------------------------------------------------------------
    | Fault description.
    |--------------------------------------------------------------------------
    */

    if (faults.length > 0) {

        faultText.textContent =
            faults[0];

    }
    else if (
        originalFault !== "NOMINAL"
    ) {

        faultText.textContent =
            originalFault;

    }
    else {

        faultText.textContent =
            "Nominal operation";

    }


    confidenceElement.textContent =
        round(
            confidence,
            1
        ) + "%";


    /*
    |--------------------------------------------------------------------------
    | AI message.
    |--------------------------------------------------------------------------
    */

    if (faults.length > 0) {

        message.textContent =
            "AI monitoring has identified an abnormal operating condition. Review the active fault indicators and inspect the corresponding sensor, wiring and solar system condition.";

    }
    else if (risk === "MEDIUM") {

        message.textContent =
            "The system is operating within a warning range. Continue monitoring voltage, current, temperature and power for abnormal trends.";

    }
    else if (displayStatus === "IDLE") {

        message.textContent =
            "Solar generation is currently low. This may be caused by low irradiation or an inactive generation condition.";

    }
    else {

        message.textContent =
            "Solar generation is within the configured monitoring limits. No critical abnormality is currently detected.";

    }


    updateFaultPanel(
        faults
    );


    updateAlert(
        displayStatus,
        faults,
        risk,
        voltage,
        current,
        temperature,
        power
    );


    updateMiniRisk(
        risk
    );

}


/* ============================================================
   ALERT
============================================================ */

function updateAlert(
    status,
    faults,
    risk,
    voltage,
    current,
    temperature,
    power
) {

    const box =
        document.getElementById(
            "alertBox"
        );


    const text =
        document.getElementById(
            "alertText"
        );


    const icon =
        document.getElementById(
            "alertIcon"
        );


    box.classList.remove(
        "fault",
        "medium",
        "offline"
    );


    /*
    |--------------------------------------------------------------------------
    | ESP32 offline is handled separately by updateDeviceStatus().
    |--------------------------------------------------------------------------
    */

    if (status === "FAULT") {

        box.classList.add(
            "fault"
        );


        icon.textContent =
            "⚠";


        text.textContent =
            "FAULT DETECTED: " +
            faults.join(
                " • "
            ) +
            ". Voltage: " +
            round(voltage,2) +
            " V | Current: " +
            round(current,2) +
            " A | Temperature: " +
            round(temperature,1) +
            " °C | Power: " +
            round(power,2) +
            " W.";

    }
    else if (risk === "MEDIUM") {

        box.classList.add(
            "medium"
        );


        icon.textContent =
            "⚠";


        text.textContent =
            "MEDIUM RISK: System values are approaching configured operating limits. Continue monitoring.";

    }
    else {

        icon.textContent =
            "✓";


        text.textContent =
            "GreenPulse AI: System operating normally. Live telemetry is being monitored continuously.";

    }

}


/* ============================================================
   LIVE METRICS
============================================================ */

function updateLiveMetrics(latest) {

    if (!latest) return;


    const voltage =
        safeNumber(
            latest.voltage
        );


    const current =
        safeNumber(
            latest.current
        );


    const temperature =
        safeNumber(
            latest.temperature
        );


    const power =
        safeNumber(
            latest.power
        );


    document.getElementById(
        "voltageValue"
    ).textContent =
        round(
            voltage,
            2
        );


    document.getElementById(
        "currentValue"
    ).textContent =
        round(
            current,
            2
        );


    document.getElementById(
        "temperatureValue"
    ).textContent =
        round(
            temperature,
            2
        );


    document.getElementById(
        "powerValue"
    ).textContent =
        round(
            power,
            2
        );


    document.getElementById(
        "heroPower"
    ).textContent =
        round(
            power,
            2
        );


    const efficiency =
        Math.min(
            100,
            Math.max(
                0,
                power /
                SYSTEM_CAPACITY_W *
                100
            )
        );


    document.getElementById(
        "efficiency"
    ).textContent =
        round(
            efficiency,
            1
        ) + "%";


    document.getElementById(
        "powerProgress"
    ).style.width =
        efficiency + "%";


    document.getElementById(
        "lastUpdate"
    ).textContent =
        formatTime(
            latest.created_at
        );

}


/* ============================================================
   DEVICE STATUS
============================================================ */

function updateDeviceStatus(
    latest
) {

    const state =
        document.getElementById(
            "deviceState"
        );


    const dot =
        document.getElementById(
            "onlineDot"
        );


    const apiState =
        document.getElementById(
            "apiState"
        );


    /*
    |--------------------------------------------------------------------------
    | API is online because fetch succeeded.
    |--------------------------------------------------------------------------
    */

    if (lastAPIOnline) {

        apiState.textContent =
            "API ONLINE";

    }
    else {

        apiState.textContent =
            "API OFFLINE";

    }


    /*
    |--------------------------------------------------------------------------
    | ESP32 status is independent from API status.
    |--------------------------------------------------------------------------
    */

    const espOnline =
        isESP32Online(
            latest
        );


    lastESP32Online =
        espOnline;


    dot.classList.remove(
        "offline",
        "warning"
    );


    if (espOnline) {

        state.textContent =
            "ESP32 ONLINE";

        dot.classList.remove(
            "offline"
        );

    }
    else {

        state.textContent =
            "ESP32 OFFLINE";

        dot.classList.add(
            "offline"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Last telemetry display.
    |--------------------------------------------------------------------------
    */

    const miniLastSeen =
        document.getElementById(
            "miniLastSeen"
        );


    if (latest) {

        const age =
            getReadingAgeSeconds(
                latest.created_at
            );


        miniLastSeen.textContent =
            formatDateTime(
                latest.created_at
            ) +
            " • " +
            formatAge(age);

    }
    else {

        miniLastSeen.textContent =
            "No telemetry received";

    }


    /*
    |--------------------------------------------------------------------------
    | If ESP32 is offline, show clear dashboard alert.
    |--------------------------------------------------------------------------
    */

    if (!espOnline) {

        const alertBox =
            document.getElementById(
                "alertBox"
            );


        const alertText =
            document.getElementById(
                "alertText"
            );


        const alertIcon =
            document.getElementById(
                "alertIcon"
            );


        alertBox.classList.remove(
            "fault",
            "medium"
        );


        alertBox.classList.add(
            "offline"
        );


        alertIcon.textContent =
            "⚠";


        if (latest) {

            const age =
                getReadingAgeSeconds(
                    latest.created_at
                );


            alertText.textContent =
                "ESP32 OFFLINE: No fresh telemetry received for " +
                Math.round(age) +
                " seconds. API/database may still be online, but the ESP32 has not sent recent data.";

        }
        else {

            alertText.textContent =
                "ESP32 OFFLINE: No telemetry record is available. Check ESP32 power, Wi-Fi and data upload connection.";

        }

    }

}


/* ============================================================
   SUMMARY
============================================================ */

function updateSummary(
    summary,
    rows
) {

    let energy =
        0;


    if (rows.length >= 2) {

        energy =
            calculateEnergy(
                rows
            );

    }


    const peak =
        summary
            ? safeNumber(
                summary.peak_power
            )
            :
            (
                rows.length
                    ?
                    Math.max(
                        ...rows.map(
                            r =>
                                safeNumber(
                                    r.power
                                )
                        )
                    )
                    :
                    0
            );


    const records =
        summary
            ? safeNumber(
                summary.total_readings
            )
            :
            rows.length;


    const faults =
        summary
            ? safeNumber(
                summary.fault_count
            )
            :
            rows.filter(
                r =>
                    String(
                        r.status
                    ).toUpperCase()
                    === "FAULT"
            ).length;


    document.getElementById(
        "dailyEnergyValue"
    ).textContent =
        round(
            energy,
            4
        );


    document.getElementById(
        "peakPowerValue"
    ).textContent =
        round(
            peak,
            2
        );


    document.getElementById(
        "summaryEnergy"
    ).textContent =
        round(
            energy,
            4
        ) + " kWh";


    document.getElementById(
        "summaryPeak"
    ).textContent =
        round(
            peak,
            2
        ) + " W";


    document.getElementById(
        "summaryRecords"
    ).textContent =
        Math.round(
            records
        );


    document.getElementById(
        "summaryFaults"
    ).textContent =
        Math.round(
            faults
        );


    /*
    |--------------------------------------------------------------------------
    | Small summary beside Current graph.
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        "miniRecords"
    ).textContent =
        Math.round(
            records
        );


    document.getElementById(
        "miniFaults"
    ).textContent =
        Math.round(
            faults
        );


    document.getElementById(
        "miniPeak"
    ).textContent =
        round(
            peak,
            2
        ) + " W";


    document.getElementById(
        "miniEnergy"
    ).textContent =
        round(
            energy,
            4
        ) + " kWh";

}


/* ============================================================
   MINI RISK
============================================================ */

function updateMiniRisk(
    risk
) {

    const element =
        document.getElementById(
            "miniRisk"
        );


    element.textContent =
        risk;


    if (risk === "HIGH") {

        element.style.color =
            "var(--red)";

    }
    else if (risk === "MEDIUM") {

        element.style.color =
            "var(--yellow)";

    }
    else {

        element.style.color =
            "var(--primary)";

    }

}


/* ============================================================
   LINEAR REGRESSION
============================================================ */

function linearForecast(
    values,
    steps = 1
) {

    const clean =
        values
            .map(
                safeNumber
            )
            .filter(
                Number.isFinite
            );


    if (clean.length < 2) {

        return clean.length
            ? clean[clean.length - 1]
            : 0;

    }


    const n =
        clean.length;


    let sumX = 0;

    let sumY = 0;

    let sumXY = 0;

    let sumX2 = 0;


    for (
        let i = 0;
        i < n;
        i++
    ) {

        sumX += i;

        sumY += clean[i];

        sumXY +=
            i *
            clean[i];

        sumX2 +=
            i *
            i;

    }


    const denominator =
        n * sumX2 -
        sumX * sumX;


    if (
        denominator === 0
    ) {

        return clean[n - 1];

    }


    const slope =
        (
            n * sumXY -
            sumX * sumY
        ) /
        denominator;


    const intercept =
        (
            sumY -
            slope * sumX
        ) /
        n;


    return intercept +
        slope *
        (n - 1 + steps);

}


/* ============================================================
   FORECAST
============================================================ */

function updateForecast(rows) {

    if (!rows.length) return;


    const voltage =
        rows.map(
            r =>
                safeNumber(
                    r.voltage
                )
        );


    const current =
        rows.map(
            r =>
                safeNumber(
                    r.current
                )
        );


    const temperature =
        rows.map(
            r =>
                safeNumber(
                    r.temperature
                )
        );


    const power =
        rows.map(
            r =>
                safeNumber(
                    r.power
                )
        );


    const forecastV =
        Math.max(
            0,
            linearForecast(
                voltage,
                1
            )
        );


    const forecastI =
        Math.max(
            0,
            linearForecast(
                current,
                1
            )
        );


    const forecastT =
        Math.max(
            0,
            linearForecast(
                temperature,
                1
            )
        );


    let forecastP =
        forecastV *
        forecastI;


    forecastP =
        Math.max(
            0,
            Math.min(
                forecastP,
                SYSTEM_CAPACITY_W * 1.5
            )
        );


    document.getElementById(
        "forecastVoltage"
    ).textContent =
        round(
            forecastV,
            2
        ) + " V";


    document.getElementById(
        "forecastCurrent"
    ).textContent =
        round(
            forecastI,
            2
        ) + " A";


    document.getElementById(
        "forecastTemperature"
    ).textContent =
        round(
            forecastT,
            2
        ) + " °C";


    document.getElementById(
        "forecastPower"
    ).textContent =
        round(
            forecastP,
            2
        ) + " W";


    const risk =
        calculateRisk(
            forecastV,
            forecastI,
            forecastT,
            forecastP
        );


    const riskElement =
        document.getElementById(
            "forecastRisk"
        );


    riskElement.className =
        "risk";


    if (
        risk === "MEDIUM"
    ) {

        riskElement.classList.add(
            "medium"
        );

    }
    else if (
        risk === "HIGH"
    ) {

        riskElement.classList.add(
            "high"
        );

    }


    riskElement.textContent =
        risk + " RISK";


    const latestPower =
        power[
            power.length - 1
        ] || 0;


    const difference =
        forecastP -
        latestPower;


    let analysis = "";


    if (
        risk === "HIGH"
    ) {

        analysis =
            "The predictive model indicates a potentially high-risk operating condition. The forecast is influenced by recent voltage, current and temperature trends. Sensor calibration and physical inspection are recommended.";

    }
    else if (
        risk === "MEDIUM"
    ) {

        analysis =
            "The predicted operating point is moderately elevated. Continue monitoring the solar voltage, current and temperature. The forecast is trend-based and may change as new ESP32 readings arrive.";

    }
    else if (
        difference > 1
    ) {

        analysis =
            "Recent telemetry indicates a possible increase in solar power output. The forecast suggests approximately " +
            round(
                forecastP,
                2
            ) +
            " W for the next prediction step.";

    }
    else if (
        difference < -1
    ) {

        analysis =
            "Recent telemetry indicates a possible reduction in solar power output. The forecast suggests approximately " +
            round(
                forecastP,
                2
            ) +
            " W for the next prediction step.";

    }
    else {

        analysis =
            "The predicted solar output is relatively stable compared with the latest measured power. Forecast power is approximately " +
            round(
                forecastP,
                2
            ) +
            " W.";

    }


    document.getElementById(
        "forecastAnalysis"
    ).textContent =
        analysis;


    updateForecastChart(
        rows,
        forecastP
    );

}


/* ============================================================
   FORECAST CHART
============================================================ */

function updateForecastChart(
    rows,
    forecastPower
) {

    if (!rows.length) return;


    const recent =
        rows.slice(
            -20
        );


    const labels =
        recent.map(
            r =>
                formatTime(
                    r.created_at
                )
        );


    const actual =
        recent.map(
            r =>
                safeNumber(
                    r.power
                )
        );


    const forecast =
        new Array(
            actual.length
        ).fill(null);


    if (forecast.length) {

        forecast[
            forecast.length - 1
        ] =
            forecastPower;

    }


    labels.push(
        "Next"
    );


    actual.push(
        null
    );


    forecast.push(
        forecastPower
    );


    if (!forecastPowerChart) return;


    forecastPowerChart.data.labels =
        labels;


    forecastPowerChart.data.datasets[0].data =
        actual;


    forecastPowerChart.data.datasets[1].data =
        forecast;


    forecastPowerChart.update(
        "none"
    );

}


/* ============================================================
   MONTHLY FORECAST
============================================================ */

function generateMonthlyForecast(
    currentPower
) {

    const factors = [

        0.85,
        0.88,
        1.00,
        1.05,
        1.08,
        0.95,
        0.82,
        0.80,
        0.88,
        1.00,
        1.02,
        0.90

    ];


    const baseDailyKWh =
        (
            Math.max(
                0,
                currentPower
            ) *
            5
        ) /
        1000;


    return factors.map(
        factor =>
            round(
                baseDailyKWh *
                factor *
                30,
                3
            )
    );

}


function updateMonthlyForecast(
    currentPower
) {

    if (!monthlyForecastChart) return;


    const values =
        generateMonthlyForecast(
            currentPower
        );


    monthlyForecastChart
        .data
        .datasets[0]
        .data =
            values;


    monthlyForecastChart.update(
        "none"
    );

}


/* ============================================================
   TELEMETRY TABLE
============================================================ */

function updateTelemetry(rows) {

    const tbody =
        document.getElementById(
            "telemetryBody"
        );


    tbody.innerHTML = "";


    if (!rows.length) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="9"
                    style="
                    text-align:center;
                    color:var(--muted);
                    padding:25px;
                    "
                >
                    No telemetry records found.

                </td>

            </tr>

        `;

        return;

    }


    const latestRows =
        rows
            .slice()
            .sort(
                (a,b) =>
                    safeNumber(b.id) -
                    safeNumber(a.id)
            )
            .slice(
                0,
                20
            );


    latestRows.forEach(
        row => {

            const tr =
                document.createElement(
                    "tr"
                );


            const status =
                String(
                    row.status ||
                    "NORMAL"
                ).toUpperCase();


            let statusClass =
                "normal";


            if (
                status === "FAULT"
            ) {

                statusClass =
                    "fault";

            }
            else if (
                status === "IDLE"
            ) {

                statusClass =
                    "idle";

            }


            tr.innerHTML = `

                <td>
                    ${safeNumber(row.id)}
                </td>

                <td>
                    ${formatDateTime(row.created_at)}
                </td>

                <td>
                    ${round(row.voltage,3)} V
                </td>

                <td>
                    ${round(row.current,3)} A
                </td>

                <td>
                    ${round(row.temperature,2)} °C
                </td>

                <td>
                    ${round(row.power,3)} W
                </td>

                <td>

                    <span
                        class="table-status ${statusClass}"
                    >
                        ${escapeHTML(status)}
                    </span>

                </td>

                <td>
                    ${escapeHTML(
                        row.fault_type ||
                        "NOMINAL"
                    )}
                </td>

                <td>
                    ${round(
                        row.ai_confidence,
                        1
                    )}%
                </td>

            `;


            tbody.appendChild(
                tr
            );

        }
    );

}


/* ============================================================
   ESCAPE HTML
============================================================ */

function escapeHTML(value) {

    return String(
        value ?? ""
    )
    .replace(
        /&/g,
        "&amp;"
    )
    .replace(
        /</g,
        "&lt;"
    )
    .replace(
        />/g,
        "&gt;"
    )
    .replace(
        /"/g,
        "&quot;"
    )
    .replace(
        /'/g,
        "&#039;"
    );

}


/* ============================================================
   REFRESH CHART COLORS
============================================================ */

function refreshCharts() {

    if (voltageChart)
        voltageChart.destroy();


    if (currentChart)
        currentChart.destroy();


    if (temperatureChart)
        temperatureChart.destroy();


    if (powerEnergyChart)
        powerEnergyChart.destroy();


    if (forecastPowerChart)
        forecastPowerChart.destroy();


    if (monthlyForecastChart)
        monthlyForecastChart.destroy();


    initializeCharts();


    if (lastRows.length) {

        updateSensorCharts(
            lastRows
        );


        updatePowerEnergy(
            lastRows
        );


        updateForecast(
            lastRows
        );


        updateMonthlyForecast(
            lastRows[
                lastRows.length - 1
            ].power
        );

    }

}


/* ============================================================
   MAIN LOAD DATA
============================================================ */

async function loadData() {

    try {

        /*
        |--------------------------------------------------------------------------
        | Load all APIs.
        |--------------------------------------------------------------------------
        */

        const [

            historyResponse,

            latestResponse,

            summaryResponse

        ] =
            await Promise.all([

                fetchJSON(
                    API.history
                ),

                fetchJSON(
                    API.latest
                ),

                fetchJSON(
                    API.summary
                )

            ]);


        /*
        |--------------------------------------------------------------------------
        | API is reachable.
        |--------------------------------------------------------------------------
        */

        lastAPIOnline =
            true;


        /*
        |--------------------------------------------------------------------------
        | Validate history.
        |--------------------------------------------------------------------------
        */

        if (
            !historyResponse.success
        ) {

            throw new Error(
                "History API failed"
            );

        }


        /*
        |--------------------------------------------------------------------------
        | history.php may return newest first.
        | Sort chronologically for charts.
        |--------------------------------------------------------------------------
        */

        const rows =
            Array.isArray(
                historyResponse.data
            )
                ?
                historyResponse.data
                    .slice()
                    .sort(
                        (a,b) => {

                            const ta =
                                new Date(
                                    a.created_at
                                ).getTime();


                            const tb =
                                new Date(
                                    b.created_at
                                ).getTime();


                            return ta - tb;

                        }
                    )
                :
                [];


        /*
        |--------------------------------------------------------------------------
        | Latest record.
        |--------------------------------------------------------------------------
        */

        const latest =
            latestResponse &&
            latestResponse.success
                ?
                latestResponse.data
                :
                (
                    rows.length
                        ?
                        rows[
                            rows.length - 1
                        ]
                        :
                        null
                );


        /*
        |--------------------------------------------------------------------------
        | Summary.
        |--------------------------------------------------------------------------
        */

        const summary =
            summaryResponse &&
            summaryResponse.success
                ?
                summaryResponse.data
                :
                null;


        lastRows =
            rows;


        lastLatest =
            latest;


        lastSummary =
            summary;


        /*
        |--------------------------------------------------------------------------
        | Update API status.
        |--------------------------------------------------------------------------
        */

        const apiState =
            document.getElementById(
                "apiState"
            );


        apiState.textContent =
            "API ONLINE";


        /*
        |--------------------------------------------------------------------------
        | Update all dashboard sections.
        |--------------------------------------------------------------------------
        */

        updateSensorCharts(
            rows
        );


        updatePowerEnergy(
            rows
        );


        if (latest) {

            updateLiveMetrics(
                latest
            );


            updateAIStatus(
                latest
            );


            updateMonthlyForecast(
                safeNumber(
                    latest.power
                )
            );


            updateForecast(
                rows
            );

        }


        updateSummary(
            summary,
            rows
        );


        updateTelemetry(
            rows
        );


        /*
        |--------------------------------------------------------------------------
        | CRITICAL:
        |
        | This determines ESP32 status from the age of the latest reading.
        | API online != ESP32 online.
        |--------------------------------------------------------------------------
        */

        updateDeviceStatus(
            latest
        );


    }
    catch (error) {

        console.error(
            "GreenPulse API error:",
            error
        );


        lastAPIOnline =
            false;


        /*
        |--------------------------------------------------------------------------
        | API offline.
        |--------------------------------------------------------------------------
        */

        const apiState =
            document.getElementById(
                "apiState"
            );


        apiState.textContent =
            "API OFFLINE";


        const state =
            document.getElementById(
                "deviceState"
            );


        const dot =
            document.getElementById(
                "onlineDot"
            );


        state.textContent =
            "API OFFLINE";


        dot.classList.add(
            "offline"
        );


        const alert =
            document.getElementById(
                "alertBox"
            );


        const alertText =
            document.getElementById(
                "alertText"
            );


        const alertIcon =
            document.getElementById(
                "alertIcon"
            );


        alert.classList.remove(
            "medium",
            "fault"
        );


        alert.classList.add(
            "offline"
        );


        alertIcon.textContent =
            "⚠";


        alertText.textContent =
            "Unable to retrieve live telemetry. Check Apache, PHP APIs and Supabase connection.";

    }

}


/* ============================================================
   NAVIGATION
============================================================ */

function setupNavigation() {

    const links =
        document.querySelectorAll(
            ".nav a"
        );


    links.forEach(
        link => {

            link.addEventListener(
                "click",
                () => {

                    links.forEach(
                        l =>
                            l.classList.remove(
                                "active"
                            )
                    );


                    link.classList.add(
                        "active"
                    );


                    document
                        .getElementById(
                            "sidebar"
                        )
                        .classList.remove(
                            "open"
                        );

                }
            );

        }
    );

}


/* ============================================================
   INITIALIZATION
============================================================ */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        createParticles();

        loadTheme();

        initializeCharts();

        setupNavigation();

        loadData();


        /*
        |--------------------------------------------------------------------------
        | Refresh every 3 seconds.
        |--------------------------------------------------------------------------
        |
        | ESP32 does NOT need to stay connected to browser.
        |
        | Correct architecture:
        |
        | ESP32
        |   ↓
        | Supabase / insert API
        |   ↓
        | PostgreSQL
        |   ↓
        | latest.php / history.php / summary.php
        |   ↓
        | Dashboard
        |
        |--------------------------------------------------------------------------
        */

        setInterval(
            loadData,
            3000
        );


        /*
        |--------------------------------------------------------------------------
        | Also check ESP32 freshness every second.
        |
        | This makes the dashboard change to OFFLINE approximately
        | when the timeout is crossed, instead of waiting for the
        | next complete API refresh.
        |--------------------------------------------------------------------------
        */

        setInterval(
            () => {

                if (!lastLatest) {

                    return;

                }


                updateDeviceStatus(
                    lastLatest
                );

            },
            1000
        );

    }
);

</script>

</body>
</html>
