<?php
require_once 'includes/auth.php';
if (!isAuthenticated()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

require_once 'config/database.php';
$db = getCT1Connection();

// Get current 2FA settings
$stmt = $db->prepare("SELECT * FROM user_2fa_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$twofa_settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's phone number from vendors table
$stmt = $db->prepare("SELECT phone_number FROM vendors WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings - RAEVOR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        /* Main content styling to match delivery.php */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: calc(100vh - 60px);
            background: #f8f9fa;
        }
        
        .sidebar-collapsed .main-content {
            margin-left: 60px;
        }
        
        .security-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        
        .security-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .security-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }
        
        .security-icon i {
            font-size: 1.8rem;
            color: white;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-enabled {
            background: #d4edda;
            color: #155724;
        }
        
        .status-disabled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .backup-codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .backup-code {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            border: 2px dashed #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Include header and sidebar from another page -->
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <h2 class="page-title" style="color: #212529; font-weight: 700; font-size: 2rem;">Security Settings</h2>
                <p class="page-subtitle" style="color: #6c757d; font-size: 1rem;">Manage your account security and two-factor authentication</p>
            </div>

            <!-- 2FA Status Card -->
            <div class="security-card">
                <div class="security-header">
                    <div class="security-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 style="margin: 0; font-weight: 600;">Two-Factor Authentication (2FA)</h4>
                        <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 0.9rem;">Add an extra layer of security to your account with SMS OTP</p>
                    </div>
                    <div>
                        <?php if ($twofa_settings && $twofa_settings['is_enabled']): ?>
                            <span class="status-badge status-enabled">
                                <i class="bi bi-check-circle me-1"></i>Enabled
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-disabled">
                                <i class="bi bi-x-circle me-1"></i>Disabled
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($twofa_settings && $twofa_settings['is_enabled']): ?>
                    <!-- 2FA Enabled State -->
                    <div class="alert alert-success">
                        <i class="bi bi-shield-check me-2"></i>
                        <strong>2FA is Active!</strong> Your account is protected with SMS verification.
                    </div>

                    <div class="mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 10px;">Registered Phone Number:</h6>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-phone me-2" style="font-size: 1.2rem; color: #28a745;"></i>
                            <span style="font-weight: 500; font-size: 1.1rem;"><?php echo htmlspecialchars($twofa_settings['phone_number']); ?></span>
                            <?php if ($twofa_settings['phone_verified']): ?>
                                <span class="badge bg-success ms-2">
                                    <i class="bi bi-patch-check me-1"></i>Verified
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning ms-2">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Not Verified
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="testOTP()">
                            <i class="bi bi-send me-2"></i>Send Test OTP
                        </button>
                        <button class="btn btn-outline-secondary" onclick="changePhoneNumber()">
                            <i class="bi bi-pencil me-2"></i>Change Phone Number
                        </button>
                        <button class="btn btn-danger" onclick="disable2FA()">
                            <i class="bi bi-shield-x me-2"></i>Disable 2FA
                        </button>
                    </div>

                <?php else: ?>
                    <!-- 2FA Disabled State -->
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>2FA is not enabled.</strong> Enable it now to secure your account with SMS verification.
                    </div>

                    <div class="mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 15px;">Enable Two-Factor Authentication:</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="phoneNumber" 
                                           placeholder="9171234567" 
                                           value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>"
                                           maxlength="10">
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter your 10-digit mobile number (e.g., 9171234567)
                                </small>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success btn-lg" onclick="enable2FA()">
                        <i class="bi bi-shield-check me-2"></i>Enable 2FA
                    </button>
                <?php endif; ?>
            </div>

            <!-- Backup Codes Card (shown only when 2FA is enabled) -->
            <?php if ($twofa_settings && $twofa_settings['is_enabled'] && $twofa_settings['backup_codes']): ?>
            <div class="security-card">
                <div class="security-header">
                    <div class="security-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="bi bi-key"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 style="margin: 0; font-weight: 600;">Backup Codes</h4>
                        <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 0.9rem;">Use these codes if you lose access to your phone</p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Save these backup codes in a secure location. Each code can only be used once.
                </div>

                <div class="backup-codes-grid">
                    <?php
                    $backupCodes = json_decode($twofa_settings['backup_codes'], true);
                    foreach ($backupCodes as $code):
                    ?>
                        <div class="backup-code"><?php echo $code; ?></div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-3">
                    <button class="btn btn-outline-primary" onclick="downloadBackupCodes()">
                        <i class="bi bi-download me-2"></i>Download Codes
                    </button>
                    <button class="btn btn-outline-secondary" onclick="regenerateBackupCodes()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Regenerate Codes
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Security Tips -->
            <div class="security-card">
                <h5 style="font-weight: 600; margin-bottom: 15px;">
                    <i class="bi bi-lightbulb me-2" style="color: #ffc107;"></i>
                    Security Tips
                </h5>
                <ul style="color: #6c757d; line-height: 1.8;">
                    <li>Never share your OTP code with anyone</li>
                    <li>RAEVOR will never ask for your OTP via phone or email</li>
                    <li>Keep your backup codes in a secure location</li>
                    <li>Update your phone number if it changes</li>
                    <li>Enable 2FA on all your important accounts</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-3">
                        We've sent a 6-digit code to your phone<br>
                        <strong id="maskedPhone"></strong>
                    </p>
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-lg text-center" 
                               id="otpCodeInput" 
                               placeholder="000000" 
                               maxlength="6"
                               style="font-size: 1.5rem; letter-spacing: 10px;">
                    </div>
                    <div class="text-center">
                        <small class="text-muted">
                            Didn't receive the code? 
                            <a href="javascript:void(0)" onclick="resendOTP()">Resend OTP</a>
                        </small>
                    </div>
                    <div id="otpError" class="alert alert-danger mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="verifyOTPCode()">
                        <i class="bi bi-check-circle me-2"></i>Verify
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const userId = <?php echo $user_id; ?>;
        let currentAction = '';
        let otpModal = null;

        document.addEventListener('DOMContentLoaded', function() {
            otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
        });

        // Enable 2FA
        function enable2FA() {
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            
            if (!phoneNumber) {
                alert('Please enter your phone number');
                return;
            }
            
            if (phoneNumber.length !== 10 || !/^9\d{9}$/.test(phoneNumber)) {
                alert('Please enter a valid 10-digit mobile number starting with 9');
                return;
            }
            
            if (confirm('Enable two-factor authentication with this phone number?')) {
                fetch('api/two_factor_auth.php?action=enable_2fa', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `user_id=${userId}&phone_number=+63${phoneNumber}`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('2FA enabled successfully! Please verify your phone number.');
                        sendVerificationOTP('+63' + phoneNumber);
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        // Disable 2FA
        function disable2FA() {
            if (confirm('Are you sure you want to disable two-factor authentication? This will make your account less secure.')) {
                fetch('api/two_factor_auth.php?action=disable_2fa', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `user_id=${userId}`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('2FA has been disabled');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        // Send verification OTP
        function sendVerificationOTP(phoneNumber) {
            fetch('api/two_factor_auth.php?action=generate_otp', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `user_id=${userId}&phone_number=${phoneNumber}&purpose=phone_verification`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentAction = 'verify_phone';
                    document.getElementById('maskedPhone').textContent = maskPhone(phoneNumber);
                    otpModal.show();
                    // Show OTP in console for testing (MOCK MODE)
                    if (data.debug && data.debug.otp_code) {
                        console.log('TEST OTP:', data.debug.otp_code);
                        alert('TEST MODE: OTP is ' + data.debug.otp_code);
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Test OTP
        function testOTP() {
            const phoneNumber = '<?php echo $twofa_settings['phone_number'] ?? ''; ?>';
            
            fetch('api/two_factor_auth.php?action=generate_otp', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `user_id=${userId}&phone_number=${phoneNumber}&purpose=login`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentAction = 'test';
                    document.getElementById('maskedPhone').textContent = maskPhone(phoneNumber);
                    otpModal.show();
                    // Show OTP in console for testing (MOCK MODE)
                    if (data.debug && data.debug.otp_code) {
                        console.log('TEST OTP:', data.debug.otp_code);
                        alert('TEST MODE: OTP is ' + data.debug.otp_code);
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Verify OTP Code
        function verifyOTPCode() {
            const otpCode = document.getElementById('otpCodeInput').value.trim();
            const errorDiv = document.getElementById('otpError');
            
            if (otpCode.length !== 6) {
                errorDiv.textContent = 'Please enter a 6-digit OTP code';
                errorDiv.style.display = 'block';
                return;
            }
            
            const purpose = currentAction === 'verify_phone' ? 'phone_verification' : 'login';
            
            fetch('api/two_factor_auth.php?action=' + (currentAction === 'verify_phone' ? 'verify_phone' : 'verify_otp'), {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `user_id=${userId}&otp_code=${otpCode}&purpose=${purpose}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    otpModal.hide();
                    alert('OTP verified successfully!');
                    location.reload();
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                }
            });
        }

        // Resend OTP
        function resendOTP() {
            alert('Sending new OTP code...');
            if (currentAction === 'verify_phone') {
                const phoneNumber = '<?php echo $twofa_settings['phone_number'] ?? ''; ?>';
                sendVerificationOTP(phoneNumber);
            } else {
                testOTP();
            }
        }

        // Mask phone number
        function maskPhone(phone) {
            if (phone.length > 6) {
                return phone.substring(0, 6) + 'xxxx' + phone.substring(phone.length - 2);
            }
            return phone;
        }

        // Download backup codes
        function downloadBackupCodes() {
            const codes = <?php echo json_encode($backupCodes ?? []); ?>;
            const text = 'RAEVOR Backup Codes\n' +
                        'Keep these codes safe!\n\n' +
                        codes.join('\n');
            
            const blob = new Blob([text], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'raevor-backup-codes.txt';
            a.click();
        }

        // Auto-format OTP input
        document.getElementById('otpCodeInput')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>


