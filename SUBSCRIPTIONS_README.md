# Gym Subscription System for Easy!Appointments

## Overview

This extension adds a comprehensive gym subscription system with monthly appointment quotas to Easy!Appointments. Users can subscribe to plans that limit the number of appointments they can book per month, with automatic quota resets and cancellation refund logic.

## Features Implemented

### 1. Subscription Plans Management
- Create and manage subscription plans with custom appointment quotas and pricing
- Define monthly appointment limits per plan
- Activate/deactivate plans

### 2. User Subscriptions
- Assign subscription plans to customers
- Track quota usage (appointments used vs. available)
- Automatic monthly quota reset on the 1st of each month
- Support for subscription start/end dates

### 3. Quota Validation
- Automatic quota checking before appointment creation
- Block booking when quota is exhausted
- Real-time quota tracking

### 4. Cancellation with Refund Logic
- **24+ hours before appointment**: Quota is refunded
- **Less than 24 hours**: No refund, quota remains consumed
- Full audit trail in appointment history

### 5. Appointment History Tracking
- Complete audit log of all quota changes
- Track cancellations and refunds
- Historical reporting capabilities

### 6. REST API Endpoints
- Full CRUD operations for subscription plans
- Full CRUD operations for user subscriptions
- Quota checking endpoint
- Integrated with existing appointments API

---

## Database Schema

### Tables Created

#### 1. `ea_subscription_plans`
Stores subscription plan templates.

```sql
- id (INT, PRIMARY KEY)
- create_datetime (DATETIME)
- update_datetime (DATETIME)
- delete_datetime (DATETIME, NULL)
- name (VARCHAR 256) - Plan name (e.g., "8 Workouts/Month")
- description (TEXT, NULL) - Plan description
- appointments_per_month (INT) - Monthly appointment quota
- price (DECIMAL 10,2) - Monthly price
- is_active (TINYINT) - Active status
- notes (TEXT, NULL) - Additional notes
```

#### 2. `ea_user_subscriptions`
Stores customer subscription instances.

```sql
- id (INT, PRIMARY KEY)
- create_datetime (DATETIME)
- update_datetime (DATETIME)
- delete_datetime (DATETIME, NULL)
- id_users_customer (INT, FK -> ea_users.id) - Customer reference
- id_subscription_plans (INT, FK -> ea_subscription_plans.id) - Plan reference
- start_date (DATE) - Subscription start date
- end_date (DATE, NULL) - Subscription end date
- appointments_quota (INT) - Monthly quota (from plan)
- appointments_used (INT) - Used appointments this month
- last_quota_reset_date (DATE) - Last reset timestamp
- is_active (TINYINT) - Active status
- notes (TEXT, NULL) - Additional notes
```

#### 3. `ea_appointment_history`
Audit trail for appointment quota changes.

```sql
- id (INT, PRIMARY KEY)
- create_datetime (DATETIME)
- id_appointments (INT, FK -> ea_appointments.id)
- id_user_subscriptions (INT, FK -> ea_user_subscriptions.id)
- action (VARCHAR 50) - Action type: created, cancelled, quota_refunded, quota_not_refunded
- quota_change (INT) - Quota delta (+1, -1, 0)
- cancellation_hours_before (DECIMAL 10,2) - Hours before appointment when cancelled
- notes (TEXT, NULL) - Additional information
```

#### 4. Modified: `ea_appointments`
Added foreign key to link appointments to subscriptions.

```sql
- id_user_subscriptions (INT, FK -> ea_user_subscriptions.id, NULL)
```

---

## Installation Instructions

### Step 1: Run Database Migrations

Navigate to your Easy!Appointments installation directory and run:

```bash
# Assuming you have CLI access
php index.php migrate latest
```

Or manually run the migration files in order:
1. `application/migrations/061_create_subscription_plans_table.php`
2. `application/migrations/062_create_user_subscriptions_table.php`
3. `application/migrations/063_add_user_subscription_id_to_appointments_table.php`
4. `application/migrations/064_create_appointment_history_table.php`

### Step 2: Set Up Monthly Quota Reset Cron Job

Add this to your server's crontab to run quota resets on the 1st of each month at 00:01:

