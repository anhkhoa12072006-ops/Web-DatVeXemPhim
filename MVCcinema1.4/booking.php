<?php
require_once 'includes/auth.php';
require_once 'models/Booking.php';

$masuat = isset($_GET['showtime']) ? (int)$_GET['showtime'] : 0;

if ($masuat == 0) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin suất chiếu
$showtimes = Booking::getShowtimeDetails($db, $masuat);

if (empty($showtimes)) {
    header('Location: index.php');
    exit;
}

$showtime = $showtimes[0];

// Lấy danh sách ghế của phòng
$seats = Booking::getSeats($db, $showtime['maphong'], $masuat);

// Nhóm ghế theo hàng
$seats_by_row = [];
foreach ($seats as $seat) {
    $row = substr($seat['tenghe'], 0, 1);
    if (!isset($seats_by_row[$row])) {
        $seats_by_row[$row] = [];
    }
    $seats_by_row[$row][] = $seat;
}

$booking_error = '';

// XỬ LÝ ĐẶT VÉ 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['seats']) && is_array($_POST['seats']) && count($_POST['seats']) > 0) {
        $selected_seats = $_POST['seats'];
        $tongtien = 0;
        
        // Tính tổng tiền an toàn từ Database
        foreach ($selected_seats as $maghe) {
            $loaighe = Booking::getSeatType($db, $maghe);
            if ($loaighe !== null) {
                $giave = $showtime['giave']; 
                if ($loaighe == 'VIP') {
                    $giave += 40000; // VIP +40k
                }
                $tongtien += $giave;
            }
        }
        
        // Phương thức thanh toán
        $phuongthuctt = $_POST['phuongthuctt'] ?? 'Tiền mặt';
        
        if ($phuongthuctt === 'Chuyển khoản VietQR') {
            $_SESSION['pending_booking'] = [
                'masuat' => $masuat,
                'seats' => $selected_seats,
                'tongtien' => $tongtien
            ];
            header('Location: vietqr_payment.php');
            exit;
        }
        
        // Tạo hóa đơn cho Tiền mặt
        $tendn = $_SESSION['tendn'];
        $mahd = Booking::createOrder($db, $tendn, $tongtien, $phuongthuctt);
        
        if ($mahd) {
            // Thêm chi tiết vé
            foreach ($selected_seats as $maghe) {
                $loaighe = Booking::getSeatType($db, $maghe);
                $giave = $showtime['giave']; 
                if ($loaighe == 'VIP') {
                    $giave += 40000;
                }
                
                Booking::createTicketDetail($db, $mahd, $masuat, $maghe, $giave);
            }
            
            $_SESSION['message'] = 'Đặt vé thành công! Chúc bạn có trải nghiệm xem phim tuyệt vời.';
            header('Location: my-tickets.php?success=1');
            exit;
        } else {
            $booking_error = "Lỗi hệ thống khi lưu hóa đơn. Vui lòng thử lại!";
        }
    } else {
        $booking_error = "Bạn chưa chọn ghế nào! Vui lòng chọn ít nhất 1 ghế trước khi thanh toán.";
    }
}

