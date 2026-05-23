<?php
require_once 'config/database.php';
require_once 'models/Auth.php';

// Nếu đã đăng nhập, chuyển về trang chủ
if (isset($_SESSION['tendn'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tendn = $_POST['tendn'] ?? '';
    $matkhau = $_POST['matkhau'] ?? '';
    
    $user = Auth::login($db, $tendn, $matkhau);
    
    if ($user !== null) {
        if (isset($user['trangthai']) && $user['trangthai'] == 'Bị khóa') {
            $error = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ bộ phận hỗ trợ!';
        } else {
            $_SESSION['tendn'] = $user['tendn'];
            $_SESSION['quyen'] = $user['quyen'];
            $_SESSION['ghichu'] = $user['ghichu'];
            
            // Chuyển hướng theo quyền
            if ($user['quyen'] == 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - KT Cinema</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/banner.php'; ?>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="bi bi-camera-reels-fill"></i>
            </div>
            
            <h2 class="auth-title">Đăng nhập CTs Cinema</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-4 border-0 shadow-sm" style="background-color: #ffe4e8; color: var(--primary-color);" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control rounded-4" id="tendn" name="tendn" placeholder="Tên đăng nhập" required autofocus>
                    <label for="tendn"><i class="bi bi-person-fill me-1"></i> Tên đăng nhập</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control rounded-4" id="matkhau" name="matkhau" placeholder="Mật khẩu" required>
                    <label for="matkhau"><i class="bi bi-lock-fill me-1"></i> Mật khẩu</label>
                </div>
                
                <div class="form-check mb-4 ps-1 d-flex align-items-center gap-2">
                    <input class="form-check-input mt-0" type="checkbox" id="remember" style="width: 1.2rem; height: 1.2rem;">
                    <label class="form-check-label text-muted fw-medium small" for="remember">
                        Ghi nhớ đăng nhập
                    </label>
                </div>
                
                <button type="submit" class="btn btn-danger w-100 py-3 mb-4 rounded-4 fs-5">
                    Đăng nhập <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                </button>
                
                <div class="text-center">
                    <p class="text-muted fw-medium mb-1">
                        Chưa có tài khoản? 
                        <a href="register.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Đăng ký ngay</a>
                    </p>
                    <p class="mt-3">
                        <a href="index.php" class="text-muted text-decoration-none fw-medium small hover-primary">
                            <i class="bi bi-arrow-left-short"></i> Trở về trang chủ
                        </a>
                    </p>
                </div>
            </form>
            
            <!-- Demo accounts Box -->
            <div class="mt-4 p-4 rounded-4 shadow-sm" style="background-color: #fff0f3; border: 1px dashed var(--border-color);">
                <p class="small mb-3 fw-bold" style="color: var(--primary-color);">
                    <i class="bi bi-info-circle-fill me-1"></i> TÀI KHOẢN TRẢI NGHIỆM:
                </p>
                <div class="row g-2 small">
                    <div class="col-6 border-end border-secondary border-opacity-10">
                        <strong style="color: var(--primary-color);">Admin:</strong><br>
                        <span class="text-muted fw-medium">User: thuyen</span><br>
                        <span class="text-muted fw-medium">Pass: 123</span>
                    </div>
                    <div class="col-6 ps-3">
                        <strong class="text-success">Khách hàng:</strong><br>
                        <span class="text-muted fw-medium">User: dan</span><br>
                        <span class="text-muted fw-medium">Pass: 123</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