```bash
# Edit crontab
crontab -e

# Add this line (adjust path to your installation)
1 0 1 * * /usr/bin/php /var/www/html/easyappointments/index.php quota_reset reset >> /var/log/ea_quota_reset.log 2>&1
```

**Alternative:** Use a secret token for web-based execution:

1. Add a setting to your database or config:
```sql
INSERT INTO ea_settings (name, value) VALUES ('quota_reset_token', 'your-secret-token-here');
```

2. Call via HTTP (use a service like cron-job.org):
```bash
curl "https://your-domain.com/index.php/quota_reset/reset?token=your-secret-token-here"
```

### Step 3: Verify Installation

Check quota reset status:
```bash
php index.php quota_reset status
```

Expected output:
```
Quota Reset Status:
===================
Total Active Subscriptions: 0
Subscriptions Needing Reset: 0
Subscriptions Already Reset This Month: 0
Current Month: January 2026
```

---

## API Documentation

All API endpoints require authentication. Use the Easy!Appointments API authentication methods.

Base URL: `https://your-domain.com/api/v1/`

### Subscription Plans API

#### List All Plans
```http
GET /api/v1/subscription_plans
```

Response:
```json
[
  {
    "id": 1,
    "name": "8 Workouts/Month",
    "description": "Standard monthly plan",
    "appointmentsPerMonth": 8,
    "price": 49.99,
    "isActive": true,
    "notes": null
  }
]
```

#### Get Single Plan
```http
GET /api/v1/subscription_plans/{id}
```

#### Create Plan
```http
POST /api/v1/subscription_plans
Content-Type: application/json

{
  "name": "12 Workouts/Month",
  "description": "Premium monthly plan",
  "appointmentsPerMonth": 12,
  "price": 69.99,
  "isActive": true
}
```

#### Update Plan
```http
PUT /api/v1/subscription_plans/{id}
Content-Type: application/json

{
  "price": 59.99
}
```

#### Delete Plan
```http
DELETE /api/v1/subscription_plans/{id}
```

### User Subscriptions API

#### List All Subscriptions
```http
GET /api/v1/user_subscriptions
```

Query Parameters:
- `customerId` - Filter by customer ID

Response:
```json
[
  {
    "id": 1,
    "customerId": 15,
    "subscriptionPlanId": 1,
    "startDate": "2026-01-01",
    "endDate": null,
    "appointmentsQuota": 8,
    "appointmentsUsed": 3,
    "lastQuotaResetDate": "2026-01-01",
    "isActive": true,
    "notes": null
  }
]
```

#### Get Customer's Active Subscription
```http
GET /api/v1/user_subscriptions?customerId={id}
```

#### Check Customer Quota
```http
GET /api/v1/user_subscriptions/check_quota?customerId={id}
```

Response:
```json
{
  "has_quota": true,
  "subscription": { ... },
  "remaining": 5,
  "quota": 8,
  "used": 3,
  "message": "You have 5 appointments remaining."
}
```

#### Create Subscription
```http
POST /api/v1/user_subscriptions
Content-Type: application/json

{
  "customerId": 15,
  "subscriptionPlanId": 1,
  "startDate": "2026-01-01",
  "isActive": true
}
```

**Note:** `appointmentsQuota` is automatically set from the plan. `appointmentsUsed` defaults to 0.

#### Update Subscription
```http
PUT /api/v1/user_subscriptions/{id}
Content-Type: application/json

{
  "endDate": "2026-12-31",
  "isActive": false
}
```

#### Delete Subscription
```http
DELETE /api/v1/user_subscriptions/{id}
```

### Appointments API (Enhanced)

The existing appointments API has been enhanced with quota validation.

#### Create Appointment
```http
POST /api/v1/appointments
Content-Type: application/json

{
  "customerId": 15,
  "serviceId": 1,
  "providerId": 2,
  "start": "2026-01-15 10:00:00",
  "end": "2026-01-15 11:00:00"
}
```

**Behavior:**
- System checks if customer has active subscription
- If subscription exists and quota > 0: Appointment created, quota incremented
- If subscription exists and quota = 0: Error returned
- If no subscription: Appointment created normally (backward compatible)

Error Response (No Quota):
```json
{
  "error": "Δεν έχετε διαθέσιμα ραντεβού. Παρακαλώ επικοινωνήστε με το γυμναστήριο.",
  "code": 500
}
```