$page_title = 'Đặt vé - ' . $showtime['tenphim'];
include 'header.php';
?>

    <style>
        /* CSS Thuần xử lý hiệu ứng ghế */
        .seat-map-wrapper { overflow-x: auto; padding-bottom: 1rem; }
        .seat-map-container { min-width: max-content; margin: 0 auto; }
        
        .seat-box {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px 8px 4px 4px; font-size: 0.85rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f1f5f9; border: 2px solid #cbd5e1; color: #475569;
            user-select: none;
        }
        
        .seat-box.vip { background-color: #ffe4e8; border-color: var(--primary-color); color: var(--primary-color); }
        
        .seat-box:hover:not(.booked) {
            transform: translateY(-3px); box-shadow: 0 4px 10px rgba(216, 17, 89, 0.25);
            background-color: var(--primary-hover); border-color: var(--primary-hover); color: white;
        }
        
        /* MAGIC CỦA CSS THUẦN: Trạng thái checked của input sẽ đổi style cho label */
        .seat-checkbox:checked + label.seat-box {
            background-color: var(--primary-color) !important; 
            border-color: var(--primary-color) !important; 
            color: white !important;
            box-shadow: 0 0 0 3px rgba(216, 17, 89, 0.25); 
            transform: translateY(-2px);
        }
        /* Ẩn số ghế, hiện dấu tick khi chọn */
        .seat-checkbox:checked + label.seat-box .seat-content { display: none; }
        .seat-checkbox:checked + label.seat-box::after {
            content: "\F26A"; /* Bootstrap Icon check-lg */
            font-family: "bootstrap-icons";
            font-size: 1.1rem;
        }
        
        .seat-box.booked {
            background-color: #e2e8f0; border-color: #cbd5e1; color: #94a3b8;
            cursor: not-allowed; opacity: 0.6;
        }
    </style>

    <section class="py-5" style="background-color: var(--bg-color);">
        <div class="container">
            <?php if ($booking_error): ?>
                <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4" style="background-color: #ffe4e8; color: var(--primary-color);" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $booking_error; ?>
                </div>
            <?php endif; ?>

            <!-- FORM BAO TRỌN 2 CỘT, KHÔNG CẦN JS SUBMIT -->
            <form method="POST" action="">
                <div class="row g-4">
                    <!-- Seat Selection Column -->
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-4 shadow-sm mb-4">
                            <div class="card-body p-4 p-md-5">
                                <h4 class="fw-bold mb-4" style="color: var(--primary-color);">
                                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> Chọn vị trí ghế ngồi
                                </h4>
                                
                                <!-- Screen -->
                                <div class="text-center mb-5 pb-4">
                                    <div class="py-2 fw-bold rounded-top-4 shadow-sm" 
                                         style="background: linear-gradient(to bottom, rgba(216,17,89,0.12), transparent); 
                                                border-top: 5px solid var(--primary-color); 
                                                color: var(--primary-color); letter-spacing: 3px;">
                                        <i class="bi bi-display"></i> MÀN HÌNH CHÍNH
                                    </div>
                                </div>
                                
                                <!-- Seats -->
                                <div class="seat-map-wrapper text-center">
                                    <div class="seat-map-container">
                                        <?php foreach ($seats_by_row as $row => $row_seats): ?>
                                            <div class="d-flex justify-content-center align-items-center mb-2 gap-2" style="flex-wrap: nowrap;">
                                                <div class="fw-bold text-muted me-2 fs-5" style="width: 25px;"><?php echo $row; ?></div>
                                                
                                                <?php foreach ($row_seats as $seat): ?>
                                                    <?php if (!$seat['dadat']): ?>
                                                        <!-- GHẾ TRỐNG: Dùng Checkbox ẩn + Label -->
                                                        <input type="checkbox" name="seats[]" value="<?php echo $seat['maghe']; ?>" id="seat_<?php echo $seat['maghe']; ?>" class="seat-checkbox d-none" 
                                                               data-price="<?php echo (int)$showtime['giave'] + ($seat['loaighe'] == 'VIP' ? 40000 : 0); ?>" 
                                                               data-name="<?php echo $seat['tenghe']; ?>">
                                                        
                                                        <label for="seat_<?php echo $seat['maghe']; ?>" class="seat-box <?php echo $seat['loaighe'] == 'VIP' ? 'vip' : ''; ?>"
                                                               title="<?php echo $seat['tenghe']; ?> - <?php echo number_format((int)$showtime['giave'] + ($seat['loaighe'] == 'VIP' ? 40000 : 0)); ?>đ">
                                                            <span class="seat-content"><?php echo substr($seat['tenghe'], 1); ?></span>
                                                        </label>
                                                    <?php else: ?>
                                                        <!-- GHẾ ĐÃ ĐẶT: Hiển thị cục gạch -->
                                                        <div class="seat-box booked" title="Ghế đã được đặt">
                                                            <span class="seat-content"><i class="bi bi-x-lg"></i></span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                
                                                <div class="fw-bold text-muted ms-2 fs-5" style="width: 25px;"><?php echo $row; ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Legend -->
                                <div class="mt-5 p-3 p-md-4 rounded-4" style="background-color: #fff0f3; border: 1px dashed var(--border-color);">
                                    <div class="row text-center small fw-bold text-muted g-3">
                                        <div class="col-6 col-sm-3 d-flex flex-column align-items-center">
                                            <div class="seat-box mb-2" style="cursor:default; transform: none; box-shadow: none;"></div>
                                            <span>Thường</span>
                                        </div>
                                        <div class="col-6 col-sm-3 d-flex flex-column align-items-center">
                                            <div class="seat-box vip mb-2" style="cursor:default; transform: none; box-shadow: none;"></div>
                                            <span>VIP (+40k)</span>
                                        </div>
                                        <div class="col-6 col-sm-3 d-flex flex-column align-items-center">
                                            <div class="seat-box mb-2" style="cursor:default; transform: none; background-color: var(--primary-color); border-color: var(--primary-color); color: white;">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                            <span>Đang chọn</span>
                                        </div>
                                        <div class="col-6 col-sm-3 d-flex flex-column align-items-center">
                                            <div class="seat-box booked mb-2" style="cursor:default; transform: none;">
                                                <i class="bi bi-x-lg"></i>
                                            </div>
                                            <span>Đã đặt</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Summary Column -->
                    <div class="col-lg-4">
                        <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 100px;">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold mb-0" style="color: var(--primary-color);">
                                    <i class="bi bi-receipt me-2"></i> Thông tin đặt vé
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <img src="assets/images/<?php echo $showtime['hinh']; ?>" 
                                     class="img-fluid rounded-3 mb-3 shadow-sm w-100"
                                     style="height: 220px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/300x400'">
                                     
                                <h5 class="fw-bold mb-1" style="color: var(--text-color);"><?php echo htmlspecialchars($showtime['tenphim']); ?></h5>
                                <p class="text-muted small mb-3"><i class="bi bi-tags-fill text-danger me-1"></i> <?php echo htmlspecialchars($showtime['theloai']); ?></p>
                                
                                <hr class="border-secondary border-opacity-10 mb-3">
                                
                                <div class="p-3 rounded-4 mb-3" style="background-color: #fffafb; border: 1px solid var(--border-color);">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted fw-semibold"><i class="bi bi-door-closed-fill" style="color: var(--primary-color);"></i> Phòng chiếu:</span>
                                        <span class="fw-bold" style="color: var(--text-color);"><?php echo $showtime['tenphong']; ?> (<?php echo htmlspecialchars($showtime['tenrap']); ?>)</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted fw-semibold"><i class="bi bi-calendar-event-fill" style="color: var(--primary-color);"></i> Ngày:</span>
                                        <span class="fw-bold" style="color: var(--text-color);"><?php echo date('d/m/Y', strtotime($showtime['ngaychieu'])); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold"><i class="bi bi-clock-fill" style="color: var(--primary-color);"></i> Giờ:</span>
                                        <span class="fw-bold" style="color: var(--primary-color);"><?php echo substr($showtime['giochieu'], 0, 5); ?></span>
                                    </div>
                                </div>
                                
                                <div class="p-3 rounded-4 mb-4" style="background-color: rgba(216, 17, 89, 0.05); border: 1px dashed var(--primary-color);">
                                    <div class="mb-2 d-flex justify-content-between align-items-start">
                                        <span class="text-muted fw-semibold me-2" style="white-space: nowrap;">Ghế đã chọn:</span>
                                        <span id="selectedSeats" class="fw-bold text-end" style="color: var(--primary-color); word-break: break-word;">Chưa chọn ghế</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">Số lượng:</span>
                                        <span class="fw-bold" style="color: var(--text-color);"><span id="seatCount">0</span> vé</span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-muted mb-3">Phương thức thanh toán</h6>
                                    
                                    <div class="form-check border rounded-3 p-3 mb-2 d-flex align-items-center bg-white" style="cursor:pointer;" onclick="document.getElementById('pt_momo').click();">
                                        <input class="form-check-input ms-0 me-3" type="radio" name="phuongthuctt" id="pt_momo" value="Chuyển khoản VietQR" checked>
                                        <label class="form-check-label fw-bold flex-grow-1 d-flex align-items-center" style="cursor:pointer;" for="pt_momo">
                                            <i class="bi bi-qr-code-scan text-primary fs-4 me-2"></i>
                                            Chuyển khoản VietQR (MoMo, Ngân hàng)
                                        </label>
                                    </div>
                                    
                                    <div class="form-check border rounded-3 p-3 d-flex align-items-center bg-white" style="cursor:pointer;" onclick="document.getElementById('pt_cash').click();">
                                        <input class="form-check-input ms-0 me-3" type="radio" name="phuongthuctt" id="pt_cash" value="Tiền mặt">
                                        <label class="form-check-label fw-bold flex-grow-1 d-flex align-items-center" style="cursor:pointer;" for="pt_cash">
                                            <i class="bi bi-cash-stack text-success fs-4 me-2"></i>
                                            Thanh toán tại quầy
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 fw-bold text-muted">TỔNG TIỀN</h5>
                                    <h3 class="mb-0 fw-bold" style="color: var(--primary-color);" id="totalPrice">0 ₫</h3>
                                </div>
                                
                                <!-- NÚT SUBMIT CHUẨN HTML -->
                                <button type="submit" class="btn btn-danger w-100 btn-lg rounded-pill fw-bold shadow-sm mb-3">
                                    Thanh toán ngay <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                                </button>
                                
                                <a href="movie-detail.php?id=<?php echo $showtime['maphim']; ?>" 
                                   class="btn btn-light w-100 rounded-pill fw-semibold border text-muted" style="background-color: #f8fafc;">
                                    <i class="bi bi-arrow-left-short"></i> Quay lại chi tiết phim
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tui thề đoạn JS này chỉ dùng để hiển thị text lên giao diện bên phải cho đẹp   
        document.querySelectorAll('.seat-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                let selectedSeats = [];
                let total = 0;
                
                document.querySelectorAll('.seat-checkbox:checked').forEach(box => {
                    selectedSeats.push(box.dataset.name);
                    total += parseInt(box.dataset.price);
                });
                
                document.getElementById('selectedSeats').innerHTML = 
                    selectedSeats.length > 0 ? selectedSeats.join(', ') : 'Chưa chọn ghế';
                document.getElementById('seatCount').textContent = selectedSeats.length;
                document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + ' ₫';
            });
        });
    </script>
</body>
</html>