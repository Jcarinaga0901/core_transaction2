<?php
/**
 * SMS Gateway Integration
 * Supports multiple SMS providers (Semaphore, Twilio, etc.)
 */

class SMSGateway {
    private $provider;
    private $config;
    
    // SMS Provider Configuration
    const PROVIDERS = [
        'semaphore' => [
            'api_url' => 'https://api.semaphore.co/api/v4/messages',
            'api_key' => 'YOUR_SEMAPHORE_API_KEY', // Replace with your actual API key
            'sender_name' => 'RAEVOR'
        ],
        'twilio' => [
            'account_sid' => 'YOUR_TWILIO_ACCOUNT_SID',
            'auth_token' => 'YOUR_TWILIO_AUTH_TOKEN',
            'from_number' => '+1234567890'
        ],
        'mock' => [
            'enabled' => true // For testing without actual SMS
        ]
    ];
    
    public function __construct($provider = 'semaphore') {
        $this->provider = $provider;
        $this->config = self::PROVIDERS[$provider] ?? self::PROVIDERS['mock'];
    }
    
    /**
     * Send SMS OTP
     */
    public function sendOTP($phoneNumber, $otpCode, $purpose = 'login') {
        // Format phone number
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        
        // Generate message based on purpose
        $message = $this->generateMessage($otpCode, $purpose);
        
        // Send via selected provider
        switch ($this->provider) {
            case 'semaphore':
                return $this->sendViaSemaphore($phoneNumber, $message);
            
            case 'twilio':
                return $this->sendViaTwilio($phoneNumber, $message);
            
            case 'mock':
            default:
                return $this->sendViaMock($phoneNumber, $message, $otpCode);
        }
    }
    
    /**
     * Send via Semaphore SMS (Philippines)
     */
    private function sendViaSemaphore($phoneNumber, $message) {
        $ch = curl_init();
        
        $parameters = [
            'apikey' => $this->config['api_key'],
            'number' => $phoneNumber,
            'message' => $message,
            'sendername' => $this->config['sender_name']
        ];
        
        curl_setopt($ch, CURLOPT_URL, $this->config['api_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'provider' => 'semaphore'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to send OTP',
                'error' => $response
            ];
        }
    }
    
    /**
     * Send via Twilio (International)
     */
    private function sendViaTwilio($phoneNumber, $message) {
        $ch = curl_init();
        
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->config['account_sid']}/Messages.json";
        
        $data = [
            'From' => $this->config['from_number'],
            'To' => $phoneNumber,
            'Body' => $message
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['account_sid'] . ':' . $this->config['auth_token']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 201) {
            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'provider' => 'twilio'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to send OTP',
                'error' => $response
            ];
        }
    }
    
    /**
     * Mock SMS sender for testing (logs to console)
     */
    private function sendViaMock($phoneNumber, $message, $otpCode) {
        // Log to error log for testing
        error_log("=== MOCK SMS ===");
        error_log("To: $phoneNumber");
        error_log("Message: $message");
        error_log("OTP Code: $otpCode");
        error_log("================");
        
        return [
            'success' => true,
            'message' => 'OTP sent successfully (MOCK MODE)',
            'provider' => 'mock',
            'otp_code' => $otpCode, // Only in mock mode for testing
            'phone' => $phoneNumber
        ];
    }
    
    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phoneNumber) {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with country code (Philippines: +63)
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '63' . substr($phoneNumber, 1);
        }
        
        // Add + prefix if not present
        if (substr($phoneNumber, 0, 1) !== '+') {
            $phoneNumber = '+' . $phoneNumber;
        }
        
        return $phoneNumber;
    }
    
    /**
     * Generate message based on purpose
     */
    private function generateMessage($otpCode, $purpose) {
        $messages = [
            'login' => "Your RAEVOR login OTP is: {$otpCode}. Valid for 5 minutes. Do not share this code.",
            'phone_verification' => "Your RAEVOR phone verification code is: {$otpCode}. Valid for 10 minutes.",
            'password_reset' => "Your RAEVOR password reset code is: {$otpCode}. Valid for 10 minutes."
        ];
        
        return $messages[$purpose] ?? $messages['login'];
    }
    
    /**
     * Validate phone number format
     */
    public static function validatePhoneNumber($phoneNumber) {
        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Check if it's a valid Philippine mobile number
        // Should be 11 digits starting with 09 or 10 digits starting with 9
        if (preg_match('/^(09|9)\d{9}$/', $clean)) {
            return true;
        }
        
        // Check if it's in international format
        if (preg_match('/^\+?63\d{10}$/', $phoneNumber)) {
            return true;
        }
        
        return false;
    }
}


