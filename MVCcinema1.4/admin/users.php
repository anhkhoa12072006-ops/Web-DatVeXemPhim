<?php
require_once 'includes/auth.php';
require_once 'models/User.php';

$page_title = 'Quản lý người dùng - CTs Cinema Admin';
$current_page = 'users';

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $tendn = $_POST['tendn'] ?? '';
        $quyen = $_POST['quyen'] ?? '';
        $ghichu = $_POST['ghichu'] ?? '';
        
        if ($_POST['action'] == 'add') {
            $matkhau = (int)($_POST['matkhau'] ?? 0);
            if (User::add($db, $tendn, $matkhau, $quyen, $ghichu)) {
                $_SESSION['message'] = 'Thêm người dùng mới thành công!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Tên đăng nhập đã tồn tại hoặc có lỗi xảy ra!';
                $_SESSION['msg_type'] = 'danger';
            }
        } elseif ($_POST['action'] == 'edit') {
            $tendn_old = $_POST['tendn_old'] ?? '';
            $result = User::update($db, $tendn_old, $quyen, $ghichu, $_SESSION['tendn']);
            if ($result) {
                $_SESSION['message'] = 'Cập nhật thông tin thành công!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Bạn không thể tự hạ quyền chính mình hoặc có lỗi xảy ra!';
                $_SESSION['msg_type'] = 'danger';
            }
        } elseif ($_POST['action'] == 'toggle_status') {
            $result = User::toggleStatus($db, $tendn);
            if ($result === 'admin_error') {
                $_SESSION['message'] = 'Không thể vô hiệu hóa tài khoản Admin!';
                $_SESSION['msg_type'] = 'danger';
            } elseif ($result) {
                $_SESSION['message'] = $result == 'Bị khóa' ? 'Đã khóa tài khoản người dùng!' : 'Đã mở khóa tài khoản!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'reset_password') {
            $new_password = (int)($_POST['new_password'] ?? 0);
            if (User::resetPassword($db, $tendn, $new_password)) {
                $_SESSION['message'] = 'Đã reset mật khẩu cho ' . $tendn;
                $_SESSION['msg_type'] = 'success';
            }
        }
        header('Location: users.php');
        exit;
    }
}

// Filter logic
$role_filter = $_GET['role'] ?? '';
$search = $_GET['search'] ?? '';

$all_records = User::getAll($db, $role_filter, $search);


$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$users = array_slice($all_records, ($page - 1) * $limit, $limit);
// Stats calculation
$total_users = count($users);
$total_admins = 0; $total_customers = 0;
foreach($users as $u) {
    if($u['quyen'] == 'admin') $total_admins++;
    else $total_customers++;
}

include 'includes/header.php';
?>

