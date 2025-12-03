# Quick Reference Guide
## Implementation Steps Summary

---

## 🎯 **MAIN FEATURES TO ADD**

1. ✅ Subscription & One-off Payment Options
2. ✅ User Dashboard (My Wills, My Subscription, Notifications)
3. ✅ PDF Upload & Viewing System
4. ✅ Full Notification System (User & Admin)
5. ✅ Controlled Access (One-off vs Subscriber)

---

## 📊 **DATABASE TABLES NEEDED**

| Table Name | Purpose |
|------------|---------|
| `wills` | Store user wills with status & plan type |
| `subscriptions` | Manage user subscriptions |
| `payments` | Track all payments |
| `will_pdfs` | Store uploaded PDF files |
| `notifications` | System notifications |
| `amendment_requests` | User amendment/update requests |

---

## 🔄 **WORKFLOW**

```
User Registration
    ↓
Complete Questionnaire
    ↓
Choose Plan (One-off £99 OR Yearly Subscription)
    ↓
Payment Processing (Stripe)
    ↓
Will Created (Status: Pending)
    ↓
Admin Reviews & Uploads PDF
    ↓
Will Status: Approved
    ↓
User Can View/Download PDF
    ↓
[If Subscriber] Can Request Amendments
    ↓
[If One-off] Upgrade Required for Updates
```

---

## 💳 **PAYMENT FLOW**

### One-off Payment:
1. User completes form
2. Pays £99 one-time
3. Gets will (read-only access)
4. No updates without upgrade

### Subscription Payment:
1. User completes form
2. Pays yearly subscription
3. Gets will (full access)
4. Can request amendments anytime
5. Auto-renewal with reminders

---

## 🔔 **NOTIFICATION TYPES**

### To Users:
- Will submission received
- Payment successful
- Will approved
- PDF uploaded
- Subscription renewal reminders (30, 14, 7, 1 days)
- Renewal success/failure
- Amendment request status
- Paywall alert (one-off users)

### To Admins:
- New will submission
- Payment received
- Subscription events
- Amendment requests
- User edits
- PDF upload confirmations
- Expiring subscriptions

---

## 🎨 **USER DASHBOARD SECTIONS**

### 1. My Wills
- List of all wills
- Status badges
- Plan type
- Actions: View, Download PDF, Request Amendment, Start New

### 2. My Subscription
- Active plan info
- Renewal date
- Payment history
- Cancel/Upgrade options

### 3. Notifications
- All notifications
- Unread count
- Mark as read
- Delete

---

## 👨‍💼 **ADMIN FEATURES**

- Dashboard stats (total wills, subscriptions, pending items)
- Upload PDF to user will
- Change will status
- View/Process amendment requests
- Manage subscriptions
- Send custom notifications
- View payment history

---

## 🔐 **ACCESS RULES**

| User Type | Can View Will | Can Download PDF | Can Request Amendment | Can Request Update |
|-----------|---------------|------------------|----------------------|-------------------|
| One-off | ✅ | ✅ | ❌ | ❌ (Upgrade Required) |
| Subscriber | ✅ | ✅ | ✅ | ✅ |

---

## 📁 **KEY FILES TO CREATE**

### Models:
- `app/Models/Will.php`
- `app/Models/Subscription.php`
- `app/Models/Payment.php`
- `app/Models/WillPdf.php`
- `app/Models/Notification.php`
- `app/Models/AmendmentRequest.php`

### Controllers:
- `app/Http/Controllers/NotificationController.php`
- `app/Http/Controllers/StripeWebhookController.php`

### Services:
- `app/Services/PaymentService.php`
- `app/Services/NotificationService.php`
- `app/Services/AccessControlService.php`

### Jobs:
- `app/Jobs/ProcessSubscriptionRenewal.php`

### Middleware:
- `app/Http/Middleware/CheckWillAccess.php`
- `app/Http/Middleware/ShareNotifications.php`

---

## 🚀 **IMPLEMENTATION PHASES**

### Phase 1: Database (Steps 1-7)
Create all migrations and models

### Phase 2: Payment System (Steps 14-18)
Enhance payment with subscription support

### Phase 3: User Dashboard (Steps 19-25)
Build user interface

### Phase 4: PDF System (Steps 26-30)
Upload and viewing

### Phase 5: Notifications (Steps 31-38)
Complete notification system

### Phase 6: Access Control (Steps 39-42)
Security and permissions

### Phase 7: Admin Panel (Steps 43-50)
Admin features

### Phase 8: Testing (Steps 51-56)
Testing and deployment

---

## ⚡ **QUICK START**

1. Read `IMPLEMENTATION_PLAN.md` for detailed steps
2. Start with Phase 1 (Database migrations)
3. Follow steps in order
4. Test each phase before moving to next
5. Deploy to staging first

---

**Ready to start? Begin with Step 1! 🎯**

