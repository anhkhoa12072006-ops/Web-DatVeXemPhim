<?php
require_once 'includes/auth.php';
require_once 'models/Movie.php';
require_once 'models/Category.php';

$page_title = 'Quản lý phim - CTs Cinema Admin';
$current_page = 'movies';

// Hàm hỗ trợ xử lý ảnh
if (!function_exists('processImage')) {
    function processImage($file, $oldImage = '') {
        $uploadDir = '../assets/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = uniqid('movie_') . '.' . $extension;
        $destPath = $uploadDir . $newName;

        $imgSize = getimagesize($file['tmp_name']);
        if (!$imgSize) return $oldImage;
        list($width, $height) = $imgSize;

        $maxWidth = 1920;
        $maxHeight = 1080;
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = $width * $ratio;
            $newHeight = $height * $ratio;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $srcImage = null;
        if ($extension == 'jpg' || $extension == 'jpeg') $srcImage = @imagecreatefromjpeg($file['tmp_name']);
        elseif ($extension == 'png') $srcImage = @imagecreatefrompng($file['tmp_name']);
        elseif ($extension == 'webp') $srcImage = @imagecreatefromwebp($file['tmp_name']);

        if ($srcImage) {
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($extension == 'png' || $extension == 'webp') {
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
                $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
                imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
            }
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            if ($extension == 'jpg' || $extension == 'jpeg') imagejpeg($dstImage, $destPath, 90);
            elseif ($extension == 'png') imagepng($dstImage, $destPath);
            elseif ($extension == 'webp') imagewebp($dstImage, $destPath, 90);

            imagedestroy($srcImage);
            imagedestroy($dstImage);

            if ($oldImage && file_exists($uploadDir . $oldImage) && $oldImage != 'NoImage.png') {
                @unlink($uploadDir . $oldImage);
            }
            return $newName;
        } else {
            if(move_uploaded_file($file['tmp_name'], $destPath)){
                if ($oldImage && file_exists($uploadDir . $oldImage) && $oldImage != 'NoImage.png') {
                    @unlink($uploadDir . $oldImage);
                }
                return $newName;
            }
        }
        return $oldImage;
    }
}

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $tenphim = $_POST['tenphim'] ?? '';
        $theloai = $_POST['theloai'] ?? '';
        $madm = (int)($_POST['madm'] ?? 0);
        $mota = $_POST['mota'] ?? '';
        $trangthai = $_POST['trangthai'] ?? 'Đang chiếu';
        $giave = (float)($_POST['giave'] ?? 80000);
        $daodien = $_POST['daodien'] ?? '';
        $dienvien = $_POST['dienvien'] ?? '';
        $thoiluong = (int)($_POST['thoiluong'] ?? 0);
        $ngonngu = $_POST['ngonngu'] ?? '';
        $kiemduyet = $_POST['kiemduyet'] ?? '';
        $trailer = $_POST['trailer'] ?? '';
        
        if ($_POST['action'] == 'add') {
            $hinh = '';
            if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == UPLOAD_ERR_OK) {
                $hinh = processImage($_FILES['hinh']);
            }
            if (Movie::add($db, $tenphim, $theloai, $madm, $mota, $trangthai, $giave, $hinh, $daodien, $dienvien, $thoiluong, $ngonngu, $kiemduyet, $trailer)) {
                $_SESSION['message'] = 'Thêm phim thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'edit') {
            $maphim = (int)$_POST['maphim'];
            $hinh_old = $_POST['hinh_old'] ?? '';
            $hinh = $hinh_old;
            if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == UPLOAD_ERR_OK) {
                $hinh = processImage($_FILES['hinh'], $hinh_old);
            }
            
            if (Movie::update($db, $maphim, $tenphim, $theloai, $madm, $mota, $trangthai, $giave, $hinh, $daodien, $dienvien, $thoiluong, $ngonngu, $kiemduyet, $trailer)) {
                $_SESSION['message'] = 'Cập nhật phim thành công!';
                $_SESSION['msg_type'] = 'success';
            }
        } elseif ($_POST['action'] == 'delete') {
            $maphim = (int)$_POST['maphim'];
            
            if (Movie::delete($db, $maphim)) {
                $_SESSION['message'] = 'Xóa phim thành công!';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Không thể xóa phim này vì đã có khách hàng đặt vé!';
                $_SESSION['msg_type'] = 'danger';
            }
        }
        header('Location: movies.php');
        exit;
    }
}

// Lấy danh sách phim với filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

$all_records = Movie::getAll($db, $search, $category, $status);

