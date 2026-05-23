<?php
class Dashboard {
    public static function getStats($db) {
        $total_movies = count($db->select("SELECT maphim FROM phim"));
        $total_showtimes = count($db->select("SELECT masuat FROM suatchieu WHERE ngaychieu = CURDATE()"));
        $total_orders = count($db->select("SELECT mahd FROM hoadon"));
        $total_users = count($db->select("SELECT tendn FROM nguoidung WHERE quyen = 'user'"));

        $revenue_data = $db->select("SELECT SUM(tongtien) as total FROM hoadon WHERE trangthai = 'Đã thanh toán' AND MONTH(ngaydat) = MONTH(CURDATE())");
        $total_revenue = $revenue_data[0]['total'] ?? 0;

        return [
            'total_movies' => $total_movies,
            'total_showtimes' => $total_showtimes,
            'total_orders' => $total_orders,
            'total_users' => $total_users,
            'total_revenue' => $total_revenue
        ];
    }

    public static function getRecentOrders($db, $limit = 8) {
        return $db->select("
            SELECT h.mahd, n.tendn, h.ngaydat, h.tongtien, h.trangthai,
                   COUNT(DISTINCT c.maghe) as sove
            FROM hoadon h
            JOIN nguoidung n ON h.tendn = n.tendn
            LEFT JOIN chitietve c ON h.mahd = c.mahd
            GROUP BY h.mahd
            ORDER BY h.ngaydat DESC
            LIMIT $limit
        ");
    }

    public static function getPopularMovies($db, $limit = 5) {
        return $db->select("
            SELECT t.maphim, t.tenphim, t.hinh, COUNT(c.maghe) as sove
            FROM phim t
            LEFT JOIN suatchieu s ON t.maphim = s.maphim
            LEFT JOIN chitietve c ON s.masuat = c.masuat
            GROUP BY t.maphim, t.tenphim, t.hinh
            ORDER BY sove DESC
            LIMIT $limit
        ");
    }
}
