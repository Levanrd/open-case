# Project Planning: Open Case

## 1. Project Overview

Open Case is a tax readiness and business closure support portal for Philippine business owners. It helps users understand possible BIR related issues, organize their business documents, receive a preliminary case result, and collaborate with an internal accountant or business consultant for paid professional assistance.

Open Case is not a BIR system. It does not replace an accountant, tax practitioner, lawyer, or BIR officer. The system only gives a preliminary review based on user provided answers and uploaded documents.

## 2. Product Positioning

### Main Positioning

Open Case helps business owners know what may be missing before their BIR issue becomes costly.

### Safer Public Message

Open Case helps Philippine business owners understand possible BIR open cases, closure requirements, and missing tax documents before working with a professional accountant.

### Avoid Saying

Open Case tells users exactly how much they owe.

Open Case can guarantee business closure.

Open Case is connected to BIR.

Open Case accepts tax payments for BIR.

## 3. Target Audience

1. Small business owners
2. Freelancers
3. Self employed individuals
4. Single proprietors
5. Online sellers
6. Service contractors
7. Business owners who stopped operating but never formally closed their BIR registration
8. Business owners who received BIR open case notices
9. Business owners confused by Form 2303, 1701Q, 1701, 2551Q, 0605, books of accounts, receipts, invoices, and closure requirements

## 4. Internal Accountant Model

Open Case will not allow public accountant registration.

The accountant or business consultant role is controlled internally. Only the platform owner or admin can create accountant accounts. This keeps the service quality controlled and avoids turning the app into an open marketplace too early.

### Accountant Access Rules

1. Accountants cannot register publicly.
2. Admin creates accountant accounts manually.
3. Admin may invite an accountant using a verified email.
4. Accountant accounts should require two factor authentication.
5. Accountants can only access cases assigned to them.
6. Accountant actions must be logged in the audit trail.
7. Google login for accountant accounts is only allowed if the email already belongs to an approved accountant account.

### Why This Is Better For MVP

This approach is safer, simpler, and more realistic. Since you already have an accountant or business consultant who can work on cases, the app should focus on lead capture, case intake, document organization, payment, and collaboration. Do not build a marketplace before proving the workflow.

## 5. Core Problem

Many business owners believe that when their business stops operating, their BIR duties also stop. In reality, a registered business may continue to have filing obligations until the registration is properly updated, cancelled, or closed.

Open Case helps users answer these questions:

1. Is my business still active in BIR records?
2. What tax types am I registered under?
3. What returns might I have missed?
4. What documents should I prepare?
5. Am I ready for closure, or do I need cleanup first?
6. Should I work with an accountant?

## 6. Main Workflow

1. Business owner creates an account.
2. Business owner starts a case.
3. Business owner selects the concern.
4. Business owner fills out the guided form.
5. Business owner uploads available BIR documents.
6. System generates a preliminary result.
7. User sees possible issues, risk level, missing items, and recommended next step.
8. User chooses a paid service package.
9. User pays the Open Case service fee.
10. Admin assigns the case to the internal accountant or consultant.
11. Accountant reviews the case.
12. Accountant requests missing documents if needed.
13. User uploads additional files.
14. Accountant adds notes, checklist updates, and recommendations.
15. Case is marked completed.

## 7. User Roles

### Business Owner

The business owner can:

1. Register and log in
2. Log in with Google
3. Create a business profile
4. Start a case
5. Fill out tax related forms
6. Upload documents
7. View preliminary results
8. Select a service package
9. Pay for professional review
10. Track case progress
11. Respond to document requests

### Accountant Or Business Consultant

The accountant or consultant can:

1. Log in using an approved internal account
2. View assigned paid cases
3. Review business details
4. Review uploaded documents
5. Add private notes
6. Add client visible notes
7. Create document requests
8. Update checklist items
9. Update case status
10. Upload prepared files or instructions
11. Mark case as ready for BIR processing or completed

### Admin

The admin can:

1. Manage users
2. Create accountant accounts
3. Assign cases
4. Manage service packages
5. Manage questionnaire rules
6. Manage document categories
7. View payment status
8. Review audit logs
9. Update disclaimers
10. Disable suspicious accounts
11. Generate operational reports

