<?php
require_once 'includes/auth.php';
require_once 'models/Setting.php';

$page_title = 'Cấu hình hệ thống - CTs Cinema Admin';
$current_page = 'settings';

// Xử lý lưu cấu hình
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $vietqr_bank = $_POST['vietqr_bank'] ?? '';
    $vietqr_account = $_POST['vietqr_account'] ?? '';
    $vietqr_name = $_POST['vietqr_name'] ?? '';

    Setting::update($db, $vietqr_bank, $vietqr_account, $vietqr_name);

    $_SESSION['message'] = 'Cập nhật cấu hình VietQR thành công!';
    $_SESSION['msg_type'] = 'success';
    header('Location: settings.php');
    exit;
}

// Lấy danh sách cấu hình hiện tại
$settings = Setting::getAll($db);

// Khởi tạo giá trị mặc định nếu chưa có
$current_bank = $settings['vietqr_bank'] ?? 'vietcombank';
$current_account = $settings['vietqr_account'] ?? '1012345678';
$current_name = $settings['vietqr_name'] ?? 'NGUYEN NGOC MINH NHA';

include 'includes/header.php';
?>

<div class="d-flex flex-column flex-lg-row min-vh-100 w-100 overflow-hidden">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1 w-100" style="min-width: 0;">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="page-title ms-2">
                    <h4 class="mb-0"><i class="bi bi-gear me-2 text-primary"></i>Cấu hình hệ thống</h4>
                </div>
            </div>
        </header>

        <div class="content">
            <!-- Thông báo Alert -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'success'; ?> alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom p-4">
                            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--primary-color);">
                                <i class="bi bi-qr-code-scan fs-4"></i> Cấu hình Thanh toán VietQR (Hỗ trợ MoMo & Bank)
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                
                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold">Môi trường (Environment)</label>
                                    <input type="text" class="form-control form-control-lg bg-light" value="VietQR - Quét mã mọi ngân hàng và ví điện tử" disabled>
                                    <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle"></i> Khách hàng có thể dùng App MoMo, ZaloPay, ViettelPay hoặc bất kỳ app Ngân hàng nào để quét thanh toán.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold">Ngân hàng nhận tiền <span class="text-danger">*</span></label>
                                    <select class="form-select" name="vietqr_bank" required>
                                        <option value="vietcombank" <?php echo $current_bank == 'vietcombank' ? 'selected' : ''; ?>>Vietcombank - Ngân hàng Ngoại Thương</option>
                                        <option value="mbbank" <?php echo $current_bank == 'mbbank' ? 'selected' : ''; ?>>MBBank - Ngân hàng Quân Đội</option>
                                        <option value="tpbank" <?php echo $current_bank == 'tpbank' ? 'selected' : ''; ?>>TPBank - Ngân hàng Tiên Phong</option>
                                        <option value="techcombank" <?php echo $current_bank == 'techcombank' ? 'selected' : ''; ?>>Techcombank - Ngân hàng Kỹ Thương</option>
                                        <option value="vietinbank" <?php echo $current_bank == 'vietinbank' ? 'selected' : ''; ?>>Vietinbank - Ngân hàng Công Thương</option>
                                        <option value="bidv" <?php echo $current_bank == 'bidv' ? 'selected' : ''; ?>>BIDV - Ngân hàng Đầu tư và Phát triển</option>
                                        <option value="agribank" <?php echo $current_bank == 'agribank' ? 'selected' : ''; ?>>Agribank - Ngân hàng Nông Nghiệp</option>
                                        <option value="acb" <?php echo $current_bank == 'acb' ? 'selected' : ''; ?>>ACB - Ngân hàng Á Châu</option>
                                        <option value="vib" <?php echo $current_bank == 'vib' ? 'selected' : ''; ?>>VIB - Ngân hàng Quốc Tế</option>
                                        <option value="vpbank" <?php echo $current_bank == 'vpbank' ? 'selected' : ''; ?>>VPBank - Ngân hàng Việt Nam Thịnh Vượng</option>
                                        <option value="hdbank" <?php echo $current_bank == 'hdbank' ? 'selected' : ''; ?>>HDBank - Ngân hàng Phát triển TP.HCM</option>
                                        <option value="sacombank" <?php echo $current_bank == 'sacombank' ? 'selected' : ''; ?>>Sacombank - Ngân hàng Sài Gòn Thương Tín</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold">Số tài khoản nhận tiền <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="vietqr_account" value="<?php echo htmlspecialchars($current_account); ?>" placeholder="Ví dụ: 0333119742" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold">Tên chủ tài khoản <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="vietqr_name" value="<?php echo htmlspecialchars($current_name); ?>" placeholder="VI DU: NGUYEN VAN A (Viết hoa không dấu)" required>
                                </div>
                                
                                <div class="text-end border-top pt-4">
                                    <button type="submit" class="btn btn-danger px-5 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-save me-2"></i> Lưu cấu hình
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb text-warning"></i> Sức mạnh của VietQR</h5>
                            <p class="text-muted small mb-3">
                                Đây là chuẩn thanh toán hiện đại nhất Việt Nam hiện nay. Mã QR sinh ra <strong>có thể dùng mọi App Ngân hàng hoặc App MoMo để quét</strong>.
                            </p>
                            <p class="text-muted small mb-0">
                                Mã QR sẽ tự động nhúng sẵn Số tài khoản, Ngân hàng, Số tiền và Nội dung. Khách hàng chỉ việc quét và bấm chuyển tiền mà không cần nhập thủ công.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


