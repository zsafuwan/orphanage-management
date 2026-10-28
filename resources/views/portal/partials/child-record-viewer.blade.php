<div id="modal_child_dossier" class="portal-modal-backdrop">
    <div class="portal-modal child-record-viewer" role="dialog" aria-modal="true" aria-labelledby="child-record-title">
        <header class="record-viewer-header">
            <div class="record-viewer-eyebrow"><i class="fas fa-folder-open" aria-hidden="true"></i> CHILD RECORDS <span>VIEW ONLY</span></div>
            <h3 id="child-record-title"><span id="dossier_record_label">Child Records</span><span class="record-title-divider"> / </span><span id="dossier_modal_name"></span></h3>
            <p id="dossier_modal_meta"></p>
            <button type="button" class="record-viewer-close" aria-label="Close child record" onclick="closeModal('modal_child_dossier')">&times;</button>
        </header>

        <div class="record-viewer-body">
            <section class="record-child-profile" aria-label="Child profile">
                <img id="dossier_modal_avatar" src="{{ asset('favicon.svg') }}" alt="Child portrait">
                <div class="record-child-identity">
                    <span class="record-small-label">GETTING TO KNOW</span>
                    <h4 id="dossier_modal_head_name"></h4>
                    <div id="dossier_modal_head_sub" class="record-profile-tags"></div>
                    <p class="record-child-home"><i class="fas fa-house" aria-hidden="true"></i><span id="dossier_modal_head_dorm"></span></p>
                </div>
            </section>

            <nav class="dossier-nav-tabs" role="group" aria-label="Child record sections">
                <button type="button" class="dossier-tab-btn" data-dossier-tab="dossier_tab_overview" aria-controls="dossier_tab_overview" aria-pressed="false" onclick="ChildWelfareUI.switchDossierTab('dossier_tab_overview')"><i class="fas fa-user" aria-hidden="true"></i> Overview</button>
                <button type="button" class="dossier-tab-btn" data-dossier-tab="dossier_tab_papers" aria-controls="dossier_tab_papers" aria-pressed="false" onclick="ChildWelfareUI.switchDossierTab('dossier_tab_papers')"><i class="fas fa-graduation-cap" aria-hidden="true"></i> Exam Results</button>
                <button type="button" class="dossier-tab-btn" data-dossier-tab="dossier_tab_medical" aria-controls="dossier_tab_medical" aria-pressed="false" onclick="ChildWelfareUI.switchDossierTab('dossier_tab_medical')"><i class="fas fa-heart-pulse" aria-hidden="true"></i> Medical Record</button>
            </nav>

            <section id="dossier_tab_overview" class="dossier-pane" aria-label="Child overview">
                <div class="record-overview-grid">
                    <article class="record-summary-panel">
                        <h4><i class="fas fa-graduation-cap" aria-hidden="true"></i> Learning & school</h4>
                        <dl class="record-facts">
                            <div><dt>School</dt><dd id="dossier_modal_school"></dd></div>
                            <div><dt>Grade</dt><dd id="dossier_modal_grade"></dd></div>
                            <div><dt>GPA & standing</dt><dd id="dossier_modal_gpa"></dd></div>
                            <div><dt>Attendance</dt><dd id="dossier_modal_attendance"></dd></div>
                        </dl>
                    </article>
                    <article class="record-summary-panel">
                        <h4><i class="fas fa-heart-pulse" aria-hidden="true"></i> Health at a glance</h4>
                        <dl class="record-facts">
                            <div><dt>Condition</dt><dd id="dossier_modal_condition"></dd></div>
                            <div><dt>Allergies</dt><dd id="dossier_modal_allergies"></dd></div>
                            <div><dt>Medicines</dt><dd id="dossier_modal_meds"></dd></div>
                            <div><dt>Next visit</dt><dd id="dossier_modal_next_visit"></dd></div>
                        </dl>
                    </article>
                </div>
            </section>

            <section id="dossier_tab_papers" class="dossier-pane" aria-label="Saved exam results" style="display:none;">
                <div id="dossier_paper_history_list"></div>
            </section>

            <section id="dossier_tab_medical" class="dossier-pane" aria-label="Saved medical record" style="display:none;">
                <article class="record-summary-panel record-health-panel">
                    <h4><i class="fas fa-notes-medical" aria-hidden="true"></i> Health & care details</h4>
                    <dl class="record-facts record-health-facts">
                        <div><dt>Vaccinations</dt><dd id="dossier_medical_immunization"></dd></div>
                        <div><dt>Blood type</dt><dd id="dossier_medical_blood"></dd></div>
                        <div><dt>Clinic</dt><dd id="dossier_medical_clinic"></dd></div>
                        <div><dt>Doctor</dt><dd id="dossier_medical_doctor"></dd></div>
                        <div><dt>Medical condition</dt><dd id="dossier_medical_condition"></dd></div>
                        <div><dt>Allergies</dt><dd id="dossier_medical_allergies"></dd></div>
                        <div><dt>Medicines</dt><dd id="dossier_medical_medications"></dd></div>
                        <div><dt>Next clinic visit</dt><dd id="dossier_medical_review"></dd></div>
                        <div class="record-care-notes"><dt>Care notes</dt><dd id="dossier_medical_notes"></dd></div>
                    </dl>
                </article>
            </section>
        </div>

        <footer class="record-viewer-footer"><span><i class="fas fa-shield-heart" aria-hidden="true"></i> Care records, kept together.</span><button type="button" onclick="closeModal('modal_child_dossier')">Close Record <i class="fas fa-check" aria-hidden="true"></i></button></footer>
    </div>
</div>