## 8. Authentication Requirements

Open Case supports:

1. Email and password login
2. Login with Google
3. Password reset
4. Email verification
5. Role based redirects
6. Admin created accountant accounts
7. Account linking by email for Google login
8. Two factor authentication for admin and accountant accounts

### Google Login Rules

Use the label "Continue with Google" instead of "Login with Gmail."

Google login is for authentication only. The app must not request Gmail inbox access, Google Drive access, or Google Calendar access.

### Role Redirects

After login:

1. Business owner goes to `/dashboard`
2. Accountant goes to `/accountant/cases`
3. Admin goes to `/admin`

### Accountant Login Rule

If a Google account email matches an approved accountant account, the accountant may log in. If not, the system creates or logs in the user as a business owner only.

## 9. Core Features

### User Authentication

1. Register
2. Login
3. Continue with Google
4. Logout
5. Forgot password
6. Email verification
7. Role based access

### Business Profile Intake

Fields:

1. Registered business name
2. Trade name
3. TIN, encrypted in storage
4. TIN last four digits, for display only
5. RDO code
6. Business type
7. Registered address
8. Registration date
9. Business activity
10. Line of business
11. Current business status
12. Last year of operation
13. Whether the business is still operating
14. Whether the user wants closure, cleanup, or consultation

### Tax Profile Builder

The user can manually enter tax information or upload the Certificate of Registration.

Capture:

1. Taxpayer type
2. Registered tax types
3. Form types
4. Filing frequency
5. Filing start date
6. Due date notes
7. Registration fee requirement
8. Books of accounts status
9. Receipts or invoices status
10. Authority to Print status
11. RDO information

### Document Upload

Allowed document types:

1. Certificate of Registration
2. Tax year summary list
3. Open case summary
4. Filed tax returns
5. Payment confirmations
6. Receipts or invoices inventory
7. Books of accounts details
8. BIR notices
9. Closure related documents
10. Authorization letter
11. Special Power of Attorney, if applicable

### Preliminary Result Engine

Result types:

1. Low Risk
2. Needs Review
3. Likely Open Case
4. Closure Preparation Needed
5. Accountant Required

The result engine should not compute exact penalties in the MVP.

### Case Management

Each case should have:

1. Case number
2. Business owner
3. Assigned accountant
4. Case type
5. Current status
6. Risk level
7. Checklist
8. Documents
9. Notes
10. Payment status
11. Activity log
12. Final recommendation

Suggested case statuses:

1. Draft
2. Submitted
3. Awaiting Payment
4. Paid
5. Accountant Assigned
6. Under Review
7. Needs More Documents
8. In Progress
9. Ready For BIR Processing
10. Completed
11. Cancelled

### Payment Flow

Payments are for Open Case professional service fees only.

Payment package examples:

1. Basic Review
2. Open Case Review
3. Business Closure Assistance
4. Full Compliance Cleanup
5. Monthly Compliance Help

Important message:

BIR taxes, penalties, and government fees are separate from Open Case service fees.

## 10. Suggested Case Types

1. Business Closure
2. Open Case Review
3. Missing Filing Review
4. Tax Type Clarification
5. Registration Update
6. Compliance Cleanup
7. General BIR Consultation

## 11. Suggested Questionnaire

### Basic Concern

1. What do you need help with?
2. Is your business still operating?
3. Did you already file for closure?
4. Did you receive a BIR notice?
5. Do you have your Certificate of Registration?
6. Do you know your RDO?
7. Do you have unused receipts or invoices?
8. Do you have books of accounts?
9. When was your last filed return?
10. What year did the business stop operating, if applicable?

### Tax Type Questions

1. Are you registered under percentage tax?
2. Are you registered under income tax quarterly filing?
3. Are you registered under annual income tax filing?
4. Are you required to pay annual registration fee?
5. Are you registered under withholding tax?
6. Are there tax types listed on your COR that you do not understand?

### Document Availability

1. Do you have your COR?
2. Do you have filed return copies?
3. Do you have payment confirmations?
4. Do you have unused receipts or invoices?
5. Do you have books of accounts?
6. Do you have a BIR open case summary?
7. Do you have closure related forms?