<div class="d-flex flex-column flex-lg-row min-vh-100 w-100 overflow-hidden">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1 w-100" style="min-width: 0;">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"><i class="bi bi-list fs-4"></i></button>
                <div class="page-title ms-2">
                    <h4 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Hệ thống thành viên</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
                    <i class="bi bi-person-plus-fill me-2"></i>Thêm thành viên
                </button>
            </div>
        </header>

        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'success'; ?> border-0 shadow-sm rounded-4 fade show">
                    <i class="bi bi-info-circle-fill me-2"></i> <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                </div>
            <?php endif; ?>

            <!-- Stats Overview -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stats-card stats-info">
                        <div class="stats-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="stats-content">
                            <p>Tổng thành viên</p>
                            <h3><?php echo number_format($total_users); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card stats-danger">
                        <div class="stats-icon"><i class="bi bi-shield-check-fill"></i></div>
                        <div class="stats-content">
                            <p>Ban quản trị</p>
                            <h3><?php echo number_format($total_admins); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card stats-success">
                        <div class="stats-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div class="stats-content">
                            <p>Khách hàng</p>
                            <h3><?php echo number_format($total_customers); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" name="search" placeholder="Tìm theo tên hoặc ghi chú..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="role">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>Quản trị viên (Admin)</option>
                                <option value="user" <?php echo $role_filter == 'user' ? 'selected' : ''; ?>>Người dùng (User)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-light w-100 fw-bold">Lọc danh sách</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Thành viên</th>
                                    <th>Vai trò / Trạng thái</th>
                                    <th>Ghi chú</th>
                                    <th>Đơn hàng</th>
                                    <th>Chi tiêu</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="admin-avatar" style="width:42px; height:42px; font-size: 1.1rem; flex-shrink: 0; background: <?php echo $user['quyen'] == 'admin' ? 'linear-gradient(135deg, #f43f5e, #fb7185)' : 'linear-gradient(135deg, #475569, #64748b)'; ?>">
                                                        <?php echo strtoupper(substr($user['tendn'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold d-block text-main"><?php echo htmlspecialchars($user['tendn']); ?></span>
                                                        <span class="text-muted small">#<?php echo abs(crc32($user['tendn'])) % 1000; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1 align-items-start">
                                                    <span class="badge bg-<?php echo $user['quyen'] == 'admin' ? 'danger' : 'secondary'; ?> bg-opacity-10 text-<?php echo $user['quyen'] == 'admin' ? 'danger' : 'light'; ?> border border-<?php echo $user['quyen'] == 'admin' ? 'danger' : 'secondary'; ?> border-opacity-25 px-3">
                                                        <?php echo strtoupper($user['quyen']); ?>
                                                    </span>
                                                    <span class="badge bg-<?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Hoạt động' ? 'success' : 'warning'; ?> bg-opacity-10 text-<?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Hoạt động' ? 'success' : 'warning'; ?> px-2" style="font-size: 0.7rem;">
                                                        <i class="bi bi-<?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Hoạt động' ? 'check-circle' : 'lock-fill'; ?> me-1"></i><?php echo $user['trangthai'] ?? 'Hoạt động'; ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-muted small"><?php echo !empty($user['ghichu']) ? htmlspecialchars($user['ghichu']) : '-'; ?></td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info px-2"><?php echo $user['sodon']; ?> đơn</span></td>
                                            <td class="fw-bold text-success"><?php echo $user['tongtien'] ? number_format($user['tongtien']) . 'đ' : '0đ'; ?></td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <!-- Nút Sửa giống orders.php -->
                                                    <button class="btn btn-outline-warning btn-sm" onclick='editUser(<?php echo json_encode($user, JSON_HEX_APOS); ?>)' title="Cập nhật">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <!-- Nút Reset Key giống style orders.php -->
                                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#resetModal<?php echo md5($user['tendn']); ?>" title="Reset mật khẩu">
                                                        <i class="bi bi-key-fill"></i>
                                                    </button>
                                                    <!-- Nút Khóa/Mở khóa -->
                                                    <?php if ($user['quyen'] != 'admin'): ?>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn <?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Bị khóa' ? 'mở khóa' : 'khóa'; ?> người dùng này?');">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="tendn" value="<?php echo htmlspecialchars($user['tendn']); ?>">
                                                        <button type="submit" class="btn btn-outline-<?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Bị khóa' ? 'success' : 'danger'; ?> btn-sm" title="<?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Bị khóa' ? 'Mở khóa' : 'Khóa'; ?>">
                                                            <i class="bi bi-<?php echo ($user['trangthai'] ?? 'Hoạt động') == 'Bị khóa' ? 'unlock-fill' : 'lock-fill'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Modal Reset Password -->
                                                <div class="modal fade text-start" id="resetModal<?php echo md5($user['tendn']); ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                                        <div class="modal-content border-0 rounded-4 shadow-lg">
                                                            <form method="POST">
                                                                <div class="modal-header border-0 bg-info bg-opacity-10 p-4">
                                                                    <h5 class="modal-title fw-bold text-info"><i class="bi bi-shield-lock-fill me-2"></i>Reset Pass</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-4">
                                                                    <input type="hidden" name="action" value="reset_password">
                                                                    <input type="hidden" name="tendn" value="<?php echo htmlspecialchars($user['tendn']); ?>">
                                                                    <p class="text-muted small mb-3">Người dùng: <strong><?php echo htmlspecialchars($user['tendn']); ?></strong></p>
                                                                    <label class="form-label text-muted small fw-bold">MẬT KHẨU MỚI (SỐ)</label>
                                                                    <input type="password" class="form-control rounded-3" name="new_password" required placeholder="Nhập số...">
                                                                </div>
                                                                <div class="modal-footer border-0 p-4 pt-0">
                                                                    <button type="submit" class="btn btn-info w-100 fw-bold text-white rounded-3 py-2">Xác nhận đổi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 opacity-50">
                                            <i class="bi bi-people-fill fs-1 d-block mb-2"></i>
                                            Chưa tìm thấy thành viên nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='mt-4'><?php include '../pagination.php'; ?></div>

<!-- Modal Thêm/Sửa Người dùng -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle"><i class="bi bi-person-plus-fill me-2"></i>Thêm người dùng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="tendn_old" id="tendn_old">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">TÊN ĐĂNG NHẬP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fs-6 rounded-3" name="tendn" id="tendn" required>
                    </div>
                    
                    <div class="mb-4" id="passwordField">
                        <label class="form-label text-muted small fw-bold">MẬT KHẨU KHỞI TẠO <span class="text-danger">*</span></label>
                        <input type="password" class="form-control rounded-3" name="matkhau" id="matkhau">
                        <small class="text-muted mt-1 d-block">Lưu ý: Chỉ nhập chuỗi chữ số theo cấu trúc cũ.</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">VAI TRÒ / QUYỀN HẠN <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" name="quyen" id="quyen" required>
                            <option value="user">Người dùng (User)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                        </select>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-muted small fw-bold">GHI CHÚ THÊM</label>
                        <textarea class="form-control rounded-3" name="ghichu" id="ghichu" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary p-4 d-flex gap-2">
                    <button type="button" class="btn btn-outline-light flex-fill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger flex-fill fw-bold">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Thêm người dùng mới';
    document.getElementById('action').value = 'add';
    document.getElementById('tendn_old').value = '';
    document.getElementById('tendn').value = '';
    document.getElementById('tendn').removeAttribute('readonly');
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('matkhau').setAttribute('required', 'required');
    document.getElementById('quyen').value = 'user';
    document.getElementById('ghichu').value = '';
}

function editUser(user) {
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Cập nhật thành viên';
    document.getElementById('action').value = 'edit';
    document.getElementById('tendn_old').value = user.tendn;
    document.getElementById('tendn').value = user.tendn;
    document.getElementById('tendn').setAttribute('readonly', 'readonly');
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('matkhau').removeAttribute('required');
    document.getElementById('quyen').value = user.quyen;
    document.getElementById('ghichu').value = user.ghichu;
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>

