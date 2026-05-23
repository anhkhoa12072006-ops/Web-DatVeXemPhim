<?php
require_once 'includes/auth.php';
require_once 'models/Ticket.php';

$tendn = $_SESSION['tendn'];

// Lấy danh sách vé đã đặt
$tickets = Ticket::getByUser($db, $tendn);
$page_title = 'Vé của tôi - CTs Cinema';
include_once 'header.php';


?>
    <style>
        body { 
            background: linear-gradient(135deg, #fff0f3 0%, #ffe4e8 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }
        .ticket-wrapper {
            filter: drop-shadow(0 10px 20px rgba(220, 53, 69, 0.15));
            transition: transform 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .ticket-wrapper:hover {
            transform: translateY(-5px);
        }
        
        .ticket-top {
            background: #fff;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            position: relative;
            flex-grow: 1;
        }
        
        .ticket-bottom {
            background: #fff;
            border-radius: 0 0 16px 16px;
            padding: 1.5rem;
            position: relative;
        }

        .ticket-divider {
            position: relative;
            height: 30px;
            /* Tạo hai hình chữ nhật nền trắng, nhưng bị đục lỗ tròn trong suốt ở 2 mép */
            background: 
                radial-gradient(circle at 0 50%, transparent 15px, #fff 16px) left top / 51% 100% no-repeat,
                radial-gradient(circle at 100% 50%, transparent 15px, #fff 16px) right top / 51% 100% no-repeat;
        }
        
        .ticket-divider-line {
            position: absolute;
            top: 15px;
            left: 20px;
            right: 20px;
            border-top: 2px dashed #dee2e6;
        }

        .ticket-cinema-brand {
            font-family: 'Courier New', Courier, monospace;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 15px;
        }

        .qr-code img {
            border: 4px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        /* Print Styles */
        @media print {
            body { 
                background: #fff !important; 
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Ẩn hoàn toàn các phần tử thừa khỏi layout để không sinh trang trắng */
            header, footer, .navbar, h2, .alert, .btn-danger[href="index.php"] { 
                display: none !important; 
            }
            .ticket-card:not(.print-active) { 
                display: none !important; 
            }
            body * { visibility: hidden; }
            .print-active, .print-active * { visibility: visible; }
            .print-active {
                position: absolute !important;
                left: 50% !important;
                top: 0 !important;
                width: 100%;
                max-width: 320px; /* Thu nhỏ bề ngang bằng vé rạp thật */
                margin: 0 auto;
                transform: translateX(-50%) !important;
                filter: none !important;
                font-size: 0.85rem !important; /* Thu nhỏ font chữ */
            }
            .print-active .fs-5 { font-size: 1rem !important; }
            .print-active .fs-6 { font-size: 0.85rem !important; }
            .print-active .ticket-cinema-brand { font-size: 1rem !important; margin-bottom: 10px; }
            .print-active .ticket-top, .print-active .ticket-bottom { padding: 1rem !important; }
            .print-active .qr-code img { width: 90px; height: 90px; } /* Thu nhỏ mã QR */
            
            .action-buttons { display: none !important; }
            .ticket-wrapper { 
                break-inside: avoid; 
                height: auto !important; /* Không kéo dãn bằng trang A4 */
                display: block !important;
            }
            .ticket-top {
                flex-grow: 0 !important; /* Hủy kéo dãn phần thân trên */
            }
        }
    </style>

    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-ticket-perforated text-danger"></i> Vé của tôi</h2>
                <a href="index.php" class="btn btn-danger rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg"></i> Đặt vé mới
                </a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Đặt vé thành công! Vui lòng đưa mã QR trên vé cho nhân viên rạp để quét lấy vé cứng.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($tickets)): ?>
                <div class="row g-4">
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="col-md-6 col-lg-4">
                            <!-- BẮT ĐẦU VÉ -->
                            <div class="ticket-wrapper ticket-card">
                                
                                <!-- Phần thân trên: Thông tin phim & rạp -->
                                <div class="ticket-top">
                                    <div class="ticket-cinema-brand">
                                        CTs Cinema
                                    </div>
                                    <div class="text-center mb-3">
                                        <div class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($ticket['tenrap'] ?? 'Hệ thống CTs Cinema'); ?></div>
                                        <small class="text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo htmlspecialchars($ticket['diachi'] ?? 'Địa chỉ rạp'); ?></small>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                        <img src="assets/images/<?php echo $ticket['hinh']; ?>" 
                                             class="rounded shadow-sm me-3"
                                             style="height: 80px; width: 60px; object-fit: cover;"
                                             onerror="this.src='https://via.placeholder.com/60x80'">
                                        <div>
                                            <h5 class="mb-1 fw-bold text-dark text-uppercase"><?php echo htmlspecialchars($ticket['tenphim']); ?></h5>
                                            <span class="badge bg-danger mb-1"><?php echo htmlspecialchars($ticket['theloai']); ?></span>
                                            <div class="small text-muted">ID: #<?php echo str_pad($ticket['mahd'], 6, '0', STR_PAD_LEFT); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3 text-center mb-3">
                                        <div class="col-6 border-end">
                                            <small class="text-muted d-block text-uppercase">Ngày chiếu</small>
                                            <strong class="fs-5 text-dark"><?php echo date('d/m/Y', strtotime($ticket['ngaychieu'])); ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block text-uppercase">Giờ chiếu</small>
                                            <strong class="fs-5 text-dark"><?php echo substr($ticket['giochieu'], 0, 5); ?></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3 text-center bg-light rounded-3 py-2">
                                        <div class="col-4 border-end">
                                            <small class="text-muted d-block text-uppercase">Phòng</small>
                                            <strong class="text-dark"><?php echo $ticket['tenphong']; ?></strong>
                                        </div>
                                        <div class="col-8 text-start ps-3">
                                            <small class="text-muted d-block text-uppercase">Ghế (<?php echo $ticket['sove']; ?> vé)</small>
                                            <strong class="text-danger fs-6 text-break"><?php echo $ticket['ghengoi']; ?></strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Đường rãnh xé vé đứt đoạn -->
                                <div class="ticket-divider">
                                    <div class="ticket-divider-line"></div>
                                </div>
                                
                                <!-- Phần thân dưới: Cuống vé (QR + Nút) -->
                                <div class="ticket-bottom text-center">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="text-start">
                                            <small class="text-muted d-block text-uppercase">Trạng thái</small>
                                            <span class="badge bg-<?php echo $ticket['trangthai'] == 'Đã thanh toán' ? 'success' : 'warning'; ?> fs-6 mt-1">
                                                <?php echo $ticket['trangthai']; ?>
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block text-uppercase">Tổng tiền</small>
                                            <strong class="text-dark fs-5"><?php echo number_format($ticket['tongtien']); ?> đ</strong>
                                        </div>
                                    </div>
                                    
                                    <div class="qr-code mb-3 mx-auto">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?php echo urlencode('VXP'.$ticket['mahd'].'-'.$tendn); ?>" alt="QR Code" class="img-fluid">
                                    </div>
                                    <small class="text-muted d-block mb-3">Quét mã này tại máy tự động hoặc quầy vé</small>
                                    
                                    <div class="d-flex gap-2 action-buttons">
                                        <button class="btn btn-danger flex-grow-1 fw-bold rounded-pill print-btn shadow-sm">
                                            <i class="bi bi-printer-fill me-2"></i> In vé
                                        </button>
                                        <?php if ($ticket['maphim']): ?>
                                            <a href="movie-detail.php?id=<?php echo $ticket['maphim']; ?>" 
                                               class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- KẾT THÚC VÉ -->
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card text-center py-5 border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <i class="bi bi-ticket-perforated display-1 text-muted"></i>
                        <h4 class="mt-3 mb-3">Chưa có vé nào</h4>
                        <p class="text-muted mb-4">Bạn chưa đặt vé phim nào. Hãy chọn phim yêu thích và đặt vé ngay!</p>
                        <a href="index.php" class="btn btn-danger btn-lg rounded-pill px-4">
                            <i class="bi bi-film me-2"></i> Khám phá phim
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Include html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.print-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const originalBtn = this;
                    const originalContent = originalBtn.innerHTML;
                    
                    // Trạng thái đang xử lý
                    originalBtn.disabled = true;
                    originalBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang tạo PDF...';
                    
                    const ticketWrapper = this.closest('.ticket-wrapper');
                    const orderIdEl = ticketWrapper.querySelector('.small.text-muted');
                    const orderId = orderIdEl ? orderIdEl.textContent.replace('ID: #', '').trim() : 'ticket';
                    
                    // Tạo bản sao để in
                    const element = ticketWrapper.cloneNode(true);
                    const actionButtons = element.querySelector('.action-buttons');
                    if (actionButtons) actionButtons.remove();
                    
                    // Cấu hình PDF tối ưu
                    const opt = {
                        margin:       0.2,
                        filename:     'Ve_Xem_Phim_' + orderId + '.pdf',
                        image:        { type: 'jpeg', quality: 0.95 },
                        html2canvas:  { 
                            scale: 1.5, // Giảm tỉ lệ xuống 1.5 để nhanh hơn mà vẫn nét
                            useCORS: true,
                            logging: false,
                            letterRendering: true
                        },
                        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                    };

                    // Thực hiện xuất PDF
                    html2pdf().set(opt).from(element).save().then(() => {
                        // Khôi phục trạng thái nút
                        originalBtn.disabled = false;
                        originalBtn.innerHTML = originalContent;
                    }).catch(err => {
                        console.error('Lỗi tạo PDF:', err);
                        originalBtn.disabled = false;
                        originalBtn.innerHTML = originalContent;
                        alert('Có lỗi xảy ra khi tạo PDF. Vui lòng thử lại!');
                    });
                });
            });
        });
    </script>

<?php include_once 'footer.php'; ?>