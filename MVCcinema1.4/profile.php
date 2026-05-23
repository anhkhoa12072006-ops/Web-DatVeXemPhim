<?php
require_once 'includes/auth.php';
require_once 'models/UserProfile.php';

$tendn = $_SESSION['tendn'];

// Lấy thông tin user
$users = UserProfile::getByUsername($db, $tendn);
if (empty($users)) {
    header('Location: logout.php');
    exit;
}
$user = $users[0];

// Thống kê
$stats = UserProfile::getStats($db, $tendn);
$total_tickets = $stats['total_tickets'];
$total_spent = $stats['total_spent'];

// Xử lý cập nhật thông tin
$message = '';
$msg_type = 'success';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $ghichu = $_POST['ghichu'] ?? '';
        
        if (UserProfile::updateProfile($db, $tendn, $ghichu)) {
            $_SESSION['ghichu'] = $ghichu;
            $message = 'Cập nhật thông tin cá nhân thành công!';
            $user['ghichu'] = $ghichu;
        } else {
            $message = 'Đã có lỗi xảy ra, vui lòng thử lại!';
            $msg_type = 'danger';
        }
    } elseif (isset($_POST['change_password'])) {
        $old_password = (int)$_POST['old_password'];
        $new_password = (int)$_POST['new_password'];
        $confirm_password = (int)$_POST['confirm_password'];
        
        if ($old_password != $user['matkhau']) {
            $message = 'Mật khẩu cũ không chính xác!';
            $msg_type = 'danger';
        } elseif ($new_password != $confirm_password) {
            $message = 'Mật khẩu mới không khớp nhau!';
            $msg_type = 'danger';
        } elseif (empty($new_password)) {
            $message = 'Mật khẩu không được để trống!';
            $msg_type = 'danger';
        } else {
            if (UserProfile::changePassword($db, $tendn, $new_password)) {
                $message = 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới nhé.';
            } else {
                $message = 'Đã có lỗi xảy ra!';
                $msg_type = 'danger';
            }
        }
    }
}

$page_title = 'Tài khoản của tôi - CTs Cinema';
include_once 'header.php';
?>

    <section class="py-5" style="background-color: var(--bg-color);">
        <div class="container py-4">
            <h2 class="mb-5 section-title" style="color: var(--text-color);">Tài khoản của tôi</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-<?php echo $msg_type == 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- User Info Card (Left Column) -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center p-4 p-xl-5">
                            <!-- Avatar -->
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow" 
                                 style="width: 120px; height: 120px; background: linear-gradient(135deg, #ff4d6d, var(--primary-color)); border: 4px solid #fff;">
                                <span class="text-white fw-bold" style="font-size: 3rem;">
                                    <?php echo strtoupper(substr($user['tendn'], 0, 1)); ?>
                                </span>
                            </div>
                            
                            <h4 class="fw-bold mb-2" style="color: var(--text-color);"><?php echo htmlspecialchars($user['tendn']); ?></h4>
                            
                            <div class="mb-3">
                                <span class="badge bg-<?php echo $user['quyen'] == 'admin' ? 'danger' : 'secondary'; ?> bg-opacity-10 text-<?php echo $user['quyen'] == 'admin' ? 'danger' : 'secondary'; ?> border border-<?php echo $user['quyen'] == 'admin' ? 'danger' : 'secondary'; ?> border-opacity-25 px-3 py-2 rounded-pill">
                                    <i class="bi bi-shield-check me-1"></i> <?php echo strtoupper($user['quyen']); ?>
                                </span>
                            </div>
                            
                            <p class="text-muted small mb-4 px-2">
                                <?php echo !empty($user['ghichu']) ? htmlspecialchars($user['ghichu']) : '<em class="opacity-50">Chưa có thông tin ghi chú.</em>'; ?>
                            </p>
                            
                            <hr class="border-secondary border-opacity-25 mb-4">
                            
                            <!-- Stats inside profile -->
                            <div class="row text-center g-3">
                                <div class="col-6">
                                    <div class="p-3 rounded-4" style="background-color: rgba(216, 17, 89, 0.08);">
                                        <h4 class="mb-1 fw-bold" style="color: var(--primary-color);"><?php echo $total_tickets; ?></h4>
                                        <small class="fw-semibold text-muted">Đơn hàng</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-4" style="background-color: rgba(216, 17, 89, 0.08);">
                                        <h4 class="mb-1 fw-bold" style="color: var(--primary-color);"><?php echo number_format($total_spent / 1000); ?>k</h4>
                                        <small class="fw-semibold text-muted">Đã chi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings & Forms (Right Column) -->
                <div class="col-lg-8">
                    <!-- Update Profile -->
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-0" style="color: var(--primary-color);">
                                <i class="bi bi-person-gear me-2"></i>Cập nhật thông tin
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">TÊN ĐĂNG NHẬP</label>
                                        <input type="text" class="form-control bg-light text-muted" value="<?php echo htmlspecialchars($user['tendn']); ?>" disabled>
                                        <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Không thể thay đổi tên đăng nhập</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">GHI CHÚ / GIỚI THIỆU</label>
                                        <input type="text" class="form-control" name="ghichu" placeholder="Nhập một vài điều về bạn..." value="<?php echo htmlspecialchars($user['ghichu']); ?>">
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-danger px-4 py-2 fw-bold">
                                            Lưu thay đổi
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-0" style="color: var(--primary-color);">
                                <i class="bi bi-shield-lock-fill me-2"></i>Đổi mật khẩu
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label text-muted small fw-bold">MẬT KHẨU HIỆN TẠI</label>
                                        <input type="password" class="form-control" name="old_password" required placeholder="Nhập mật khẩu cũ...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">MẬT KHẨU MỚI</label>
                                        <input type="password" class="form-control" name="new_password" required placeholder="Nhập số (VD: 123)">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">XÁC NHẬN MẬT KHẨU MỚI</label>
                                        <input type="password" class="form-control" name="confirm_password" required placeholder="Nhập lại mật khẩu mới...">
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" name="change_password" class="btn btn-danger px-4 py-2 fw-bold">
                                            Cập nhật mật khẩu
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-0" style="color: var(--primary-color);">
                                <i class="bi bi-lightning-charge-fill me-2"></i>Thao tác nhanh
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="my-tickets.php" class="btn btn-outline-danger w-100 py-3 rounded-4 fw-bold d-flex flex-column align-items-center gap-2">
                                        <i class="bi bi-ticket-perforated fs-4"></i> Lịch sử vé
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="index.php" class="btn btn-danger w-100 py-3 rounded-4 fw-bold d-flex flex-column align-items-center gap-2 shadow-sm">
                                        <i class="bi bi-film fs-4"></i> Đặt vé ngay
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="logout.php" class="btn btn-light border w-100 py-3 rounded-4 fw-bold d-flex flex-column align-items-center gap-2 text-muted" style="background-color: #f8fafc;">
                                        <i class="bi bi-box-arrow-right fs-4"></i> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include_once 'footer.php'; ?>