<?php
require_once 'includes/auth.php';
require_once 'models/Cinema.php';

$page_title = 'Quản lý rạp chiếu - CTs Cinema Admin';
$current_page = 'cinemas';

// Xử lý thêm/sửa/xóa rạp chiếu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $tenrap = $_POST['tenrap'] ?? '';
        $diachi = $_POST['diachi'] ?? '';
        $hotline = $_POST['hotline'] ?? '';
        
        if ($_POST['action'] == 'add') {
            if (Cinema::add($db, $tenrap, $diachi, $hotline)) {
                $_SESSION['message'] = 'Thêm rạp chiếu thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'edit') {
            $marap = (int)$_POST['marap'];
            if (Cinema::update($db, $marap, $tenrap, $diachi, $hotline)) {
                $_SESSION['message'] = 'Cập nhật rạp chiếu thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'delete') {
            $marap = (int)$_POST['marap'];
            if (Cinema::delete($db, $marap)) {
                $_SESSION['message'] = 'Xóa rạp chiếu thành công!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Không thể xóa rạp này vì đang có phòng chiếu phụ thuộc!';
                $_SESSION['msg_type'] = 'danger';
            }
        }
        header('Location: cinemas.php');
        exit;
    }
}

// Lấy danh sách rạp
$all_records = Cinema::getAll($db);


$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$cinemas = array_slice($all_records, ($page - 1) * $limit, $limit);
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
                    <h4 class="mb-0"><i class="bi bi-building me-2 text-primary"></i>Quản lý rạp chiếu</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#cinemaModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle me-2"></i> Thêm rạp chiếu
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

            <!-- Cinemas Grid -->
            <div class="row g-4">
                <?php if (!empty($cinemas)): ?>
                    <?php foreach ($cinemas as $cinema): ?>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4" style="transition: transform 0.3s ease;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <h5 class="mb-2 fw-bold d-flex align-items-center gap-2">
                                                <i class="bi bi-building text-primary"></i> 
                                                <?php echo htmlspecialchars($cinema['tenrap']); ?>
                                            </h5>
                                            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1">
                                                <i class="bi bi-door-open-fill"></i> <?php echo $cinema['sophong']; ?> phòng chiếu
                                            </span>
                                        </div>
                                        
                                        <!-- Menu Thao tác (Dropdown) -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-light border-0 rounded-circle p-2" data-bs-toggle="dropdown" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-secondary rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick='editCinema(<?php echo json_encode($cinema, JSON_HEX_APOS); ?>)'>
                                                        <i class="bi bi-pencil-square text-warning"></i> Chỉnh sửa thông tin
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider border-secondary"></li>
                                                <li>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa rạp này không?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="marap" value="<?php echo $cinema['marap']; ?>">
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                                                            <i class="bi bi-trash3"></i> Xóa rạp chiếu
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <p class="mb-1 text-muted small"><i class="bi bi-geo-alt-fill me-2"></i><strong>Địa chỉ:</strong></p>
                                        <p class="mb-2 fw-medium text-truncate" title="<?php echo htmlspecialchars($cinema['diachi']); ?>"><?php echo htmlspecialchars($cinema['diachi']); ?></p>
                                        
                                        <p class="mb-1 text-muted small"><i class="bi bi-telephone-fill me-2"></i><strong>Hotline:</strong></p>
                                        <p class="mb-0 fw-medium"><?php echo htmlspecialchars($cinema['hotline']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card border-0 rounded-4">
                            <div class="card-body text-center py-5 text-muted">
                                <i class="bi bi-building fs-1 d-block mb-3 opacity-50"></i>
                                <h5 class="fw-normal">Chưa có rạp chiếu nào</h5>
                                <p class="mb-0">Vui lòng thêm rạp chiếu mới để bắt đầu.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <div class='mt-4'><?php include '../pagination.php'; ?></div>
        </div>
    </div>
</div>

<!-- Cinema Modal -->
<div class="modal fade" id="cinemaModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm rạp chiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="marap" id="marap">
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Tên rạp chiếu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fs-6" name="tenrap" id="tenrap" placeholder="Ví dụ: CTs Cinema Center..." required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Địa chỉ <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="diachi" id="diachi" rows="2" placeholder="Ví dụ: 123 Nguyễn Văn Cừ..." required></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Hotline <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="hotline" id="hotline" placeholder="Ví dụ: 19001234" required>
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
    document.getElementById('modalTitle').textContent = 'Thêm rạp chiếu mới';
    document.getElementById('action').value = 'add';
    document.getElementById('marap').value = '';
    document.querySelector('#cinemaModal form').reset();
}

function editCinema(cinema) {
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa rạp chiếu';
    document.getElementById('action').value = 'edit';
    document.getElementById('marap').value = cinema.marap;
    document.getElementById('tenrap').value = cinema.tenrap;
    document.getElementById('diachi').value = cinema.diachi;
    document.getElementById('hotline').value = cinema.hotline;
    
    new bootstrap.Modal(document.getElementById('cinemaModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>


