<?php
class Category {
    public static function getAll($db) {
        return $db->select("SELECT * FROM danhmuc ORDER BY tendm");
    }
}
