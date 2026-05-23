<?php
class Order {
    public static function getAll($db, $status_filter = '', $search = '') {
        $sql = "SELECT h.*, n.quyen,
                (SELECT COUNT(*) FROM chitietve c WHERE c.mahd = h.mahd) as sove
                FROM hoadon h
                JOIN nguoidung n ON h.tendn = n.tendn
                WHERE 1=1";

        if ($status_filter) {
            $sql .= " AND h.trangthai = '" . $db->escape($status_filter) . "'";
        }
        if ($search) {
            $search_escaped = $db->escape($search);
            $sql .= " AND (h.tendn LIKE '%$search_escaped%' OR h.mahd LIKE '%$search_escaped%')";
        }
        $sql .= " ORDER BY h.ngaydat DESC";

        return $db->select($sql);
    }

    public static function getDetails($db, $mahd) {
        $mahd = (int)$mahd;
        return $db->select("
            SELECT c.*, g.tenghe, s.ngaychieu, s.giochieu, t.tenphim, p.tenphong
            FROM chitietve c
            JOIN ghengoi g ON c.maghe = g.maghe
            JOIN suatchieu s ON c.masuat = s.masuat
            JOIN phim t ON s.maphim = t.maphim
            JOIN phongchieu p ON s.maphong = p.maphong
            WHERE c.mahd = $mahd
        ");
    }

    public static function updateStatus($db, $mahd, $trangthai) {
        $mahd = (int)$mahd;
        $trangthai = $db->escape($trangthai);
        
        return $db->execute("UPDATE hoadon SET trangthai='$trangthai' WHERE mahd=$mahd");
    }
}
