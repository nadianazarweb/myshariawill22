# Step-by-Step Implementation Plan
## Sharia Will - Subscription & Dashboard Features

---

## 📋 **OVERVIEW**

Yeh plan comprehensive features add karne ke liye hai:
1. Subscription aur One-off payment options
2. User dashboard to manage wills
3. PDF upload aur viewing system
4. Full admin aur user notifications
5. Controlled access for updates aur amendments

---

## 🗂️ **PHASE 1: DATABASE STRUCTURE** (Step 1-5)

### **Step 1: Wills Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_wills_table.php`

**Columns:**
- `id` (primary key)
- `user_id` (foreign key to users)
- `title` (string, nullable - user can name their will)
- `status` (enum: 'Draft', 'Pending', 'Approved', 'Completed', 'Expired')
- `plan_type` (enum: 'one-off', 'yearly')
- `submission_date` (datetime)
- `approval_date` (datetime, nullable)
- `expiry_date` (datetime, nullable - for subscriptions)
- `last_updated` (datetime)
- `created_at`, `updated_at`

**Actions:**
- Migration file create karo
- Model `Will.php` create karo with relationships

---

### **Step 2: Subscriptions Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_subscriptions_table.php`

**Columns:**
- `id` (primary key)
- `user_id` (foreign key)
- `will_id` (foreign key, nullable - can have subscription without will)
- `plan_type` (enum: 'one-off', 'yearly')
- `stripe_subscription_id` (string, nullable)
- `stripe_customer_id` (string, nullable)
- `status` (enum: 'active', 'cancelled', 'expired', 'past_due')
- `start_date` (date)
- `end_date` (date)
- `renewal_date` (date, nullable)
- `next_payment_date` (date, nullable)
- `amount` (decimal 10,2)
- `currency` (string, default 'gbp')
- `cancelled_at` (datetime, nullable)
- `created_at`, `updated_at`

**Actions:**
- Migration file create karo
- Model `Subscription.php` create karo

---

### **Step 3: Payments Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_payments_table.php`

**Columns:**
- `id` (primary key)
- `user_id` (foreign key)
- `will_id` (foreign key, nullable)
- `subscription_id` (foreign key, nullable)
- `payment_type` (enum: 'one-off', 'subscription', 'renewal', 'upgrade')
- `stripe_payment_intent_id` (string, nullable)
- `stripe_session_id` (string, nullable)
- `amount` (decimal 10,2)
- `currency` (string, default 'gbp')
- `status` (enum: 'pending', 'completed', 'failed', 'refunded')
- `payment_date` (datetime)
- `stripe_response` (text, nullable - JSON)
- `created_at`, `updated_at`

**Actions:**
- Migration file create karo
- Model `Payment.php` create karo

---

### **Step 4: Will PDFs Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_will_pdfs_table.php`

**Columns:**
- `id` (primary key)
- `will_id` (foreign key)
- `uploaded_by` (foreign key to users - admin who uploaded)
- `file_path` (string)
- `file_name` (string)
- `file_size` (integer, bytes)
- `version` (integer, default 1 - for multiple versions)
- `is_latest` (boolean, default true)
- `uploaded_at` (datetime)
- `created_at`, `updated_at`

**Actions:**
- Migration file create karo
- Model `WillPdf.php` create karo

---

### **Step 5: Notifications Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_notifications_table.php`

**Columns:**
- `id` (primary key)
- `user_id` (foreign key, nullable - null means admin notification)
- `type` (enum: 'info', 'success', 'warning', 'error')
- `title` (string)
- `message` (text)
- `related_type` (string, nullable - 'will', 'payment', 'subscription', 'amendment')
- `related_id` (integer, nullable)
- `is_read` (boolean, default false)
- `read_at` (datetime, nullable)
- `created_at`, `updated_at`

**Actions:**
- Migration file create karo
- Model `Notification.php` create karo

---

### **Step 6: Amendment Requests Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_amendment_requests_table.php`