$limit = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_items = count($all_records);
$total_pages = ceil($total_items / $limit);
$movies = array_slice($all_records, ($page - 1) * $limit, $limit);
$categories = Category::getAll($db);

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
                    <h4 class="mb-0"><i class="bi bi-film me-2 text-primary"></i>Kho Phim</h4>
                </div>
            </div>
            <div class="header-right">
                <button class="btn btn-danger rounded-pill px-4 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#movieModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle me-2"></i>Thêm phim mới
                </button>
            </div>
        </header>

        <div class="content">
            <!-- Thông báo Alert -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'success'; ?> alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Search & Filter Card -->
            <div class="card border-0 mb-4 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" name="search" placeholder="Nhập tên phim, thể loại..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="category">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['madm']; ?>" <?php echo $category == $cat['madm'] ? 'selected' : ''; ?>>
                                        <?php echo $cat['tendm']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Đang chiếu" <?php echo $status == 'Đang chiếu' ? 'selected' : ''; ?>>Đang chiếu</option>
                                <option value="Sắp chiếu" <?php echo $status == 'Sắp chiếu' ? 'selected' : ''; ?>>Sắp chiếu</option>
                                <option value="Ngừng chiếu" <?php echo $status == 'Ngừng chiếu' ? 'selected' : ''; ?>>Ngừng chiếu</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-light w-100">
                                <i class="bi bi-funnel me-1"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Movies Grid -->
            <div class="row g-3 g-md-4">
                <?php if (!empty($movies)): ?>
                    <?php foreach ($movies as $movie): ?>
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="movie-card-admin shadow-sm border h-100" style="background: #fff; border-radius: 16px; transition: all 0.3s ease; display: flex; flex-direction: column;">
                                <!-- Phần ảnh và nhãn -->
                                <div class="position-relative">
                                    <img src="../assets/images/<?php echo htmlspecialchars($movie['hinh']); ?>" 
                                         class="w-100" 
                                         style="height: 320px; object-fit: cover; border-radius: 16px 16px 0 0;"
                                         alt="<?php echo htmlspecialchars($movie['tenphim']); ?>"
                                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                                    
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75 rounded-pill px-2 py-1" style="font-size: 0.65rem;">
                                            <?php echo $movie['tendm']; ?>
                                        </span>
                                    </div>
                                    
                                    <?php 
                                        $trangthaiBadge = 'bg-success';
                                        if (($movie['trangthai'] ?? 'Đang chiếu') == 'Sắp chiếu') $trangthaiBadge = 'bg-warning text-dark';
                                        elseif (($movie['trangthai'] ?? 'Đang chiếu') == 'Ngừng chiếu') $trangthaiBadge = 'bg-secondary';
                                    ?>
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge <?php echo $trangthaiBadge; ?> shadow-sm rounded-pill px-2 py-1" style="font-size: 0.65rem;">
                                            <?php echo $movie['trangthai'] ?? 'Đang chiếu'; ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Phần thông tin -->
                                <div class="p-2 p-md-3 d-flex flex-column flex-grow-1">
                                    <h6 class="fw-bold text-truncate mb-1" title="<?php echo htmlspecialchars($movie['tenphim']); ?>">
                                        <?php echo htmlspecialchars($movie['tenphim']); ?>
                                    </h6>
                                    
                                    <div class="text-muted mb-2 d-none d-md-block" style="font-size: 0.75rem;">
                                        <i class="bi bi-tags me-1"></i><?php echo htmlspecialchars($movie['theloai']); ?>
                                    </div>
                                    
                                    <div class="text-danger fw-bold mb-3" style="font-size: 0.9rem;">
                                        <?php echo isset($movie['giave']) ? number_format($movie['giave']) . ' đ' : 'Chưa có giá'; ?>
                                    </div>
                                    
                                    <!-- Nút hành động -->
                                    <div class="mt-auto d-grid gap-2">
                                        <button class="btn btn-warning btn-sm fw-bold rounded-pill py-1" onclick='editMovie(<?php echo json_encode($movie, JSON_HEX_APOS); ?>)'>
                                            <i class="bi bi-pencil-square me-1"></i>Sửa
                                        </button>
                                        <form method="POST" class="d-grid" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bộ phim này?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="maphim" value="<?php echo $movie['maphim']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold rounded-pill py-1">
                                                <i class="bi bi-trash3 me-1"></i>Xoá
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card border-0 rounded-4">
                            <div class="card-body text-center py-5 text-muted">
                                <i class="bi bi-film fs-1 d-block mb-3 opacity-50"></i>
                                <h5 class="fw-normal">Không tìm thấy bộ phim nào phù hợp</h5>
                                <p class="mb-0">Thử thay đổi từ khóa tìm kiếm hoặc danh mục</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <div class='mt-4'><?php include '../pagination.php'; ?></div>
        </div>
    </div>
</div>

