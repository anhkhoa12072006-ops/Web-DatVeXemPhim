<?php
require_once 'includes/auth.php';
require_once 'models/Dashboard.php';

$page_title = 'Dashboard - CTs Cinema Admin';
$current_page = 'dashboard';

$stats = Dashboard::getStats($db);
$total_movies = $stats['total_movies'];
$total_showtimes = $stats['total_showtimes'];
$total_orders = $stats['total_orders'];
$total_users = $stats['total_users'];
$total_revenue = $stats['total_revenue'];

$recent_orders = Dashboard::getRecentOrders($db);
$popular_movies = Dashboard::getPopularMovies($db);

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
                    <h4 class="mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Tổng quan Dashboard</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="header-btn d-none d-sm-flex">
                    <i class="bi bi-clock"></i>
                    <span><?php echo date('d/m/Y'); ?></span>
                </button>
                <button class="header-btn position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <div class="content">
            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-sm-6 col-lg-3">
                    <div class="stats-card stats-danger">
                        <div class="stats-icon">
                            <i class="bi bi-film"></i>
                        </div>
                        <div class="stats-content">
                            <p>Tổng phim đang chiếu</p>
                            <h3><?php echo number_format($total_movies); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="stats-card stats-success">
                        <div class="stats-icon">
                            <i class="bi bi-ticket-detailed"></i>
                        </div>
                        <div class="stats-content">
                            <p>Suất chiếu hôm nay</p>
                            <h3><?php echo number_format($total_showtimes); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="stats-card stats-warning">
                        <div class="stats-icon">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="stats-content">
                            <p>Tổng đơn hàng</p>
                            <h3><?php echo number_format($total_orders); ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="stats-card stats-info">
                        <div class="stats-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="stats-content">
                            <p>Doanh thu tháng này</p>
                            <h3><?php echo number_format($total_revenue); ?><span class="fs-6 text-muted ms-1">VNĐ</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Orders Table -->
                <div class="col-lg-8">
                    <div class="card h-100 border-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">
                                <i class="bi bi-clock-history text-primary me-2"></i> Giao dịch gần đây
                            </h5>
                            <a href="orders.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                Xem tất cả
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Mã ĐH</th>
                                            <th>Khách hàng</th>
                                            <th>Ngày đặt</th>
                                            <th>Số vé</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recent_orders)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                                    Chưa có đơn hàng nào
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td><span class="text-danger fw-bold">#<?php echo $order['mahd']; ?></span></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="bg-secondary bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                <i class="bi bi-person text-light"></i>
                                                            </div>
                                                            <span class="fw-medium"><?php echo $order['tendn']; ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-muted"><?php echo date('d/m/Y', strtotime($order['ngaydat'])); ?></td>
                                                    <td><span class="badge bg-secondary bg-opacity-25 text-light"><?php echo $order['sove'] ?? 0; ?> vé</span></td>
                                                    <td class="fw-bold"><?php echo number_format($order['tongtien']); ?> đ</td>
                                                    <td>
                                                        <?php if ($order['trangthai'] == 'Đã thanh toán'): ?>
                                                            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Đã thanh toán</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning"><i class="bi bi-hourglass-split me-1"></i>Chờ xử lý</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular Movies List -->
                <div class="col-lg-4">
                    <div class="card border-0 mb-4">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="bi bi-fire text-danger me-2"></i> Phim đang hot
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($popular_movies)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-film fs-1 d-block mb-3 opacity-50"></i>
                                    Chưa có dữ liệu
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush rounded-bottom">
                                    <?php foreach ($popular_movies as $index => $movie): ?>
                                        <div class="list-group-item bg-transparent d-flex align-items-center gap-3">
                                            <div class="fw-bold text-muted fs-5">#<?php echo $index + 1; ?></div>
                                            <?php if ($movie['hinh']): ?>
                                                <img src="../assets/images/<?php echo $movie['hinh']; ?>" 
                                                     alt="<?php echo $movie['tenphim']; ?>"
                                                     class="rounded shadow-sm"
                                                     style="width: 45px; height: 65px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 65px;">
                                                    <i class="bi bi-film text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-fill overflow-hidden">
                                                <h6 class="mb-1 text-truncate" title="<?php echo $movie['tenphim']; ?>"><?php echo $movie['tenphim']; ?></h6>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                    <i class="bi bi-ticket-perforated me-1"></i> <?php echo $movie['sove']; ?> lượt mua
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

