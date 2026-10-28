@extends('layouts.portal')

@section('title', 'Director Profile & Account Details')
@section('breadcrumb', 'Director Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-profile.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-profile.css')), 0, 12) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Hero Profile Card -->
    <div class="dir-profile-hero">
        <div class="dir-profile-left">
            <div class="dir-avatar-circle">
                SS
            </div>
            <div>
                <h1 class="dir-profile-name">
                    Sister Sarah Montgomery
                    <span class="dir-badge-pill emerald">
                        <i class="fas fa-check-circle"></i> Official JKM Accreditation
                    </span>
                </h1>
                <div class="dir-profile-title">
                    Executive Director & Chairperson · HopeHaven Orphanage Board of Trustees
                </div>
                <div class="dir-badges-group">
                    <span class="dir-badge-pill indigo">
                        <i class="fas fa-crown"></i> Executive Director
                    </span>
                    <span class="dir-badge-pill indigo">
                        <i class="fas fa-landmark"></i> Board Trustee (Sole Signatory)
                    </span>
                    <span style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-calendar-alt"></i> Appointed Oct 2020 (6 Years Service)
                    </span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-director-review" onclick="updateDirectorSignature()">
                <i class="fas fa-signature"></i> Digital Signature Seal
            </button>
            <button type="button" class="btn-director-approve" onclick="triggerDirectorPasswordChange()">
                <i class="fas fa-key"></i> Security Settings
            </button>
        </div>
    </div>

    <!-- Official Directorate Credentials -->
    <div class="dir-info-card">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-id-card"></i>
                <span>Executive Director Credentials & Institutional Management</span>
            </div>
            <span style="font-size: 12px; color: #059669; font-weight: 700;">
                <i class="fas fa-shield-check"></i> Security Verified
            </span>
        </div>
        <div class="dir-info-grid">
            <div class="dir-field-box">
                <label>Official Institutional Email</label>
                <span>director@hopehaven.org.my</span>
            </div>
            <div class="dir-field-box">
                <label>Direct Director Line</label>
                <span>+60 3-4022 8891 (Office Suite A)</span>
            </div>
            <div class="dir-field-box">
                <label>Government Social Welfare Registry</label>
                <span>JKM / PAKK / WPKL / 2020-00812</span>
            </div>
            <div class="dir-field-box">
                <label>Trustee Board Approval</label>
                <span>Section 44(6) Sole Financial Signatory</span>
            </div>
            <div class="dir-field-box">
                <label>Office Headquarters</label>
                <span>Director Suite, Level 2, HopeHaven Orphanage, Kuala Lumpur</span>
            </div>
            <div class="dir-field-box">
                <label>Two-Factor Authentication (2FA)</label>
                <span style="color: #059669; font-weight: 700;"><i class="fas fa-lock"></i> Enabled & Enforced</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-profile.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-profile.js')), 0, 12) }}"></script>
@endpush
