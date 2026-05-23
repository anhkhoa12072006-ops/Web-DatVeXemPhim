<?php
require_once 'config/database.php';
require_once 'models/Auth.php';

// Nếu đã đăng nhập, chuyển về trang chủ
if (isset($_SESSION['tendn'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tendn = $_POST['tendn'] ?? '';
    $matkhau = $_POST['matkhau'] ?? '';
    $matkhau_confirm = $_POST['matkhau_confirm'] ?? '';
    $ghichu = $_POST['ghichu'] ?? '';
    
    // Validate
    if (empty($tendn)) {
        $error = 'Vui lòng nhập tên đăng nhập!';
    } elseif (strlen($tendn) < 3) {
        $error = 'Tên đăng nhập phải có ít nhất 3 ký tự!';
    } elseif (empty($matkhau)) {
        $error = 'Vui lòng nhập mật khẩu!';
    } elseif ($matkhau != $matkhau_confirm) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Kiểm tra tên đăng nhập đã tồn tại
        if (Auth::usernameExists($db, $tendn)) {
            $error = 'Tên đăng nhập đã tồn tại!';
        } else {
            // Thêm tài khoản mới
            if (Auth::register($db, $tendn, $matkhau, $ghichu)) {
                $success = 'Đăng ký thành công! Đang chuyển hướng...';
                
                // Auto login
                $_SESSION['tendn'] = $tendn;
                $_SESSION['quyen'] = 'user';
                $_SESSION['ghichu'] = $ghichu;
                
                // Chuyển hướng sau 2 giây
                header("refresh:2;url=index.php");
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - KT Cinema</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/banner.php'; ?>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="bi bi-person-vcard-fill"></i>
            </div>
            
            <h2 class="auth-title">Đăng ký tài khoản</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-4 border-0 shadow-sm" style="background-color: #ffe4e8; color: var(--primary-color);" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success rounded-4 border-0 shadow-sm d-flex align-items-center" style="background-color: #d1fae5; color: #059669;" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?php echo $success; ?></div>
                    <div class="spinner-border spinner-border-sm ms-auto text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control rounded-4" 
                           id="tendn" 
                           name="tendn" 
                           placeholder="Tên đăng nhập" 
                           required 
                           autofocus
                           minlength="3"
                           value="<?php echo htmlspecialchars($_POST['tendn'] ?? ''); ?>">
                    <label for="tendn"><i class="bi bi-person-fill me-1"></i> Tên đăng nhập</label>
                    <small class="text-muted d-block mt-1 ms-2">Ít nhất 3 ký tự</small>
                </div>
                
                <div class="form-floating mb-2">
                    <input type="password" 
                           class="form-control rounded-4" 
                           id="matkhau" 
                           name="matkhau" 
                           placeholder="Mật khẩu" 
                           required>
                    <label for="matkhau"><i class="bi bi-lock-fill me-1"></i> Mật khẩu</label>
                </div>
                <small class="text-muted d-block mb-3 ms-2">Chỉ nhập số (ví dụ: 123, 456)</small>
                
                <div class="form-floating mb-3">
                    <input type="password" 
                           class="form-control rounded-4" 
                           id="matkhau_confirm" 
                           name="matkhau_confirm" 
                           placeholder="Xác nhận mật khẩu" 
                           required>
                    <label for="matkhau_confirm"><i class="bi bi-shield-lock-fill me-1"></i> Xác nhận mật khẩu</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control rounded-4" 
                           id="ghichu" 
                           name="ghichu" 
                           placeholder="Ghi chú"
                           value="<?php echo htmlspecialchars($_POST['ghichu'] ?? ''); ?>">
                    <label for="ghichu"><i class="bi bi-chat-dots-fill me-1"></i> Ghi chú (tùy chọn)</label>
                </div>
                
                <div class="form-check mb-4 mt-3 ps-1 d-flex align-items-center gap-2">
                    <input class="form-check-input mt-0" type="checkbox" id="agree" required style="width: 1.2rem; height: 1.2rem;">
                    <label class="form-check-label text-muted fw-medium small" for="agree">
                        Tôi đồng ý với <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Điều khoản dịch vụ</a> và 
                        <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Chính sách bảo mật</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-danger w-100 py-3 mb-4 rounded-4 fs-5">
                    Đăng ký ngay <i class="bi bi-person-plus-fill ms-2"></i>
                </button>
                
                <div class="text-center">
                    <p class="text-muted fw-medium mb-1">
                        Đã có tài khoản? 
                        <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Đăng nhập ngay</a>
                    </p>
                    <p class="mt-3">
                        <a href="index.php" class="text-muted text-decoration-none fw-medium small hover-primary">
                            <i class="bi bi-arrow-left-short"></i> Trở về trang chủ
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kiểm tra mật khẩu khớp
        document.getElementById('matkhau_confirm').addEventListener('input', function() {
            const password = document.getElementById('matkhau').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Mật khẩu không khớp');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
