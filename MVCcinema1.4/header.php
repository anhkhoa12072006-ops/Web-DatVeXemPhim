<!DOCTYPE html>
<html lang="vi" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link href="bootstrap-5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/banner.php'; ?>
     <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="logo-icon me-2">
                    <i class="bi bi-film"></i>
                </div>
                <span class="fw-bold">CTs Cinema</span>
            </a>
            <!-- Nút Toggle Mobile chỉ dùng icon mũi tên -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <div class="d-flex align-items-center bg-white bg-opacity-10 px-3 py-1 rounded-pill border" style="border-color: rgba(0,0,0,0.1) !important;">
                    <span class="text-dark small fw-bold me-1" style="font-size: 0.75rem;">MENU</span>
                    <i class="bi bi-chevron-down text-dark small"></i>
                </div>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house"></i> Trang chủ
                        </a>
                    </li>
                    <?php if (isset($_SESSION['tendn'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my-tickets.php">
                                <i class="bi bi-ticket-perforated"></i> Vé của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="bi bi-person"></i> Tài khoản
                            </a>
                        </li>
                        <?php if ($_SESSION['quyen'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/index.php">
                                    <i class="bi bi-shield-lock"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-between" href="#" data-bs-toggle="dropdown">
                                <span><i class="bi bi-person-circle me-1"></i> <?php echo $_SESSION['tendn']; ?></span>
                                <i class="bi bi-chevron-down small d-lg-none opacity-50"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Tài khoản</a></li>
                                <li><a class="dropdown-item" href="my-tickets.php"><i class="bi bi-ticket"></i> Vé của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-left"></i> Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger text-white ms-2" href="register.php">
                                <i class="bi bi-person-plus"></i> Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
