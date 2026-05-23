<?php
require_once 'config/database.php';
require_once 'models/Movie.php';
require_once 'models/Category.php';

$page_title = 'CTs Cinema - Rạp chiếu phim';

// Lấy tham số bộ lọc, tìm kiếm và phân trang
$category_filter = $_GET['category'] ?? '';
$search_query = $_GET['search'] ?? '';
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 8;
$active_tab = $_GET['tab'] ?? 'now'; // 'now' hoặc 'upcoming'

$movies = Movie::getFrontEndMovies($db, $category_filter, $search_query);

$now_showing_all = [];
$upcoming_all = [];
foreach ($movies as $m) {
    if (isset($m['trangthai']) && $m['trangthai'] == 'Sắp chiếu') {
        $upcoming_all[] = $m;
    } elseif (!isset($m['trangthai']) || $m['trangthai'] == 'Đang chiếu') {
        $now_showing_all[] = $m;
    }
}

// Logic phân trang dựa trên tab đang hoạt động
$target_list = ($active_tab == 'upcoming') ? $upcoming_all : $now_showing_all;
$total_items = count($target_list);
$total_pages = ceil($total_items / $items_per_page);
$current_page = max(1, min($current_page, $total_pages ?: 1));
$offset = ($current_page - 1) * $items_per_page;

$display_movies = array_slice($target_list, $offset, $items_per_page);

$categories = Category::getAll($db);
include_once 'header.php';

