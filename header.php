<!-- Main Header -->
<header class="main-header">
    <div class="sidebar-toggle">
        <i class="bi bi-list"></i>
    </div>
    <h1 style="font-family: 'Great Vibes', cursive !important; font-size: 1.5rem; font-weight: 350; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
        <a href="index.php" class="brand-link">RAEVOR</a>
    </h1>
    <div class="header-right">
        <?php include 'includes/notification_dropdown.php'; ?>
        
        <div class="dropdown">
            <button class="btn btn-link text-secondary p-0 settings-icon-btn" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem; border: none; background: none; box-shadow: none !important; outline: none !important;">
                <i class="bi bi-gear-fill"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                <li><a class="dropdown-item" href="security_settings.php"><i class="bi bi-shield-lock me-2"></i>Security & 2FA</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>


