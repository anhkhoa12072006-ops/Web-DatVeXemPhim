<?php
require_once 'includes/auth.php';
require_once 'models/Room.php';
require_once 'models/Cinema.php';

$page_title = 'Quản lý phòng chiếu - CTs Cinema Admin';
$current_page = 'rooms';

// Lấy danh sách rạp
$cinemas = Cinema::getAll($db);

// Xử lý thêm/sửa/xóa phòng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $marap = (int)($_POST['marap'] ?? 0);
        $tenphong = $_POST['tenphong'] ?? '';
        $tongghe = (int)($_POST['tongghe'] ?? 0);
        $tinhtrang = $_POST['tinhtrang'] ?? '';
        
        if ($_POST['action'] == 'add') {
            if (Room::add($db, $marap, $tenphong, $tongghe, $tinhtrang)) {
                $_SESSION['message'] = 'Thêm phòng và setup sẵn ' . $tongghe . ' ghế thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'edit') {
            $maphong = (int)$_POST['maphong'];
            if (Room::update($db, $maphong, $marap, $tenphong, $tongghe, $tinhtrang)) {
                $_SESSION['message'] = 'Cập nhật phòng chiếu thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'delete') {
            $maphong = (int)$_POST['maphong'];
            if (Room::delete($db, $maphong)) {
                $_SESSION['message'] = 'Xóa phòng chiếu thành công!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Không thể xóa phòng này vì đang có các suất chiếu đã bán vé!';
                $_SESSION['msg_type'] = 'danger';
            }
        }
        header('Location: rooms.php');
        exit;
    }
}

// Lấy danh sách phòng
$all_records = Room::getAll($db);

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$rooms = array_slice($all_records, ($page - 1) * $limit, $limit);
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
                    <h4 class="mb-0"><i class="bi bi-door-open me-2 text-primary"></i>Quản lý phòng chiếu</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#roomModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle me-2"></i> Thêm phòng chiếu
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

            <!-- Rooms Grid -->
            <div class="row g-4">
                <?php if (!empty($rooms)): ?>
                    <?php foreach ($rooms as $room): ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 border-0 shadow-sm rounded-4" style="transition: transform 0.3s ease;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <h5 class="mb-2 fw-bold d-flex align-items-center gap-2">
                                                <i class="bi bi-door-open text-primary"></i> 
                                                <?php echo htmlspecialchars($room['tenphong']); ?>
                                            </h5>
                                            <small class="text-muted d-block mb-2"><i class="bi bi-building"></i> Rạp: <?php echo htmlspecialchars($room['tenrap'] ?? 'Không rõ'); ?></small>
                                            <span class="badge bg-<?php echo $room['tinhtrang'] == 'Hoạt động' ? 'success' : 'danger'; ?> bg-opacity-25 text-<?php echo $room['tinhtrang'] == 'Hoạt động' ? 'success' : 'danger'; ?> border border-<?php echo $room['tinhtrang'] == 'Hoạt động' ? 'success' : 'danger'; ?> border-opacity-25 px-2 py-1">
                                                <i class="bi bi-circle-fill" style="font-size: 0.5rem; margin-right: 4px; vertical-align: middle;"></i> <?php echo $room['tinhtrang']; ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Menu Thao tác (Dropdown) -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-light border-0 rounded-circle p-2" data-bs-toggle="dropdown" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-three-dots-vertical text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-secondary rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick='editRoom(<?php echo json_encode($room, JSON_HEX_APOS); ?>)'>
                                                        <i class="bi bi-pencil-square text-warning"></i> Chỉnh sửa thông tin
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="room-seats.php?id=<?php echo $room['maphong']; ?>">
                                                        <i class="bi bi-grid-3x3 text-info"></i> Cấu hình sơ đồ ghế
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider border-secondary"></li>
                                                <li>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Toàn bộ cấu hình ghế của phòng này sẽ bị mất. Bạn có chắc muốn xóa?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="maphong" value="<?php echo $room['maphong']; ?>">
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                                                            <i class="bi bi-trash3"></i> Xóa phòng chiếu
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Thống kê Mini -->
                                    <div class="row g-2 text-center mb-4 mt-auto">
                                        <div class="col-4">
                                            <div class="p-2 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                                                <h5 class="mb-0 text-danger fw-bold"><?php echo $room['tongghe']; ?></h5>
                                                <small class="text-muted" style="font-size: 0.75rem;">Tổng ghế</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                                                <h5 class="mb-0 text-success fw-bold"><?php echo $room['ghehientai']; ?></h5>
                                                <small class="text-muted" style="font-size: 0.75rem;">Đã setup</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                                                <h5 class="mb-0 text-warning fw-bold"><?php echo $room['suatchieu']; ?></h5>
                                                <small class="text-muted" style="font-size: 0.75rem;">Suất chiếu</small>
                                            </div>
                                        </div>
                                    </div>

                                   <!-- Nút Vào sơ đồ ghế -->
                                    <a href="room-seats.php?id=<?php echo $room['maphong']; ?>" 
                                    class="btn btn-outline-light w-100 rounded-3 fw-medium">
                                        <i class="bi bi-grid-3x3 me-2"></i> Vào sơ đồ ghế
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card border-0 rounded-4">
                            <div class="card-body text-center py-5 text-muted">
                                <i class="bi bi-door-closed fs-1 d-block mb-3 opacity-50"></i>
                                <h5 class="fw-normal">Chưa có phòng chiếu nào</h5>
                                <p class="mb-0">Vui lòng thêm phòng chiếu mới để bắt đầu setup ghế.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <div class='mt-4'><?php include '../pagination.php'; ?></div>
        </div>
    </div>
</div>

<!-- Room Modal -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm phòng chiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="maphong" id="maphong">
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Thuộc rạp chiếu <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg fs-6" name="marap" id="marap" required>
                                <option value="">-- Chọn rạp chiếu --</option>
                                <?php foreach ($cinemas as $cinema): ?>
                                    <option value="<?php echo $cinema['marap']; ?>"><?php echo htmlspecialchars($cinema['tenrap']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Tên phòng chiếu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fs-6" name="tenphong" id="tenphong" placeholder="Ví dụ: Phòng 01, Cinema 2..." required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Sức chứa (Tổng ghế) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="tongghe" id="tongghe" placeholder="Ví dụ: 50" value="50" required min="1">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Tình trạng</label>
                            <select class="form-select" name="tinhtrang" id="tinhtrang">
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Bảo trì">Đang bảo trì</option>
                            </select>
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
    document.getElementById('modalTitle').textContent = 'Thêm phòng chiếu mới';
    document.getElementById('action').value = 'add';
    document.getElementById('maphong').value = '';
    document.getElementById('marap').value = '';
    document.querySelector('#roomModal form').reset();
}

function editRoom(room) {
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa phòng chiếu';
    document.getElementById('action').value = 'edit';
    document.getElementById('maphong').value = room.maphong;
    document.getElementById('marap').value = room.marap;
    document.getElementById('tenphong').value = room.tenphong;
    document.getElementById('tongghe').value = room.tongghe;
    document.getElementById('tinhtrang').value = room.tinhtrang;
    
    new bootstrap.Modal(document.getElementById('roomModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