#### Cancel Appointment (with Quota Refund)
```http
DELETE /api/v1/appointments/{id}
```

**Refund Logic:**
- Cancelled **24+ hours before**: `appointments_used` decremented (quota refunded)
- Cancelled **< 24 hours before**: No refund, quota remains consumed
- All actions logged in `ea_appointment_history`

---

## Usage Workflow

### 1. Create Subscription Plans

```bash
# Using curl
curl -X POST "https://your-domain.com/api/v1/subscription_plans" \
  -H "Authorization: Basic YOUR_API_CREDENTIALS" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "8 Workouts/Month",
    "appointmentsPerMonth": 8,
    "price": 49.99,
    "isActive": true
  }'
```

### 2. Assign Subscription to Customer

```bash
curl -X POST "https://your-domain.com/api/v1/user_subscriptions" \
  -H "Authorization: Basic YOUR_API_CREDENTIALS" \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": 15,
    "subscriptionPlanId": 1,
    "startDate": "2026-01-01",
    "isActive": true
  }'
```

### 3. Customer Books Appointment

When a customer with an active subscription creates an appointment:
- Quota is checked automatically
- If quota available: Appointment created, `appointments_used` incremented
- If no quota: Error returned

### 4. Customer Cancels Appointment

When an appointment is deleted:
- If cancelled 24+ hours before: Quota refunded
- If cancelled < 24 hours: No refund
- Action logged in history

### 5. Monthly Quota Reset

On the 1st of each month (via cron):
- All active subscriptions have `appointments_used` reset to 0
- `last_quota_reset_date` updated to current date

---

## File Structure

```
application/
├── controllers/
│   ├── Subscription_plans.php              # Backend admin controller
│   ├── User_subscriptions.php              # Backend admin controller
│   ├── Quota_reset.php                     # Cron job controller
│   └── api/v1/
│       ├── Subscription_plans_api_v1.php   # REST API for plans
│       ├── User_subscriptions_api_v1.php   # REST API for subscriptions
│       └── Appointments_api_v1.php         # Modified with quota logic
├── models/
│   ├── Subscription_plans_model.php        # Plan business logic
│   ├── User_subscriptions_model.php        # Subscription logic & quota
│   └── Appointment_history_model.php       # Audit trail
├── migrations/
│   ├── 061_create_subscription_plans_table.php
│   ├── 062_create_user_subscriptions_table.php
│   ├── 063_add_user_subscription_id_to_appointments_table.php
│   └── 064_create_appointment_history_table.php
└── config/
    ├── routes.php                          # Added API routes
    └── constants.php                       # Added webhook constants
```

---

## Admin UI Integration (Optional)

To add admin UI for managing subscriptions, you can:

1. **Via API**: Use the REST API endpoints from any custom admin panel
2. **Via Backend**: Access the backend controllers at:
   - `/subscription_plans` - Manage plans
   - `/user_subscriptions` - Manage user subscriptions

**Note:** The backend controllers are created but require corresponding view files. You can either:
- Use the API endpoints directly
- Create custom view files following Easy!Appointments patterns
- Use the provided example below

---

## Example: Creating a Simple Admin Page

Create `application/views/pages/subscription_plans.php`:

```php
<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>
<div class="container-fluid backend-page">
    <h2>Subscription Plans</h2>
    <div id="subscription-plans-list"></div>
</div>

<script>
// Fetch and display plans
fetch('/api/v1/subscription_plans', {
    headers: {
        'Authorization': 'Basic ' + btoa(username + ':' + password)
    }
})
.then(response => response.json())
.then(plans => {
    const container = document.getElementById('subscription-plans-list');
    plans.forEach(plan => {
        container.innerHTML += `
            <div class="plan-item">
                <h3>${plan.name}</h3>
                <p>Appointments: ${plan.appointmentsPerMonth}/month</p>
                <p>Price: €${plan.price}</p>
            </div>
        `;
    });
});
</script>
<?php end_section(); ?>
```

---

## Testing the Implementation

### Test 1: Create a Subscription Plan
```bash
curl -X POST "http://localhost/easyappointments/api/v1/subscription_plans" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Plan",
    "appointmentsPerMonth": 5,
    "price": 25.00,
    "isActive": true
  }'
```

