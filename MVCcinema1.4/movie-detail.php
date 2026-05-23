<?php
require_once 'config/database.php';
require_once 'models/Movie.php';

$maphim = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($maphim == 0) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin phim kèm giá vé thấp nhất
$movies = Movie::getMovieDetail($db, $maphim);

if (empty($movies)) {
    header('Location: index.php');
    exit;
}

$movie = $movies[0];

// Xử lý gửi bình luận
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    if (isset($_SESSION['tendn'])) {
        $noidung = $_POST['noidung'] ?? '';
        $diemdanhgia = (int)$_POST['diemdanhgia'];
        $tendn = $_SESSION['tendn'];
        
        if ($diemdanhgia >= 1 && $diemdanhgia <= 5 && !empty($noidung)) {
            Movie::addComment($db, $maphim, $tendn, $noidung, $diemdanhgia);
            header("Location: movie-detail.php?id=$maphim#comments");
            exit;
        }
    }
}

// Lấy danh sách bình luận
$comments = Movie::getComments($db, $maphim);

// Lấy suất chiếu của phim
$showtimes = Movie::getMovieShowtimes($db, $maphim);

// Nhóm suất chiếu theo ngày
$showtimes_by_date = [];
foreach ($showtimes as $showtime) {
    $date = $showtime['ngaychieu'];
    if (!isset($showtimes_by_date[$date])) {
        $showtimes_by_date[$date] = [];
    }
    $showtimes_by_date[$date][] = $showtime;
}

