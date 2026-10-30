<!-- Full Academic Portfolio Modal -->
<div class="director-modal-overlay" id="modal_student_academic_portfolio">
    <div class="director-modal" style="max-width: 680px;">
        <div class="director-modal-head" style="background: #f5f3ff; border-bottom: 1px solid #ddd6fe;">
            <h3><i class="fas fa-graduation-cap" style="color: #7c3aed;"></i> Student Results</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_student_academic_portfolio')">&times;</button>
        </div>
        <div class="director-modal-body">
            <!-- Header Student Info -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0;">
                <div>
                    <h2 id="portfolio_student_name" style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 3px;">Daniel Arif Bin Rosli</h2>
                    <div style="font-size: 12px; color: #64748b; display: flex; gap: 8px;">
                        <span id="portfolio_school_info">SMK Seri Titiwangsa · Tingkatan 2 Amanah</span>
                        <span>·</span>
                        <span id="portfolio_sponsor_info" style="color: #7c3aed; font-weight: 600;">Sponsor: Yayasan Hasanah</span>
                    </div>
                </div>
                <div>
                    <span id="portfolio_overall_grade" class="grade-pill distinction" style="font-size: 14px; padding: 5px 12px;">Grade A (86%)</span>
                </div>
            </div>

            <!-- Academic Highlights Metrics -->
            <div class="dossier-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="dossier-box">
                    <div class="dossier-box-label">School Attendance</div>
                    <div class="dossier-box-value" id="portfolio_attendance" style="color: #059669;">98.8% (Exemplary)</div>
                </div>
                <div class="dossier-box">
                    <div class="dossier-box-label">Class Position</div>
                    <div class="dossier-box-value" id="portfolio_rank">3rd of 36 Students</div>
                </div>
                <div class="dossier-box">
                    <div class="dossier-box-label">Co-Curriculum</div>
                    <div class="dossier-box-value" id="portfolio_cocurriculum" style="font-size: 12px;">Debate & Football</div>
                </div>
            </div>

            <!-- Subject Scorecard Table -->
            <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Term 2 Mid-Year Examination Results</label>
            <table class="scorecard-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th style="text-align: center;">Marks (%)</th>
                        <th style="text-align: center;">Grade</th>
                        <th>Teacher Evaluation Note</th>
                    </tr>
                </thead>
                <tbody id="portfolio_scorecard_body">
                    <!-- Dynamically populated -->
                </tbody>
            </table>

            <!-- Volunteer Tutoring & Mentorship Notes -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 16px;">
                <div style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">Tutoring & Academic Development Mentorship</div>
                <div id="portfolio_tutoring_notes" style="font-size: 13px; color: #334155; line-height: 1.5;">
                    Enrolled in Saturday Mathematics Clinic with volunteer tutor Brother David. Showing great confidence in algebra.
                </div>
            </div>

            <!-- Teacher & Caregiver Conduct Remarks -->
            <div style="font-size: 12px; color: #64748b; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong>Class Teacher Remark:</strong> "Polite, diligent, and always punctual. A role model in 2 Amanah."
                </div>
                <button type="button" class="btn-edu" style="font-size: 11px; padding: 4px 10px;" onclick="printStudentTranscript()">
                    <i class="fas fa-print"></i> Transcript (PDF)
                </button>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_student_academic_portfolio')">Close</button>
            <button type="button" class="btn-edu" onclick="openUpdateGradesModal()">
                <i class="fas fa-edit"></i> Update Marks & Director Remark
            </button>
        </div>
    </div>
</div>

<!-- Update Marks & Academic Remark Modal -->
<div class="director-modal-overlay" id="modal_update_student_grades">
    <div class="director-modal">
        <div class="director-modal-head">
            <h3><i class="fas fa-file-pen" style="color: #7c3aed;"></i> Update Term Notes</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_update_student_grades')">&times;</button>
        </div>
        <div class="director-modal-body">
            <div style="margin-bottom: 14px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Student Name</label>
                <input type="text" id="update_grade_student_name" readonly style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 600;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label for="update_grade_average" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Term Average (%)</label>
                    <input type="number" id="update_grade_average" min="0" max="100" value="86" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none;">
                </div>
                <div>
                    <label for="update_grade_standing" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Standing</label>
                    <select id="update_grade_standing" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none;">
                        <option value="distinction">Grade A (Distinction / Honor Roll)</option>
                        <option value="credit">Grade B (Credit / Good Standing)</option>
                        <option value="remedial">Grade C / Needs Remedial Tuition</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label for="director_edu_remark" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Director Endorsement & Encouragement Note</label>
                <textarea id="director_edu_remark" rows="3" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; font-family: inherit; outline: none;" placeholder="e.g. Commended for exemplary Mathematics and Bahasa Melayu improvement. Approved for Science Robotics sponsorship."></textarea>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_update_student_grades')">Cancel</button>
            <button type="button" class="btn-edu" onclick="saveStudentGradeUpdate()">
                <i class="fas fa-check"></i> Save Evaluation
            </button>
        </div>
    </div>
</div>
