<?php
require_once 'includes/auth.php';
require_once 'models/Seat.php';

// Lấy mã phòng từ URL
$maphong = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$maphong) {
    header('Location: rooms.php');
    exit;
}

// Lấy thông tin phòng và rạp
$room_info = Seat::getRoomInfo($db, $maphong);
if (empty($room_info)) {
    header('Location: rooms.php');
    exit;
}
$room = $room_info[0];

$page_title = 'Quản lý ghế - ' . $room['tenphong'] . ' - CTs Cinema Admin';
$current_page = 'rooms';

// Xử lý thêm/sửa/xóa ghế
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $tenghe = $_POST['tenghe'] ?? '';
            $loaighe = $_POST['loaighe'] ?? 'Thường';
            
            if (Seat::add($db, $maphong, $tenghe, $loaighe)) {
                $_SESSION['message'] = 'Thêm ghế mới thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'edit') {
            $maghe = (int)$_POST['maghe'];
            $tenghe = $_POST['tenghe'] ?? '';
            $loaighe = $_POST['loaighe'] ?? 'Thường';
            
            if (Seat::update($db, $maghe, $tenghe, $loaighe)) {
                $_SESSION['message'] = 'Cập nhật thông tin ghế thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'delete') {
            $maghe = (int)$_POST['maghe'];
            
            if (Seat::delete($db, $maghe)) {
                $_SESSION['message'] = 'Đã xóa ghế khỏi sơ đồ!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Không thể xóa ghế này vì đã có khách hàng đặt vé!';
                $_SESSION['msg_type'] = 'danger';
            }
        } elseif ($_POST['action'] == 'auto_generate') {
            $rows = (int)$_POST['rows'];
            $cols = (int)$_POST['cols'];
            $loaighe = $_POST['loaighe'] ?? 'Thường';
            
            $result = Seat::autoGenerate($db, $maphong, $rows, $cols, $loaighe);
            
            if ($result === 'has_tickets') {
                $_SESSION['message'] = 'CẢNH BÁO: Phòng này đã có vé được bán ra. Không thể khởi tạo lại sơ đồ để bảo vệ dữ liệu doanh thu!';
                $_SESSION['msg_type'] = 'danger';
            } elseif ($result !== false) {
                $_SESSION['message'] = "Đã khởi tạo tự động $result ghế thành công!";
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'update_row') {
            $row_letter = $_POST['row_letter'] ?? '';
            $loaighe = $_POST['loaighe'] ?? 'Thường';
            
            if ($row_letter !== '') {
                if (Seat::updateRowType($db, $maphong, $row_letter, $loaighe)) {
                    $display_name = ($row_letter === '_numeric_') ? 'Số' : $row_letter;
                    $_SESSION['message'] = "Đã cập nhật toàn bộ dãy $display_name thành loại $loaighe!";
                    $_SESSION['msg_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Lỗi: Không thể cập nhật dãy ghế!";
                    $_SESSION['msg_type'] = 'danger';
                }
            } else {
                $_SESSION['message'] = "Mã dãy không hợp lệ!";
                $_SESSION['msg_type'] = 'warning';
            }
        }
        header("Location: room-seats.php?id=$maphong");
        exit;
    }
}

// Lấy danh sách ghế
$seats = Seat::getAllByRoom($db, $maphong);

// Nhóm ghế theo hàng để hiển thị sơ đồ
$seats_by_row = [];
foreach ($seats as $seat) {
    $row = preg_replace('/[0-9]/', '', $seat['tenghe']);
    $row_key = ($row === '') ? '_numeric_' : $row; // Dùng mã không dấu cho logic
    if (!isset($seats_by_row[$row_key])) $seats_by_row[$row_key] = [];
    $seats_by_row[$row_key][] = $seat;
}
ksort($seats_by_row);

include 'includes/header.php';
?>

<div class="d-flex flex-column flex-lg-row min-vh-100 w-100 overflow-hidden">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1 w-100" style="min-width: 0;">
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"><i class="bi bi-list fs-4"></i></button>
                <div class="page-title ms-2">
                    <h4 class="mb-0"><i class="bi bi-grid-3x3 me-2 text-primary"></i><?php echo htmlspecialchars($room['tenphong']); ?> <small class="text-muted fs-6">/ <?php echo htmlspecialchars($room['tenrap'] ?? 'Không rõ Rạp'); ?></small></h4>
                </div>
            </div>
            <div class="header-right">
                <a href="rooms.php" class="btn btn-outline-light rounded-pill px-4 fw-bold"><i class="bi bi-arrow-left me-2"></i>Trở lại Phòng</a>
            </div>
        </header>

        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'success'; ?> border-0 shadow-sm rounded-4 fade show">
                    <i class="bi bi-<?php echo ($_SESSION['msg_type'] ?? 'success') == 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> me-2"></i> 
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                </div>
            <?php endif; ?>

            <!-- Action Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card border-0 rounded-4 h-100 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="logo-box bg-primary bg-opacity-10 text-primary" style="width: 60px; height: 60px;">
                                    <i class="bi bi-grid-3x3-gap-fill fs-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="row text-center">
                                        <div class="col-6 border-end border-secondary border-opacity-25">
                                            <small class="text-muted d-block fw-bold">SỨC CHỨA TỐI ĐA</small>
                                            <h4 class="fw-bold mb-0 text-dark"><?php echo $room['tongghe']; ?></h4>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block fw-bold">GHẾ ĐÃ THIẾT LẬP</small>
                                            <h4 class="fw-bold mb-0" style="color: var(--primary-color);"><?php echo count($seats); ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 text-end">
                    <div class="d-grid gap-2">
                        <button class="btn btn-danger py-2 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#addSeatModal"><i class="bi bi-plus-circle-fill me-2"></i>Thêm ghế lẻ</button>
                        <button class="btn btn-outline-light py-2 fw-bold rounded-3 text-dark border-secondary border-opacity-25" data-bs-target="#autoGenerateModal" data-bs-toggle="modal" style="background-color: #fff;"><i class="bi bi-magic me-2 text-warning"></i>Khởi tạo tự động</button>
                    </div>
                </div>
            </div>

            <!-- Visualization -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary border-opacity-10 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">Sơ đồ phòng chiếu</h5>
                    <div class="d-flex gap-3 small text-muted fw-bold">
                        <span><i class="bi bi-square-fill text-secondary opacity-25 me-1"></i>Thường</span>
                        <span><i class="bi bi-square-fill me-1" style="color: var(--primary-color);"></i>VIP</span>
                    </div>
                </div>
                <div class="card-body p-3 p-md-5 overflow-auto text-center" style="background-color: #fffafb;">
                    <?php if (empty($seats)): ?>
                        <div class="py-5 text-muted opacity-50">
                            <i class="bi bi-grid-3x3-gap fs-1 mb-2 d-block"></i>
                            <p class="fw-medium">Chưa có dữ liệu ghế cho phòng này.</p>
                        </div>
                    <?php else: ?>
                        <!-- CSS Inline Tạm thời để fix hiển thị Màn hình & Ghế -->
                        <style>
                            .screen-container { perspective: 500px; margin-bottom: 3rem; }
                            .cinema-screen { 
                                height: 8px; width: 70%; margin: 0 auto; 
                                background: var(--primary-color); border-radius: 4px;
                                box-shadow: 0 10px 30px rgba(216, 17, 89, 0.4);
                            }
                            .seat-box {
                                width: 40px; height: 40px; border-radius: 8px 8px 4px 4px;
                                display: flex; align-items: center; justify-content: center;
                                font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
                                border: 2px solid transparent; margin: 2px;
                            }
                            .seat-normal { background-color: #f1f5f9; border-color: #cbd5e1; color: #475569; }
                            .seat-vip { background-color: #ffe4e8; border-color: var(--primary-color); color: var(--primary-color); }
                            .seat-box:hover { transform: scale(1.15) translateY(-2px); border-color: var(--primary-color); color: white; background-color: var(--primary-color); z-index: 5; box-shadow: 0 5px 15px rgba(216, 17, 89, 0.3);}
                        </style>

                        <div class="screen-container">
                            <div class="cinema-screen"></div>
                            <small class="text-muted fw-bold d-block mt-2" style="letter-spacing: 3px;">MÀN HÌNH</small>
                        </div>

                        <div class="d-inline-flex flex-column gap-2">
                            <?php foreach ($seats_by_row as $row_key => $row_seats): ?>
                                <div class="d-flex align-items-center gap-3 justify-content-center">
                                    <button class="btn btn-sm btn-light border-0 fw-bold text-muted" 
                                            style="width: 32px; height: 32px; padding: 0;"
                                            data-bs-toggle="modal" data-bs-target="#updateRowModal" 
                                            data-row="<?php echo $row_key; ?>"
                                            data-display="<?php echo $row_key === '_numeric_' ? 'Số' : $row_key; ?>">
                                        <?php echo $row_key === '_numeric_' ? 'Số' : $row_key; ?>
                                    </button>
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        <?php foreach ($row_seats as $seat): ?>
                                            <div class="seat-box <?php echo $seat['loaighe'] == 'VIP' ? 'seat-vip' : 'seat-normal'; ?>"
                                                 data-bs-toggle="modal" data-bs-target="#editSeatModal"
                                                 data-maghe="<?php echo $seat['maghe']; ?>"
                                                 data-tenghe="<?php echo $seat['tenghe']; ?>"
                                                 data-loaighe="<?php echo $seat['loaighe']; ?>"
                                                 title="Ghế <?php echo $seat['tenghe']; ?>">
                                                <?php echo preg_replace('/[^0-9]/', '', $seat['tenghe']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="btn btn-sm btn-light border-0 fw-bold text-muted" 
                                            style="width: 32px; height: 32px; padding: 0;"
                                            data-bs-toggle="modal" data-bs-target="#updateRowModal" 
                                            data-row="<?php echo $row_key; ?>"
                                            data-display="<?php echo $row_key === '_numeric_' ? 'Số' : $row_key; ?>">
                                        <?php echo $row_key === '_numeric_' ? 'Số' : $row_key; ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- List Table -->
            <?php if (!empty($seats)): ?>
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th class="ps-4">Số ghế</th>
                                    <th>Loại ghế</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($seats as $seat): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold" style="color: var(--primary-color);"><?php echo $seat['tenghe']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $seat['loaighe'] == 'VIP' ? 'bg-danger' : 'bg-secondary'; ?> bg-opacity-10 text-<?php echo $seat['loaighe'] == 'VIP' ? 'danger' : 'secondary'; ?> border border-<?php echo $seat['loaighe'] == 'VIP' ? 'danger' : 'secondary'; ?> border-opacity-25 px-3">
                                                <?php echo strtoupper($seat['loaighe']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm rounded-3">
                                                <button class="btn btn-sm btn-outline-warning border-0 px-3" data-bs-toggle="modal" data-bs-target="#editSeatModal" data-maghe="<?php echo $seat['maghe']; ?>" data-tenghe="<?php echo $seat['tenghe']; ?>" data-loaighe="<?php echo $seat['loaighe']; ?>"><i class="bi bi-pencil-fill"></i></button>
                                                <button class="btn btn-sm btn-outline-danger border-0 px-3" data-bs-toggle="modal" data-bs-target="#deleteSeatModal" data-maghe="<?php echo $seat['maghe']; ?>" data-tenghe="<?php echo $seat['tenghe']; ?>"><i class="bi bi-trash3-fill"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Thêm Ghế -->
<div class="modal fade" id="addSeatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold" style="color: var(--primary-color);"><i class="bi bi-plus-circle-fill me-2"></i>Thêm ghế lẻ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">SỐ HIỆU GHẾ (VD: A10)</label>
                        <input type="text" class="form-control rounded-3" name="tenghe" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold">LOẠI GHẾ</label>
                        <select class="form-select rounded-3" name="loaighe">
                            <option value="Thường">Thường</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-danger w-100 fw-bold rounded-3 py-2">Thêm vào sơ đồ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Ghế -->
<div class="modal fade" id="editSeatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold text-warning"><i class="bi bi-pencil-fill me-2"></i>Cập nhật ghế</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="maghe" id="edit_maghe">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">SỐ HIỆU GHẾ</label>
                        <input type="text" class="form-control rounded-3" name="tenghe" id="edit_tenghe" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold">LOẠI GHẾ</label>
                        <select class="form-select rounded-3" name="loaighe" id="edit_loaighe">
                            <option value="Thường">Thường</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark rounded-3 py-2">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa Ghế -->
<div class="modal fade" id="deleteSeatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg text-center">
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="maghe" id="delete_maghe">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Xóa ghế <span id="delete_tenghe" style="color: var(--primary-color);"></span>?</h5>
                    <p class="text-muted small mb-4">Hành động này không thể hoàn tác nếu bạn xác nhận.</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light flex-fill rounded-3 border" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger flex-fill fw-bold rounded-3">Xóa ngay</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Khởi tạo tự động -->
<div class="modal fade" id="autoGenerateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary border-opacity-10 p-4" style="background-color: #fffafb;">
                    <h5 class="modal-title fw-bold" style="color: var(--primary-color);"><i class="bi bi-magic me-2 text-warning"></i>Khởi tạo sơ đồ tự động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="auto_generate">
                    <div class="alert alert-danger border-0 rounded-3 p-3 mb-4 d-flex align-items-start gap-2 shadow-sm" style="background-color: #ffe4e8;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1" style="color: var(--primary-color);"></i>
                        <small class="fw-medium text-dark">
                            <strong>Lưu ý quan trọng:</strong> Hành động này sẽ <span style="color: var(--primary-color);">XÓA TOÀN BỘ</span> ghế cũ của phòng này và tạo lại từ đầu. Hệ thống sẽ chặn nếu phòng đã có vé đặt.
                        </small>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">SỐ HÀNG (A-Z)</label>
                            <input type="number" class="form-control rounded-3" name="rows" min="1" max="26" value="5" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">SỐ GHẾ / HÀNG</label>
                            <input type="number" class="form-control rounded-3" name="cols" min="1" max="20" value="10" required>
                        </div>
                    </div>
                    
                    <!-- ĐÃ FIX: Bổ sung trường chọn loại ghế -->
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold">LOẠI GHẾ MẶC ĐỊNH</label>
                        <select class="form-select rounded-3" name="loaighe" required>
                            <option value="Thường">Thường</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2 rounded-3">Bắt đầu tạo sơ đồ <i class="bi bi-arrow-repeat ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cập nhật Dãy -->
<div class="modal fade" id="updateRowModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold" style="color: var(--primary-color);"><i class="bi bi-layers-half me-2"></i>Dãy <span id="display_row_letter"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="update_row">
                    <input type="hidden" name="row_letter" id="update_row_letter">
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold">CHUYỂN TOÀN DÃY THÀNH</label>
                        <select class="form-select rounded-3" name="loaighe" required>
                            <option value="Thường">Ghế Thường</option>
                            <option value="VIP">Ghế VIP</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-danger w-100 fw-bold rounded-3 py-2">Cập nhật ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Chuyển dữ liệu vào Modal khi click nút
document.getElementById('editSeatModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('edit_maghe').value = button.getAttribute('data-maghe');
    document.getElementById('edit_tenghe').value = button.getAttribute('data-tenghe');
    document.getElementById('edit_loaighe').value = button.getAttribute('data-loaighe');
});

// Modal Xóa
const deleteModal = document.getElementById('deleteSeatModal');
if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
        if(event.relatedTarget) {
            const button = event.relatedTarget;
            document.getElementById('delete_maghe').value = button.getAttribute('data-maghe');
            document.getElementById('delete_tenghe').textContent = button.getAttribute('data-tenghe');
        }
    });
}

// Chuyển dữ liệu vào Modal cập nhật dãy
document.getElementById('updateRowModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const row = button.getAttribute('data-row');
    const display = button.getAttribute('data-display');
    document.getElementById('update_row_letter').value = row;
    document.getElementById('display_row_letter').textContent = display;
});
</script>

<?php include 'includes/footer.php'; ?>