$page_title = $movie['tenphim'] . ' - CTs Cinema';
include_once 'header.php';
?>

    <!-- Movie Detail Section -->
    <section class="py-5" style="background-color: var(--bg-color);">
        <div class="container">
            <!-- Nút quay lại -->
            <div class="mb-4">
                <a href="index.php" class="btn btn-outline-light rounded-pill px-4 py-2 border-0 shadow-sm" style="background-color: #ffffff; color: var(--text-color);">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại trang chủ
                </a>
            </div>
            
            <div class="row g-5">
                <!-- Poster Column -->
                <div class="col-lg-4 col-md-5">
                    <div class="movie-card border-0 shadow-lg rounded-4 overflow-hidden sticky-top" style="top: 100px;">
                        <img src="assets/images/<?php echo htmlspecialchars($movie['hinh']); ?>" 
                             class="w-100" 
                             alt="<?php echo htmlspecialchars($movie['tenphim']); ?>"
                             style="height: 550px; object-fit: cover;"
                             onerror="this.src='https://via.placeholder.com/400x600?text=<?php echo urlencode($movie['tenphim']); ?>'">
                    </div>
                </div>

                <!-- Info Column -->
                <div class="col-lg-8 col-md-7">
                    <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 mb-5">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="badge bg-danger fs-6 rounded-pill px-3 py-2"><i class="bi bi-collection-play-fill me-1"></i> <?php echo $movie['tendm']; ?></span>
                            <span class="badge bg-secondary fs-6 rounded-pill px-3 py-2"><i class="bi bi-tags-fill me-1"></i> <?php echo htmlspecialchars($movie['theloai']); ?></span>
                        </div>
                        
                        <h1 class="fw-bold mb-4" style="color: var(--primary-color); font-size: 2.5rem; line-height: 1.2;">
                            <?php echo htmlspecialchars($movie['tenphim']); ?>
                        </h1>
                        
                        <div class="row g-4 mb-4 pb-4 border-bottom border-secondary border-opacity-10">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #fff0f3;">
                                        <i class="bi bi-tag-fill fs-5" style="color: var(--primary-color);"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-0 fw-bold">THỂ LOẠI</p>
                                        <p class="mb-0 fw-semibold" style="color: var(--text-color);"><?php echo htmlspecialchars($movie['theloai']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #fff0f3;">
                                        <i class="bi bi-ticket-perforated-fill fs-5" style="color: var(--primary-color);"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-0 fw-bold">GIÁ VÉ TỪ</p>
                                        <p class="mb-0 fw-bold fs-5" style="color: var(--primary-color);">
                                            <?php echo $movie['gia'] ? number_format($movie['gia']) . ' ₫' : 'Chưa có giá'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #f0f7ff;">
                                        <i class="bi bi-clock-fill fs-5" style="color: #0d6efd;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-0 fw-bold">THỜI LƯỢNG</p>
                                        <p class="mb-0 fw-semibold"><?php echo $movie['thoiluong'] ? $movie['thoiluong'] . ' phút' : 'Đang cập nhật'; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #f0fff4;">
                                        <i class="bi bi-translate fs-5" style="color: #198754;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-0 fw-bold">NGÔN NGỮ</p>
                                        <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($movie['ngonngu'] ?: 'Đang cập nhật'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #fff8f0;">
                                        <i class="bi bi-person-badge-fill fs-5" style="color: #fd7e14;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-0 fw-bold">ĐẠO DIỄN</p>
                                        <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($movie['daodien'] ?: 'Đang cập nhật'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #f8f0ff;">
                                        <i class="bi bi-shield-check fs-5" style="color: #6f42c1;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-0 fw-bold">KIỂM DUYỆT</p>
                                        <p class="mb-0 fw-bold text-uppercase" style="color: #6f42c1;">
                                            <?php 
                                            $kd = $movie['kiemduyet'] ?: 'P';
                                            $kd_text = [
                                                'P' => 'P - Mọi lứa tuổi',
                                                'K' => 'K - Dưới 13 tuổi (có giám hộ)',
                                                'T13' => 'T13 - 13+',
                                                'T16' => 'T16 - 16+',
                                                'T18' => 'T18 - 18+'
                                            ];
                                            echo $kd_text[$kd] ?? $kd;
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-2 d-flex align-items-center gap-2" style="color: var(--text-color);">
                                <i class="bi bi-people-fill" style="color: var(--primary-color);"></i> Diễn viên
                            </h5>
                            <p class="text-muted"><?php echo htmlspecialchars($movie['dienvien'] ?: 'Đang cập nhật'); ?></p>
                        </div>

                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--text-color);">
                            <i class="bi bi-card-text" style="color: var(--primary-color);"></i> Nội dung phim
                        </h5>
                        <p class="text-muted lh-lg" style="font-size: 1.05rem;">
                            <?php echo nl2br(htmlspecialchars($movie['mota'])); ?>
                        </p>

                        <?php if ($movie['trailer']): ?>
                        <div class="mt-4 pt-4 border-top border-secondary border-opacity-10">
                            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--text-color);">
                                <i class="bi bi-play-btn-fill" style="color: var(--primary-color);"></i> Trailer
                            </h5>
                            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm">
                                <?php 
                                    $video_url = $movie['trailer'];
                                    $video_id = '';
                                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
                                        $video_id = $match[1];
                                    }
                                    if ($video_id): 
                                ?>
                                    <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                <?php else: ?>
                                    <div class="bg-dark d-flex align-items-center justify-content-center text-white">
                                        <p class="mb-0">Link trailer không hợp lệ</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Showtimes Section -->
                    <div class="mt-2">
                        <h3 class="fw-bold mb-4 d-flex align-items-center gap-3 section-title" style="color: var(--text-color);">
                            <i class="bi bi-clock-history" style="color: var(--primary-color);"></i> Lịch chiếu vé
                        </h3>
                        
                        <?php if (!empty($showtimes_by_date)): ?>
                            <?php foreach ($showtimes_by_date as $date => $times): ?>
                                <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
                                    <!-- Tiêu đề ngày tháng (Đã thay bằng nền sáng) -->
                                    <div class="card-header border-bottom border-secondary border-opacity-10 py-3 px-4" style="background-color: #fffafb;">
                                        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2" style="color: var(--primary-color);">
                                            <i class="bi bi-calendar-event-fill"></i>
                                            <?php 
                                            $dateObj = new DateTime($date);
                                            echo $dateObj->format('d/m/Y') . ' - <span class="fw-semibold text-muted fs-6">' . 
                                                 ($dateObj->format('Y-m-d') == date('Y-m-d') ? 'Hôm nay' : 
                                                 'Thứ ' . ($dateObj->format('N') == 7 ? 'CN' : ($dateObj->format('N') + 1))) . '</span>';
                                            ?>
                                        </h5>
                                    </div>
                                    
                                    <div class="card-body p-4">
                                        <div class="row g-3">
                                            <?php foreach ($times as $showtime): ?>
                                                <?php 
                                                $conlai = $showtime['tongghe'] - $showtime['daban'];
                                                $hethang = $conlai <= 0;
                                                ?>
                                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                                    <div class="p-3 text-center d-flex flex-column h-100 <?php echo $hethang ? 'bg-light border border-secondary border-opacity-25 rounded-4 opacity-75' : 'showtime-box'; ?>">
                                                        <div class="fs-3 fw-bold mb-1" style="color: <?php echo $hethang ? '#6c757d' : 'var(--primary-color)'; ?>;">
                                                            <?php echo substr($showtime['giochieu'], 0, 5); ?>
                                                        </div>
                                                        <div class="small fw-semibold mb-2" style="color: var(--text-color);">
                                                            <i class="bi bi-door-closed-fill text-muted"></i> <?php echo $showtime['tenphong']; ?>
                                                            <br><small class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($showtime['tenrap']); ?></small>
                                                        </div>
                                                        
                                                        <div class="small mb-3 mt-auto">
                                                            <?php if ($hethang): ?>
                                                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1">Đã cháy vé</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">Còn <?php echo $conlai; ?> ghế</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <?php if (!$hethang): ?>
                                                            <?php if (isset($_SESSION['tendn'])): ?>
                                                                <a href="booking.php?showtime=<?php echo $showtime['masuat']; ?>" 
                                                                   class="btn btn-danger btn-sm w-100 fw-bold rounded-pill shadow-sm mt-auto">
                                                                    ĐẶT VÉ NAY
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="login.php" class="btn btn-outline-danger btn-sm w-100 fw-bold rounded-pill mt-auto">
                                                                    Đăng nhập để đặt
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center p-4" style="background-color: #fef3c7; color: #d97706;">
                                <i class="bi bi-calendar-x-fill fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Chưa có lịch chiếu</h6>
                                    <p class="mb-0 small">Hiện tại rạp chưa cập nhật suất chiếu nào cho tác phẩm này. Vui lòng quay lại sau!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Comments Section -->
                    <div id="comments" class="mt-5 pt-4 border-top border-secondary border-opacity-10">
                        <h3 class="fw-bold mb-4 d-flex align-items-center gap-3 section-title" style="color: var(--text-color);">
                            <i class="bi bi-chat-right-text" style="color: var(--primary-color);"></i> Bình luận & Đánh giá
                        </h3>
                        
                        <?php if (isset($_SESSION['tendn'])): ?>
                            <div class="card border-0 rounded-4 shadow-sm mb-5" style="background-color: #fffafb;">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3">Thêm đánh giá của bạn</h6>
                                    <form method="POST" action="movie-detail.php?id=<?php echo $maphim; ?>">
                                        <input type="hidden" name="submit_comment" value="1">
                                        
                                        <div class="mb-3">
                                            <label class="form-label text-muted fw-semibold">Đánh giá sao:</label>
                                            <div class="d-flex gap-2">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <div class="form-check form-check-inline me-0">
                                                        <input class="form-check-input d-none" type="radio" name="diemdanhgia" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i==5 ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fs-4" for="star<?php echo $i; ?>" style="cursor:pointer; color: #ffc107;">
                                                            <i class="bi bi-star-fill"></i>
                                                        </label>
                                                    </div>
                                                <?php endfor; ?>
                                                <script>
                                                    // Đổi màu sao theo radio đã chọn
                                                    document.querySelectorAll('input[name="diemdanhgia"]').forEach(radio => {
                                                        radio.addEventListener('change', function() {
                                                            let val = this.value;
                                                            document.querySelectorAll('input[name="diemdanhgia"]').forEach(r => {
                                                                let labelIcon = document.querySelector(`label[for="${r.id}"] i`);
                                                                if (r.value <= val) {
                                                                    labelIcon.classList.replace('bi-star', 'bi-star-fill');
                                                                    labelIcon.parentElement.style.color = '#ffc107';
                                                                } else {
                                                                    labelIcon.classList.replace('bi-star-fill', 'bi-star');
                                                                    labelIcon.parentElement.style.color = '#ccc';
                                                                }
                                                            });
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <textarea class="form-control rounded-3" name="noidung" rows="3" placeholder="Chia sẻ cảm nhận của bạn về bộ phim..." required></textarea>
                                        </div>
                                        
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-danger px-4 rounded-pill fw-medium">Gửi bình luận</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary rounded-4 border-0 text-center py-4 mb-5">
                                <p class="mb-2">Vui lòng đăng nhập để gửi bình luận và đánh giá.</p>
                                <a href="login.php" class="btn btn-outline-dark btn-sm rounded-pill px-4">Đăng nhập</a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- List Comments -->
                        <div class="comments-list">
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="card border-0 border-bottom rounded-0 mb-3 pb-3 bg-transparent">
                                        <div class="d-flex gap-3">
                                            <div class="avatar bg-secondary bg-opacity-25 text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                                <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($comment['username']); ?></h6>
                                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($comment['ngaytao'])); ?></small>
                                                </div>
                                                <div class="mb-2 text-warning" style="font-size: 0.9rem;">
                                                    <?php for($i=1; $i<=5; $i++): ?>
                                                        <i class="bi <?php echo $i <= $comment['diemdanhgia'] ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($comment['noidung'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-4">Chưa có bình luận nào cho bộ phim này. Hãy là người đầu tiên đánh giá!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

<?php include_once 'footer.php'; ?>