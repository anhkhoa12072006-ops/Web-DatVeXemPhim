<?php
require_once 'includes/auth.php';
require_once 'models/Payment.php';

if (!isset($_SESSION['pending_booking'])) {
    header('Location: index.php');
    exit;
}

$booking = $_SESSION['pending_booking'];
$tongtien = $booking['tongtien'];
$masuat = $booking['masuat'];
$selected_seats = $booking['seats'];

// Khởi tạo CSRF Token để bảo mật Form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Xử lý khi user bấm nút "Đã thanh toán" (Demo giả lập IPN)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    // 1. CƠ CHẾ BẢO MẬT: Kiểm tra CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Lỗi bảo mật: CSRF Token không hợp lệ!");
    }

    // 2. Xác thực Mã giao dịch (Giả lập tự động cấp mã)
    $trans_id = "DEMO_" . rand(100000000, 999999999);
    
    $tendn = $_SESSION['tendn'];
    $mahd = Payment::createVietQROrder($db, $tendn, $tongtien, $masuat, $selected_seats);

    if ($mahd) {
        unset($_SESSION['pending_booking']);
        $_SESSION['message'] = "Thanh toán thành công (Mã GD: $trans_id)! Cảm ơn bạn đã đặt vé.";
        header('Location: my-tickets.php?success=1');
        exit;
    }
}

// Lấy cấu hình VietQR từ database
$settings = Payment::getSettings($db);

// Lấy cấu hình VietQR từ Admin
$vietqr_bank = $settings['vietqr_bank'] ?? 'BIDV';
$vietqr_account = $settings['vietqr_account'] ?? '5811747174';
$vietqr_name = $settings['vietqr_name'] ?? 'LE THI KIM THUYEN';
$orderId = time() . "_" . rand(100, 999);
$noidung = "VXP" . $orderId;

// Link tạo mã VietQR tự động điền thông tin qua API vietqr.io
$qr_url = "https://img.vietqr.io/image/" . $vietqr_bank . "-" . $vietqr_account . "-compact2.png?amount=" . $tongtien . "&addInfo=" . urlencode($noidung) . "&accountName=" . urlencode($vietqr_name);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán VietQR - CTs Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .vietqr-color { color: #004282; }
        .vietqr-bg { background-color: #004282; }
        .payment-box {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 66, 130, 0.1);
            overflow: hidden;
        }
        .qr-container {
            border: 2px dashed #004282;
            padding: 10px;
            border-radius: 15px;
            display: inline-block;
            background: #fff;
        }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/banner.php'; ?>

<div class="container">
    <div class="payment-box">
        <div class="vietqr-bg text-white text-center py-4">
            <h4 class="mb-0 fw-bold"><i class="bi bi-qr-code-scan me-2"></i>Thanh toán VietQR</h4>
        </div>
        
        <div class="p-4 text-center">
            <p class="text-muted mb-4">Mở App Ngân hàng hoặc MoMo/ZaloPay để quét mã QR bên dưới.</p>
            
            <div class="qr-container mb-4 shadow-sm">
                <img src="<?php echo $qr_url; ?>" alt="VietQR Code" class="img-fluid" style="width: 250px;">
            </div>
            
            <div class="bg-light rounded-4 p-3 mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Ngân hàng:</span>
                    <span class="fw-bold text-uppercase"><?php echo $vietqr_bank; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Chủ tài khoản:</span>
                    <span class="fw-bold"><?php echo strtoupper($vietqr_name); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Số tài khoản:</span>
                    <span class="fw-bold fs-5 vietqr-color"><?php echo $vietqr_account; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Số tiền:</span>
                    <span class="fw-bold fs-5 text-danger"><?php echo number_format($tongtien); ?> VNĐ</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Nội dung chuyển khoản:</span>
                    <span class="fw-bold text-primary"><?php echo $noidung; ?></span>
                </div>
            </div>
            
            <div class="alert alert-warning small text-start mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>Vì đây là phiên bản Demo cá nhân, hệ thống không tự động quét API ngân hàng. Vui lòng bấm nút xác nhận bên dưới sau khi bạn đã chuyển tiền thành công trên ứng dụng MoMo.
            </div>
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger small text-start mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <button type="submit" name="confirm_payment" class="btn vietqr-bg text-white w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>Tôi đã thanh toán thành công
                </button>
            </form>
            
            <a href="index.php" class="text-decoration-none text-muted fw-semibold">
                <i class="bi bi-arrow-left me-1"></i>Hủy giao dịch & Quay lại
            </a>
        </div>
    </div>
</div>

</body>
</html>
