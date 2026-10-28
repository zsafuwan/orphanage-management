# HopeHaven interface audit — 26 October 2026

Scope: the public homepage and 22 distinct portal screens, inspected in the user's Herd Studio preview. Database integration and authentication implementation remain the next phase, as requested. No test records are intentionally added to the user's Herd origin.

## Fixes

| Finding | Resolution |
| --- | --- |
| Blade screens did not use Vite's live reload. | A local-only revision endpoint watches views, CSS, JavaScript, routes and application PHP. Styles update in place; source changes refresh automatically. A visible unfinished form postpones a page refresh. |
| Same-page storage writes emitted no update; several pages ignored changes in other tabs. | Explicit store writes notify subscribed views, with native storage events for other tabs and reconciliation on focus, visibility and back/forward restoration. |
| Background rendering could reset selected children or unfinished form values. | Sync rendering preserves field values, selections and caret position. |
| Director medical/education screens and clinic overview displayed a different set of children from caregivers. | These screens now derive records and counts from the shared child registry. Unknown clinical/academic values show “Not recorded”. |
| Director dialogs on medical, education, donations and staffing pages lacked shared modal CSS and appeared as ordinary page content. | Shared overlay styling, close buttons, Escape, backdrop closing and keyboard focus management. |
| Search bypassed selected ward/cohort/category/status filters. | Medical, education and director inventory use one combined filter predicate. |
| Child IDs and welfare IDs could collide after deletions or with existing seed sequences. | New IDs use the highest sequence for the current year. |
| An empty child registry restored demo children. | An intentionally saved empty array remains empty. |
| Infants were assigned age eight. | Age zero is preserved; future birth dates are rejected. |
| Blank medical fields silently kept previous information. | Clearing optional fields now clears their saved value. |
| An academic paper with no subjects received an invented score of 85. | Saving requires at least one subject. |
| Staff dashboard links switched users into the admin view. | Staff child-welfare links preserve the staff context. |
| Empty portrait URL caused a broken image request. | Valid placeholder used until a child record is opened. |
| Notifications treated warning/info strings as success. | Type strings use the shared typed notification renderer. |
| Mixed 24-hour time and AM/PM; mismatched date labels. | Removed invalid suffixes and fixed relative labels. |
| Styles and scripts could be cached across development refreshes. | Shared assets are versioned; development CSS updates use revision query strings. |
| Duty acknowledgements and completed routines reset on refresh and had stale tab counts. | Daily browser-local duty state persists and syncs to other staff tabs; counts are recalculated. |
| Mobile profile, director filter bars and public map caused horizontal overflow. | Grid minimum sizes, wrapping and search control widths corrected; tables retain their own horizontal scrolling. |
| User text could be interpreted as markup in notifications and staff cards. | Escape values before inserting them into HTML. |
| Repeated supply receipt actions could add the same quantity twice. | Already received requisitions are ignored; invalid stock counts/dispensing quantities are rejected. |
| Empty welfare breakdowns invented a cost of 50. | Require actual line items rather than inventing a budget. |

## Page coverage

Each screen below was opened in Herd Studio and checked for its title, page heading, desktop overflow and broken images. Interaction and sync verification is recorded below separately.

- Public: `/`.
- Admin: `/dashboard`, `/child-welfare`, `/inventory`, `/welfare-requests`, `/staff-volunteers`, `/donations`, `/profile`, `/register`.
- Staff: `/dashboard/staff`, `/staff/inventory`, `/staff/welfare-requests`, `/staff/staff-volunteers`, `/staff/donations`, `/staff/profile`.
- Director: `/director`, `/director/inventory-requests`, `/director/medical-records`, `/director/children-education`, `/director/staff-volunteers`, `/director/donations`, `/director/profile`, `/director/register-staff`.

URL aliases that render the same template are not separate screens.

## Verification

- All 10 JavaScript regression tests passed (`node --test tests/js/live-sync.test.cjs`): same-page notifications; batching; cross-tab notifications; focus reconciliation; unfinished form preservation; ID sequences; empty registries; infant/future birth dates; clearing medical fields; failed storage writes; idempotent duty actions; escaped user text.
- Blade compilation and JavaScript/PHP syntax checks.
- All 23 distinct screens opened in Herd Studio; desktop layout and image checks completed.
- Mobile layout checks at 390 × 844 across all 23 screens; overflow failures corrected and rechecked.
- Primary dialog opening/closing verified on ten admin, staff and director screens. Clinical/academic record selection, combined ward/cohort/search filters and Escape-to-close verified.
- Isolated localhost cross-tab UI tests passed: child registration → director medical list; medical edit → director list while a draft note remains intact; staff welfare request → admin queue → approved status back on staff page; stock adjustment; donation entry; duty acknowledgement and routine completion.
- Source update test passed: an idle page refreshed automatically, the editing page postponed refreshing and retained its note, and CSS updated in place.
- Browser tests used the user's Herd Studio tab for the audit and isolated localhost tabs for mutations. No audit records were added to the user's Herd-domain storage.
- `vp build` passed; all 28 public/inline JavaScript scripts passed syntax checks; changed PHP files passed syntax checks; Blade templates compiled successfully.

## Deliberate limits and next phase

- Data sync currently uses browser storage: it works across tabs/frames of the same origin and browser profile, including Herd Studio. It does not synchronize across devices, separate browser profiles, or different origins such as localhost and the Herd domain. A server transport is needed for that phase.
- Some export, account provisioning, password, finance and messaging controls are prototype actions. Their database/backend implementations are retained for the next phase; a toast is not proof of a generated file or a real account.
- Admin financial benchmarks, staffing rosters and director grant ledgers still contain independent sample datasets. They are not represented as a shared financial accounting system.
- Authentication/role enforcement and production security were not implemented as part of this frontend audit.