## 12. Suggested Rule Based Result Logic

### Missing COR

If the user says they have a business but no COR uploaded, return:

COR needed for accurate review.

### Stopped Operating Without Closure

If the user stopped operating but did not file closure, return:

Closure review recommended.

### Percentage Tax

If the user has tax type 2551Q, ask for quarterly percentage tax return records.

### Quarterly Income Tax

If the user has tax type 1701Q, ask for quarterly income tax return records.

### Annual Income Tax

If the user has tax type 1701, ask for annual income tax return records.

### Registration Fee

If the user has an annual registration fee requirement, ask for Form 0605 or payment confirmation.

### Unused Receipts Or Invoices

If the user has unused receipts or invoices, include inventory review as a possible requirement.

### BIR Notice

If the user received a BIR notice, recommend accountant review immediately.

### Multiple Years With No Filing

If the user has multiple years with no filing, mark the case as high priority.

## 13. MVP Scope

### Included In MVP

1. Authentication
2. Google login
3. Business owner dashboard
4. Start case form
5. Document upload
6. Rule based preliminary result
7. Service package selection
8. Payment status tracking
9. Admin dashboard
10. Admin created accountant accounts
11. Accountant dashboard
12. Case checklist
13. Case notes
14. Document request system
15. Email notifications
16. Audit log

### Not Included In MVP

1. Public accountant registration
2. Accountant marketplace
3. AI final decision making
4. Automatic penalty calculation
5. Direct BIR integration
6. Scraping BIR portals
7. Asking users for BIR login credentials
8. Mobile app
9. Real time chat
10. Automated filing

## 14. Timeline

### Week 1: Planning And Compliance Review

1. Finalize user flow
2. Finalize questionnaire
3. Define case types
4. Define service packages
5. Draft disclaimers
6. Draft privacy policy requirements
7. Document the accountant or consultant workflow
8. Create wireframes for the owner dashboard, result page, accountant page, and admin page

### Week 2: Project Foundation

1. Set up Laravel 12
2. Set up PHP 8.5
3. Set up Inertia.js v3 with Vue 3
4. Set up Tailwind CSS 4
5. Set up shadcn vue
6. Set up authentication
7. Set up Google login
8. Set up roles and permissions
9. Set up base layouts
10. Set up pnpm, oxlint, and oxfmt
11. Set up Pest 4 and browser testing

### Week 3: Database And Core Models

1. Create users table updates
2. Create businesses table
3. Create cases table
4. Create case answers table
5. Create documents table
6. Create case results table
7. Create checklist items table
8. Create case notes table
9. Create service packages table
10. Create payments table
11. Create audit logs table

### Week 4: Business Owner Flow

1. Build start case page
2. Build business profile form
3. Build tax details form
4. Build document upload
5. Build case submission
6. Build result page
7. Build owner dashboard

### Week 5: Result Engine And Checklist

1. Create rule based assessment service
2. Map answers to case result
3. Generate recommended checklist
4. Generate recommended service package
5. Add admin editable rules if possible
6. Add result disclaimer acceptance

### Week 6: Accountant Dashboard

1. Build accountant case list
2. Build case detail page
3. Build document viewer
4. Build checklist update
5. Build internal notes
6. Build client visible notes
7. Build request missing document feature
8. Build status update flow

### Week 7: Payment And Admin

1. Add payment provider
2. Add service package checkout
3. Add payment status tracking
4. Add admin dashboard
5. Add accountant assignment
6. Add case monitoring
7. Add basic reports

### Week 8: Testing And Launch Preparation

1. Write unit tests
2. Write feature tests
3. Write browser tests
4. Test file uploads
5. Test role permissions
6. Test result logic
7. Test payment flow
8. Test privacy and access control
9. Prepare staging environment
10. Launch beta with controlled users

## 15. Suggested Database Structure

### users

1. id
2. first_name
3. last_name
4. email
5. phone
6. password
7. role
8. provider
9. provider_id
10. avatar
11. email_verified_at
12. two_factor_secret
13. two_factor_confirmed_at
14. created_at
15. updated_at

### businesses

