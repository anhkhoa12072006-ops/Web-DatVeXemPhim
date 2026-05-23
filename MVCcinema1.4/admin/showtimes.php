<?php
require_once 'includes/auth.php';
require_once 'models/Showtime.php';
require_once 'models/Movie.php';
require_once 'models/Room.php';

$page_title = 'Quản lý suất chiếu - CTs Cinema Admin';
$current_page = 'showtimes';

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $maphim = (int)$_POST['maphim'];
        $maphong = (int)$_POST['maphong'];
        $ngaychieu = $_POST['ngaychieu'] ?? '';
        $giochieu = $_POST['giochieu'] ?? '';

        if ($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
            $currentDate = date('Y-m-d');
            $currentTime = date('H:i');
            $oneHourLater = date('H:i', strtotime('+1 hour'));

            if ($ngaychieu < $currentDate) {
                $_SESSION['message'] = 'Ngày chiếu phải từ ngày hiện tại trở đi!';
                $_SESSION['msg_type'] = 'danger';
            } elseif ($ngaychieu == $currentDate && $giochieu < $oneHourLater) {
                $_SESSION['message'] = 'Giờ chiếu phải sau ít nhất 1 giờ kể từ lúc thêm suất chiếu!';
                $_SESSION['msg_type'] = 'danger';
            } else {
                if ($_POST['action'] == 'add') {
                    if (Showtime::add($db, $maphim, $maphong, $ngaychieu, $giochieu)) {
                        $_SESSION['message'] = 'Thêm suất chiếu thành công!';
                        $_SESSION['msg_type'] = 'success';
                    }
                } else {
                    $masuat = (int)$_POST['masuat'];
                    if (Showtime::update($db, $masuat, $maphim, $maphong, $ngaychieu, $giochieu)) {
                        $_SESSION['message'] = 'Cập nhật suất chiếu thành công!';
                        $_SESSION['msg_type'] = 'success';
                    }
                }
            }
        } elseif ($_POST['action'] == 'delete') {
            $masuat = (int)$_POST['masuat'];
            if (Showtime::delete($db, $masuat)) {
                $_SESSION['message'] = 'Xóa suất chiếu thành công!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Không thể xóa suất chiếu đã có người đặt vé!';
                $_SESSION['msg_type'] = 'danger';
            }
        }
        header('Location: showtimes.php');
        exit;
    }
}

// Lấy danh sách suất chiếu
$date_filter = $_GET['date'] ?? '';
$movie_filter = $_GET['movie'] ?? '';

$all_records = Showtime::getAll($db, $date_filter, $movie_filter);

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$showtimes = array_slice($all_records, ($page - 1) * $limit, $limit);

$movies = $db->select("SELECT maphim, tenphim FROM phim ORDER BY tenphim");
$rooms = $db->select("SELECT p.maphong, p.tenphong, r.tenrap FROM phongchieu p LEFT JOIN rapchieu r ON p.marap = r.marap ORDER BY r.tenrap, p.tenphong");

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
                    <h4 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Quản lý suất chiếu</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#showtimeModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle me-2"></i> Thêm suất chiếu
                </button>
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

            <!-- Filter Card -->
            <div class="card border-0 mb-4 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-medium mb-1">Ngày chiếu</label>
                            <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-medium mb-1">Bộ phim</label>
                            <select class="form-select" name="movie">
                                <option value="">Tất cả phim</option>
                                <?php foreach ($movies as $movie): ?>
                                    <option value="<?php echo $movie['maphim']; ?>" <?php echo $movie_filter == $movie['maphim'] ? 'selected' : ''; ?>>
                                        <?php echo $movie['tenphim']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-light w-100">
                                <i class="bi bi-funnel me-1"></i> Lọc dữ liệu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Showtimes Table -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Mã</th>
                                    <th>Phim</th>
                                    <th>Rạp chiếu</th>
                                    <th>Phòng</th>
                                    <th>Ngày chiếu</th>
                                    <th>Giờ chiếu</th>
                                    <th>Giá vé (Từ phim)</th>
                                    <th>Vé đã bán</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($showtimes)): ?>
                                    <?php foreach ($showtimes as $showtime): ?>
                                        <?php 
                                        $conlai = $showtime['tongghe'] - $showtime['daban'];
                                        $percent = ($showtime['tongghe'] > 0) ? ($showtime['daban'] / $showtime['tongghe']) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td class="ps-4 text-muted">#<?php echo $showtime['masuat']; ?></td>
                                            <td class="fw-medium text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($showtime['tenphim']); ?>">
                                                <?php echo htmlspecialchars($showtime['tenphim']); ?>
                                            </td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="bi bi-building me-1"></i><?php echo $showtime['tenrap'] ?? 'Không rõ'; ?></span></td>
                                            <td><span class="badge bg-secondary bg-opacity-25 text-light px-2 py-1"><?php echo $showtime['tenphong']; ?></span></td>
                                            <td class="text-muted"><?php echo date('d/m/Y', strtotime($showtime['ngaychieu'])); ?></td>
                                            <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fs-6"><?php echo substr($showtime['giochieu'], 0, 5); ?></span></td>
                                            <td class="fw-bold"><?php echo isset($showtime['giave']) ? number_format($showtime['giave']) : 0; ?>đ</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1 rounded-pill bg-dark" style="height: 6px; width: 60px;">
                                                        <div class="progress-bar rounded-pill bg-<?php echo $percent > 80 ? 'danger' : ($percent > 50 ? 'warning' : 'success'); ?>" 
                                                             style="width: <?php echo $percent; ?>%"></div>
                                                    </div>
                                                    <span class="small text-muted" style="min-width: 45px;"><?php echo $showtime['daban']; ?>/<?php echo $showtime['tongghe']; ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($conlai <= 0): ?>
                                                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25">Hết vé</span>
                                                <?php elseif ($conlai < 10): ?>
                                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">Sắp hết</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Còn <?php echo $conlai; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-warning btn-sm" onclick='editShowtime(<?php echo json_encode($showtime, JSON_HEX_APOS); ?>)' title="Sửa">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa suất chiếu này?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="masuat" value="<?php echo $showtime['masuat']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-calendar-x fs-1 text-muted opacity-50 d-block mb-3"></i>
                                            <p class="text-muted mb-0">Chưa có dữ liệu suất chiếu</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <div class='mt-4'><?php include '../pagination.php'; ?></div>
        </div>
    </div>
