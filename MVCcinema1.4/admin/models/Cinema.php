<?php
class Cinema {
    public static function getAll($db) {
        return $db->select("
            SELECT r.*,
                   (SELECT COUNT(*) FROM phongchieu p WHERE p.marap = r.marap) as sophong
            FROM rapchieu r
            ORDER BY r.marap
        ");
    }

    public static function add($db, $tenrap, $diachi, $hotline) {
        $tenrap = $db->escape($tenrap);
        $diachi = $db->escape($diachi);
        $hotline = $db->escape($hotline);
        return $db->execute("INSERT INTO rapchieu (tenrap, diachi, hotline) VALUES ('$tenrap', '$diachi', '$hotline')");
    }

    public static function update($db, $marap, $tenrap, $diachi, $hotline) {
        $marap = (int)$marap;
        $tenrap = $db->escape($tenrap);
        $diachi = $db->escape($diachi);
        $hotline = $db->escape($hotline);
        return $db->execute("UPDATE rapchieu SET tenrap='$tenrap', diachi='$diachi', hotline='$hotline' WHERE marap=$marap");
    }

    public static function delete($db, $marap) {
        $marap = (int)$marap;
        return $db->execute("DELETE FROM rapchieu WHERE marap=$marap");
    }
}
