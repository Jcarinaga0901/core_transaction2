# Two-Factor Authentication (2FA) Setup Guide

## Overview
This 2FA system adds SMS OTP-based security to your RAEVOR seller account. Users will receive a 6-digit code via SMS when logging in or performing sensitive operations.

## Features
- ✅ SMS OTP verification via Semaphore or Twilio
- ✅ Phone number verification
- ✅ Backup codes for emergency access
- ✅ Rate limiting and account lockout protection
- ✅ Mock SMS mode for testing
- ✅ OTP expiry and attempt tracking
- ✅ Security logs and audit trail

## Installation Steps

### 1. Database Setup
Run the database setup script to create required tables:

```bash
php scripts/setup_2fa.php
```

This creates:
- `user_2fa_settings` - Stores user 2FA preferences
- `otp_codes` - Stores generated OTP codes
- `two_fa_attempts` - Logs 2FA attempts
- Adds `phone_number` column to `vendors` table

### 2. Configure SMS Gateway

Edit `api/sms_gateway.php` and configure your SMS provider:

#### Option A: Semaphore (Philippines)
```php
'semaphore' => [
    'api_url' => 'https://api.semaphore.co/api/v4/messages',
    'api_key' => 'YOUR_SEMAPHORE_API_KEY', // Get from semaphore.co
    'sender_name' => 'RAEVOR'
]
```

#### Option B: Twilio (International)
```php
'twilio' => [
    'account_sid' => 'YOUR_TWILIO_ACCOUNT_SID',
    'auth_token' => 'YOUR_TWILIO_AUTH_TOKEN',
    'from_number' => '+1234567890' // Your Twilio number
]
```

#### Option C: Mock Mode (Testing)
```php
'mock' => [
    'enabled' => true // Uses console logging instead of SMS
]
```

### 3. Update SMS Provider in Code

In `api/two_factor_auth.php`, change the SMS provider:

```php
// Line ~64
$smsGateway = new SMSGateway('semaphore'); // or 'twilio' or 'mock'
```

## How to Use

### For Users

#### Enable 2FA
1. Go to **Security & 2FA** page
2. Enter your Philippine mobile number (e.g., 9171234567)
3. Click **Enable 2FA**
4. Verify your phone with the OTP sent
5. Save your backup codes securely

#### Login with 2FA
1. Enter username and password
2. Receive 6-digit OTP via SMS
3. Enter OTP within 5 minutes
4. Access granted

#### Disable 2FA
1. Go to **Security & 2FA** page
2. Click **Disable 2FA**
3. Confirm action

### For Developers

#### Generate OTP
```php
$smsGateway = new SMSGateway('semaphore');
$result = $smsGateway->sendOTP('+639171234567', '123456', 'login');
```

#### Verify OTP
```javascript
fetch('api/two_factor_auth.php?action=verify_otp', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `user_id=${userId}&otp_code=${otpCode}&purpose=login`
})
.then(r => r.json())
.then(data => {
    if (data.success) {
        // OTP verified
    }
});
```

#### Check 2FA Status
```javascript
fetch(`api/two_factor_auth.php?action=check_2fa_status&user_id=${userId}`)
.then(r => r.json())
.then(data => {
    if (data.is_enabled) {
        // 2FA is enabled
    }
});
```

## API Endpoints

### POST /api/two_factor_auth.php?action=generate_otp
Generate and send OTP code
```
Parameters:
- user_id: User ID
- phone_number: Phone number (+63 format)
- purpose: login|phone_verification|password_reset
```

### POST /api/two_factor_auth.php?action=verify_otp
Verify OTP code
```
Parameters:
- user_id: User ID
- otp_code: 6-digit code
- purpose: login|phone_verification|password_reset
```

### POST /api/two_factor_auth.php?action=enable_2fa
Enable 2FA for user
```
Parameters:
- user_id: User ID
- phone_number: Phone number
```

### POST /api/two_factor_auth.php?action=disable_2fa
Disable 2FA for user
```
Parameters:
- user_id: User ID
```

### GET /api/two_factor_auth.php?action=check_2fa_status
Check 2FA status
```
Parameters:
- user_id: User ID
```

## Security Features

### Rate Limiting
- Max 3 OTP requests per 15 minutes per user
- Prevents spam and abuse

### Account Lockout
- 5 failed OTP attempts in 30 minutes = temporary lock
- Prevents brute force attacks

### OTP Expiry
- Login OTP: 5 minutes
- Verification OTP: 10 minutes

### Backup Codes
- 10 one-time use codes generated on enable
- Use if phone is lost or unavailable
- Can be regenerated

## Testing

### Mock Mode Testing
1. Set provider to 'mock' in `api/two_factor_auth.php`
2. OTP will be logged to console/error log
3. Check browser console or PHP error log

### Test Flow
1. Enable 2FA with any phone number
2. Click "Send Test OTP"
3. Check console for OTP code
4. Enter code in modal
5. Verify success

## SMS Provider Setup

### Semaphore (Philippines)
1. Sign up at https://semaphore.co
2. Purchase SMS credits
3. Get API key from dashboard
4. Add API key to config

### Twilio (International)
1. Sign up at https://twilio.com
2. Get Account SID and Auth Token
3. Purchase phone number
4. Add credentials to config

## Troubleshooting

### OTP not received
- Check SMS credits balance
- Verify phone number format
- Check SMS gateway credentials
- Review error logs

### OTP expired
- OTP expires after 5-10 minutes
- Request new OTP

### Account locked
- Wait 30 minutes
- Or contact admin to unlock

### Phone number issues
- Must be Philippine format: +639XXXXXXXXX
- Or 10 digits starting with 9

## Security Best Practices

1. **Never share OTP codes** - RAEVOR will never ask
2. **Keep backup codes safe** - Store offline
3. **Update phone number** - Keep it current
4. **Use strong passwords** - 2FA enhances, not replaces
5. **Log out on shared devices** - Prevent unauthorized access

## File Structure

```
CT2/
├── api/
│   ├── sms_gateway.php          # SMS provider integration
│   └── two_factor_auth.php      # 2FA API endpoints
├── scripts/
│   └── setup_2fa.php            # Database setup script
├── includes/
│   ├── header.php               # Header with settings menu
│   └── sidebar.php              # Sidebar with security link
├── security_settings.php        # 2FA management page
└── 2FA_SETUP_GUIDE.md          # This file
```

## Support

For issues or questions:
1. Check error logs in PHP error log
2. Verify SMS provider configuration
3. Test with mock mode first
4. Contact system administrator

## Version History

- **v1.0** - Initial SMS OTP 2FA implementation
- Features: SMS OTP, backup codes, rate limiting, security logging


