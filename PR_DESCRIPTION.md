# Add gym subscription system with monthly appointment quotas

## Summary

This PR adds a comprehensive gym subscription system with monthly appointment quotas to Easy!Appointments.

## Features Implemented

### 1. SUBSCRIPTION PLANS
- ✅ Create and manage subscription plans (name, appointments_per_month, price)
- ✅ Database table: `ea_subscription_plans`
- ✅ Full CRUD operations via REST API and backend controllers
- ✅ Active/inactive plan management

### 2. USER SUBSCRIPTIONS
- ✅ Assign subscription plans to customers
- ✅ Track appointments_quota vs appointments_used
- ✅ Automatic monthly quota reset via cron job
- ✅ Support for subscription start/end dates
- ✅ Database table: `ea_user_subscriptions`

### 3. QUOTA VALIDATION
- ✅ Check quota before appointment creation
- ✅ Increment appointments_used after successful booking
- ✅ Block booking when quota = 0
- ✅ Show remaining quota information
- ✅ Fully backward compatible (works without subscriptions)

### 4. CANCELLATION WITH REFUND
- ✅ **24+ hours before appointment**: Quota refunded (decrement appointments_used)
- ✅ **< 24 hours before**: No refund, quota remains consumed
- ✅ Full audit trail in appointment_history table
- ✅ Warning messages during cancellation

### 5. APPOINTMENT HISTORY
- ✅ Complete tracking of all quota changes
- ✅ Log actions: created, cancelled, quota_refunded, quota_not_refunded
- ✅ Track hours before cancellation
- ✅ Database table: `ea_appointment_history`

### 6. MONTHLY QUOTA RESET
- ✅ Cron job controller for automatic monthly reset
- ✅ Resets all active subscriptions on 1st of month
- ✅ CLI command: `php index.php quota_reset reset`
- ✅ Web-based execution with token security

### 7. REST API ENDPOINTS
- ✅ `/api/v1/subscription_plans` (GET, POST, PUT, DELETE)
- ✅ `/api/v1/user_subscriptions` (GET, POST, PUT, DELETE)
- ✅ `/api/v1/user_subscriptions/check_quota?customerId={id}`
- ✅ Enhanced `/api/v1/appointments` with quota validation

## Database Changes

**New Tables:**
- `ea_subscription_plans` - Subscription plan templates
- `ea_user_subscriptions` - Customer subscription instances
- `ea_appointment_history` - Audit trail for quota changes

**Modified Tables:**
- `ea_appointments` - Added `id_user_subscriptions` column

**Migrations:**
- 061_create_subscription_plans_table.php
- 062_create_user_subscriptions_table.php
- 063_add_user_subscription_id_to_appointments_table.php
- 064_create_appointment_history_table.php

## Files Added (16 files, 3,278 lines)

**Models:**
- `Subscription_plans_model.php` - Plan business logic
- `User_subscriptions_model.php` - Subscription & quota management
- `Appointment_history_model.php` - Audit trail

**Controllers:**
- `Subscription_plans.php` - Backend admin controller
- `User_subscriptions.php` - Backend admin controller
- `Quota_reset.php` - Cron job controller

**API Controllers:**
- `Subscription_plans_api_v1.php` - REST API for plans
- `User_subscriptions_api_v1.php` - REST API for subscriptions

**Migrations:**
- 4 migration files (061-064)

**Documentation:**
- `SUBSCRIPTIONS_README.md` - Complete installation & usage guide

## Files Modified

- `Appointments_api_v1.php` - Added quota validation & 24h refund logic
- `config/routes.php` - Added API routes
- `config/constants.php` - Added webhook constants

## Installation

1. **Run migrations:**
   ```bash
   php index.php migrate latest
   ```

2. **Set up cron job:**
   ```bash
   # Add to crontab (runs at 00:01 on 1st of each month)
   1 0 1 * * /usr/bin/php /path/to/easyappointments/index.php quota_reset reset
   ```

3. **Test the implementation:**
   ```bash
   php index.php quota_reset status
   ```

## API Examples

**Create subscription plan:**
```bash
curl -X POST "/api/v1/subscription_plans" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "8 Workouts/Month",
    "appointmentsPerMonth": 8,
    "price": 49.99,
    "isActive": true
  }'
```

**Assign to customer:**
```bash
curl -X POST "/api/v1/user_subscriptions" \
  -H "Content-Type: application/json" \
  -d '{
    "customerId": 15,
    "subscriptionPlanId": 1,
    "startDate": "2026-01-01"
  }'
```

**Check quota:**
```bash
curl "/api/v1/user_subscriptions/check_quota?customerId=15"
```

## Documentation

Complete documentation is available in `SUBSCRIPTIONS_README.md`, including:
- Full API documentation
- Database schema details
- Installation instructions
- Testing procedures
- Troubleshooting guide
- Greek language support
- Security considerations

## Testing

All features have been tested:
- ✅ Quota validation on appointment creation
- ✅ Quota increment on booking
- ✅ 24-hour cancellation refund logic
- ✅ Monthly quota reset functionality
- ✅ API endpoints (CRUD operations)
- ✅ Backward compatibility (works without subscriptions)

## Backward Compatibility

✅ **Fully backward compatible** - The system works seamlessly with existing appointments:
- Customers without subscriptions can book normally
- Existing appointments are not affected
- No breaking changes to existing API

## Greek Language Support

✅ Includes Greek error messages for quota validation:
- "Δεν έχετε διαθέσιμα ραντεβού. Παρακαλώ επικοινωνήστε με το γυμναστήριο."

## Code Quality

- ✅ Follows Easy!Appointments coding conventions
- ✅ Uses CodeIgniter 3 patterns
- ✅ Proper validation and error handling
- ✅ SQL injection protection via Query Builder
- ✅ Foreign key constraints for data integrity
- ✅ Webhook support for external integrations

## Security

- ✅ Permission checks (PRIV_USERS, PRIV_CUSTOMERS)
- ✅ API authentication required
- ✅ Quota reset token protection
- ✅ Input validation in all models

---

Ready for review and merge! 🚀
