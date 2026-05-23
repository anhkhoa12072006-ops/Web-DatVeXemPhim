<div class="offcanvas-lg offcanvas-start sidebar-container" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header d-lg-none border-bottom">
        <h5 class="offcanvas-title fw-bold text-main" id="sidebarMenuLabel">
            <i class="bi bi-film text-primary me-2"></i> CTs Cinema
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column h-100 sidebar">
        <div class="sidebar-header d-none d-lg-flex">
            <div class="sidebar-brand">
                <div class="logo-box">
                    <i class="bi bi-film"></i>
                </div>
                <div class="brand-text">
                    <h4>CTs Cinema</h4>
                    <p>Công ty siu nhân</p>
                </div>
            </div>
        </div>

        <ul class="nav flex-column sidebar-nav flex-grow-1">
            <!-- Main Menu -->
            <li class="nav-section mt-2">
                <div class="nav-section-title">QUẢN LÝ CHÍNH</div>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'movies') ? 'active' : ''; ?>" href="movies.php">
                    <i class="bi bi-film"></i>
                    <span>Quản lý phim</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'showtimes') ? 'active' : ''; ?>" href="showtimes.php">
                    <i class="bi bi-clock-history"></i>
                    <span>Suất chiếu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'cinemas') ? 'active' : ''; ?>" href="cinemas.php">
                    <i class="bi bi-building"></i>
                    <span>Rạp chiếu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'rooms') ? 'active' : ''; ?>" href="rooms.php">
                    <i class="bi bi-door-open"></i>
                    <span>Phòng chiếu</span>
                </a>
            </li>
            
            <!-- Business -->
            <li class="nav-section">
                <div class="nav-section-title">KINH DOANH</div>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'orders') ? 'active' : ''; ?>" href="orders.php">
                    <i class="bi bi-receipt"></i>
                    <span>Đơn hàng</span>
                </a>
            </li>
            
            <!-- System -->
            <li class="nav-section">
                <div class="nav-section-title">HỆ THỐNG</div>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'categories') ? 'active' : ''; ?>" href="categories.php">
                    <i class="bi bi-tag"></i>
                    <span>Danh mục</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>" href="users.php">
                    <i class="bi bi-people"></i>
                    <span>Người dùng</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'settings') ? 'active' : ''; ?>" href="settings.php">
                    <i class="bi bi-gear"></i>
                    <span>Cấu hình hệ thống</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <?php echo strtoupper(substr($_SESSION['tendn'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="admin-info">
                    <h6><?php echo $_SESSION['tendn'] ?? 'Admin'; ?></h6>
                    <p><?php echo ucfirst($_SESSION['quyen'] ?? 'admin'); ?></p>
                </div>
            </div>
            <div class="mt-3 d-grid gap-2">
                <a href="../index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-globe"></i> Xem trang web
                </a>
                <a href="logout.php" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>
</div>
