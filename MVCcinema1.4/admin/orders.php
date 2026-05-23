<?php
require_once 'includes/auth.php';
require_once 'models/Order.php';

$page_title = 'Quản lý đơn hàng - CTs Cinema Admin';
$current_page = 'orders';

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $mahd = (int)$_POST['mahd'];
    $trangthai = $_POST['trangthai'] ?? '';
    
    if (Order::updateStatus($db, $mahd, $trangthai)) {
        $_SESSION['message'] = 'Cập nhật trạng thái đơn #' . str_pad($mahd, 4, '0', STR_PAD_LEFT) . ' thành công!';
        $_SESSION['msg_type'] = 'success';
    }
    header('Location: orders.php');
    exit;
}

// Filter
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$all_records = Order::getAll($db, $status_filter, $search);


$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$orders = array_slice($all_records, ($page - 1) * $limit, $limit);
// Thống kê nhanh
$total_revenue = 0;
$total_orders = count($orders);
foreach ($orders as $order) {
    if ($order['trangthai'] == 'Đã thanh toán') {
        $total_revenue += $order['tongtien'];
    }
}

include 'includes/header.php';
?>

<div class="d-flex flex-column flex-lg-row min-vh-100 w-100 overflow-hidden">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1 w-100" style="min-width: 0;">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"> <!-- Đã fix ID ở đây -->
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="page-title ms-2">
                    <h4 class="mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Quản lý giao dịch</h4>
                </div>
            </div>
            <div class="header-right">
                <div class="header-btn">
                    <i class="bi bi-calendar-event me-2"></i>
                    <span><?php echo date('d/m/Y'); ?></span>
                </div>
            </div>
        </header>

        <div class="content">
            <?php if (isset($_SESSION['message'])): 
                  $msg_type = $_SESSION['msg_type'] ?? 'success';
            ?>
                <div class="alert alert-<?php echo $msg_type; ?> border-0 shadow-sm rounded-4 fade show">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                </div>
            <?php endif; ?>

            <!-- Quick Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stats-card stats-info">
                        <div class="stats-icon"><i class="bi bi-receipt"></i></div>
                        <div class="stats-content">
                            <p>Tổng đơn hàng</p>
                            <h3><?php echo number_format($total_orders); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card stats-success">
                        <div class="stats-icon"><i class="bi bi-cash-stack"></i></div>
                        <div class="stats-content">
                            <p>Doanh thu thực nhận</p>
                            <h3><?php echo number_format($total_revenue); ?>đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card stats-warning">
                        <div class="stats-icon"><i class="bi bi-ticket-perforated"></i></div>
                        <div class="stats-content">
                            <p>Vé đã xuất</p>
                            <h3><?php echo number_format(array_sum(array_column($orders, 'sove'))); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" name="search" placeholder="Mã đơn, tên tài khoản khách hàng..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Đã thanh toán" <?php echo $status_filter == 'Đã thanh toán' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                <option value="Chờ thanh toán" <?php echo $status_filter == 'Chờ thanh toán' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                                <option value="Chờ xác nhận" <?php echo $status_filter == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                <option value="Đã hủy" <?php echo $status_filter == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-light w-100 fw-bold">Lọc đơn</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Table -->
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày giao dịch</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): 
                                        $badge_color = ($order['trangthai'] == 'Đã thanh toán' ? 'success' : (strpos($order['trangthai'], 'Chờ') !== false ? 'warning' : 'danger'));
                                    ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-danger">#<?php echo str_pad($order['mahd'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="admin-avatar" style="width:30px; height:30px; font-size: 0.8rem;">
                                                        <?php echo strtoupper(substr($order['tendn'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <span class="d-block fw-medium"><?php echo htmlspecialchars($order['tendn']); ?></span>
                                                        <span class="badge bg-secondary bg-opacity-10 text-muted" style="font-size: 0.65rem;"><?php echo strtoupper($order['quyen']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-muted small"><?php echo date('d/m/Y - H:i', strtotime($order['ngaydat'])); ?></td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3"><?php echo $order['sove']; ?> vé</span></td>
                                            <td class="fw-bold"><?php echo number_format($order['tongtien']); ?> đ</td>
                                            <td>
                                                <span class="badge bg-<?php echo $badge_color; ?> bg-opacity-10 text-<?php echo $badge_color; ?> border border-<?php echo $badge_color; ?> border-opacity-25 px-3">
                                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> <?php echo $order['trangthai']; ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $order['mahd']; ?>" title="Xem chi tiết"><i class="bi bi-eye"></i></button>
                                                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $order['mahd']; ?>" title="Đổi trạng thái"><i class="bi bi-pencil-square"></i></button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Detail Modal (Hóa đơn style) -->
                                        <div class="modal fade" id="detailModal<?php echo $order['mahd']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" id="print_invoice_<?php echo $order['mahd']; ?>">
                                                    <div class="modal-header border-0 bg-danger bg-opacity-10 p-4">
                                                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-ticket-perforated me-2"></i>Chi tiết đơn #<?php echo str_pad($order['mahd'], 4, '0', STR_PAD_LEFT); ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Tên tài khoản:</span>
                                                            <span class="fw-bold"><?php echo htmlspecialchars($order['tendn']); ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-4">
                                                            <span class="text-muted">Ngày thực hiện:</span>
                                                            <span><?php echo date('d/m/Y H:i:s', strtotime($order['ngaydat'])); ?></span>
                                                        </div>
                                                        
                                                        <div class="p-3 bg-dark bg-opacity-50 rounded-4 mb-4 border border-secondary border-opacity-10">
                                                            <?php
                                                            $details = Order::getDetails($db, $order['mahd']);
                                                            foreach ($details as $d): ?>
                                                                <div class="mb-3 last-child-mb-0">
                                                                    <div class="fw-bold text-primary"><?php echo htmlspecialchars($d['tenphim']); ?></div>
                                                                    <div class="d-flex justify-content-between small text-muted">
                                                                        <span><?php echo $d['tenphong']; ?> | Suất: <?php echo substr($d['giochieu'],0,5); ?> | Ghế: <?php echo $d['tenghe']; ?></span>
                                                                        <span><?php echo number_format($d['giave']); ?>đ</span>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>

                                                        <div class="d-flex justify-content-between fs-5 fw-bold">
                                                            <span>Tổng thanh toán</span>
                                                            <span class="text-danger"><?php echo number_format($order['tongtien']); ?> đ</span>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 d-flex gap-2 action-buttons">
                                                        <button class="btn btn-danger flex-grow-1 fw-bold rounded-3 export-admin-btn" data-id="<?php echo $order['mahd']; ?>">
                                                            <i class="bi bi-file-earmark-pdf"></i> Xuất Hóa Đơn
                                                        </button>
                                                        <button class="btn btn-outline-light flex-grow-1 fw-bold rounded-3" data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Update Modal -->
                                        <div class="modal fade" id="statusModal<?php echo $order['mahd']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                                    <form method="POST">
                                                        <div class="modal-header border-0 p-4">
                                                            <h5 class="modal-title fw-bold">Cập nhật đơn hàng</h5>
                                                        </div>
                                                        <div class="modal-body px-4 pb-4">
                                                            <input type="hidden" name="mahd" value="<?php echo $order['mahd']; ?>">
                                                            <label class="form-label text-muted small fw-bold">CHỌN TRẠNG THÁI MỚI</label>
                                                            <select class="form-select form-select-lg fs-6 rounded-3" name="trangthai">
                                                                <option value="Đã thanh toán" <?php echo $order['trangthai'] == 'Đã thanh toán' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                                                <option value="Chờ thanh toán" <?php echo $order['trangthai'] == 'Chờ thanh toán' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                                                                <option value="Chờ xác nhận" <?php echo $order['trangthai'] == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                                                <option value="Đã hủy" <?php echo $order['trangthai'] == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                                                            </select>
                                                        </div>
                                                        <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                                                            <button type="button" class="btn btn-outline-light flex-fill" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" name="update_status" class="btn btn-danger flex-fill fw-bold">Lưu lại</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 opacity-50">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Chưa tìm thấy đơn hàng nào khớp với yêu cầu
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4"><?php include '../pagination.php'; ?></div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Include html2pdf library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.export-admin-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const invoiceId = this.dataset.id;
                const element = document.getElementById('print_invoice_' + invoiceId);
                
                // Hide buttons and close icon temporarily
                const footer = element.querySelector('.modal-footer');
                const closeBtn = element.querySelector('.btn-close');
                footer.style.display = 'none';
                if (closeBtn) closeBtn.style.display = 'none';
                
                const opt = {
                    margin:       0.5,
                    filename:     'hoa_don_CTs_Cinema_' + invoiceId + '.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save().then(() => {
                    // Restore
                    footer.style.display = 'flex';
                    if (closeBtn) closeBtn.style.display = 'block';
                });
            });
        });
    });
</script>