?>

    <!-- Hero Carousel: chỉ hiện trên desktop -->
    <?php
    $carousel_movies = array_slice($now_showing_all, 0, 3);
    $slide_count = count($carousel_movies);
    ?>
    <section class="hero-carousel p-0 border-bottom border-secondary border-opacity-10 d-none d-md-block">
        <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-indicators">
                <?php for($i = 0; $i < $slide_count; $i++): ?>
                    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $i == 0 ? 'active' : ''; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
                <?php endfor; ?>
            </div>
            <div class="carousel-inner">
                <?php if (!empty($carousel_movies)): ?>
                    <?php foreach ($carousel_movies as $index => $movie): ?>
                        <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>" data-bs-interval="3000">
                            <div class="d-flex align-items-center" style="min-height: 550px; background: linear-gradient(135deg, #fff0f3 0%, #ffe4e8 100%);">
                                <div class="container py-5">
                                    <div class="row align-items-center g-5">
                                        <div class="col-lg-6 order-2 order-lg-1">
                                            <span class="badge bg-danger rounded-pill px-3 py-2 fs-6 mb-3 shadow-sm">
                                                <i class="bi bi-star-fill text-warning me-1"></i> Phim Mới Nổi Bật
                                            </span>
                                            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color); line-height: 1.2;">
                                                <?php echo htmlspecialchars($movie['tenphim']); ?>
                                            </h1>
                                            <p class="lead mb-4 fw-medium text-muted">
                                                <i class="bi bi-tags-fill text-danger me-2"></i> <?php echo htmlspecialchars($movie['theloai']); ?>
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-collection-play-fill text-danger me-2"></i> <?php echo htmlspecialchars($movie['tendm']); ?>
                                            </p>
                                            <a href="movie-detail.php?id=<?php echo $movie['maphim']; ?>" class="btn btn-danger btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                                                <i class="bi bi-ticket-perforated-fill me-2"></i> Mua Vé Ngay
                                            </a>
                                        </div>
                                        <div class="col-lg-6 order-1 order-lg-2 text-center text-lg-end">
                                            <img src="assets/images/<?php echo htmlspecialchars($movie['hinh']); ?>"
                                                 alt="<?php echo htmlspecialchars($movie['tenphim']); ?>"
                                                 class="img-fluid rounded-4 shadow-lg border border-4 border-white"
                                                 style="max-height: 400px; object-fit: cover; transform: rotate(2deg); transition: transform 0.3s;"
                                                 onmouseover="this.style.transform='rotate(0deg) scale(1.05)'"
                                                 onmouseout="this.style.transform='rotate(2deg)'"
                                                 onerror="this.src='https://via.placeholder.com/300x450?text=<?php echo urlencode($movie['tenphim']); ?>'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active">
                        <div class="d-flex align-items-center justify-content-center text-center" style="min-height: 400px; background: linear-gradient(135deg, #fff0f3 0%, #ffe4e8 100%);">
                            <div>
                                <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">Chào mừng đến CTs Cinema</h1>
                                <p class="lead mb-4 text-muted fw-medium">Trải nghiệm điện ảnh đẳng cấp với hệ thống rạp hiện đại</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($slide_count > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                    <div class="bg-dark bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </div>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                    <div class="bg-dark bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </div>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== MOVIES SECTION (Hỗ trợ cả Mobile & Desktop) ===== -->
    <section class="py-5" id="movies-section">
        <div class="container">
            
            <!-- Tab Headers (Ngang hàng cho cả 2 thiết bị) -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div class="movie-tabs d-inline-flex p-1 rounded-pill shadow-sm" style="background:#f1f3f5;">
                    <a href="?tab=now<?php echo $category_filter ? '&category='.$category_filter : ''; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>" 
                       class="tab-link px-4 py-2 rounded-pill fw-bold text-decoration-none <?php echo $active_tab == 'now' ? 'active' : 'text-muted'; ?>">
                        <i class="bi bi-play-circle-fill me-1"></i> Đang chiếu
                    </a>
                    <a href="?tab=upcoming<?php echo $category_filter ? '&category='.$category_filter : ''; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>" 
                       class="tab-link px-4 py-2 rounded-pill fw-bold text-decoration-none <?php echo $active_tab == 'upcoming' ? 'active' : 'text-muted'; ?>">
                        <i class="bi bi-clock-history me-1"></i> Sắp chiếu
                    </a>
                </div>

                <!-- BỘ LỌC VÀ TÌM KIẾM (Desktop) -->
                <div class="d-none d-md-flex gap-2 align-items-center">
                    <form method="GET" class="position-relative">
                        <input type="hidden" name="tab" value="<?php echo $active_tab; ?>">
                        <?php if ($category_filter): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                        <?php endif; ?>
                        <input type="text" name="search" class="form-control rounded-pill px-4 shadow-sm border-0" 
                               placeholder="Tìm tên phim..." 
                               value="<?php echo htmlspecialchars($search_query); ?>" 
                               style="width: 250px; padding-right: 45px !important; background-color: #fff;">
                        <button type="submit" class="btn position-absolute end-0 top-50 translate-middle-y border-0 text-muted rounded-pill" style="z-index: 5;">
                            <i class="bi bi-search" style="color: var(--primary-color);"></i>
                        </button>
                    </form>

                    <div class="dropdown">
                        <button class="btn btn-outline-danger dropdown-toggle rounded-pill px-4 fw-bold shadow-sm" type="button" data-bs-toggle="dropdown" style="background-color: #fff;">
                            <i class="bi bi-funnel-fill me-1"></i> 
                            <?php 
                                $active_cat_name = 'Tất cả';
                                if ($category_filter) {
                                    foreach ($categories as $cat) {
                                        if ($cat['madm'] == $category_filter) {
                                            $active_cat_name = $cat['tendm'];
                                            break;
                                        }
                                    }
                                }
                                echo $active_cat_name;
                            ?>
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-4 mt-2">
                            <li><a class="dropdown-item fw-medium py-2" href="?tab=<?php echo $active_tab; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>">Tất cả danh mục</a></li>
                            <?php foreach ($categories as $cat): ?>
                                <li><a class="dropdown-item fw-medium py-2" href="?tab=<?php echo $active_tab; ?>&category=<?php echo $cat['madm']; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>"><?php echo htmlspecialchars($cat['tendm']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Mobile Search (chỉ hiện trên mobile) -->
            <div class="d-md-none mb-4">
                <form method="GET" class="position-relative">
                    <input type="hidden" name="tab" value="<?php echo $active_tab; ?>">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left:16px;"></i>
                    <input type="text" name="search" class="form-control rounded-pill fw-medium border-0 shadow-sm"
                           placeholder="Tìm tên phim..."
                           value="<?php echo htmlspecialchars($search_query); ?>"
                           style="padding-left:42px; background:#fff;">
                </form>
            </div>

            <!-- Movies Grid -->
            <div class="row g-3 g-md-4">
                <?php if (!empty($display_movies)): ?>
                    <?php foreach ($display_movies as $movie): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="movie-card h-100 bg-white border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                                <div class="movie-poster position-relative">
                                    <a href="movie-detail.php?id=<?php echo $movie['maphim']; ?>">
                                        <img src="assets/images/<?php echo htmlspecialchars($movie['hinh']); ?>" 
                                             alt="<?php echo htmlspecialchars($movie['tenphim']); ?>"
                                             class="w-100" style="aspect-ratio: 2/3; object-fit: cover;"
                                             onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                                    </a>
                                    
                                    <!-- Badges -->
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <?php if ($active_tab == 'now' && $movie['diemtrungbinh']): ?>
                                            <span class="badge shadow-sm fw-bold" style="background:rgba(0,0,0,0.7); font-size:0.75rem;">
                                                <i class="bi bi-star-fill text-warning me-1"></i><?php echo $movie['diemtrungbinh']; ?>
                                            </span>
                                        <?php elseif ($active_tab == 'upcoming'): ?>
                                            <span class="badge bg-warning text-dark fw-bold shadow-sm" style="font-size:0.7rem;">Sắp chiếu</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Overlay Desktop -->
                                    <div class="movie-overlay d-none d-md-flex position-absolute top-0 start-0 w-100 h-100 align-items-center justify-content-center" 
                                         style="background: rgba(216, 17, 89, 0.85); opacity: 0; transition: 0.3s;">
                                        <a href="movie-detail.php?id=<?php echo $movie['maphim']; ?>" class="btn btn-light text-danger fw-bold rounded-pill px-4">
                                            <?php echo $active_tab == 'now' ? 'Mua vé' : 'Chi tiết'; ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="movie-info p-3">
                                    <h6 class="movie-title fw-bold text-truncate mb-1" style="color: var(--text-color);"><?php echo htmlspecialchars($movie['tenphim']); ?></h6>
                                    <p class="text-muted small text-truncate mb-2"><?php echo htmlspecialchars($movie['theloai']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-danger small">
                                            <?php echo $movie['gia'] ? number_format($movie['gia']) . '₫' : 'Chưa giá'; ?>
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 px-2 py-1" style="font-size: 0.65rem;">
                                            <?php echo htmlspecialchars($movie['tendm']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-camera-reels fs-1 text-muted opacity-50"></i>
                        <p class="mt-3 fw-medium text-muted">Không tìm thấy phim nào phù hợp.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center pagination-modern">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link rounded-circle me-2 shadow-sm" href="?page=<?php echo $current_page - 1; ?>&tab=<?php echo $active_tab; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link rounded-circle mx-1 shadow-sm" href="?page=<?php echo $i; ?>&tab=<?php echo $active_tab; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link rounded-circle ms-2 shadow-sm" href="?page=<?php echo $current_page + 1; ?>&tab=<?php echo $active_tab; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </section>

    <style>
        .tab-link { transition: all 0.3s; color: #6c757d; }
        .tab-link.active { background: #fff; color: var(--primary-color) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .movie-card { transition: transform 0.3s; }
        .movie-card:hover { transform: translateY(-5px); }
        .movie-card:hover .movie-overlay { opacity: 1 !important; }
        .pagination-modern .page-link { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: none; color: #6c757d; font-weight: bold; }
        .pagination-modern .page-item.active .page-link { background: var(--primary-color); color: #fff; }
    </style>

<?php include_once 'footer.php'; ?>
 upBtn.classList.add('active');
                nowBtn.classList.remove('active');
                upPanel.classList.remove('d-none');
                nowPanel.classList.add('d-none');
            }
        }
    </script>

<?php include_once 'footer.php'; ?>
