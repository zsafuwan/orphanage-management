@extends('layouts.portal')

@section('title', 'Admin Dashboard')
@section('breadcrumb', 'Workspace | Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <!-- Chart.js CDN for interactive Adminator Monthly Performance Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
@endpush

@section('content')
<!-- Adminator Hero Section -->
<section class="adminator-hero portal-dashboard-hero">
    <div class="portal-hero-copy">
    <div class="hero-text">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
            <span class="hero-date" id="heroLiveDate" style="margin-bottom: 0;">
                <i class="far fa-calendar-alt"></i> {{ now()->format('l · F j · Y') }}
            </span>
            <span style="background: rgba(255, 44, 90, 0.1); color: var(--primary); font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; border: 1px solid rgba(255, 44, 90, 0.2); display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-crown"></i> Admin View
            </span>
        </div>
        <h1 class="hero-title">
            Welcome back, <span class="accent-name">Sister Grace</span> 👋
        </h1>
    </div>
    <div class="hero-actions">
        <a href="{{ route('register') }}" class="btn-outline btn-sm" title="Create a new admin account">
            <i class="fas fa-user-shield" style="color: var(--primary);"></i> Create Admin
        </a>
        <button class="btn-primary btn-sm" onclick="exportSanctuaryReport()">
            <i class="fas fa-download"></i> Download Report
        </button>
    </div>
    </div>
    @include('portal.partials.workspace-art')
</section>

@include('portal.partials.care-shortcuts')

<!-- Adminator 4-Card KPI Metrics Grid -->
<section class="kpi-grid" aria-label="Key orphanage metrics">
    <!-- Metric 1: Total Visits / Children In Sanctuary (Success Theme) -->
    <article class="kpi-card c-success">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon success">
                    <i class="fas fa-child"></i>
                </div>
                <div class="kpi-label">Children in Care</div>
            </div>
            <span class="kpi-pill up">
                <i class="fas fa-arrow-up"></i> +12%
            </span>
        </div>
        <div class="kpi-value">4 Children</div>
        <div class="kpi-compare">
            <i class="fas fa-arrow-up" style="color: #10b981;"></i> <strong>100%</strong> enrolled in formal schooling <span class="sep">·</span> term 2
        </div>
    </article>


    <!-- Metric 3: Unique Visitors / Welfare Aid Pending (Purple Theme) -->
    <article class="kpi-card c-purple">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon purple">
                    <i class="fas fa-file-medical-alt"></i>
                </div>
                <div class="kpi-label">Welfare Requests</div>
            </div>
            <span class="kpi-pill flat">
                <i class="fas fa-clock"></i> 1 Review
            </span>
        </div>
        <div class="kpi-value">1 Ticket</div>
        <div class="kpi-compare">
            <i class="fas fa-check" style="color: #8b5cf6;"></i> WR-2026-042 surgery approved <span class="sep">·</span> in fulfillment
        </div>
    </article>

    <!-- Metric 4: Bounce Rate / Inflows (Primary Blue Theme) -->
    <article class="kpi-card c-primary">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon primary">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="kpi-label">Donations Received</div>
            </div>
            <span class="kpi-pill info">
                <i class="fas fa-heart"></i> +28%
            </span>
        </div>
        <div class="kpi-value">$10,280 USD</div>
        <div class="kpi-compare">
            <i class="fas fa-check-circle" style="color: #3b82f6;"></i> matching approved children budgets <span class="sep">·</span> 100% audited
        </div>
    </article>
</section>

<!-- 5 Integrated Subsystems Quick Bar (Adminator Quick Launcher) -->
<div style="margin-bottom: 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b;">
            <i class="fas fa-th-large"></i> Quick Links
        </span>
        <span class="badge badge-success">4 Sections</span>
    </div>

    <div class="subsystems-admin-grid">
        <a href="{{ route('child-welfare') }}" class="subsystem-admin-card s-welfare">
            <div>
                <div class="s-title">Child Care & Education</div>
            </div>
            <div class="s-meta"><i class="fas fa-user-graduate"></i> 4 Active Profiles</div>
        </a>


        <a href="{{ route('welfare-requests') }}" class="subsystem-admin-card s-requests">
            <div>
                <div class="s-title">Welfare Requests</div>
            </div>
            <div class="s-meta"><i class="fas fa-clipboard-check"></i> 1 Pending Review</div>
        </a>

        <a href="{{ route('staff-volunteers') }}" class="subsystem-admin-card s-staff">
            <div>
                <div class="s-title">Staff & Volunteers</div>
            </div>
            <div class="s-meta"><i class="fas fa-user-clock"></i> 5 Staff & Volunteers</div>
        </a>

        <a href="{{ route('donations') }}" class="subsystem-admin-card s-donations">
            <div>
                <div class="s-title">Donations & Funds</div>
            </div>
            <div class="s-meta"><i class="fas fa-donate"></i> $10.2k Inflows</div>
        </a>
    </div>
</div>

<!-- Main Adminator Grid (Row 1: Regional/Subsystem Analytics + Performance Chart) -->
<div class="adminator-grid">
    <!-- Card 1: Subsystem Resource Distribution (Adminator "Site Visits / Geography" with Progress Bars & Radials) -->
    <section class="col-6 admin-card">
        <div class="admin-card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Operational Status</span>
                <h2 class="card-title">Staff Coverage</h2>
            </div>
            <a class="card-action-link" href="{{ route('staff-volunteers') }}">View Staff <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="sv-regions">
            <!-- Region 4: Caregiver Duty Staffing -->
            <div class="sv-region">
                <div class="sv-region-row">
                    <span class="sv-region-name">
                        <span class="sv-marker" style="background: #8b5cf6;"></span> Daily Caregiver Shift Coverage
                    </span>
                    <span class="sv-region-val">24/7 Rostered <span class="sv-region-pct">100% Covered</span></span>
                </div>
                <div class="sv-bar-track">
                    <div class="sv-bar-fill" style="width: 100%; background: #8b5cf6;"></div>
                </div>
            </div>
        </div>

        <!-- Adminator 3-Radial Circular SVG Gauges -->
        <div class="sv-radials-row">
            <div class="sv-radial-box">
                <div class="sv-radial-circle">
                    <svg viewBox="0 0 80 80">
                        <circle class="radial-track" cx="40" cy="40" r="32"/>
                        <circle class="radial-fill emerald" cx="40" cy="40" r="32" stroke-dasharray="201.06" stroke-dashoffset="30.15"/>
                    </svg>
                    <span class="radial-pct">85%</span>
                </div>
                <div class="radial-text">
                    <div class="radial-title">School Att.</div>
                    <div class="radial-caption">Term average</div>
                </div>
            </div>

            <div class="sv-radial-box">
                <div class="sv-radial-circle">
                    <svg viewBox="0 0 80 80">
                        <circle class="radial-track" cx="40" cy="40" r="32"/>
                        <circle class="radial-fill blue" cx="40" cy="40" r="32" stroke-dasharray="201.06" stroke-dashoffset="60.31"/>
                    </svg>
                    <span class="radial-pct">70%</span>
                </div>
                <div class="radial-text">
                    <div class="radial-title">Budget Used</div>
                    <div class="radial-caption">Q2 orphanage</div>
                </div>
            </div>

            <div class="sv-radial-box">
                <div class="sv-radial-circle">
                    <svg viewBox="0 0 80 80">
                        <circle class="radial-track" cx="40" cy="40" r="32"/>
                        <circle class="radial-fill amber" cx="40" cy="40" r="32" stroke-dasharray="201.06" stroke-dashoffset="20.10"/>
                    </svg>
                    <span class="radial-pct">90%</span>
                </div>
                <div class="radial-text">
                    <div class="radial-title">Clinic Check</div>
                    <div class="radial-caption">Welfare routine</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Card 2: Adminator Monthly Performance Chart.js -->
    <section class="col-6 admin-card">
        <div class="admin-card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Donations & Support Trends</span>
                <h2 class="card-title">Donations & Spending</h2>
            </div>
            <span style="font-size: 11.5px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 8px;">
                2026 Financial Year
            </span>
        </div>

        <div style="position: relative; height: 215px; width: 100%;">
            <canvas id="adminatorMonthlyChart"></canvas>
        </div>

        <!-- Adminator Monthly Footer Metric Cells -->
        <div class="monthly-strip">
            <div class="stat-cell">
                <div class="stat-cell-label">Donation Growth</div>
                <div class="stat-cell-value">
                    +48% <i class="fas fa-arrow-trend-up trend-up"></i>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-cell-label">April Donations</div>
                <div class="stat-cell-value">
                    $10.2K <i class="fas fa-arrow-trend-up trend-up"></i>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-cell-label">Support Payments</div>
                <div class="stat-cell-value">
                    $4.8K <i class="fas fa-arrow-trend-up trend-up"></i>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-cell-label">Remaining Funds</div>
                <div class="stat-cell-value">
                    $5.4K <i class="fas fa-arrow-trend-up trend-up"></i>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Main Adminator Grid (Row 2: Todo List Widget + Recent Requisitions Table) -->
<div class="adminator-grid">
    <!-- Card 3: Adminator Interactive Todo List Widget -->
    <section class="col-6 admin-card">
        <div class="admin-card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Priority Tasks</span>
                <h2 class="card-title">Tasks</h2>
            </div>
            <button class="btn-outline btn-sm" onclick="addNewTodoPrompt()" style="font-size: 11.5px; padding: 4px 10px;">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>

        <ul class="adminator-todo-list" id="dashboard_todo_list">
            <li class="adminator-todo-item">
                <div class="todo-left">
                    <input type="checkbox" class="todo-checkbox" id="td1" onchange="toggleTodo(this)">
                    <label for="td1" class="todo-text">Coordinate Maya Cruz Pediatric Dental surgery transport</label>
                </div>
                <span class="todo-pill urgent">URGENT</span>
            </li>
            <li class="adminator-todo-item">
                <div class="todo-left">
                    <input type="checkbox" class="todo-checkbox" id="td2" onchange="toggleTodo(this)">
                    <label for="td2" class="todo-text">Receive wholesale rice delivery (48 bags) from Harvest Millers</label>
                </div>
                <span class="todo-pill upcoming">TODAY</span>
            </li>
            <li class="adminator-todo-item">
                <div class="todo-left">
                    <input type="checkbox" class="todo-checkbox" id="td3" onchange="toggleTodo(this)">
                    <label for="td3" class="todo-text">Review Marcus Aurelius Term 2 Physics & Math report card</label>
                </div>
                <span class="todo-pill warn">TOMORROW</span>
            </li>
            <li class="adminator-todo-item">
                <div class="todo-left">
                    <input type="checkbox" class="todo-checkbox" id="td4" onchange="toggleTodo(this)">
                    <label for="td4" class="todo-text">Issue official tax deductible receipts for Elena Rostova ($5,000)</label>
                </div>
                <span class="todo-pill low">ROUTINE</span>
            </li>
            <li class="adminator-todo-item is-done">
                <div class="todo-left">
                    <input type="checkbox" class="todo-checkbox" id="td5" checked onchange="toggleTodo(this)">
                    <label for="td5" class="todo-text">Complete fire safety & CPR training roster verification</label>
                </div>
                <span class="todo-pill done">DONE</span>
            </li>
        </ul>
    </section>

    <!-- Card 4: Adminator Commerce/Transaction Table Style -->
    <section class="col-6 admin-card">
        <div class="admin-card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Audit & Dispatch</span>
                <h2 class="card-title">Recent Support Provided</h2>
            </div>
            <a class="card-action-link" href="{{ route('welfare-requests') }}">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="adminator-table">
                <thead>
                    <tr>
                        <th>Item / Purpose</th>
                        <th>Status</th>
                        <th>Child</th>
                        <th style="text-align: right;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Pediatric Dental Surgery</strong></td>
                        <td><span class="adminator-tag tag-new">Approved</span></td>
                        <td>Maya Cruz (10 yrs)</td>
                        <td style="text-align: right; font-weight: 700; color: #10b981;">$850</td>
                    </tr>
                    <tr>
                        <td><strong>High School Science Textbooks</strong></td>
                        <td><span class="adminator-tag tag-new">Dispatched</span></td>
                        <td>Marcus Aurelius (14 yrs)</td>
                        <td style="text-align: right; font-weight: 700; color: #0284c7;">$320</td>
                    </tr>
                    <tr>
                        <td><strong>Pediatric Allergy Syrup & Drops</strong></td>
                        <td><span class="adminator-tag tag-used">In Transit</span></td>
                        <td>Chloe Bennett (7 yrs)</td>
                        <td style="text-align: right; font-weight: 700; color: #b45309;">$145</td>
                    </tr>
                    <tr>
                        <td><strong>Prescription Spectacles</strong></td>
                        <td><span class="adminator-tag tag-unavail">Pending Dir</span></td>
                        <td>Lucas Vance (12 yrs)</td>
                        <td style="text-align: right; font-weight: 700; color: #b91c1c;">$210</td>
                    </tr>
                    <tr>
                        <td><strong>Primary School Uniform Set</strong></td>
                        <td><span class="adminator-tag tag-new">Fulfilled</span></td>
                        <td>Maya Cruz (10 yrs)</td>
                        <td style="text-align: right; font-weight: 700; color: #10b981;">$95</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Main Adminator Grid (Row 3: Adminator Weather Widget + Quick Chat Messenger) -->
<div class="adminator-grid">
    <!-- Card 5: Adminator Weather Forecast Widget -->
    <section class="col-6 admin-card">
        <div class="admin-card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Local Climate & Orphanage Yard</span>
                <h2 class="card-title">Orphanage Weather Forecast</h2>
            </div>
            <span style="font-size: 11px; font-weight: 700; color: #64748b;">
                <i class="fas fa-location-dot" style="color: var(--primary);"></i> HopeHaven Campus
            </span>
        </div>

        <div class="adminator-wx-hero">
            <div class="wx-temp-wrap">
                <div class="wx-icon-box">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <div>
                    <div class="wx-temp-number">74<sup>°F</sup></div>
                    <div class="wx-condition-desc">Partly <strong>Sunny</strong> · Mild outdoor play conditions</div>
                </div>
            </div>
            <div class="wx-location-date">
                <h5>{{ now()->format('l') }}</h5>
                <p>{{ now()->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- 7-Day Mini Forecast Strip -->
        <div class="wx-week-forecast">
            <div class="wx-day-cell is-today">
                <div class="wx-day-name">TODAY</div>
                <div class="wx-day-icon"><i class="fas fa-sun"></i></div>
                <div class="wx-day-temp">74°</div>
            </div>
            <div class="wx-day-cell">
                <div class="wx-day-name">FRI</div>
                <div class="wx-day-icon"><i class="fas fa-cloud-sun"></i></div>
                <div class="wx-day-temp">72°</div>
            </div>
            <div class="wx-day-cell">
                <div class="wx-day-name">SAT</div>
                <div class="wx-day-icon"><i class="fas fa-sun"></i></div>
                <div class="wx-day-temp">76°</div>
            </div>
            <div class="wx-day-cell">
                <div class="wx-day-name">SUN</div>
                <div class="wx-day-icon"><i class="fas fa-cloud"></i></div>
                <div class="wx-day-temp">70°</div>
            </div>
            <div class="wx-day-cell">
                <div class="wx-day-name">MON</div>
                <div class="wx-day-icon"><i class="fas fa-cloud-rain"></i></div>
                <div class="wx-day-temp">66°</div>
            </div>
            <div class="wx-day-cell">
                <div class="wx-day-name">TUE</div>
                <div class="wx-day-icon"><i class="fas fa-cloud-sun"></i></div>
                <div class="wx-day-temp">71°</div>
            </div>
            <div class="wx-day-cell">
                <div class="wx-day-name">WED</div>
                <div class="wx-day-icon"><i class="fas fa-sun"></i></div>
                <div class="wx-day-temp">75°</div>
            </div>
        </div>
    </section>

    <!-- Card 6: Adminator Quick Messenger Chat Widget -->
    <section class="col-6 admin-card">
        <div class="admin-card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Coordination Channel</span>
                <h2 class="card-title">Staff Chat</h2>
            </div>
            <span class="badge badge-success"><i class="fas fa-circle" style="font-size: 8px;"></i> 3 Online</span>
        </div>

        <div class="adminator-chat">
            <div class="chat-message-stream" id="chat_messages_stream">
                <div class="chat-bubble-row">
                    <div class="chat-avatar-pill">SG</div>
                    <div class="chat-bubble-stack">
                        <div class="chat-bubble">Good morning care team. Dental surgery for Maya is cleared by Dr. Evans.</div>
                        <div class="chat-timestamp">08:15 AM · Sister Grace</div>
                    </div>
                </div>

                <div class="chat-bubble-row is-me">
                    <div class="chat-avatar-pill me">ME</div>
                    <div class="chat-bubble-stack">
                        <div class="chat-bubble">Understood! Transport vehicle is fueled and Marcus completed his study session.</div>
                        <div class="chat-timestamp">08:22 AM · You</div>
                    </div>
                </div>

                <div class="chat-bubble-row">
                    <div class="chat-avatar-pill" style="background: #3b82f6;">DK</div>
                    <div class="chat-bubble-stack">
                        <div class="chat-bubble">Wholesale grain delivery has just arrived at the east gate bay.</div>
                        <div class="chat-timestamp">08:30 AM · David Kim</div>
                    </div>
                </div>
            </div>

            <form class="chat-input-bar" onsubmit="sendQuickChat(event)">
                <input type="text" id="chat_quick_input" placeholder="Type quick memo to on-duty staff…" autocomplete="off" required>
                <button type="submit" class="chat-send-btn" aria-label="Send Message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Chart.js matching Adminator 2026 aesthetics
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('adminatorMonthlyChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
                    datasets: [
                        {
                            label: 'Donations ($)',
                            data: [4200, 6800, 5100, 7400, 8900, 10280],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Direct Welfare Aid ($)',
                            data: [3100, 4500, 3900, 4100, 4300, 4850],
                            borderColor: '#10b981',
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#10b981'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: "'Poppins', sans-serif" },
                            bodyFont: { family: "'Poppins', sans-serif" },
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: { family: "'Poppins', sans-serif", size: 10 },
                                callback: function(value) { return '$' + value; }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { family: "'Poppins', sans-serif", size: 11 }
                            }
                        }
                    }
                }
            });
        }
    });

    // Todo List Toggle Functionality
    function toggleTodo(checkbox) {
        var item = $(checkbox).closest('.adminator-todo-item');
        if (checkbox.checked) {
            item.addClass('is-done');
            item.find('.todo-pill').removeClass('urgent upcoming warn low').addClass('done').text('DONE');
        } else {
            item.removeClass('is-done');
            item.find('.todo-pill').removeClass('done').addClass('low').text('ROUTINE');
        }
    }

    // Add new Task prompt simulation
    function addNewTodoPrompt() {
        var taskText = prompt("Enter a task:");
        if (taskText && taskText.trim() !== "") {
            var newId = "td_" + Date.now();
            var newItemHtml = `
                <li class="adminator-todo-item animated fadeIn">
                    <div class="todo-left">
                        <input type="checkbox" class="todo-checkbox" id="${newId}" onchange="toggleTodo(this)">
                        <label for="${newId}" class="todo-text">${taskText.trim()}</label>
                    </div>
                    <span class="todo-pill upcoming">NEW</span>
                </li>
            `;
            $('#dashboard_todo_list').prepend(newItemHtml);
        }
    }

    // Quick chat message send simulation
    function sendQuickChat(e) {
        e.preventDefault();
        var input = $('#chat_quick_input');
        var text = input.val().trim();
        if (!text) return;

        var now = new Date();
        var timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        var bubbleHtml = `
            <div class="chat-bubble-row is-me animated fadeInUp">
                <div class="chat-avatar-pill me">ME</div>
                <div class="chat-bubble-stack">
                    <div class="chat-bubble">${text}</div>
                    <div class="chat-timestamp">${timeStr} · You</div>
                </div>
            </div>
        `;

        $('#chat_messages_stream').append(bubbleHtml);
        input.val('');
        var stream = document.getElementById('chat_messages_stream');
        stream.scrollTop = stream.scrollHeight;
    }

    // Export report simulation
    function exportSanctuaryReport() {
        var toast = $("#portal_toast");
        toast.html('<i class="fas fa-file-excel" style="color:#10b981; font-size:16px;"></i> Generating orphanage 2026 Comprehensive Report (PDF/XLSX)...').css("display", "flex");
        setTimeout(function() {
            toast.html('<i class="fas fa-check-circle" style="color:#10b981; font-size:16px;"></i> Orphanage Executive Audit exported successfully.');
            setTimeout(function() { toast.fadeOut(400); }, 3000);
        }, 1200);
    }
</script>
@endpush
