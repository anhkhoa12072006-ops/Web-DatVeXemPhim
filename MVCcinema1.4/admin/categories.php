<?php
require_once 'includes/auth.php';
require_once 'models/Category.php';

$page_title = 'Quản lý danh mục - CTs Cinema Admin';
$current_page = 'categories';

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $tendm = $_POST['tendm'] ?? '';
        $logo = $_POST['logo'] ?? '';
        $ghichu = $_POST['ghichu'] ?? '';
        
        if ($_POST['action'] == 'add') {
            if (Category::add($db, $tendm, $logo, $ghichu)) {
                $_SESSION['message'] = 'Đã thêm danh mục mới thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'edit') {
            $madm = (int)$_POST['madm'];
            if (Category::update($db, $madm, $tendm, $logo, $ghichu)) {
                $_SESSION['message'] = 'Cập nhật danh mục thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'delete') {
            $madm = (int)$_POST['madm'];
            if (Category::delete($db, $madm)) {
                $_SESSION['message'] = 'Đã xóa danh mục khỏi hệ thống!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Cảnh báo: Không thể xóa vì đang có phim thuộc danh mục này!';
                $_SESSION['msg_type'] = 'danger';
            }
        }
        header('Location: categories.php');
        exit;
    }
}

// Lấy danh sách danh mục
$all_records = Category::getAll($db);


$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$categories = array_slice($all_records, ($page - 1) * $limit, $limit);
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
                    <h4 class="mb-0"><i class="bi bi-tag me-2 text-primary"></i>Danh mục phim</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle me-2"></i>Thêm danh mục
                </button>
            </div>
        </header>

        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> border-0 shadow-sm rounded-4 fade show">
                    <i class="bi bi-info-circle-fill me-2"></i> <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                </div>
            <?php endif; ?>

            <!-- Categories Grid -->
            <div class="row g-4">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="logo-box bg-danger bg-opacity-10 text-danger" style="width: 52px; height: 52px;">
                                            <i class="bi bi-tags-fill fs-3"></i>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-secondary">
                                                <li><a class="dropdown-item py-2" href="#" onclick='editCategory(<?php echo json_encode($category, JSON_HEX_APOS); ?>)'><i class="bi bi-pencil-square me-2 text-warning"></i>Sửa</a></li>
                                                <li><hr class="dropdown-divider border-secondary"></li>
                                                <li>
                                                    <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="madm" value="<?php echo $category['madm']; ?>">
                                                        <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-trash3 me-2"></i>Xóa</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($category['tendm']); ?></h5>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 mb-3">
                                        <?php echo $category['sophim']; ?> tác phẩm
                                    </span>

                                    <div class="mt-2 pt-3 border-top border-secondary border-opacity-10">
                                        <p class="text-muted small mb-2"><i class="bi bi-info-circle me-2"></i>Ghi chú:</p>
                                        <p class="small text-light mb-0"><?php echo !empty($category['ghichu']) ? htmlspecialchars($category['ghichu']) : '<em class="opacity-50">Không có ghi chú</em>'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card border-0 rounded-4 py-5 text-center opacity-50">
                            <i class="bi bi-folder-x fs-1 mb-3"></i>
                            <h5>Chưa có danh mục nào</h5>
                            <p>Hãy nhấn "Thêm danh mục" để bắt đầu phân loại phim của bạn.</p>
                        </div>
                    </div>
                <?php endif; ?>
            <div class='mt-4'><?php include '../pagination.php'; ?></div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="madm" id="madm">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">TÊN DANH MỤC <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fs-6 rounded-3" name="tendm" id="tendm" placeholder="VD: Hành động, Viễn tưởng..." required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">MÃ LOGO (NẾU CÓ)</label>
                        <input type="text" class="form-control rounded-3" name="logo" id="logo" placeholder="VD: bi-film, category-icon...">
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-muted small fw-bold">MÔ TẢ CHI TIẾT</label>
                        <textarea class="form-control rounded-3" name="ghichu" id="ghichu" rows="4" placeholder="Nhập một vài mô tả về danh mục này..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary p-4 d-flex gap-2">
                    <button type="button" class="btn btn-outline-light flex-fill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger flex-fill fw-bold">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').textContent = 'Thêm danh mục mới';
    document.getElementById('action').value = 'add';
    document.getElementById('madm').value = '';
    document.getElementById('tendm').value = '';
    document.getElementById('logo').value = '';
    document.getElementById('ghichu').value = '';
}

function editCategory(category) {
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa danh mục';
    document.getElementById('action').value = 'edit';
    document.getElementById('madm').value = category.madm;
    document.getElementById('tendm').value = category.tendm;
    document.getElementById('logo').value = category.logo;
    document.getElementById('ghichu').value = category.ghichu;
    
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>

