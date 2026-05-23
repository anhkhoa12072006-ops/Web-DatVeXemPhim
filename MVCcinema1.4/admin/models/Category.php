<?php
class Category {
    public static function getAll($db) {
        return $db->select("
            SELECT d.*, (SELECT COUNT(*) FROM phim t WHERE t.madm = d.madm) as sophim
            FROM danhmuc d ORDER BY d.tendm
        ");
    }

    public static function add($db, $tendm, $logo, $ghichu) {
        $tendm = $db->escape($tendm);
        $logo = $db->escape($logo);
        $ghichu = $db->escape($ghichu);
        return $db->execute("INSERT INTO danhmuc (tendm, logo, ghichu) VALUES ('$tendm', '$logo', '$ghichu')");
    }

    public static function update($db, $madm, $tendm, $logo, $ghichu) {
        $madm = (int)$madm;
        $tendm = $db->escape($tendm);
        $logo = $db->escape($logo);
        $ghichu = $db->escape($ghichu);
        return $db->execute("UPDATE danhmuc SET tendm='$tendm', logo='$logo', ghichu='$ghichu' WHERE madm=$madm");
    }

    public static function delete($db, $madm) {
        $madm = (int)$madm;
        $movies = $db->select("SELECT * FROM phim WHERE madm = $madm");
        if (!empty($movies)) {
            return false; // Cannot delete, has movies
        }
        return $db->execute("DELETE FROM danhmuc WHERE madm=$madm");
    }
}