</div>

<!-- Showtime Modal -->
<div class="modal fade" id="showtimeModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm suất chiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="masuat" id="masuat">
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Phim <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg fs-6" name="maphim" id="maphim" required>
                                <option value="">-- Chọn phim --</option>
                                <?php foreach ($movies as $movie): ?>
                                    <option value="<?php echo $movie['maphim']; ?>"><?php echo $movie['tenphim']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Phòng chiếu <span class="text-danger">*</span></label>
                            <select class="form-select" name="maphong" id="maphong" required>
                                <option value="">-- Chọn phòng --</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo $room['maphong']; ?>"><?php echo htmlspecialchars($room['tenphong']); ?> (<?php echo htmlspecialchars($room['tenrap'] ?? 'Không có rạp'); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Ngày chiếu <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ngaychieu" id="ngaychieu" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Giờ chiếu <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="giochieu" id="giochieu" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary p-4">
                    <button type="button" class="btn btn-outline-light px-4 rounded-3" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger px-5 rounded-3 fw-medium">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').textContent = 'Thêm suất chiếu';
    document.getElementById('action').value = 'add';
    document.getElementById('masuat').value = '';
    document.querySelector('#showtimeModal form').reset();
}

function editShowtime(showtime) {
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa suất chiếu';
    document.getElementById('action').value = 'edit';
    document.getElementById('masuat').value = showtime.masuat;
    document.getElementById('maphim').value = showtime.maphim;
    document.getElementById('maphong').value = showtime.maphong;
    document.getElementById('ngaychieu').value = showtime.ngaychieu;
    document.getElementById('giochieu').value = showtime.giochieu;
    
    // Khi chỉnh sửa có thể bỏ qua ràng buộc "hiện tại" để cho phép sửa suất cũ (nếu cần), 
    // nhưng ở đây chúng ta giữ nguyên min date.
    document.getElementById('ngaychieu').min = ""; // Cho phép sửa cả suất cũ nếu là admin

    new bootstrap.Modal(document.getElementById('showtimeModal')).show();
}

// Client-side validation for time
document.getElementById('showtimeModal').querySelector('form').onsubmit = function(e) {
    const action = document.getElementById('action').value;
    if (action === 'delete') return true;

    const ngayInput = document.getElementById('ngaychieu').value;
    const gioInput = document.getElementById('giochieu').value;
    const now = new Date();
    const todayStr = now.toISOString().split('T')[0];
    
    if (ngayInput < todayStr && action === 'add') {
        alert('Ngày chiếu phải từ ngày hiện tại trở đi!');
        return false;
    }
    
    if (ngayInput === todayStr) {
        const selectedTime = new Date(todayStr + 'T' + gioInput);
        const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
        
        if (selectedTime < oneHourLater) {
            alert('Giờ chiếu phải sau ít nhất 1 giờ kể từ lúc này!');
            return false;
        }
    }
    return true;
};
</script>

<?php include 'includes/footer.php'; ?>
