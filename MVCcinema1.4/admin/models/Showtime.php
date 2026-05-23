<?php
class Showtime {
    public static function getAll($db, $date_filter = '', $movie_filter = '') {
        $sql = "SELECT s.*, t.tenphim, t.giave, p.tenphong, r.tenrap,
                (SELECT COUNT(*) FROM chitietve c WHERE c.masuat = s.masuat) as daban,
                p.tongghe
                FROM suatchieu s
                JOIN phim t ON s.maphim = t.maphim
                JOIN phongchieu p ON s.maphong = p.maphong
                LEFT JOIN rapchieu r ON p.marap = r.marap
                WHERE 1=1";

        if ($date_filter) {
            $sql .= " AND s.ngaychieu = '" . $db->escape($date_filter) . "'";
        }
        if ($movie_filter) {
            $sql .= " AND s.maphim = " . (int)$movie_filter;
        }
        $sql .= " ORDER BY s.ngaychieu DESC, s.giochieu DESC";

        return $db->select($sql);
    }

    public static function add($db, $maphim, $maphong, $ngaychieu, $giochieu) {
        $maphim = (int)$maphim;
        $maphong = (int)$maphong;
        $ngaychieu = $db->escape($ngaychieu);
        $giochieu = $db->escape($giochieu);

        $sql = "INSERT INTO suatchieu (maphim, maphong, ngaychieu, giochieu) 
                VALUES ($maphim, $maphong, '$ngaychieu', '$giochieu')";
        return $db->execute($sql);
    }

    public static function update($db, $masuat, $maphim, $maphong, $ngaychieu, $giochieu) {
        $masuat = (int)$masuat;
        $maphim = (int)$maphim;
        $maphong = (int)$maphong;
        $ngaychieu = $db->escape($ngaychieu);
        $giochieu = $db->escape($giochieu);

        $sql = "UPDATE suatchieu SET maphim=$maphim, maphong=$maphong, ngaychieu='$ngaychieu', 
                giochieu='$giochieu' WHERE masuat=$masuat";
        return $db->execute($sql);
    }

    public static function delete($db, $masuat) {
        $masuat = (int)$masuat;
        
        $tickets = $db->select("SELECT * FROM chitietve WHERE masuat = $masuat");
        if (!empty($tickets)) {
            return false; // Cannot delete, has tickets
        }
        
        return $db->execute("DELETE FROM suatchieu WHERE masuat=$masuat");
    }
}