**Columns:**
- `id` (primary key)
- `will_id` (foreign key)
- `user_id` (foreign key)
- `request_type` (enum: 'amendment', 'update')
- `status` (enum: 'pending', 'approved', 'declined', 'completed')
- `request_details` (text - JSON or text)
- `admin_notes` (text, nullable)
- `requested_at` (datetime)
- `processed_at` (datetime, nullable)
- `processed_by` (foreign key to users, nullable)
- `created_at`, `updated_at`

**Actions:**
- Migration file create karo
- Model `AmendmentRequest.php` create karo

---

### **Step 7: Update Users Table Migration**
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_add_subscription_fields_to_users_table.php`

**Add Columns:**
- `stripe_customer_id` (string, nullable)
- `default_payment_method` (string, nullable)
- `subscription_status` (enum: 'none', 'one-off', 'yearly', nullable)

**Actions:**
- Migration file create karo

---

## 🎨 **PHASE 2: MODELS & RELATIONSHIPS** (Step 8-13)

### **Step 8: Will Model**
**File:** `app/Models/Will.php`

**Relationships:**
- `belongsTo(User::class)`
- `hasMany(WillPdf::class)`
- `hasMany(AmendmentRequest::class)`
- `hasOne(Subscription::class)`
- `hasMany(Payment::class)`

**Methods:**
- `getStatusBadgeAttribute()` - status ke liye badge color
- `canRequestAmendment()` - check if user can request amendment
- `getLatestPdf()` - latest PDF return kare

---

### **Step 9: Subscription Model**
**File:** `app/Models/Subscription.php`

**Relationships:**
- `belongsTo(User::class)`
- `belongsTo(Will::class, nullable)`
- `hasMany(Payment::class)`

**Methods:**
- `isActive()` - check if subscription active hai
- `daysUntilRenewal()` - renewal tak kitne din
- `renew()` - renewal process
- `cancel()` - cancel subscription

---

### **Step 10: Payment Model**
**File:** `app/Models/Payment.php`

**Relationships:**
- `belongsTo(User::class)`
- `belongsTo(Will::class, nullable)`
- `belongsTo(Subscription::class, nullable)`

**Methods:**
- `isSuccessful()` - check if payment successful
- `getFormattedAmount()` - formatted amount return

---

### **Step 11: WillPdf Model**
**File:** `app/Models/WillPdf.php`

**Relationships:**
- `belongsTo(Will::class)`
- `belongsTo(User::class, 'uploaded_by')`

**Methods:**
- `getFileUrl()` - file URL return
- `getFileSizeHuman()` - human readable file size

---

### **Step 12: Notification Model**
**File:** `app/Models/Notification.php`

**Relationships:**
- `belongsTo(User::class, nullable)`

**Methods:**
- `markAsRead()` - mark as read
- `scopeUnread()` - unread notifications query
- `scopeForUser()` - user ke liye notifications

---

### **Step 13: AmendmentRequest Model**
**File:** `app/Models/AmendmentRequest.php`

**Relationships:**
- `belongsTo(Will::class)`
- `belongsTo(User::class)`
- `belongsTo(User::class, 'processed_by')`

**Methods:**
- `approve()` - approve request
- `decline()` - decline request
- `isPending()` - check if pending

---

## 💳 **PHASE 3: PAYMENT SYSTEM ENHANCEMENT** (Step 14-18)

### **Step 14: Payment Service Class**
**File:** `app/Services/PaymentService.php`

**Methods:**
- `createOneOffPayment($user, $will, $amount)` - one-off payment create
- `createSubscription($user, $will, $planType)` - subscription create
- `handlePaymentSuccess($sessionId)` - payment success handle
- `handlePaymentFailure($sessionId)` - payment failure handle
- `processRenewal($subscription)` - subscription renewal
- `cancelSubscription($subscription)` - cancel subscription
- `upgradeSubscription($subscription, $newPlan)` - upgrade subscription

**Actions:**
- Service class create karo
- Stripe integration improve karo
- Error handling add karo

---

### **Step 15: Update Payment Controller Methods**
**File:** `app/Http/Controllers/FrontEndController.php`

**Update Methods:**
- `take_to_stripe_checkout()` - plan type (one-off/yearly) support add karo
- `payment_status()` - will record create karo after payment
- New method: `choose_payment_plan()` - user ko plan choose karne do

**Actions:**
- Payment flow update karo
- Will record automatically create karo after successful payment

---

### **Step 16: Subscription Renewal Job**
**File:** `app/Jobs/ProcessSubscriptionRenewal.php`

**Purpose:**
- Automatic subscription renewal
- Reminder emails send (30, 14, 7, 1 days before)
- Failed payment handling

**Actions:**
- Job class create karo
- Schedule in `app/Console/Kernel.php`

---

### **Step 17: Payment History Feature**
**File:** `app/Http/Controllers/CustomerController.php`

**New Method:**
- `payment_history()` - user ki payment history show karo

**Actions:**
- Route add karo
- View create karo

---

### **Step 18: Stripe Webhook Handler**
**File:** `app/Http/Controllers/StripeWebhookController.php`

**Purpose:**
- Handle Stripe webhooks (subscription renewed, payment failed, etc.)
- Automatic updates

**Actions:**
- Controller create karo
- Route add karo (CSRF exempt)
- Webhook events handle karo

---

## 👤 **PHASE 4: USER DASHBOARD** (Step 19-25)

### **Step 19: Dashboard Controller Methods**
**File:** `app/Http/Controllers/CustomerController.php`

**New/Update Methods:**
- `dashboard()` - main dashboard with stats
- `my_wills()` - user ki sab wills list
- `will_detail($willId)` - specific will ka detail
- `my_subscription()` - subscription details
- `cancel_subscription()` - cancel subscription
- `upgrade_subscription()` - upgrade subscription

**Actions:**
- Methods implement karo
- Routes add karo

---

### **Step 20: My Wills View**
**File:** `resources/views/customer/my_wills.blade.php`

**Features:**
- Table with all wills
- Status badges
- Plan type display
- Last updated date
- Action buttons:
  - View Summary
  - Download PDF
  - Request Amendment (if subscribed)
  - Request Update (subscribers only)
  - Start New Will

**Actions:**
- View file create karo
- Styling add karo
- JavaScript for actions

---

### **Step 21: Will Detail View**
**File:** `resources/views/customer/will_detail.blade.php`

**Features:**
- Will information
- Status timeline
- PDF viewer/download
- Amendment history
- Request amendment form (if allowed)

**Actions:**
- View file create karo
- PDF viewer integrate karo (iframe or embed)

---

### **Step 22: My Subscription View**
**File:** `resources/views/customer/my_subscription.blade.php`

**Features:**
- Active plan display
- Renewal date
- Next payment date
- Payment history link
- Cancel/Upgrade buttons
- Subscription status

**Actions:**
- View file create karo
- Subscription management UI

---

### **Step 23: Start New Will Feature**
**File:** `app/Http/Controllers/CustomerController.php`

**New Method:**
- `start_new_will()` - new will form start karo with new payment

**Actions:**
- Method create karo
- Route add karo
- Payment flow integrate karo

---

### **Step 24: Request Amendment Feature**
**File:** `app/Http/Controllers/CustomerController.php`

**New Methods:**
- `request_amendment($willId)` - amendment request form
- `submit_amendment_request()` - submit amendment request
- `request_update($willId)` - update request

**Actions:**
- Methods create karo
- Forms create karo
- Validation add karo

---

### **Step 25: Update Dashboard Route**
**File:** `routes/web.php`

**Update:**
- Dashboard route ko new dashboard point karo
- All new routes add karo

---

## 📄 **PHASE 5: PDF MANAGEMENT** (Step 26-30)

### **Step 26: PDF Upload Controller Method**
**File:** `app/Http/Controllers/BackEndController.php`

**New Method:**
- `upload_will_pdf($willId)` - PDF upload karo
- `update_will_pdf($pdfId)` - PDF replace karo
- `delete_will_pdf($pdfId)` - PDF delete karo

**Actions:**
- Methods create karo
- File upload handling
- Storage path setup

---

### **Step 27: PDF Upload View (Admin)**
**File:** `resources/views/backend/upload_will_pdf.blade.php`

**Features:**
- File upload form
- Will information display
- Previous PDFs list
- Version management

**Actions:**
- View create karo
- Upload form design karo

---

### **Step 28: PDF Viewer Component**
**File:** `resources/views/components/pdf_viewer.blade.php`

**Features:**
- PDF display (iframe/embed)
- Download button
- Print button
- Version selector (if multiple versions)

**Actions:**
- Component create karo
- Reusable component banao

---

### **Step 29: PDF Storage Setup**
**File:** `config/filesystems.php`

**Actions:**
- Storage disk configure karo for PDFs
- Directory structure setup
- Permissions check

---

### **Step 30: PDF Download Route**
**File:** `routes/web.php`

**New Route:**
- `GET /will/{willId}/pdf/download` - secure PDF download
- `GET /will/{willId}/pdf/view` - PDF view

**Actions:**
- Routes add karo
- Security middleware add karo (user can only download their own PDFs)

---

## 🔔 **PHASE 6: NOTIFICATION SYSTEM** (Step 31-38)

### **Step 31: Notification Service**
**File:** `app/Services/NotificationService.php`

**Methods:**
- `notifyUser($userId, $type, $title, $message, $relatedType, $relatedId)` - user ko notify karo
- `notifyAdmin($type, $title, $message, $relatedType, $relatedId)` - admin ko notify karo
- `sendEmailNotification($user, $notification)` - email send karo
- `markAsRead($notificationId)` - mark as read

**Actions:**
- Service class create karo
- Email integration

---

### **Step 32: Notification Events**
**Files:**
- `app/Events/WillSubmitted.php`
- `app/Events/PaymentReceived.php`
- `app/Events/WillApproved.php`
- `app/Events/PdfUploaded.php`
- `app/Events/SubscriptionRenewed.php`
- `app/Events/AmendmentRequested.php`

**Actions:**
- Event classes create karo
- Event listeners create karo

---

### **Step 33: Notification Listeners**
**Files:**
- `app/Listeners/SendWillSubmittedNotification.php`
- `app/Listeners/SendPaymentNotification.php`
- `app/Listeners/SendWillApprovedNotification.php`
- `app/Listeners/SendPdfUploadedNotification.php`
- `app/Listeners/SendSubscriptionRenewalReminder.php`
- `app/Listeners/SendAmendmentRequestNotification.php`

**Actions:**
- Listener classes create karo
- `EventServiceProvider.php` mein register karo

---

### **Step 34: Notification Controller**
**File:** `app/Http/Controllers/NotificationController.php`

**Methods:**
- `index()` - all notifications list
- `markAsRead($id)` - mark single as read
- `markAllAsRead()` - mark all as read
- `delete($id)` - delete notification
- `getUnreadCount()` - unread count (AJAX)

**Actions:**
- Controller create karo
- Routes add karo

---

### **Step 35: Notification Views**
**Files:**
- `resources/views/customer/notifications.blade.php`
- `resources/views/components/notification_bell.blade.php`

**Features:**
- Notification list
- Unread count badge
- Mark as read functionality
- Delete functionality
- Real-time updates (optional - AJAX polling)

**Actions:**
- Views create karo
- Styling add karo

---

### **Step 36: Email Templates**
**Files:**
- `resources/views/emails/will_submitted.blade.php`
- `resources/views/emails/payment_successful.blade.php`
- `resources/views/emails/will_approved.blade.php`
- `resources/views/emails/pdf_uploaded.blade.php`
- `resources/views/emails/subscription_renewal_reminder.blade.php`
- `resources/views/emails/subscription_renewed.blade.php`
- `resources/views/emails/renewal_failed.blade.php`
- `resources/views/emails/amendment_requested.blade.php`
- `resources/views/emails/amendment_approved.blade.php`
- `resources/views/emails/paywall_alert.blade.php`

**Actions:**
- Email templates create karo
- Professional design

---

### **Step 37: Notification Middleware**
**File:** `app/Http/Middleware/ShareNotifications.php`

**Purpose:**
- Every request par unread notifications share karo
- View mein access karne ke liye

**Actions:**
- Middleware create karo
- `Kernel.php` mein register karo

---

### **Step 38: Scheduled Tasks for Reminders**
**File:** `app/Console/Kernel.php`

**Commands:**
- Daily check for subscriptions expiring in 30 days
- Daily check for subscriptions expiring in 14 days
- Daily check for subscriptions expiring in 7 days
- Daily check for subscriptions expiring in 1 day
- Process subscription renewals

**Actions:**
- Commands create karo
- Schedule in `Kernel.php`

---

## 🔐 **PHASE 7: ACCESS CONTROL** (Step 39-42)

### **Step 39: Access Control Middleware**
**File:** `app/Http/Middleware/CheckWillAccess.php`

**Purpose:**
- Check if user can access specific will
- Check if user can request amendments
- Check subscription status

**Actions:**
- Middleware create karo
- Logic implement karo

---

### **Step 40: Access Control Service**
**File:** `app/Services/AccessControlService.php`

**Methods:**
- `canViewWill($user, $will)` - can user view will?
- `canRequestAmendment($user, $will)` - can user request amendment?
- `canRequestUpdate($user, $will)` - can user request update?
- `isSubscriber($user)` - is user subscriber?
- `isOneOffUser($user)` - is user one-off?

**Actions:**
- Service class create karo
- Business logic implement karo

---

### **Step 41: Paywall Component**
**File:** `resources/views/components/paywall.blade.php`

**Purpose:**
- One-off users ko upgrade prompt show karo
- When they try to request update

**Actions:**
- Component create karo
- Upgrade CTA add karo

---

### **Step 42: Update Routes with Access Control**
**File:** `routes/web.php`

**Actions:**
- Middleware apply karo on sensitive routes
- Access control enforce karo

---

## 👨‍💼 **PHASE 8: ADMIN PANEL ENHANCEMENTS** (Step 43-50)

### **Step 43: Admin Dashboard Stats**
**File:** `app/Http/Controllers/BackEndController.php`

**Update Method:**
- `dashboard()` - add stats:
  - Total wills
  - Active subscriptions
  - Pending amendments
  - Expiring subscriptions (7 days)
  - Recent payments

**Actions:**
- Dashboard method update karo
- Stats calculate karo

---

### **Step 44: Admin Wills Management**
**File:** `app/Http/Controllers/BackEndController.php`

**New Methods:**
- `manage_wills()` - all wills list with filters
- `will_detail_admin($willId)` - will detail for admin
- `update_will_status($willId)` - update will status
- `view_amendment_requests()` - all amendment requests
- `process_amendment_request($requestId)` - approve/decline amendment

**Actions:**
- Methods create karo
- Routes add karo

---

### **Step 45: Admin Will Status Update**
**File:** `resources/views/backend/will_detail.blade.php`

**Features:**
- Will information
- Status dropdown (Draft, Pending, Approved, Completed)
- PDF upload section
- Amendment requests list
- User information
- Payment history

**Actions:**
- View create karo
- Status update form

---

### **Step 46: Admin PDF Upload Interface**
**File:** `resources/views/backend/upload_pdf.blade.php`

**Features:**
- File upload
- Will selection
- Version management
- Previous versions list

**Actions:**
- View create karo
- Upload interface design

---

### **Step 47: Admin Amendment Management**
**File:** `resources/views/backend/amendment_requests.blade.php`

**Features:**
- All requests list
- Filter by status
- Request details
- Approve/Decline buttons
- Admin notes field

**Actions:**
- View create karo
- Management interface

---

### **Step 48: Admin Subscription Management**
**File:** `app/Http/Controllers/BackEndController.php`

**New Methods:**
- `manage_subscriptions()` - all subscriptions
- `subscription_detail($id)` - subscription detail
- `cancel_subscription_admin($id)` - cancel subscription (admin)
- `renew_subscription_manual($id)` - manual renewal

**Actions:**
- Methods create karo
- Views create karo

---

### **Step 49: Admin Notification Management**
**File:** `app/Http/Controllers/BackEndController.php`

**New Methods:**
- `send_custom_notification()` - custom notification send
- `notification_history()` - notification history

**Actions:**
- Methods create karo
- Forms create karo

---

### **Step 50: Admin Reports & Analytics**
**File:** `app/Http/Controllers/BackEndController.php`

**New Methods:**
- `reports()` - reports page
- `export_wills()` - export wills data
- `revenue_report()` - revenue analytics

**Actions:**
- Methods create karo
- Reports generate karo

---

## 🧪 **PHASE 9: TESTING & VALIDATION** (Step 51-53)

### **Step 51: Form Validation**
**Files:**
- `app/Http/Requests/RequestAmendmentRequest.php`
- `app/Http/Requests/UploadPdfRequest.php`
- `app/Http/Requests/UpdateWillStatusRequest.php`

**Actions:**
- Form request classes create karo
- Validation rules add karo

---

### **Step 52: Unit Tests**
**Files:**
- `tests/Unit/WillTest.php`
- `tests/Unit/SubscriptionTest.php`
- `tests/Unit/PaymentTest.php`
- `tests/Unit/NotificationTest.php`

**Actions:**
- Test cases write karo
- Run tests

---

### **Step 53: Integration Testing**
**Files:**
- `tests/Feature/PaymentFlowTest.php`
- `tests/Feature/SubscriptionRenewalTest.php`
- `tests/Feature/AmendmentRequestTest.php`

**Actions:**
- Feature tests write karo
- End-to-end testing

---

## 🚀 **PHASE 10: DEPLOYMENT & FINALIZATION** (Step 54-56)

### **Step 54: Database Seeding**
**File:** `database/seeders/SubscriptionPlansSeeder.php`

**Purpose:**
- Default subscription plans seed karo
- Pricing setup

**Actions:**
- Seeder create karo
- Run seeder

---

### **Step 55: Environment Configuration**
**File:** `.env`

**Add:**
- Stripe webhook secret
- Subscription prices
- Email settings
- Storage paths

**Actions:**
- Configuration update karo
- Documentation update karo

---

### **Step 56: Documentation & Cleanup**
**Files:**
- `README.md` update
- Code comments add karo
- Unused code remove karo

**Actions:**
- Documentation complete karo
- Code cleanup
- Final review

---

## 📝 **IMPLEMENTATION ORDER SUMMARY**

### **Week 1: Foundation**
- Steps 1-7: Database migrations
- Steps 8-13: Models & relationships
- Steps 14-18: Payment system

### **Week 2: User Features**
- Steps 19-25: User dashboard
- Steps 26-30: PDF management
- Steps 31-38: Notifications

### **Week 3: Admin & Access**
- Steps 39-42: Access control
- Steps 43-50: Admin panel

### **Week 4: Testing & Polish**
- Steps 51-53: Testing
- Steps 54-56: Deployment

---

## ⚠️ **IMPORTANT NOTES**

1. **Backup:** Implementation se pehle database backup lo
2. **Staging:** Pehle staging environment mein test karo
3. **Stripe:** Webhook endpoints configure karo
4. **Email:** Email templates test karo
5. **Permissions:** File storage permissions check karo
6. **Security:** All routes secure karo, middleware use karo
7. **Performance:** Database indexes add karo where needed
8. **Error Handling:** Proper error handling add karo

---

## ✅ **CHECKLIST**

- [ ] All migrations created and tested
- [ ] All models created with relationships
- [ ] Payment system working (one-off & subscription)
- [ ] User dashboard complete
- [ ] PDF upload/view working
- [ ] Notifications system working
- [ ] Access control implemented
- [ ] Admin panel enhanced
- [ ] All tests passing
- [ ] Documentation complete

---

**Plan Complete! Ab hum step-by-step implement karein ge. Ready? 🚀**

