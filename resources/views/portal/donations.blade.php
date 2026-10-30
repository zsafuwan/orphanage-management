@extends('layouts.portal')

@section('title', 'Executive Donations & Spending Overview')
@section('breadcrumb', 'Workspace | Admin Donations & Cash Flow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/donations.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/donations.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <!-- Chart.js CDN for Multi-Orphanage Cash Flow Benchmark Visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/embedded/portal-donations-1.css') }}?v={{ substr(hash_file('sha256', public_path('css/embedded/portal-donations-1.css')), 0, 12) }}">
@endpush

@section('content')

<!-- Subsystem Admin Header -->
<div class="admin-donations-hero">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div style="max-width: 720px;">
            
            <h1 style="font-size: 25px; font-weight: 800; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                Donations Across Orphanages
            </h1>
            <p style="font-size: 13.5px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Compare donations and funds across orphanages.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        </div>
    </div>
</div>

<!-- Clearance Level Ribbon -->
<div class="rbac-banner" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-shield-alt" style="color: #0f766e; font-size: 18px;"></i>
        <div>
            <span style="font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase;">Admin Access:</span>
            <span style="font-size: 12px; color: var(--dark-muted); margin-left: 4px;">Viewing As: <strong style="color: #0f766e;">Sister Grace (Administrator)</strong> · Regional Multi-Orphanage Solvency Feed Active</span>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
        <span style="font-size: 12px; color: #64748b;">Live Treasury Feed · 6 Orphanages Reconciled</span>
    </div>
</div>