1. id
2. user_id
3. registered_name
4. trade_name
5. tin_encrypted
6. tin_last_four
7. rdo_code
8. business_type
9. registered_address
10. registration_date
11. business_status
12. line_of_business
13. created_at
14. updated_at

### cases

1. id
2. business_id
3. assigned_accountant_id
4. case_number
5. case_type
6. status
7. risk_level
8. summary
9. submitted_at
10. completed_at
11. created_at
12. updated_at

### case_answers

1. id
2. case_id
3. question_key
4. answer_value
5. created_at
6. updated_at

### documents

1. id
2. case_id
3. uploaded_by
4. document_type
5. file_path
6. original_filename
7. mime_type
8. file_size
9. status
10. created_at
11. updated_at

### case_results

1. id
2. case_id
3. result_type
4. risk_score
5. recommendation
6. generated_summary
7. disclaimer_accepted
8. created_at
9. updated_at

### checklist_items

1. id
2. case_id
3. title
4. description
5. status
6. assigned_to
7. due_date
8. created_at
9. updated_at

### case_notes

1. id
2. case_id
3. user_id
4. note
5. visibility
6. created_at
7. updated_at

### service_packages

1. id
2. name
3. description
4. price
5. case_type
6. active
7. created_at
8. updated_at

### payments

1. id
2. case_id
3. user_id
4. package_id
5. amount
6. status
7. provider
8. provider_reference
9. paid_at
10. created_at
11. updated_at

### audit_logs

1. id
2. user_id
3. case_id
4. action
5. ip_address
6. user_agent
7. created_at

## 16. Security Requirements

1. Encrypt TIN values.
2. Mask TIN display.
3. Store uploaded files in private storage.
4. Use signed temporary URLs for document viewing.
5. Use role based access control.
6. Allow accountants to access assigned cases only.
7. Log admin and accountant actions.
8. Require two factor authentication for admin and accountant accounts.
9. Create a document retention policy.
10. Add consent checkbox before processing documents.
11. Add privacy policy.
12. Add terms of service.
13. Prepare a breach response process.
14. Use regular backups.
15. Never expose uploaded files through public URLs.
16. Prevent private pages from being indexed.

## 17. Disclaimers

Add these clearly in the app:

1. Open Case is not an official BIR website.
2. Open Case does not collect tax payments for BIR.
3. Results are preliminary and based only on user provided information.
4. Final tax advice must come from a qualified accountant, tax practitioner, lawyer, or BIR officer.
5. Uploaded documents are used only for case review and assistance.
6. Government taxes, penalties, and filing fees are separate from Open Case service fees.

## 18. Do Nots

1. Do not claim the app is connected to BIR unless formally authorized.
2. Do not show full TIN values after submission.
3. Do not store uploaded documents in public storage.
4. Do not let accountants see unassigned cases.
5. Do not guarantee that a business can be closed.
6. Do not guarantee exact penalties from rule based results.
7. Do not say "you owe this amount" based only on app rules.
8. Do not accept tax payment for BIR.
9. Do not scrape private BIR portals.
10. Do not ask users for BIR login credentials.
11. Do not use sample documents in marketing unless all personal data is redacted and permission is given.
12. Do not make AI the final decision maker for tax advice.
13. Do not make public registration available for accountants.

## 19. Suggested Pages

1. Landing page
2. How it works
3. Pricing
4. Login
5. Register
6. Start case
7. Business owner dashboard
8. Business profile page
9. Case result page
10. Upload documents page
11. Case detail page
12. Payment page
13. Accountant dashboard
14. Accountant case review page
15. Admin dashboard
16. Manage accountant accounts
17. Manage packages
18. Manage case rules
19. Privacy policy
20. Terms of service
21. Contact page

## 20. Future Roadmap

### Phase 2

1. Admin editable result rules
2. Better document tagging
3. Case comments with attachments
4. SMS notifications
5. More detailed reports
6. Client downloadable case summary

### Phase 3

1. OCR assisted document reading
2. AI assisted document classification
3. AI generated checklist suggestions
4. Advanced accountant workload reports
5. Recurring monthly compliance packages

### Phase 4

1. Optional mobile app
2. More payment methods
3. Multi accountant support
4. Team accounts for accounting firms, only if the controlled internal model is proven
