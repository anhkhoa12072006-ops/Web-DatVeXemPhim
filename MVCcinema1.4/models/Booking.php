<?php
class Booking {
    public static function getShowtimeDetails($db, $masuat) {
        $masuat = (int)$masuat;
        return $db->select("
            SELECT s.*, t.tenphim, t.hinh, t.theloai, t.giave, p.tenphong, p.maphong, d.tendm, r.tenrap
            FROM suatchieu s
            JOIN phim t ON s.maphim = t.maphim
            JOIN phongchieu p ON s.maphong = p.maphong
            LEFT JOIN rapchieu r ON p.marap = r.marap
            LEFT JOIN danhmuc d ON t.madm = d.madm
            WHERE s.masuat = $masuat
        ");
    }

    public static function getSeats($db, $maphong, $masuat) {
        $maphong = (int)$maphong;
        $masuat = (int)$masuat;
        return $db->select("
            SELECT g.*, 
                   CASE WHEN c.maghe IS NOT NULL THEN 1 ELSE 0 END as dadat
            FROM ghengoi g
            LEFT JOIN chitietve c ON g.maghe = c.maghe AND c.masuat = $masuat
            WHERE g.maphong = $maphong
            ORDER BY LEFT(g.tenghe, 1), CAST(SUBSTRING(g.tenghe, 2) AS UNSIGNED)
        ");
    }

    public static function getSeatType($db, $maghe) {
        $maghe = (int)$maghe;
        $seat_info = $db->select("SELECT loaighe FROM ghengoi WHERE maghe = $maghe");
        return !empty($seat_info) ? $seat_info[0]['loaighe'] : null;
    }

    public static function createOrder($db, $tendn, $tongtien, $phuongthuctt) {
        $tendn = $db->escape($tendn);
        $tongtien = (int)$tongtien;
        $phuongthuctt = $db->escape($phuongthuctt);
        
        $sql = "INSERT INTO hoadon (tendn, tongtien, phuongthuctt, trangthai) VALUES ('$tendn', $tongtien, '$phuongthuctt', 'Chờ thanh toán')";
        if ($db->execute($sql)) {
            return $db->getInsertId();
        }
        return false;
    }

    public static function createTicketDetail($db, $mahd, $masuat, $maghe, $giave) {
        $mahd = (int)$mahd;
        $masuat = (int)$masuat;
        $maghe = (int)$maghe;
        $giave = (int)$giave;
        
        return $db->execute("INSERT INTO chitietve (mahd, masuat, maghe, giave) VALUES ($mahd, $masuat, $maghe, $giave)");
    }
}
