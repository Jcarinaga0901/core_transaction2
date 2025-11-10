<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';
requireAuth('login.php');

$currentPage = basename($_SERVER['PHP_SELF']);
$vendorId = $_SESSION['vendor_id'] ?? null;

// Handle document upload
if (isset($_POST['upload_documents'])) {
    try {
        $pdo = getDBConnection();
        
        // Create seller_documents table if it doesn't exist
        $createTable = "CREATE TABLE IF NOT EXISTS seller_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            vendor_id INT NOT NULL,
            business_permit VARCHAR(255) NULL,
            bir VARCHAR(255) NULL,
            dti VARCHAR(255) NULL,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            admin_notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL,
            INDEX (vendor_id),
            INDEX (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($createTable);
        
        // Create uploads/documents directory if it doesn't exist
        $upload_dir = 'uploads/documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $uploaded_files = [];
        $document_types = ['business_permit', 'bir', 'dti'];
        
        foreach ($document_types as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$type];
                
                // Validate file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = mime_content_type($file['tmp_name']);
                
                if (in_array($file_type, $allowed_types) && $file['size'] <= 5 * 1024 * 1024) {
                    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = $type . '_' . $vendorId . '_' . time() . '.' . $file_extension;
                    $file_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $file_path)) {
                        $uploaded_files[$type] = $file_path;
                    }
                }
            }
        }
        
        if (!empty($uploaded_files)) {
            // Check if document record exists
            $stmt = $pdo->prepare("SELECT id FROM seller_documents WHERE vendor_id = ?");
            $stmt->execute([$vendorId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing record
                $update_fields = [];
                $update_values = [];
                
                foreach ($uploaded_files as $type => $path) {
                    $update_fields[] = "$type = ?";
                    $update_values[] = $path;
                }
                
                $update_values[] = $vendorId;
                $sql = "UPDATE seller_documents SET " . implode(', ', $update_fields) . ", updated_at = NOW() WHERE vendor_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($update_values);
            } else {
                // Insert new record
                $stmt = $pdo->prepare("INSERT INTO seller_documents (vendor_id, business_permit, bir, dti) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $vendorId,
                    $uploaded_files['business_permit'] ?? null,
                    $uploaded_files['bir'] ?? null,
                    $uploaded_files['dti'] ?? null
                ]);
            }
            
            // Create notification for successful document upload
            require_once 'includes/notification_helper.php';
            $uploadedTypes = [];
            foreach ($uploaded_files as $type => $file) {
                if ($file) {
                    $uploadedTypes[] = ucfirst(str_replace('_', ' ', $type));
                }
            }
            notifyDocumentsUploaded($pdo, $_SESSION['user_id'], $uploadedTypes, $vendorId);
            
            header("Location: my_shop.php?success=" . urlencode("Documents uploaded successfully!"));
            exit;
        } else {
            header("Location: my_shop.php?error=" . urlencode("No valid files uploaded"));
            exit;
        }
        
    } catch (Exception $e) {
        error_log("Document upload error: " . $e->getMessage());
        header("Location: my_shop.php?error=" . urlencode("Upload failed: " . $e->getMessage()));
        exit;
    }
}

