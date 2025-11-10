<?php
// Enhanced session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);

session_start();

// Include database configuration
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: index.php');
    exit();
}

$success = false;
$error = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    try {
        $pdo = getDBConnection();
        
        // Get and sanitize form data
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $brand_name = trim($_POST['brand_name'] ?? '');
        $id_type = trim($_POST['id_type'] ?? '');
        $id_number = trim($_POST['id_number'] ?? '');
        
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($brand_name) || empty($id_type)) {
            $error = 'Please fill in all required fields including ID verification.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters long.';
        } elseif (!isset($_FILES['id_document']) || $_FILES['id_document']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Please upload a valid ID document.';
        } else {
            // Handle ID document upload
            $id_file = $_FILES['id_document'];
            $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($id_file['type'], $allowed_types)) {
                $error = 'ID document must be a JPG, PNG, or PDF file.';
            } elseif ($id_file['size'] > $max_size) {
                $error = 'ID document must be smaller than 5MB.';
            } else {
                // Create upload directory if it doesn't exist
                $upload_dir = 'uploads/id_documents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $file_extension = pathinfo($id_file['name'], PATHINFO_EXTENSION);
                $id_filename = 'id_' . time() . '_' . uniqid() . '.' . $file_extension;
                $id_file_path = $upload_dir . $id_filename;
                
                // Move uploaded file
                if (move_uploaded_file($id_file['tmp_name'], $id_file_path)) {
                    // Check if username already exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        $error = 'Username already taken. Please choose another.';
                    } else {
                        // Check if email already exists
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        if ($stmt->fetch()) {
                            $error = 'Email already registered. Please use another email or login.';
                        } else {
                            // Start transaction
                            $pdo->beginTransaction();
                            
                            try {
                                // Create vendor/brand for this seller
                                $stmt = $pdo->prepare("INSERT INTO vendors (name, email, phone, created_at) VALUES (?, ?, ?, NOW())");
                                $stmt->execute([$brand_name, $email, $phone]);
                                $vendor_id = $pdo->lastInsertId();
                                
                                // Hash password
                                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                
                                // Check if ID verification columns exist
                                $columns_check = $pdo->query("SHOW COLUMNS FROM users LIKE 'id_type'");
                                
                                if ($columns_check->rowCount() > 0) {
                                    // Create user account with ID verification data
                                    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, phone, is_verified, role, vendor_id, id_type, id_number, id_document_path, created_at) 
                                                          VALUES (?, ?, ?, ?, ?, 0, 'seller', ?, ?, ?, ?, NOW())");
                                    $stmt->execute([$username, $hashed_password, $email, $full_name, $phone, $vendor_id, $id_type, $id_number, $id_file_path]);
                                } else {
                                    // Create user account without ID verification data (fallback)
                                    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, phone, is_verified, role, vendor_id, created_at) 
                                                          VALUES (?, ?, ?, ?, ?, 0, 'seller', ?, NOW())");
                                    $stmt->execute([$username, $hashed_password, $email, $full_name, $phone, $vendor_id]);
                                    
                                    // Log that ID verification columns are missing
                                    error_log("Warning: ID verification columns missing in users table. Registration completed without ID verification.");
                                }
                                
                                // Commit transaction
                                $pdo->commit();
                                
                                $success = true;
                                
                                // Log successful registration
                                error_log("New seller registered: $username (Brand: $brand_name, Vendor ID: $vendor_id) - ID Type: $id_type");
                                
                            } catch (Exception $e) {
                                // Rollback on error
                                $pdo->rollBack();
                                throw $e;
                            }
                        }
                    }
                } else {
                    $error = 'Failed to upload ID document. Please try again.';
                }
            }
        }
    } catch (PDOException $e) {
        $error = 'Registration failed. Please try again. ' . $e->getMessage();
        error_log("Registration error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAEVOR - Become a Seller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .register-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            display: flex;
            overflow: hidden;
            min-height: 600px;
        }

        .info-panel {
            flex: 1;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 30px 30px, 20px 20px;
        }

        .info-panel::after {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .info-panel h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffffff;
            margin: 0 0 30px 0;
            position: relative;
            z-index: 1;
        }

        .info-panel p {
            color: #ffffff;
            font-size: 1.1rem;
            margin: 0 0 40px 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .benefits-list li {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            color: #ffffff;
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .benefits-list li:hover {
            transform: translateX(5px);
        }

        .benefits-list li i {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .register-panel {
            flex: 1.5;
            padding: 50px 40px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .register-logo {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .register-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .register-subtitle {
            color: #718096;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px 18px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            border-color: #6c757d;
            box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.1);
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }

        .btn-register {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            border-radius: 12px;
            padding: 18px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.4);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 18px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: rgba(209, 231, 221, 0.9);
            color: #0f5132;
            border: 1px solid rgba(15, 81, 50, 0.2);
        }

        .alert-danger {
            background: rgba(248, 215, 218, 0.9);
            color: #721c24;
            border: 1px solid rgba(114, 28, 36, 0.2);
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #718096;
        }

        .login-link a {
            color: #6c757d;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #495057;
            text-decoration: underline;
        }

        .success-box {
            text-align: center;
            padding: 50px;
        }

        .success-box i {
            font-size: 5rem;
            color: #22c55e;
            margin-bottom: 25px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .success-box h3 {
            color: #0f5132;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: 700;
        }

        /* ID Verification Section */
        .id-verification {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
            position: relative;
            overflow: hidden;
        }

        .id-verification::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #6c757d 0%, #495057 100%);
        }

        .id-verification h6 {
            color: #6c757d;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        .id-verification .text-muted {
            color: #718096 !important;
        }

        .alert-warning {
            background: rgba(255, 243, 205, 0.9);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                max-width: 500px;
                margin: 10px;
            }
            
            .info-panel {
                flex: none;
                min-height: 300px;
                padding: 40px 30px;
            }
            
            .register-panel {
                flex: none;
                padding: 40px 30px;
            }

            .info-panel h1 {
                font-size: 2.2rem;
            }

            .register-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .register-container {
                margin: 5px;
                border-radius: 15px;
            }
            
            .info-panel, .register-panel {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Information Panel -->
        <div class="info-panel">
            <h1>Become a Seller</h1>
            <p>Start your online clothing business with RAEVOR today!</p>
            <ul class="benefits-list">
                <li><i class="bi bi-check-circle"></i> Create your own brand</li>
                <li><i class="bi bi-check-circle"></i> Manage products easily</li>
                <li><i class="bi bi-check-circle"></i> Track orders & sales</li>
                <li><i class="bi bi-check-circle"></i> Real-time analytics</li>
                <li><i class="bi bi-check-circle"></i> Secure payments</li>
                <li><i class="bi bi-check-circle"></i> 24/7 seller support</li>
            </ul>
        </div>

        <!-- Registration Panel -->
        <div class="register-panel">
            <div class="register-logo">RAEVOR</div>
            <h2 class="register-title">Create Seller Account</h2>
            <p class="register-subtitle">Join our marketplace and start selling</p>

            <?php if ($success): ?>
                <div class="success-box">
                    <i class="bi bi-check-circle-fill"></i>
                    <h3>Registration Successful!</h3>
                    <p class="mb-3">Your seller account has been created successfully.</p>
                    <div class="alert alert-info" style="max-width: 500px; margin: 0 auto 20px;">
                        <i class="bi bi-clock me-2"></i>
                        <strong>ID Verification Pending:</strong> Your account is under review. You'll receive an email once your ID is verified and your account is activated.
                    </div>
                    <a href="login.php" class="btn btn-register" style="max-width: 300px; margin: 0 auto;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login to Dashboard
                    </a>
                </div>
            <?php else: ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                   placeholder="Choose a username" required>
                            <small class="text-muted">Min. 3 characters</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                   placeholder="your@email.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" 
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                               placeholder="Your full name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                               placeholder="+63-XXX-XXX-XXXX">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand/Store Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="brand_name" 
                               value="<?php echo htmlspecialchars($_POST['brand_name'] ?? ''); ?>" 
                               placeholder="Your brand or store name" required>
                        <small class="text-muted">This will be your vendor name in the system</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" 
                                   placeholder="Min. 6 characters" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" 
                                   placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <!-- ID Verification Section -->
                    <div class="id-verification">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-shield-check me-2"></i>Identity Verification Required
                        </h6>
                        <p class="text-muted small mb-3">For security and compliance, please upload a valid government-issued ID.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_type" required>
                                    <option value="">Select ID Type</option>
                                    <option value="drivers_license">Driver's License</option>
                                    <option value="passport">Passport</option>
                                    <option value="national_id">National ID</option>
                                    <option value="voters_id">Voter's ID</option>
                                    <option value="postal_id">Postal ID</option>
                                    <option value="sss_id">SSS ID</option>
                                    <option value="gsis_id">GSIS ID</option>
                                    <option value="philhealth_id">PhilHealth ID</option>
                                    <option value="tin_id">TIN ID</option>
                                    <option value="senior_citizen_id">Senior Citizen ID</option>
                                    <option value="ofw_id">OFW ID</option>
                                    <option value="other">Other Government ID</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID Number</label>
                                <input type="text" class="form-control" name="id_number" 
                                       placeholder="Enter ID number (optional)" 
                                       value="<?php echo htmlspecialchars($_POST['id_number'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload ID Document <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="id_document" accept="image/*,.pdf" required>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Accepted formats: JPG, PNG, PDF (Max: 5MB). Ensure the ID is clearly visible and readable.
                            </small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Your ID will be verified by our team. False or fraudulent documents will result in account termination.
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" style="color: #495057; cursor: pointer;">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" name="register" class="btn btn-register">
                        <i class="bi bi-person-plus me-2"></i>Create Seller Account
                    </button>

                    <div class="login-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Terms & Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">
                        <i class="bi bi-file-text me-2"></i>Terms & Conditions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                        <h6 class="fw-bold text-primary mb-3">RAEVOR Marketplace - Terms & Conditions</h6>
                        
                        <h6 class="fw-bold">1. Acceptance of Terms</h6>
                        <p class="mb-3">By registering as a seller on RAEVOR, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not register or use our services.</p>
                        
                        <h6 class="fw-bold">2. Seller Responsibilities</h6>
                        <ul class="mb-3">
                            <li>Provide accurate and truthful information during registration</li>
                            <li>Maintain the quality and authenticity of all products listed</li>
                            <li>Process orders promptly and professionally</li>
                            <li>Provide excellent customer service</li>
                            <li>Comply with all applicable laws and regulations</li>
                        </ul>
                        
                        <h6 class="fw-bold">3. Product Listings</h6>
                        <ul class="mb-3">
                            <li>All product descriptions must be accurate and detailed</li>
                            <li>Product images must be clear and representative</li>
                            <li>Prohibited items are strictly forbidden</li>
                            <li>Pricing must be competitive and transparent</li>
                        </ul>
                        
                        <h6 class="fw-bold">4. Order Processing</h6>
                        <ul class="mb-3">
                            <li>Orders must be processed within 24-48 hours</li>
                            <li>Shipping information must be updated promptly</li>
                            <li>Returns and refunds must be handled according to our policy</li>
                            <li>Customer communication should be professional and timely</li>
                        </ul>
                        
                        <h6 class="fw-bold">5. Commission and Fees</h6>
                        <ul class="mb-3">
                            <li>RAEVOR charges a commission on successful sales</li>
                            <li>Commission rates will be communicated separately</li>
                            <li>Payment processing fees may apply</li>
                            <li>All fees are non-refundable once services are rendered</li>
                        </ul>
                        
                        <h6 class="fw-bold">6. Intellectual Property</h6>
                        <ul class="mb-3">
                            <li>You retain ownership of your brand and product designs</li>
                            <li>You grant RAEVOR license to use your content for marketing purposes</li>
                            <li>Do not infringe on others' intellectual property rights</li>
                            <li>Report any IP violations immediately</li>
                        </ul>
                        
                        <h6 class="fw-bold">7. Prohibited Activities</h6>
                        <ul class="mb-3">
                            <li>Fraudulent or misleading practices</li>
                            <li>Sale of counterfeit or illegal products</li>
                            <li>Manipulation of reviews or ratings</li>
                        <li>Circumventing platform fees or policies</li>
                    </ul>
                    
                        <h6 class="fw-bold">8. Account Suspension</h6>
                        <p class="mb-3">RAEVOR reserves the right to suspend or terminate accounts that violate these terms, engage in fraudulent activities, or fail to meet performance standards.</p>
                        
                        <h6 class="fw-bold">9. Data Protection</h6>
                        <p class="mb-3">We are committed to protecting your personal information in accordance with our Privacy Policy. By using our services, you consent to the collection and use of your data as described.</p>
                        
                        <h6 class="fw-bold">10. Changes to Terms</h6>
                        <p class="mb-3">RAEVOR may update these terms at any time. Continued use of our services after changes constitutes acceptance of the new terms.</p>
                        
                        <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                            <strong>Contact:</strong> For questions about these terms, please contact our support team at support@raevor.com
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="acceptTerms()">
                        <i class="bi bi-check-circle me-2"></i>I Accept
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        const password = document.querySelector('input[name="password"]');
        const confirmPassword = document.querySelector('input[name="confirm_password"]');
        
        confirmPassword.addEventListener('input', function() {
            if (this.value && password.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        password.addEventListener('input', function() {
            if (confirmPassword.value && this.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });

        // Terms & Conditions functionality
        function acceptTerms() {
            const termsCheckbox = document.getElementById('terms');
            termsCheckbox.checked = true;
            
            // Show a brief success message
            const termsLink = document.querySelector('a[data-bs-target="#termsModal"]');
            const originalText = termsLink.innerHTML;
            termsLink.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i>Terms & Conditions (Accepted)';
            termsLink.style.color = '#22c55e';
            
            // Reset after 3 seconds
            setTimeout(() => {
                termsLink.innerHTML = originalText;
                termsLink.style.color = '#495057';
            }, 3000);
        }

        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            const submitBtn = document.querySelector('button[name="register"]');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    const termsCheckbox = document.getElementById('terms');
                    if (!termsCheckbox.checked) {
                        e.preventDefault();
                        alert('Please accept the Terms & Conditions to continue.');
                        termsCheckbox.focus();
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>