<!-- Movie Modal -->
<div class="modal fade" id="movieModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Thêm phim mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" name="maphim" id="maphim">
                    <input type="hidden" name="hinh_old" id="hinh_old">
                    
                    <div class="row g-4">
                        <div class="col-md-9">
                            <label class="form-label text-muted fw-medium">Tên phim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fs-6" name="tenphim" id="tenphim" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select form-control-lg fs-6" name="trangthai" id="trangthai" required>
                                <option value="Đang chiếu">Đang chiếu</option>
                                <option value="Sắp chiếu">Sắp chiếu</option>
                                <option value="Ngừng chiếu">Ngừng chiếu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Thể loại</label>
                            <input type="text" class="form-control" name="theloai" id="theloai" placeholder="VD: Hành động / Viễn tưởng">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" name="madm" id="madm" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['madm']; ?>"><?php echo $cat['tendm']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Giá vé cơ bản (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="giave" id="giave" required min="0" step="1000" placeholder="VD: 80000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium">Thời lượng (phút)</label>
                            <input type="number" class="form-control" name="thoiluong" id="thoiluong" min="0" placeholder="VD: 120">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium">Kiểm duyệt</label>
                            <select class="form-select" name="kiemduyet" id="kiemduyet">
                                <option value="P">P (Mọi lứa tuổi)</option>
                                <option value="K">K (Dưới 13+ phụ huynh)</option>
                                <option value="T13">T13 (13+)</option>
                                <option value="T16">T16 (16+)</option>
                                <option value="T18">T18 (18+)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Đạo diễn</label>
                            <input type="text" class="form-control" name="daodien" id="daodien" placeholder="Nhập tên đạo diễn">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Ngôn ngữ</label>
                            <input type="text" class="form-control" name="ngonngu" id="ngonngu" placeholder="VD: Tiếng Việt - Phụ đề Anh">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Diễn viên</label>
                            <input type="text" class="form-control" name="dienvien" id="dienvien" placeholder="Nhập tên các diễn viên, cách nhau bởi dấu phẩy">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Link Video / Trailer (YouTube)</label>
                            <input type="url" class="form-control" name="trailer" id="trailer" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted fw-medium">Tải lên hình ảnh (tự động tối ưu Full HD)</label>
                            <input type="file" class="form-control" name="hinh" id="hinh" accept="image/*">
                            <small class="text-muted mt-2 d-block" id="current_image_text"><i class="bi bi-info-circle me-1"></i>Chọn file ảnh từ máy tính (ảnh cũ sẽ tự động bị xóa nếu có thay đổi).</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-medium">Nội dung mô tả</label>
                            <textarea class="form-control" name="mota" id="mota" rows="4" placeholder="Nhập tóm tắt nội dung phim..."></textarea>
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
    document.getElementById('modalTitle').textContent = 'Thêm phim mới';
    document.getElementById('action').value = 'add';
    document.getElementById('maphim').value = '';
    document.getElementById('hinh_old').value = '';
    document.getElementById('current_image_text').innerHTML = '<i class="bi bi-info-circle me-1"></i>Chọn file ảnh từ máy tính để tải lên.';
    document.querySelector('#movieModal form').reset();
    document.getElementById('trangthai').value = 'Đang chiếu';
    document.getElementById('giave').value = '80000';
}

function editMovie(movie) {
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa phim';
    document.getElementById('action').value = 'edit';
    document.getElementById('maphim').value = movie.maphim;
    document.getElementById('tenphim').value = movie.tenphim;
    document.getElementById('theloai').value = movie.theloai;
    document.getElementById('madm').value = movie.madm;
    document.getElementById('trangthai').value = movie.trangthai || 'Đang chiếu';
    document.getElementById('giave').value = movie.giave || 80000;
    document.getElementById('hinh_old').value = movie.hinh;
    if (movie.hinh) {
        document.getElementById('current_image_text').innerHTML = '<i class="bi bi-image me-1"></i>Ảnh hiện tại: <strong>' + movie.hinh + '</strong> (Chọn file mới để ghi đè)';
    } else {
        document.getElementById('current_image_text').innerHTML = '<i class="bi bi-info-circle me-1"></i>Chưa có ảnh, hãy chọn file để tải lên.';
    }
    document.getElementById('mota').value = movie.mota;
    document.getElementById('daodien').value = movie.daodien || '';
    document.getElementById('dienvien').value = movie.dienvien || '';
    document.getElementById('thoiluong').value = movie.thoiluong || 0;
    document.getElementById('ngonngu').value = movie.ngonngu || '';
    document.getElementById('kiemduyet').value = movie.kiemduyet || 'P';
    document.getElementById('trailer').value = movie.trailer || '';
    
    new bootstrap.Modal(document.getElementById('movieModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