### Test 2: Assign Subscription to Customer
```bash
# First, get a customer ID from your database
curl -X POST "http://localhost/easyappointments/api/v1/user_subscriptions" \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": 1,
    "subscriptionPlanId": 1,
    "startDate": "2026-01-01"
  }'
```

### Test 3: Check Customer Quota
```bash
curl "http://localhost/easyappointments/api/v1/user_subscriptions/check_quota?customerId=1"
```

### Test 4: Book Appointments Until Quota Exhausted
```bash
# Book 5 appointments (should succeed)
# Book 6th appointment (should fail with quota error)
```

### Test 5: Cancel Appointment with Refund
```bash
# Delete appointment 24+ hours before
curl -X DELETE "http://localhost/easyappointments/api/v1/appointments/123"
# Check quota - should be refunded
```

### Test 6: Monthly Reset
```bash
php index.php quota_reset reset
# Verify all subscriptions reset to 0 used appointments
```

---

## Greek Language Support

The system includes Greek error messages for quota validation:

- **No quota remaining**: "Δεν έχετε διαθέσιμα ραντεβού. Παρακαλώ επικοινωνήστε με το γυμναστήριο."

To add more translations, edit your language file:
`application/language/greek/translations_lang.php`

```php
$lang['subscription_plans'] = 'Πακέτα Συνδρομής';
$lang['user_subscriptions'] = 'Συνδρομές Χρηστών';
$lang['appointments_quota'] = 'Όριο Ραντεβού';
$lang['appointments_used'] = 'Χρησιμοποιημένα';
$lang['no_quota_remaining'] = 'Δεν έχετε διαθέσιμα ραντεβού.';
$lang['quota_refunded'] = 'Το ραντεβού επιστράφηκε στο όριο σας.';
```

---

## Webhooks

The system triggers webhook events for:
- `subscription_plan_save`
- `subscription_plan_delete`
- `user_subscription_save`
- `user_subscription_delete`

Configure webhooks in Easy!Appointments settings to integrate with external systems.

---

## Security Considerations

1. **Quota Reset Token**: Set a strong secret token for web-based quota resets
2. **API Authentication**: All endpoints require Easy!Appointments API authentication
3. **Permission Checks**: Controllers verify user permissions (PRIV_USERS, PRIV_CUSTOMERS)
4. **SQL Injection Protection**: All queries use CodeIgniter Query Builder
5. **Foreign Key Constraints**: Database enforces referential integrity

---

## Troubleshooting

### Issue: Quota not resetting
**Solution:** Check cron job is running:
```bash
# Check cron logs
grep quota_reset /var/log/syslog

# Manually test reset
php index.php quota_reset status
php index.php quota_reset reset
```

### Issue: Appointments not checking quota
**Solution:** Verify customer has active subscription:
```bash
curl "http://localhost/easyappointments/api/v1/user_subscriptions?customerId=X"
```

### Issue: Database errors
**Solution:** Verify migrations ran successfully:
```bash
# Check migrations table
SELECT * FROM ea_migrations ORDER BY version DESC LIMIT 1;
# Should show version >= 64
```

### Issue: API returns 404
**Solution:** Verify routes are loaded:
```bash
# Check routes.php includes:
grep subscription_plans application/config/routes.php
```

---

## Future Enhancements

Potential features for future development:

1. **Rollover Quota**: Allow unused appointments to roll over to next month
2. **Multi-tier Pricing**: Different prices for different customer segments
3. **Promo Codes**: Discount codes for subscription purchases
4. **Subscription Pause**: Allow customers to pause/resume subscriptions
5. **Email Notifications**: Quota low warnings, reset notifications
6. **Analytics Dashboard**: Subscription revenue, usage statistics
7. **Auto-renewal**: Automatic subscription renewal on end date
8. **Payment Integration**: Stripe/PayPal integration for subscription payments

---

## Support

For issues or questions:
1. Check the Easy!Appointments documentation: https://easyappointments.org
2. Review migration logs in `application/logs/`
3. Check database for foreign key constraints and data integrity

---

## License

This extension follows the Easy!Appointments license (GPL-3.0).

---

## Credits

Developed as an extension to Easy!Appointments by Alex Tselegidis.
Subscription system implements gym-specific quota management with monthly resets and cancellation policies.
