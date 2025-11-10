# Quick Start: SMS OTP 2FA Setup

## ✅ What's Been Created

I've implemented a complete SMS OTP-based Two-Factor Authentication (2FA) system for your RAEVOR seller platform with the following components:

### Files Created:
1. **scripts/setup_2fa.php** - Database setup script
2. **api/sms_gateway.php** - SMS provider integration (Semaphore/Twilio/Mock)
3. **api/two_factor_auth.php** - 2FA API endpoints
4. **security_settings.php** - User-facing 2FA management page
5. **includes/header.php** - Header with security settings link
6. **includes/sidebar.php** - Sidebar with 2FA navigation
7. **2FA_SETUP_GUIDE.md** - Comprehensive setup guide

## 🚀 Setup Instructions

### Step 1: Start MySQL Server
```
1. Open XAMPP Control Panel
2. Click "Start" for MySQL
3. Wait for green indicator
```

### Step 2: Run Database Setup
```bash
C:\xampp\php\php.exe scripts/setup_2fa.php
```

This creates 4 new tables:
- `user_2fa_settings` - User 2FA preferences
- `otp_codes` - Generated OTP codes
- `two_fa_attempts` - Security logs
- Adds `phone_number` column to `vendors` table

### Step 3: Configure SMS Provider

Edit `api/sms_gateway.php`:

**For Testing (Recommended First):**
```php
// Line 67 in api/two_factor_auth.php
$smsGateway = new SMSGateway('mock'); // Already set!
```

**For Production (Semaphore - Philippines):**
```php
'semaphore' => [
    'api_key' => 'YOUR_API_KEY_HERE', // Get from semaphore.co
    'sender_name' => 'RAEVOR'
]
```

Then change line 67:
```php
$smsGateway = new SMSGateway('semaphore');
```

### Step 4: Access 2FA Settings

1. Login to your seller account
2. Click the **gear icon** (⚙️) in the header
3. Select **"Security & 2FA"**
4. Or go directly to: `http://localhost/CT2/security_settings.php`

## 📱 How It Works

### Enable 2FA:
1. Enter your phone number (e.g., 9171234567)
2. Click **"Enable 2FA"**
3. Receive OTP via SMS (or console in mock mode)
4. Enter OTP to verify
5. Save backup codes

### Login with 2FA (When Integrated):
1. Enter username/password
2. Receive 6-digit OTP via SMS
3. Enter OTP within 5 minutes
4. Access granted

## 🔧 Testing in Mock Mode

### Enable 2FA:
1. Go to `security_settings.php`
2. Enter any 10-digit number (e.g., 9171234567)
3. Click "Enable 2FA"
4. **Check browser console** for OTP code (or popup alert)
5. Enter the OTP code
6. Verify success

### Test OTP:
1. Click "Send Test OTP" button
2. Check console/alert for OTP code
3. Enter code in modal
4. Success!

## 📋 Features Included

✅ **SMS OTP Generation** - 6-digit codes
✅ **Multiple SMS Providers** - Semaphore (PH), Twilio (International), Mock
✅ **Phone Verification** - Verify phone before enabling
✅ **Backup Codes** - 10 emergency codes
✅ **Rate Limiting** - Max 3 OTP per 15 minutes
✅ **Account Lockout** - 5 failed attempts = 30min lock
✅ **OTP Expiry** - 5 minutes (login), 10 minutes (verification)
✅ **Security Logs** - Track all 2FA attempts
✅ **Mock Mode** - Test without actual SMS
✅ **Beautiful UI** - Matches your system design

## 🔐 Security Features

| Feature | Protection |
|---------|-----------|
| Rate Limiting | 3 OTP requests per 15 min |
| Account Lockout | 5 failed attempts = 30 min lock |
| OTP Expiry | 5-10 minutes validity |
| Attempt Tracking | Max 3 attempts per OTP |
| Backup Codes | Emergency access (10 codes) |
| IP Logging | Track login locations |

## 📱 SMS Provider Setup

### Semaphore (Philippines) - Recommended
1. Go to https://semaphore.co
2. Sign up for account
3. Purchase SMS credits (₱1 per SMS)
4. Get API key from dashboard
5. Add to `api/sms_gateway.php`

### Twilio (International)
1. Go to https://twilio.com
2. Sign up and verify account
3. Get Account SID + Auth Token
4. Purchase phone number
5. Add credentials to config

## 🎨 UI Preview

The security settings page includes:
- Current 2FA status badge
- Phone number display with verification status
- Enable/Disable buttons
- Test OTP functionality
- Backup codes display and download
- Security tips section

## 🔗 Integration Points

### Add to Login Flow (Future):
```php
// After username/password verification
if ($user['2fa_enabled']) {
    // Send OTP
    // Show OTP input page
    // Verify OTP
    // Grant access
}
```

### Add to Sensitive Operations:
```php
// Before password change, withdrawal, etc.
if ($user['2fa_enabled']) {
    // Request OTP verification
}
```

## 🐛 Troubleshooting

### Database Setup Failed
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Run setup script manually

### OTP Not Received (Production)
- Check SMS credits balance
- Verify API key is correct
- Check phone number format (+639XXXXXXXXX)
- Review error logs

### Mock Mode Issues
- OTP appears in browser console
- Also shows as alert popup (for easy testing)
- Check PHP error log if not appearing

## 📞 Phone Number Formats

**Accepted Formats:**
- `9171234567` (10 digits starting with 9)
- `09171234567` (11 digits starting with 09)
- `+639171234567` (International format)

**System Converts To:**
- `+639171234567` (Stored format)

## 🎯 Next Steps

1. ✅ Start MySQL server
2. ✅ Run `scripts/setup_2fa.php`
3. ✅ Access `security_settings.php`
4. ✅ Test in mock mode
5. 🔲 Get Semaphore API key (optional)
6. 🔲 Configure production SMS
7. 🔲 Integrate with login flow
8. 🔲 Test with real users

## 💡 Tips

- **Start with Mock Mode** - Test flow without SMS costs
- **Save Backup Codes** - Users should download them
- **Monitor Attempts** - Check `two_fa_attempts` table
- **Clear Old OTPs** - Cleanup expired codes periodically
- **Test Lockout** - Verify security works

## 📝 Database Schema

### user_2fa_settings
```sql
- id, user_id, is_enabled
- phone_number, phone_verified
- backup_codes (JSON)
- created_at, updated_at
```

### otp_codes
```sql
- id, user_id, phone_number
- otp_code, purpose
- is_used, attempts
- expires_at, created_at
```

### two_fa_attempts
```sql
- id, user_id, ip_address
- attempt_type, status
- created_at
```

## ✨ Ready to Use!

Once MySQL is running and setup script completes, your 2FA system is fully functional! 

Access it at: `http://localhost/CT2/security_settings.php`

---

Need help? Check the full guide: `2FA_SETUP_GUIDE.md`


