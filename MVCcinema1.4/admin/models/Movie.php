<?php
class Movie {
    public static function getAll($db, $search = '', $category = '', $status = '') {
        $sql = "SELECT t.*, d.tendm 
                FROM phim t 
                LEFT JOIN danhmuc d ON t.madm = d.madm 
                WHERE 1=1";

        if ($search) {
            $search_escaped = $db->escape($search);
            $sql .= " AND (t.tenphim LIKE '%$search_escaped%' OR t.theloai LIKE '%$search_escaped%')";
        }
        if ($category) {
            $sql .= " AND t.madm = " . (int)$category;
        }
        if ($status) {
            $status_escaped = $db->escape($status);
            $sql .= " AND t.trangthai = '$status_escaped'";
        }
        $sql .= " ORDER BY t.maphim DESC";

        return $db->select($sql);
    }

    public static function add($db, $tenphim, $theloai, $madm, $mota, $trangthai, $giave, $hinh, $daodien = '', $dienvien = '', $thoiluong = 0, $ngonngu = '', $kiemduyet = '', $trailer = '') {
        $tenphim = $db->escape($tenphim);
        $theloai = $db->escape($theloai);
        $madm = (int)$madm;
        $mota = $db->escape($mota);
        $trangthai = $db->escape($trangthai);
        $giave = (float)$giave;
        $hinh = $db->escape($hinh);
        $daodien = $db->escape($daodien);
        $dienvien = $db->escape($dienvien);
        $thoiluong = (int)$thoiluong;
        $ngonngu = $db->escape($ngonngu);
        $kiemduyet = $db->escape($kiemduyet);
        $trailer = $db->escape($trailer);

        $sql = "INSERT INTO phim (tenphim, theloai, madm, hinh, mota, trangthai, giave, daodien, dienvien, thoiluong, ngonngu, kiemduyet, trailer) 
                VALUES ('$tenphim', '$theloai', $madm, '$hinh', '$mota', '$trangthai', $giave, '$daodien', '$dienvien', $thoiluong, '$ngonngu', '$kiemduyet', '$trailer')";
        return $db->execute($sql);
    }

    public static function update($db, $maphim, $tenphim, $theloai, $madm, $mota, $trangthai, $giave, $hinh, $daodien = '', $dienvien = '', $thoiluong = 0, $ngonngu = '', $kiemduyet = '', $trailer = '') {
        $maphim = (int)$maphim;
        $tenphim = $db->escape($tenphim);
        $theloai = $db->escape($theloai);
        $madm = (int)$madm;
        $mota = $db->escape($mota);
        $trangthai = $db->escape($trangthai);
        $giave = (float)$giave;
        $hinh = $db->escape($hinh);
        $daodien = $db->escape($daodien);
        $dienvien = $db->escape($dienvien);
        $thoiluong = (int)$thoiluong;
        $ngonngu = $db->escape($ngonngu);
        $kiemduyet = $db->escape($kiemduyet);
        $trailer = $db->escape($trailer);

        $sql = "UPDATE phim SET tenphim='$tenphim', theloai='$theloai', madm=$madm, 
                hinh='$hinh', mota='$mota', trangthai='$trangthai', giave=$giave,
                daodien='$daodien', dienvien='$dienvien', thoiluong=$thoiluong,
                ngonngu='$ngonngu', kiemduyet='$kiemduyet', trailer='$trailer' 
                WHERE maphim=$maphim";
        return $db->execute($sql);
    }

    public static function delete($db, $maphim) {
        $maphim = (int)$maphim;
        
        // Check constraints
        $check_tickets = $db->select("
            SELECT COUNT(*) as total 
            FROM chitietve c
            JOIN suatchieu s ON c.masuat = s.masuat
            WHERE s.maphim = $maphim
        ");
        
        if (!empty($check_tickets) && $check_tickets[0]['total'] > 0) {
            return false; // Cannot delete, has tickets
        }

        // Delete image
        $movie = $db->select("SELECT hinh FROM phim WHERE maphim=$maphim");
        if ($movie && !empty($movie[0]['hinh'])) {
            $img = '../assets/images/' . $movie[0]['hinh'];
            if (file_exists($img)) @unlink($img);
        }
        
        return $db->execute("DELETE FROM phim WHERE maphim=$maphim");
    }
}