<!-- Top Executive KPI Metrics -->
<div class="metrics-grid" style="margin-bottom: 24px;">
    <!-- 1. Regional Cash Flow Leader -->
    <div class="metric-card" style="border-left: 4px solid #0f766e; background: linear-gradient(180deg, #ffffff 0%, #f0fdfa 100%);">
        <div class="metric-header">
            <span class="metric-label" style="font-weight: 800; color: #0f766e;">Top Regional Inflow</span>
            <div class="metric-icon" style="background: rgba(15, 118, 110, 0.12); color: #0f766e;">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #0f766e; font-size: 26px; font-weight: 800;" id="kpi_top_inflow_val">$145,200.00</div>
        <div class="metric-sub" style="color: #0f766e; font-weight: 600;">
            <i class="fas fa-home"></i> HopeHaven Orphanage (Rank #1 Regional Leader)
        </div>
    </div>

    <!-- 2. Combined Giving YTD -->
    <div class="metric-card" style="border-left: 4px solid #10b981;">
        <div class="metric-header">
            <span class="metric-label" style="font-weight: 800; color: #065f46;">HopeHaven Net Cash Flow</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #10b981; font-size: 26px; font-weight: 800;" id="kpi_net_cashflow_val">+$46,800.00</div>
        <div class="metric-sub" style="color: #15803d;">
            Inflow ($145.2k) - Operating Expenses ($98.4k)
        </div>
    </div>

    <!-- 3. Sector Average Solvency Reserve -->
    <div class="metric-card" style="border-left: 4px solid #3b82f6;">
        <div class="metric-header">
            <span class="metric-label">Sector Solvency Reserve</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-shield-virus"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #1d4ed8; font-size: 26px;">7.2 Months</div>
        <div class="metric-sub">Average liquidity cushion across 6 sanctuaries</div>
    </div>

    <!-- 4. Active Regional Benefactors -->
    <div class="metric-card" style="border-left: 4px solid #8b5cf6;">
        <div class="metric-header">
            <span class="metric-label">Regional Donor Network</span>
            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                <i class="fas fa-hands-helping"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #7c3aed; font-size: 26px;">124 Patrons</div>
        <div class="metric-sub">Foundations, CSR & Faith Communities</div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="tabs-container">
    <button class="tab-btn active" data-tab="tab_cashflow_comparison">
        <i class="fas fa-chart-bar"></i> Top Cash Flow (Different Orphanages)
    </button>
    <button class="tab-btn" data-tab="tab_contributions">
        <i class="fas fa-receipt"></i> HopeHaven Ledger (5)
    </button>
    <button class="tab-btn" data-tab="tab_donors">
        <i class="fas fa-users"></i> Donor Registry (4)
    </button>
    <button class="tab-btn" data-tab="tab_allocations">
        <i class="fas fa-hands-helping"></i> Allocation to Orphanage Needs
    </button>
    <button class="tab-btn" data-tab="tab_summaries">
        <i class="fas fa-chart-pie"></i> Financial Summaries & Reports
    </button>
</div>

<!-- ==========================================================================
     TAB 1: TOP CASH FLOW OF DIFFERENT ORPHANAGES (CORE USER REQUIREMENT)
     ========================================================================== -->
<div id="tab_cashflow_comparison" class="tab-pane" style="display: block;">
    
    <!-- Cash Flow Benchmark Graph Card -->
    <div class="portal-card" style="padding: 26px; margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 6px; background: #e0f2fe; color: #0369a1; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; margin-bottom: 6px; text-transform: uppercase;">
                    <i class="fas fa-chart-area"></i> Liquidity & Treasury Graph
                </div>
                <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">
                    Top Cash Flow Comparison Across Different Orphanages
                </h3>
                <p style="font-size: 13px; color: #64748b; margin: 0;">
                    Visualizing Total Monetary Inflow (Donations & Grants), Spending (Care, Food & Facilities), and Net Cash Surplus for 6 regional child care centers.
                </p>
            </div>

            <!-- Timeframe & Visualization Controls -->
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                <div style="display: flex; gap: 6px; align-items: center;">
                    <span style="font-size: 11.5px; font-weight: 700; color: #64748b; margin-right: 4px;">Timeframe:</span>
                    <button class="chart-timeframe-btn active" onclick="switchCashFlowTimeframe('30d', this)">
                        Past 30 Days (Oct 2026)
                    </button>
                    <button class="chart-timeframe-btn" onclick="switchCashFlowTimeframe('q3', this)">
                        Q3 2026 (Quarterly)
                    </button>
                    <button class="chart-timeframe-btn" onclick="switchCashFlowTimeframe('ytd', this)">
                        Financial 2026 (YTD)
                    </button>
                </div>

                <div style="display: flex; gap: 6px; align-items: center;">
                    <span style="font-size: 11.5px; font-weight: 700; color: #64748b; margin-right: 4px;">Display Mode:</span>
                    <button class="chart-view-btn active" id="btn_mode_grouped" onclick="switchChartViewMode('grouped')">
                        <i class="fas fa-chart-bar"></i> Inflow vs Outflow vs Net
                    </button>
                    <button class="chart-view-btn" id="btn_mode_net" onclick="switchChartViewMode('net')">
                        <i class="fas fa-sort-amount-down"></i> Net Surplus Ranked
                    </button>
                    <button class="chart-view-btn" id="btn_mode_line" onclick="switchChartViewMode('line')">
                        <i class="fas fa-chart-line"></i> Cash Flow Trajectory
                    </button>
                </div>
            </div>
        </div>

        <!-- Canvas Container -->
        <div style="position: relative; height: 380px; width: 100%; background: #fafbfc; border-radius: 8px; padding: 12px; border: 1px solid #f1f5f9;">
            <canvas id="orphanageCashFlowChart"></canvas>
        </div>

        <!-- Quick Graph Legend & Solvency Snapshot -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
            <div style="display: flex; gap: 18px; align-items: center; flex-wrap: wrap;">
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 12px; height: 12px; background: #0f766e; border-radius: 3px; display: inline-block;"></span>
                    <strong style="color: #0f172a;">Cash Inflow (Donations/Grants)</strong>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 12px; height: 12px; background: #f43f5e; border-radius: 3px; display: inline-block;"></span>
                    <strong style="color: #0f172a;">Care Spending</strong>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 12px; height: 12px; background: #10b981; border-radius: 3px; display: inline-block;"></span>
                    <strong style="color: #0f172a;">Net Cash Flow Surplus</strong>
                </span>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <span class="badge" style="background: #e0f2fe; color: #0284c7; font-weight: 700;">
                    <i class="fas fa-check-circle"></i> ISO-26000 Non-Profit Standard Compliant
                </span>
                <button class="btn-outline btn-sm" onclick="showPortalToast('Multi-orphanage liquidity dataset exported as CSV audit report!', true)">
                    <i class="fas fa-download"></i> Export Graph Data
                </button>
            </div>
        </div>
    </div>

    <!-- Ranked Multi-Orphanage Quick Summary Cards -->
    <div style="margin-bottom: 24px;">
        <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-layer-group" style="color: #0f766e;"></i>
            Regional Orphanage Solvency & Cash Inflow Leaderboard
        </h4>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
            
            <!-- 1. HopeHaven Orphanage (Our Facility) -->
            <div class="orphanage-rank-card is-our-home">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="rank-number-badge rank-gold">#1</span>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0; color: #0f766e;">HopeHaven Orphanage</h4>
                            <span style="font-size: 11px; color: #0f766e; font-weight: 700;">★ Our Facility (Selangor Division)</span>
                        </div>
                    </div>
                    <span class="benchmark-pill positive">
                        <i class="fas fa-arrow-up"></i> +$46.8k Net
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: rgba(15, 118, 110, 0.05); padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div>Inflow: <strong style="color: #0f766e;">$145,200</strong></div>
                    <div>Outflow: <strong style="color: #e11d48;">$98,400</strong></div>
                    <div>Solvency: <strong>8.4 Months</strong></div>
                    <div>Capacity: <strong>48 Children</strong></div>
                </div>

                <div style="font-size: 11.5px; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                    <span>Primary Donor: <em>Meridian Heritage Foundation</em></span>
                    <span style="color: #10b981; font-weight: 700;">Highest Inflow</span>
                </div>
            </div>

            <!-- 2. St. Jude Children's Home -->
            <div class="orphanage-rank-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="rank-number-badge rank-silver">#2</span>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0;">St. Jude Children's Home</h4>
                            <span style="font-size: 11px; color: #64748b;">Perak Diocesan Trust</span>
                        </div>
                    </div>
                    <span class="benchmark-pill positive">
                        <i class="fas fa-arrow-up"></i> +$36.4k Net
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div>Inflow: <strong style="color: #0f766e;">$128,500</strong></div>
                    <div>Outflow: <strong style="color: #e11d48;">$92,100</strong></div>
                    <div>Solvency: <strong>9.1 Months</strong></div>
                    <div>Capacity: <strong>42 Children</strong></div>
                </div>

                <div style="font-size: 11.5px; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                    <span>Primary Donor: <em>Parish Altar Society & Grants</em></span>
                    <span style="color: #0284c7; font-weight: 700;">Strongest Reserve</span>
                </div>
            </div>

            <!-- 3. Grace Orphan Care Centre -->
            <div class="orphanage-rank-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="rank-number-badge rank-bronze">#3</span>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0;">Grace Orphan Care Centre</h4>
                            <span style="font-size: 11px; color: #64748b;">Penang Island Welfare</span>
                        </div>
                    </div>
                    <span class="benchmark-pill positive">
                        <i class="fas fa-arrow-up"></i> +$27.7k Net
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div>Inflow: <strong style="color: #0f766e;">$112,000</strong></div>
                    <div>Outflow: <strong style="color: #e11d48;">$84,300</strong></div>
                    <div>Solvency: <strong>6.8 Months</strong></div>
                    <div>Capacity: <strong>36 Children</strong></div>
                </div>

                <div style="font-size: 11.5px; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                    <span>Primary Donor: <em>Corporate CSR Consortium</em></span>
                    <span style="color: #64748b; font-weight: 600;">Sustainable</span>
                </div>
            </div>

            <!-- 4. Darul Aitam Care Foundation -->
            <div class="orphanage-rank-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="rank-number-badge rank-standard">#4</span>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0;">Darul Aitam Care Foundation</h4>
                            <span style="font-size: 11px; color: #64748b;">Johor Community Board</span>
                        </div>
                    </div>
                    <span class="benchmark-pill positive">
                        <i class="fas fa-arrow-up"></i> +$21.6k Net
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div>Inflow: <strong style="color: #0f766e;">$96,400</strong></div>
                    <div>Outflow: <strong style="color: #e11d48;">$74,800</strong></div>
                    <div>Solvency: <strong>5.9 Months</strong></div>
                    <div>Capacity: <strong>32 Children</strong></div>
                </div>

                <div style="font-size: 11.5px; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                    <span>Primary Donor: <em>Waqf & Individual Donors</em></span>
                    <span style="color: #64748b; font-weight: 600;">Positive Trend</span>
                </div>
            </div>

            <!-- 5. Sunshine Haven Children's Village -->
            <div class="orphanage-rank-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="rank-number-badge rank-standard">#5</span>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0;">Sunshine Haven Village</h4>
                            <span style="font-size: 11px; color: #64748b;">Melaka Foster Network</span>
                        </div>
                    </div>
                    <span class="benchmark-pill positive">
                        <i class="fas fa-arrow-up"></i> +$16.5k Net
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div>Inflow: <strong style="color: #0f766e;">$84,700</strong></div>
                    <div>Outflow: <strong style="color: #e11d48;">$68,200</strong></div>
                    <div>Solvency: <strong>5.2 Months</strong></div>
                    <div>Capacity: <strong>28 Children</strong></div>
                </div>

                <div style="font-size: 11.5px; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                    <span>Primary Donor: <em>Rotary International Club</em></span>
                    <span style="color: #64748b; font-weight: 600;">Stable</span>
                </div>
            </div>

            <!-- 6. Peace Memorial Orphanage -->
            <div class="orphanage-rank-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="rank-number-badge rank-standard">#6</span>
                        <div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0;">Peace Memorial Orphanage</h4>
                            <span style="font-size: 11px; color: #64748b;">Pahang Valley Haven</span>
                        </div>
                    </div>
                    <span class="benchmark-pill positive">
                        <i class="fas fa-arrow-up"></i> +$10.8k Net
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <div>Inflow: <strong style="color: #0f766e;">$72,300</strong></div>
                    <div>Outflow: <strong style="color: #e11d48;">$61,500</strong></div>
                    <div>Solvency: <strong>4.6 Months</strong></div>
                    <div>Capacity: <strong>24 Children</strong></div>
                </div>

                <div style="font-size: 11.5px; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                    <span>Primary Donor: <em>Family Memorial Trusts</em></span>
                    <span style="color: #f59e0b; font-weight: 600;">Budget Capped</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Comparative Table of Orphanages -->
    <div class="portal-card" style="margin-bottom: 24px;">
        <div style="padding: 20px 24px 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h4 style="font-size: 16px; font-weight: 800; margin: 0; color: #0f172a;">
                    Orphanage Funds
                </h4>
                <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">
                    Official audit data reconciled against state welfare and non-profit regulatory submissions.
                </p>
            </div>
            <div>
                <span class="badge badge-success" style="font-size: 11.5px; padding: 4px 10px;">
                    <i class="fas fa-check-double"></i> 100% Tax Deductible Compliance
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Rank</th>
                        <th>Orphanage & Jurisdiction</th>
                        <th>Cash Inflow (Receipts)</th>
                        <th>Spending</th>
                        <th>Net Cash Flow</th>
                        <th>Solvency Reserve</th>
                        <th>Operating Margin</th>
                        <th class="action-column" style="text-align: right;">Drilldown</th>
                    </tr>
                </thead>
                <tbody id="orphanage_cashflow_tbody">
                    <!-- Row 1: HopeHaven -->
                    <tr style="background: rgba(15, 118, 110, 0.04); font-weight: 600;">
                        <td><span class="rank-number-badge rank-gold">#1</span></td>
                        <td>
                            <strong style="color: #0f766e;">HopeHaven Orphanage</strong>
                            <div style="font-size: 11px; color: #64748b;">Selangor · 48 Residents · Non-Profit ID #ORP-MY-104</div>
                        </td>
                        <td><strong style="color: #0f766e; font-size: 14px;">$145,200</strong></td>
                        <td><span style="color: #e11d48;">$98,400</span></td>
                        <td><span class="benchmark-pill positive" style="font-size: 12px;">+$46,800</span></td>
                        <td><strong>8.4 mos</strong></td>
                        <td><span class="badge badge-success">32.2% Surplus</span></td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="showPortalToast('HopeHaven Detailed Q3 Audit Ledger opened.', true)">
                                <i class="fas fa-file-invoice-dollar"></i> Audit
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2: St. Jude -->
                    <tr>
                        <td><span class="rank-number-badge rank-silver">#2</span></td>
                        <td>
                            <strong>St. Jude Children's Home</strong>
                            <div style="font-size: 11px; color: #64748b;">Perak · 42 Residents · Non-Profit ID #ORP-MY-109</div>
                        </td>
                        <td><strong style="color: #0f766e; font-size: 14px;">$128,500</strong></td>
                        <td><span style="color: #e11d48;">$92,100</span></td>
                        <td><span class="benchmark-pill positive" style="font-size: 12px;">+$36,400</span></td>
                        <td><strong>9.1 mos</strong></td>
                        <td><span class="badge badge-success">28.3% Surplus</span></td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="showPortalToast('St. Jude Children Home audit PDF loaded.', true)">
                                <i class="fas fa-file-invoice-dollar"></i> Audit
                            </button>
                        </td>
                    </tr>

                    <!-- Row 3: Grace Orphan Care -->
                    <tr>
                        <td><span class="rank-number-badge rank-bronze">#3</span></td>
                        <td>
                            <strong>Grace Orphan Care Centre</strong>
                            <div style="font-size: 11px; color: #64748b;">Penang · 36 Residents · Non-Profit ID #ORP-MY-115</div>
                        </td>
                        <td><strong style="color: #0f766e; font-size: 14px;">$112,000</strong></td>
                        <td><span style="color: #e11d48;">$84,300</span></td>
                        <td><span class="benchmark-pill positive" style="font-size: 12px;">+$27,700</span></td>
                        <td><strong>6.8 mos</strong></td>
                        <td><span class="badge badge-success">24.7% Surplus</span></td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="showPortalToast('Grace Care Centre financial records retrieved.', true)">
                                <i class="fas fa-file-invoice-dollar"></i> Audit
                            </button>
                        </td>
                    </tr>

                    <!-- Row 4: Darul Aitam -->
                    <tr>
                        <td><span class="rank-number-badge rank-standard">#4</span></td>
                        <td>
                            <strong>Darul Aitam Care Foundation</strong>
                            <div style="font-size: 11px; color: #64748b;">Johor · 32 Residents · Non-Profit ID #ORP-MY-122</div>
                        </td>
                        <td><strong style="color: #0f766e; font-size: 14px;">$96,400</strong></td>
                        <td><span style="color: #e11d48;">$74,800</span></td>
                        <td><span class="benchmark-pill positive" style="font-size: 12px;">+$21,600</span></td>
                        <td><strong>5.9 mos</strong></td>
                        <td><span class="badge badge-info">22.4% Surplus</span></td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="showPortalToast('Darul Aitam Care Foundation audit verified.', true)">
                                <i class="fas fa-file-invoice-dollar"></i> Audit
                            </button>
                        </td>
                    </tr>

                    <!-- Row 5: Sunshine Haven -->
                    <tr>
                        <td><span class="rank-number-badge rank-standard">#5</span></td>
                        <td>
                            <strong>Sunshine Haven Village</strong>
                            <div style="font-size: 11px; color: #64748b;">Melaka · 28 Residents · Non-Profit ID #ORP-MY-134</div>
                        </td>
                        <td><strong style="color: #0f766e; font-size: 14px;">$84,700</strong></td>
                        <td><span style="color: #e11d48;">$68,200</span></td>
                        <td><span class="benchmark-pill positive" style="font-size: 12px;">+$16,500</span></td>
                        <td><strong>5.2 mos</strong></td>
                        <td><span class="badge badge-info">19.5% Surplus</span></td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="showPortalToast('Sunshine Haven Village records fetched.', true)">
                                <i class="fas fa-file-invoice-dollar"></i> Audit
                            </button>
                        </td>
                    </tr>

                    <!-- Row 6: Peace Memorial -->
                    <tr>
                        <td><span class="rank-number-badge rank-standard">#6</span></td>
                        <td>
                            <strong>Peace Memorial Orphanage & Ward</strong>
                            <div style="font-size: 11px; color: #64748b;">Pahang · 24 Residents · Non-Profit ID #ORP-MY-148</div>
                        </td>
                        <td><strong style="color: #0f766e; font-size: 14px;">$72,300</strong></td>
                        <td><span style="color: #e11d48;">$61,500</span></td>
                        <td><span class="benchmark-pill positive" style="font-size: 12px;">+$10,800</span></td>
                        <td><strong>4.6 mos</strong></td>
                        <td><span class="badge badge-secondary">14.9% Surplus</span></td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="showPortalToast('Peace Memorial Orphanage ledger opened.', true)">
                                <i class="fas fa-file-invoice-dollar"></i> Audit
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Secondary Analytics Row: Inflow Channels & Solvency Policies -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="portal-card" style="padding: 24px;">
            <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                Donation Sources
            </h4>
            <p style="font-size: 12px; color: #64748b; margin-bottom: 20px;">
                Where cash flow originates across the 6 regional orphanages.
            </p>

            <div style="display: flex; flex-direction: column; gap: 16px; font-size: 12.5px;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Corporate CSR & Foundations</span>
                        <strong>42% ($268,400)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 42%; height: 100%; background: #0f766e;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Individual & Family Patrons</span>
                        <strong>28% ($178,900)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 28%; height: 100%; background: #10b981;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Faith & Religious Alliances</span>
                        <strong>18% ($115,000)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 18%; height: 100%; background: #3b82f6;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Government Child Subsidy Grants</span>
                        <strong>12% ($76,800)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 12%; height: 100%; background: #f59e0b;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="portal-card" style="padding: 24px;">
            <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                Fund Guidelines
            </h4>
            <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
                orphanage regulatory board policies for HopeHaven Administration.
            </p>

            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 12.5px;">
                <div style="display: flex; gap: 10px; align-items: flex-start; padding: 10px; background: #f8fafc; border-radius: 6px;">
                    <i class="fas fa-check-circle" style="color: #10b981; margin-top: 3px;"></i>
                    <div>
                        <strong>Minimum 6-Month Operating Reserve:</strong>
                        <div style="font-size: 11.5px; color: #64748b;">HopeHaven currently holds 8.4 months of runway, safely exceeding the mandatory statutory reserve threshold.</div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; align-items: flex-start; padding: 10px; background: #f8fafc; border-radius: 6px;">
                    <i class="fas fa-lock" style="color: #0f766e; margin-top: 3px;"></i>
                    <div>
                        <strong>Restricted vs Unrestricted Funds:</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Medical and Higher Education endowments are legally locked to designated child welfare categories.</div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; align-items: flex-start; padding: 10px; background: #f8fafc; border-radius: 6px;">
                    <i class="fas fa-hand-holding-usd" style="color: #3b82f6; margin-top: 3px;"></i>
                    <div>
                        <strong>Staff Ward Petty Cash Reconciliation:</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Staff front desk records cash receipts and disbursements; automatic daily sync to Sister Grace's general ledger.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ==========================================================================
     TAB 2: CONTRIBUTION LEDGER (HOPEHAVEN)
     ========================================================================== -->
<div id="tab_contributions" class="tab-pane" style="display: none;">
    <!-- Search & Filter Bar -->
    <div class="filter-bar" style="margin-bottom: 16px;">
        <div class="search-box-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" class="portal-input search-input" data-search-target=".searchable-donation" placeholder="Search by receipt #, donor name, or allocated need...">
        </div>
        <div style="display: flex; gap: 10px;">
            <select class="portal-select category-filter" data-filter-target=".searchable-donation">
                <option value="All">All Contribution Types</option>
                <option value="Monetary">Monetary (Cash / Wire)</option>
                <option value="In-Kind">In-Kind (Material Goods)</option>
            </select>
        </div>
    </div>

    <div class="portal-card">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Donor Entity</th>
                        <th>Type</th>
                        <th>Recognized Amount / Goods</th>
                        <th>Orphanage Need Allocation</th>
                        <th>Date</th>
                        <th class="action-column" style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody id="donations_tbody">
                    <tr class="searchable-donation" data-category="Monetary">
                        <td style="font-family: monospace; font-weight: 700;">RCP-2026-0881</td>
                        <td>
                            <strong>The Meridian Heritage Foundation</strong>
                            <div style="font-size: 11px; color: #64748b;">Foundation Patron</div>
                        </td>
                        <td><span class="badge badge-success">Monetary</span></td>
                        <td>
                            <strong style="color: #10b981; font-size: 15px;">$5,000 USD</strong>
                            <div style="font-size: 11px; color: #64748b;">Bank Wire Transfer</div>
                        </td>
                        <td>
                            <strong>Higher Education & STEM Fund</strong>
                            <div style="margin-top: 2px;"><span class="badge badge-info">Fully Allocated</span></div>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-15</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_view_receipt')">
                                <i class="fas fa-print"></i> E-Receipt
                            </button>
                        </td>
                    </tr>

                    <tr class="searchable-donation" data-category="In-Kind">
                        <td style="font-family: monospace; font-weight: 700;">RCP-2026-0882</td>
                        <td>
                            <strong>Evergreen Food Staples Ltd (CSR)</strong>
                            <div style="font-size: 11px; color: #64748b;">Corporate Sponsor</div>
                        </td>
                        <td><span class="badge badge-warning">In-Kind</span></td>
                        <td>
                            <strong>30x Fortified Rice Bags (25kg)</strong>
                            <div style="font-size: 11px; color: #64748b;">Est. Value: $855 USD</div>
                        </td>
                        <td>
                            <strong>Central Pantry & Kitchen</strong>
                            <div style="margin-top: 2px;"><span class="badge badge-info">Fully Allocated</span></div>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-08</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_view_receipt')">
                                <i class="fas fa-print"></i> E-Receipt
                            </button>
                        </td>
                    </tr>

                    <tr class="searchable-donation" data-category="Monetary">
                        <td style="font-family: monospace; font-weight: 700;">RCP-2026-0883</td>
                        <td>
                            <strong>Dr. Gregory & Eleanor Vance</strong>
                            <div style="font-size: 11px; color: #64748b;">Individual Donor</div>
                        </td>
                        <td><span class="badge badge-success">Monetary</span></td>
                        <td>
                            <strong style="color: #10b981; font-size: 15px;">$1,500 USD</strong>
                            <div style="font-size: 11px; color: #64748b;">Credit Card</div>
                        </td>
                        <td>
                            <strong>Emergency Pediatric Dental Reserve</strong>
                            <div style="margin-top: 2px;"><span class="badge badge-info">Fully Allocated</span></div>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-12</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_view_receipt')">
                                <i class="fas fa-print"></i> E-Receipt
                            </button>
                        </td>
                    </tr>

                    <tr class="searchable-donation" data-category="In-Kind">
                        <td style="font-family: monospace; font-weight: 700;">RCP-2026-0884</td>
                        <td>
                            <strong>St. Jude Parish Altar Society</strong>
                            <div style="font-size: 11px; color: #64748b;">Faith Community</div>
                        </td>
                        <td><span class="badge badge-warning">In-Kind</span></td>
                        <td>
                            <strong>45x Winter Fleece Hoodies</strong>
                            <div style="font-size: 11px; color: #64748b;">Est. Value: $1,125 USD</div>
                        </td>
                        <td>
                            <strong>Winter Cottage Wardrobe</strong>
                            <div style="margin-top: 2px;"><span class="badge badge-info">Fully Allocated</span></div>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">2026-09-29</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_view_receipt')">
                                <i class="fas fa-print"></i> E-Receipt
                            </button>
                        </td>
                    </tr>

                    <tr class="searchable-donation" data-category="Monetary">
                        <td style="font-family: monospace; font-weight: 700;">RCP-2026-0885</td>
                        <td>
                            <strong>Anonymous Community Donor</strong>
                            <div style="font-size: 11px; color: #64748b;">Private Giving</div>
                        </td>
                        <td><span class="badge badge-success">Monetary</span></td>
                        <td>
                            <strong style="color: #10b981; font-size: 15px;">$800 USD</strong>
                            <div style="font-size: 11px; color: #64748b;">Cheque</div>
                        </td>
                        <td>
                            <strong>General Orphanage Utilities & Solar</strong>
                            <div style="margin-top: 2px;"><span class="badge badge-secondary">General Reserve</span></div>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-18</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_view_receipt')">
                                <i class="fas fa-print"></i> E-Receipt
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 3: DONOR REGISTRY
     ========================================================================== -->
<div id="tab_donors" class="tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="badge badge-purple">Platinum Patron</span>
                <span style="font-family: monospace; font-size: 11px; color: #64748b;">DNR-1001</span>
            </div>
            <h4 style="font-size: 16px; font-weight: 800; margin-top: 10px;">The Meridian Heritage Foundation</h4>
            <span style="font-size: 12px; color: #14b8a6; font-weight: 600;">Philanthropic Foundation</span>

            <div style="margin-top: 16px; font-size: 12px; display: flex; flex-direction: column; gap: 6px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Lifetime Giving:</span> <strong style="color: #10b981;">$45,000 USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Contribution Count:</span> <strong>8 Grants</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Tax Exemption ID:</span> <span style="font-family: monospace;">EIN-94-2849102</span>
                </div>
            </div>
        </div>

        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="badge badge-warning">Gold Donor</span>
                <span style="font-family: monospace; font-size: 11px; color: #64748b;">DNR-1002</span>
            </div>
            <h4 style="font-size: 16px; font-weight: 800; margin-top: 10px;">Dr. Gregory & Eleanor Vance</h4>
            <span style="font-size: 12px; color: #14b8a6; font-weight: 600;">Individual Patrons</span>

            <div style="margin-top: 16px; font-size: 12px; display: flex; flex-direction: column; gap: 6px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Lifetime Giving:</span> <strong style="color: #10b981;">$18,500 USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Contribution Count:</span> <strong>14 Donations</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Focus:</span> <span>Pediatric Emergency Reserve</span>
                </div>
            </div>
        </div>

        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="badge badge-warning">Gold Donor</span>
                <span style="font-family: monospace; font-size: 11px; color: #64748b;">DNR-1003</span>
            </div>
            <h4 style="font-size: 16px; font-weight: 800; margin-top: 10px;">Evergreen Food Staples (CSR)</h4>
            <span style="font-size: 12px; color: #14b8a6; font-weight: 600;">Corporate Sponsor</span>

            <div style="margin-top: 16px; font-size: 12px; display: flex; flex-direction: column; gap: 6px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Lifetime In-Kind:</span> <strong style="color: #10b981;">$12,400 USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Contribution Count:</span> <strong>6 Shipments</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Focus:</span> <span>Monthly Fortified Grain Deliveries</span>
                </div>
            </div>
        </div>

        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="badge badge-info">Silver Friend</span>
                <span style="font-family: monospace; font-size: 11px; color: #64748b;">DNR-1004</span>
            </div>
            <h4 style="font-size: 16px; font-weight: 800; margin-top: 10px;">St. Jude Parish Altar Society</h4>
            <span style="font-size: 12px; color: #14b8a6; font-weight: 600;">Faith Community Partner</span>

            <div style="margin-top: 16px; font-size: 12px; display: flex; flex-direction: column; gap: 6px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Lifetime Giving:</span> <strong style="color: #10b981;">$6,800 USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Contribution Count:</span> <strong>12 Seasonal Drives</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Focus:</span> <span>Clothing, Coats & Holiday Gifts</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 4: ALLOCATIONS TO SANCTUARY NEEDS
     ========================================================================== -->
<div id="tab_allocations" class="tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="badge badge-info">Fund Allocation</span>
                <span class="badge badge-success">100% Deployed</span>
            </div>
            <h4 style="font-size: 16px; font-weight: 800;">Maya Cruz Dental Surgery Treatment</h4>
            <p style="font-size: 12.5px; color: #64748b; margin-top: 4px;">Funded via $1,500 donation from Dr. Gregory & Eleanor Vance (Receipt RCP-2026-0883).</p>
            <div style="background: #f8fafc; padding: 12px; border-radius: 6px; margin-top: 12px; font-size: 12px;">
                <div>Assigned Need: <strong>Emergency Pediatric Surgery</strong></div>
                <div>Linked Ticket: <strong style="font-family: monospace;">WR-2026-042</strong></div>
            </div>
        </div>

        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="badge badge-info">In-Kind Distribution</span>
                <span class="badge badge-success">Stocked to Pantry</span>
            </div>
            <h4 style="font-size: 16px; font-weight: 800;">30x Jasmine Rice Sacks (25kg)</h4>
            <p style="font-size: 12.5px; color: #64748b; margin-top: 4px;">Donated by Evergreen Food Staples (Receipt RCP-2026-0882). Valued at $855.</p>
            <div style="background: #f8fafc; padding: 12px; border-radius: 6px; margin-top: 12px; font-size: 12px;">
                <div>Assigned Need: <strong>Pantry Daily Grain Rations</strong></div>
                <div>Storeroom SKU: <strong style="font-family: monospace;">NUT-RIC-01</strong></div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 5: SUMMARIES & ANALYTICS
     ========================================================================== -->
<div id="tab_summaries" class="tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="portal-card" style="padding: 26px;">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">HopeHaven Allocation by Orphanage Need</h3>
            <p style="font-size: 12px; color: #64748b; margin-bottom: 20px;">How donations are used.</p>

            <div style="display: flex; flex-direction: column; gap: 16px; font-size: 12px;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Child Education & STEM Tutoring</span>
                        <strong>38% ($20,500)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 38%; height: 100%; background: #3b82f6;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Food, Nutrition & Kitchen Rations</span>
                        <strong>27% ($14,500)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 27%; height: 100%; background: #10b981;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Healthcare, Dentistry & Surgeries</span>
                        <strong>20% ($10,800)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 20%; height: 100%; background: #ef4444;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Winter Heating & Facility Maintenance</span>
                        <strong>15% ($8,100)</strong>
                    </div>
                    <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 15%; height: 100%; background: #f59e0b;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="portal-card" style="padding: 26px;">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Top Donors</h3>
            <p style="font-size: 12px; color: #64748b; margin-bottom: 20px;">Cumulative lifetime patrons.</p>

            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 12.5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <div>
                        <strong>#1 The Meridian Heritage Foundation</strong>
                        <div style="font-size: 11px; color: #64748b;">Platinum Patron</div>
                    </div>
                    <strong style="color: #10b981; font-size: 14px;">$45,000 USD</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <div>
                        <strong>#2 Dr. Gregory & Eleanor Vance</strong>
                        <div style="font-size: 11px; color: #64748b;">Gold Donor</div>
                    </div>
                    <strong style="color: #10b981; font-size: 14px;">$18,500 USD</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <div>
                        <strong>#3 Evergreen Food Staples Ltd</strong>
                        <div style="font-size: 11px; color: #64748b;">Corporate Sponsor</div>
                    </div>
                    <strong style="color: #10b981; font-size: 14px;">$12,400 USD</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 1: RECORD CONTRIBUTION -->
<div id="modal_record_donation" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 520px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Record Donation Contribution</h3>
                <p class="modal-sub">Log cash gift or material in-kind delivery.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_record_donation')">&times;</button>
        </div>

        <form onsubmit="handleDonationSubmit(event)">
            <div class="form-group">
                <label class="form-label">Donor Name *</label>
                <input type="text" id="don_name_input" class="form-control" placeholder="e.g. Hope Community Circle" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contribution Type</label>
                    <select class="form-control">
                        <option value="Monetary">Monetary ($ Cash / Wire)</option>
                        <option value="In-Kind">In-Kind (Material Goods)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount ($ USD) or Value</label>
                    <input type="number" id="don_amount_input" class="form-control" value="500" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Allocated Orphanage Need</label>
                <input type="text" id="don_need_input" class="form-control" placeholder="e.g. Higher Education Scholarship, Food Pantry">
            </div>

            <div class="form-group">
                <label class="form-label">Stewardship Notes / Receipt Message</label>
                <textarea class="form-control" rows="2" placeholder="Letter of thanks sent, tax acknowledgment issued..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_record_donation')">Cancel</button>
                <button type="submit" class="btn-primary">Generate E-Receipt</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: REGISTER DONOR -->
<div id="modal_register_donor" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 480px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Register Donor</h3>
                <p class="modal-sub">Add individual, corporate or foundation patron.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_register_donor')">&times;</button>
        </div>

        <form onsubmit="event.preventDefault(); closeModal('modal_register_donor'); showPortalToast('Donor registered in directory!', true);">
            <div class="form-group">
                <label class="form-label">Donor / Entity Name</label>
                <input type="text" class="form-control" placeholder="e.g. Pacific Heritage Trust" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Entity Classification</label>
                    <select class="form-control">
                        <option>Foundation</option>
                        <option>Individual</option>
                        <option>Corporate</option>
                        <option>Faith Community</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Recognition Tier</label>
                    <select class="form-control">
                        <option>Platinum Patron</option>
                        <option>Gold Donor</option>
                        <option>Silver Friend</option>
                        <option>Community Supporter</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email / Contact</label>
                <input type="email" class="form-control" placeholder="giving@trust.org">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_register_donor')">Cancel</button>
                <button type="submit" class="btn-primary">Save Donor Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: VIEW E-RECEIPT -->
<div id="modal_view_receipt" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 550px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Official Donation Receipt</h3>
                <p class="modal-sub">Receipt No: RCP-2026-0881</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_view_receipt')">&times;</button>
        </div>

        <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px; background: #fafbfc;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 800; color: var(--primary);">HopeHaven Orphanage</h4>
                    <span style="font-size: 11px; color: #64748b;">501(c)(3) Tax-Exempt Non-Profit orphanage</span>
                </div>
                <div style="text-align: right; font-size: 12px;">
                    <div>Date: <strong>Oct 15, 2026</strong></div>
                    <span class="badge badge-success">Official Receipt</span>
                </div>
            </div>

            <div style="font-size: 12.5px; display: flex; flex-direction: column; gap: 8px;">
                <div>Received from: <strong>The Meridian Heritage Foundation</strong></div>
                <div>Amount Recognized: <strong style="font-size: 16px; color: #10b981;">$5,000.00 USD</strong></div>
                <div>Payment Method: <strong>Bank Wire Transfer</strong></div>
                <div>Allocated Orphanage Purpose: <strong>Higher Education & STEM Tutoring Fund</strong></div>
            </div>

            <div style="margin-top: 18px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; font-style: italic;">
                "HopeHaven Orphanage certifies that no goods or services were provided in exchange for this contribution other than intangible religious and charitable benefits."
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-outline" onclick="closeModal('modal_view_receipt')">Close</button>
            <button type="button" class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // =========================================================================
    // Multi-Orphanage Cash Flow Benchmark Data & Chart.js Rendering
    // =========================================================================

    const orphanageCashFlowData = {
        '30d': {
            labels: [
                'HopeHaven Orphanage (Our Facility)',
                'St. Jude Children\'s Home',
                'Grace Orphan Care Centre',
                'Darul Aitam Care Foundation',
                'Sunshine Haven Village',
                'Peace Memorial Orphanage'
            ],
            shortLabels: ['HopeHaven', 'St. Jude', 'Grace Care', 'Darul Aitam', 'Sunshine Haven', 'Peace Memorial'],
            inflows: [145200, 128500, 112000, 96400, 84700, 72300],
            outflows: [98400, 92100, 84300, 74800, 68200, 61500],
            netCash: [46800, 36400, 27700, 21600, 16500, 10800],
            kpiTop: '$145,200.00',
            kpiNet: '+$46,800.00'
        },
        'q3': {
            labels: [
                'HopeHaven Orphanage (Our Facility)',
                'St. Jude Children\'s Home',
                'Grace Orphan Care Centre',
                'Darul Aitam Care Foundation',
                'Sunshine Haven Village',
                'Peace Memorial Orphanage'
            ],
            shortLabels: ['HopeHaven', 'St. Jude', 'Grace Care', 'Darul Aitam', 'Sunshine Haven', 'Peace Memorial'],
            inflows: [412000, 375400, 321000, 284500, 248000, 212000],
            outflows: [284000, 268000, 245000, 218000, 198000, 181000],
            netCash: [128000, 107400, 76000, 66500, 50000, 31000],
            kpiTop: '$412,000.00',
            kpiNet: '+$128,000.00'
        },
        'ytd': {
            labels: [
                'HopeHaven Orphanage (Our Facility)',
                'St. Jude Children\'s Home',
                'Grace Orphan Care Centre',
                'Darul Aitam Care Foundation',
                'Sunshine Haven Village',
                'Peace Memorial Orphanage'
            ],
            shortLabels: ['HopeHaven', 'St. Jude', 'Grace Care', 'Darul Aitam', 'Sunshine Haven', 'Peace Memorial'],
            inflows: [1285000, 1150000, 982000, 864000, 758000, 642000],
            outflows: [896000, 832000, 761000, 678000, 614000, 554000],
            netCash: [389000, 318000, 221000, 186000, 144000, 88000],
            kpiTop: '$1,285,000.00',
            kpiNet: '+$389,000.00'
        }
    };

    let activeCashFlowTimeframe = '30d';
    let activeChartViewMode = 'grouped'; // 'grouped', 'net', 'line'
    let cashFlowChartInstance = null;

    function buildChartConfig(timeframeKey, mode) {
        const dataset = orphanageCashFlowData[timeframeKey];
        const labels = (window.innerWidth < 768) ? dataset.shortLabels : dataset.labels;

        if (mode === 'grouped') {
            return {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Monetary Inflow (Donations & Grants)',
                            data: dataset.inflows,
                            backgroundColor: 'rgba(15, 118, 110, 0.85)',
                            borderColor: '#0f766e',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Care Spending',
                            data: dataset.outflows,
                            backgroundColor: 'rgba(244, 63, 94, 0.78)',
                            borderColor: '#f43f5e',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Net Cash Flow Surplus',
                            data: dataset.netCash,
                            backgroundColor: 'rgba(16, 185, 129, 0.9)',
                            borderColor: '#10b981',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { weight: '600', size: 12 },
                                color: '#334155',
                                usePointStyle: true,
                                padding: 18
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let val = context.parsed.y !== null ? context.parsed.y : context.parsed.x;
                                    return ' ' + context.dataset.label + ': $' + Number(val).toLocaleString() + ' USD';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                color: '#64748b',
                                callback: function(value) {
                                    return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#334155', font: { weight: '600', size: 11.5 } }
                        }
                    }
                }
            };
        } else if (mode === 'net') {
            return {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Net Cash Flow Surplus ($ USD)',
                            data: dataset.netCash,
                            backgroundColor: [
                                '#0f766e',
                                '#0284c7',
                                '#10b981',
                                '#8b5cf6',
                                '#f59e0b',
                                '#64748b'
                            ],
                            borderRadius: 8,
                            borderWidth: 1.5,
                            borderColor: '#ffffff'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' Net Surplus: $' + Number(context.parsed.x).toLocaleString() + ' USD';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                color: '#64748b',
                                callback: function(value) {
                                    return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                                }
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#1e293b', font: { weight: '700', size: 12 } }
                        }
                    }
                }
            };
        } else { // line trajectory
            return {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Cash Inflow Trend',
                            data: dataset.inflows,
                            borderColor: '#0f766e',
                            backgroundColor: 'rgba(15, 118, 110, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        },
                        {
                            label: 'Spending Trend',
                            data: dataset.outflows,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.08)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        },
                        {
                            label: 'Net Cash Flow',
                            data: dataset.netCash,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            fill: false,
                            borderDash: [5, 5],
                            tension: 0.3,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { font: { weight: '600' }, color: '#334155' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': $' + Number(context.parsed.y).toLocaleString() + ' USD';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                color: '#64748b',
                                callback: function(value) {
                                    return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#334155', font: { weight: '600' } }
                        }
                    }
                }
            };
        }
    }

    function initOrphanageCashFlowChart() {
        const canvas = document.getElementById('orphanageCashFlowChart');
        if (!canvas) return;

        if (cashFlowChartInstance) {
            cashFlowChartInstance.destroy();
        }

        const config = buildChartConfig(activeCashFlowTimeframe, activeChartViewMode);
        cashFlowChartInstance = new Chart(canvas, config);
    }

    function switchCashFlowTimeframe(timeframeKey, btnElement) {
        activeCashFlowTimeframe = timeframeKey;
        document.querySelectorAll('.chart-timeframe-btn').forEach(b => b.classList.remove('active'));
        if (btnElement) btnElement.classList.add('active');

        // Update KPI Header Values
        const dataset = orphanageCashFlowData[timeframeKey];
        const topEl = document.getElementById('kpi_top_inflow_val');
        const netEl = document.getElementById('kpi_net_cashflow_val');
        if (topEl) topEl.textContent = dataset.kpiTop;
        if (netEl) netEl.textContent = dataset.kpiNet;

        initOrphanageCashFlowChart();
        showPortalToast('Switched Cash Flow Benchmark to: ' + (btnElement ? btnElement.innerText : timeframeKey), true);
    }

    function switchChartViewMode(mode) {
        activeChartViewMode = mode;
        document.querySelectorAll('.chart-view-btn').forEach(b => b.classList.remove('active'));
        if (mode === 'grouped') document.getElementById('btn_mode_grouped')?.classList.add('active');
        if (mode === 'net') document.getElementById('btn_mode_net')?.classList.add('active');
        if (mode === 'line') document.getElementById('btn_mode_line')?.classList.add('active');

        initOrphanageCashFlowChart();
    }

    // Initialize chart when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initOrphanageCashFlowChart, 150);

        // Re-render chart if user switches tabs back to tab_cashflow_comparison
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (this.dataset.tab === 'tab_cashflow_comparison') {
                    setTimeout(() => {
                        if (cashFlowChartInstance) {
                            cashFlowChartInstance.resize();
                        } else {
                            initOrphanageCashFlowChart();
                        }
                    }, 220);
                }
            });
        });
    });
</script>
@endpush