// Get document status
$documentStatus = null;
if ($vendorId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM seller_documents WHERE vendor_id = ?");
        $stmt->execute([$vendorId]);
        $documentStatus = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching document status: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Documents - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="sidebar-toggle">
            <i class="bi bi-list"></i>
        </div>
        <h1 style="font-family: 'Great Vibes', cursive !important; font-size: 1.5rem; font-weight: 350; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
            <a href="index.php" class="brand-link">RAEVOR</a></h1>
        
        <div class="user-info ms-auto d-flex align-items-center">
            <?php include 'includes/welcome_user.php'; ?>
            
            <?php include 'includes/notification_dropdown.php'; ?>
            
            <div class="dropdown">
                <button class="btn btn-link text-secondary p-0 settings-icon-btn" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem; border: none; background: none; box-shadow: none !important; outline: none !important;">
                    <i class="bi bi-gear-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    <li><a class="dropdown-item" href="my_shop.php"><i class="bi bi-shop me-2"></i>My Shop</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Side Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Core Transaction 2</h3>
            <p style="margin: 5px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Seller Dashboard</p>
        </div>
        <ul class="sidebar-nav">
            <li class="sidebar-item <?php if($currentPage == 'index.php') echo 'active'; ?>">
                <a href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item <?php if($currentPage == 'my_shop.php') echo 'active'; ?>">
                <a href="my_shop.php">
                    <i class="bi bi-shop"></i>
                    <span class="menu-text">My Shop</span>
                </a>
            </li>
            <li class="sidebar-item <?php if($currentPage == 'seller_documents.php') echo 'active'; ?>">
                <a href="seller_documents.php">
                    <i class="bi bi-file-earmark-check"></i>
                    <span class="menu-text">Business Documents</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Success/Error Messages -->
            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; font-weight: 500;">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">Business Documents</h2>
                        <p class="page-subtitle">Upload and manage your business documents</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentsModal">
                            <i class="bi bi-cloud-upload me-1"></i>Upload Documents
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Document Status -->
            <?php if($documentStatus): ?>
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>Document Status</h6>
                            <p class="mb-2">
                                <strong>Status:</strong> 
                                <span class="badge <?php 
                                    echo $documentStatus['status'] === 'approved' ? 'bg-success' : 
                                        ($documentStatus['status'] === 'rejected' ? 'bg-danger' : 'bg-warning'); 
                                ?>">
                                    <?php echo ucfirst($documentStatus['status']); ?>
                                </span>
                            </p>
                            <?php if($documentStatus['admin_notes']): ?>
                                <p class="mb-0"><strong>Admin Notes:</strong> <?php echo htmlspecialchars($documentStatus['admin_notes']); ?></p>
                            <?php endif; ?>
                            <small class="text-muted">
                                Last updated: <?php echo date('M j, Y g:i A', strtotime($documentStatus['updated_at'])); ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewDocumentsModal">
                                <i class="bi bi-eye me-1"></i>View Documents
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Document Upload Cards -->
            <div class="row g-3">
                <!-- Business Permit -->
                <div class="col-md-4">
                    <div class="card h-100 border-2 <?php echo ($documentStatus && $documentStatus['business_permit']) ? 'border-success' : 'border-light'; ?>">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-building text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="card-title">Business Permit</h6>
                            <p class="card-text text-muted small">Upload your business permit document</p>
                            
                            <?php if($documentStatus && $documentStatus['business_permit']): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $documentStatus['business_permit']; ?>" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                         alt="Business Permit">
                                </div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Uploaded
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-clock me-1"></i>Not Uploaded
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- BIR -->
                <div class="col-md-4">
                    <div class="card h-100 border-2 <?php echo ($documentStatus && $documentStatus['bir']) ? 'border-success' : 'border-light'; ?>">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-receipt text-info" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="card-title">BIR Registration</h6>
                            <p class="card-text text-muted small">Upload your BIR registration document</p>
                            
                            <?php if($documentStatus && $documentStatus['bir']): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $documentStatus['bir']; ?>" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                         alt="BIR Registration">
                                </div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Uploaded
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-clock me-1"></i>Not Uploaded
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- DTI -->
                <div class="col-md-4">
                    <div class="card h-100 border-2 <?php echo ($documentStatus && $documentStatus['dti']) ? 'border-success' : 'border-light'; ?>">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-award text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="card-title">DTI Registration</h6>
                            <p class="card-text text-muted small">Upload your DTI registration document</p>
                            
                            <?php if($documentStatus && $documentStatus['dti']): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $documentStatus['dti']; ?>" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                         alt="DTI Registration">
                                </div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Uploaded
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-clock me-1"></i>Not Uploaded
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Documents Modal -->
    <div class="modal fade" id="uploadDocumentsModal" tabindex="-1" aria-labelledby="uploadDocumentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="uploadDocumentsModalLabel">
                        <i class="bi bi-cloud-upload me-2"></i>Upload Business Documents
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Instructions:</strong> Upload clear, readable images of your business documents. Supported formats: JPG, PNG, GIF, WebP (Max 5MB each).
                        </div>
                        
                        <div class="row g-3">
                            <!-- Business Permit -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">Business Permit</h6>
                                        <input type="file" class="form-control" name="business_permit" accept="image/*" onchange="previewImage(this, 'bp_preview')">
                                        <div id="bp_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- BIR -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-receipt text-info" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">BIR Registration</h6>
                                        <input type="file" class="form-control" name="bir" accept="image/*" onchange="previewImage(this, 'bir_preview')">
                                        <div id="bir_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- DTI -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-award text-warning" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">DTI Registration</h6>
                                        <input type="file" class="form-control" name="dti" accept="image/*" onchange="previewImage(this, 'dti_preview')">
                                        <div id="dti_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="upload_documents" class="btn btn-primary">
                            <i class="bi bi-cloud-upload me-1"></i>Upload Documents
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Image preview functionality
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 100px; max-height: 100px; object-fit: cover;" alt="Preview">
                        <div class="small text-success mt-1">
                            <i class="bi bi-check-circle me-1"></i>Ready to upload
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        }
    </script>
</body>
</html>