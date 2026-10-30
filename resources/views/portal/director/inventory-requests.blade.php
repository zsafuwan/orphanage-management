@extends('layouts.portal')
@section('title', 'Inventory & Supplies')
@section('breadcrumb', 'Inventory & Supplies')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-inventory.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-inventory.css')), 0, 12) }}">
@endpush
@section('content')
<div class="portal-container inventory-director-page">
    <div class="page-header-card">
        <div><span class="subsystem-pill"><i class="fas fa-boxes-stacked" aria-hidden="true"></i> INVENTORY & SUPPLIES</span><h1 class="page-title">Stock & Staff Requests</h1><p class="page-desc">Review supply requests and current stock.</p></div>
        <button type="button" class="btn-outline" onclick="exportRequisitionsAudit()"><i class="fas fa-download" aria-hidden="true"></i> Download Requests</button>
    </div>
    <div class="inv-stats-row">
        <div class="inv-stat-card"><div class="inv-stat-icon amber"><i class="fas fa-clock" aria-hidden="true"></i></div><div><div class="inv-stat-val" id="stat_pending_count">0</div><div class="inv-stat-lbl">Requests Awaiting Approval</div></div></div>
        <div class="inv-stat-card"><div class="inv-stat-icon rose"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></div><div><div class="inv-stat-val" id="stat_low_stock_count">0</div><div class="inv-stat-lbl">Low Stock Items</div></div></div>
        <div class="inv-stat-card"><div class="inv-stat-icon indigo"><i class="fas fa-boxes-stacked" aria-hidden="true"></i></div><div><div class="inv-stat-val" id="stat_stock_item_count">0</div><div class="inv-stat-lbl">Items in Inventory</div></div></div>
        <div class="inv-stat-card"><div class="inv-stat-icon emerald"><i class="fas fa-check" aria-hidden="true"></i></div><div><div class="inv-stat-val" id="stat_approved_request_count">0</div><div class="inv-stat-lbl">Approved Requests</div></div></div>
    </div>
    <nav class="inventory-section-links" aria-label="Inventory sections"><a href="#director-requests"><i class="fas fa-clipboard-list" aria-hidden="true"></i> Staff Requests</a><a href="#director-stock"><i class="fas fa-boxes-stacked" aria-hidden="true"></i> Current Stock</a></nav>
    <section id="director-requests" class="director-card inventory-panel">
        <div class="director-card-head"><div><h2 class="director-card-title">Staff Supply Requests</h2><p class="inventory-panel-note">Approval does not change stock. Staff confirm receipt when items arrive.</p></div></div>
        <div class="inv-toolbar inventory-panel-toolbar">
            <div class="inv-filter-tabs" role="group" aria-label="Request status">
                <button type="button" class="inv-tab-btn active" data-filter="all" aria-pressed="true">All <span class="inv-tab-badge" data-request-count="all">0</span></button>
                <button type="button" class="inv-tab-btn" data-filter="Pending" aria-pressed="false">Pending <span class="inv-tab-badge" data-request-count="Pending">0</span></button>
                <button type="button" class="inv-tab-btn" data-filter="Approved" aria-pressed="false">Approved <span class="inv-tab-badge" data-request-count="Approved">0</span></button>
                <button type="button" class="inv-tab-btn" data-filter="Received" aria-pressed="false">Received <span class="inv-tab-badge" data-request-count="Received">0</span></button>
                <button type="button" class="inv-tab-btn" data-filter="Rejected" aria-pressed="false">Returned <span class="inv-tab-badge" data-request-count="Rejected">0</span></button>
            </div>
            <div class="inv-search-wrap"><input type="search" class="inv-search-input" id="inv_search_input" aria-label="Search inventory requests" placeholder="Search item, staff, location or request ID…"></div>
        </div>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Staff supply requests"><table class="inv-table"><thead><tr><th>Request / Staff</th><th>Item / Quantity Requested</th><th>Destination</th><th>Priority</th><th>Current Stock</th><th>Status / Feedback</th><th>Action</th></tr></thead><tbody id="director_requests_tbody"></tbody></table></div>
        <p id="director_requests_summary" class="inventory-result-count" role="status"></p>
    </section>
    <section id="director-stock" class="director-card inventory-panel">
        <div class="director-card-head"><div><h2 class="director-card-title">Current Stock</h2><p class="inventory-panel-note">The same quantities staff see and update in their inventory page.</p></div></div>
        <div class="inventory-stock-toolbar"><input type="search" class="inv-search-input" id="director_stock_search" aria-label="Search current stock" placeholder="Search item, ID or storage location…"><label class="inventory-low-filter"><input type="checkbox" id="director_low_only"> Low stock only</label></div>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Current inventory stock"><table class="inv-table"><thead><tr><th>Item / ID</th><th>Category</th><th>Storage Location</th><th>In Stock</th><th>Low Stock Alert At</th><th>Stock Level</th></tr></thead><tbody id="director_stock_tbody"></tbody></table></div>
        <p id="director_stock_summary" class="inventory-result-count" role="status"></p>
    </section>
</div>
@include('portal.director.partials.inventory-modals')
@endsection
@push('scripts')
    <script src="{{ asset('js/portal/director-inventory.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-inventory.js')), 0, 12) }}"></script>
@endpush
