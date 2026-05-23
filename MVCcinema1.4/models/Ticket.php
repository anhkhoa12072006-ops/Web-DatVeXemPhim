<?php
class Ticket {
    public static function getByUser($db, $tendn) {
        $tendn = $db->escape($tendn);
        return $db->select("
            SELECT h.mahd, h.ngaydat, h.tongtien, h.trangthai,
                   s.masuat, s.ngaychieu, s.giochieu,
                   t.maphim, t.tenphim, t.hinh, t.theloai,
                   p.tenphong, r.tenrap, r.diachi,
                   GROUP_CONCAT(g.tenghe ORDER BY g.tenghe SEPARATOR ', ') as ghengoi,
                   COUNT(DISTINCT c.maghe) as sove
            FROM hoadon h
            LEFT JOIN chitietve c ON h.mahd = c.mahd
            LEFT JOIN suatchieu s ON c.masuat = s.masuat
            LEFT JOIN phim t ON s.maphim = t.maphim
            LEFT JOIN phongchieu p ON s.maphong = p.maphong
            LEFT JOIN rapchieu r ON p.marap = r.marap
            LEFT JOIN ghengoi g ON c.maghe = g.maghe
            WHERE h.tendn = '$tendn'
            GROUP BY h.mahd, h.ngaydat, h.tongtien, h.trangthai, 
                     s.masuat, s.ngaychieu, s.giochieu, 
                     t.maphim, t.tenphim, t.hinh, t.theloai, 
                     p.tenphong, r.tenrap, r.diachi
            ORDER BY h.ngaydat DESC
        ");
    }
}
