<?php
class Movie {
    public static function getFrontEndMovies($db, $category_filter = '', $search_query = '') {
        $sql = "SELECT t.*, d.tendm, 
                       (SELECT ROUND(AVG(diemdanhgia), 1) FROM binhluan b WHERE b.maphim = t.maphim) as diemtrungbinh,
                       (SELECT COUNT(*) FROM binhluan b WHERE b.maphim = t.maphim) as luotdanhgia,
                       t.giave as gia
                FROM phim t LEFT JOIN danhmuc d ON t.madm = d.madm WHERE 1=1";

        if ($category_filter) {
            $sql .= " AND t.madm = " . (int)$category_filter;
        }
        if ($search_query) {
            $search_escaped = $db->escape($search_query);
            $sql .= " AND t.tenphim LIKE '%$search_escaped%'";
        }
        $sql .= " ORDER BY t.maphim DESC";

        return $db->select($sql);
    }

    public static function getMovieDetail($db, $maphim) {
        $maphim = (int)$maphim;
        return $db->select("
            SELECT t.*, d.tendm,
                   t.giave as gia
            FROM phim t 
            LEFT JOIN danhmuc d ON t.madm = d.madm 
            WHERE t.maphim = $maphim
        ");
    }

    public static function addComment($db, $maphim, $tendn, $noidung, $diemdanhgia) {
        $maphim = (int)$maphim;
        $tendn = $db->escape($tendn);
        $noidung = $db->escape($noidung);
        $diemdanhgia = (int)$diemdanhgia;
        
        $sql = "INSERT INTO binhluan (tendn, maphim, noidung, diemdanhgia) VALUES ('$tendn', $maphim, '$noidung', $diemdanhgia)";
        return $db->execute($sql);
    }

    public static function getComments($db, $maphim) {
        $maphim = (int)$maphim;
        return $db->select("
            SELECT b.*, n.tendn as username 
            FROM binhluan b 
            JOIN nguoidung n ON b.tendn = n.tendn 
            WHERE b.maphim = $maphim 
            ORDER BY b.ngaytao DESC
        ");
    }

    public static function getMovieShowtimes($db, $maphim) {
        $maphim = (int)$maphim;
        return $db->select("
            SELECT s.*, p.tenphong, p.tongghe, r.tenrap, r.diachi,
                   (SELECT COUNT(*) FROM chitietve c WHERE c.masuat = s.masuat) as daban
            FROM suatchieu s
            JOIN phongchieu p ON s.maphong = p.maphong
            LEFT JOIN rapchieu r ON p.marap = r.marap
            WHERE s.maphim = $maphim AND TIMESTAMP(s.ngaychieu, s.giochieu) >= NOW()
            ORDER BY s.ngaychieu, s.giochieu
        ");
    }
}
