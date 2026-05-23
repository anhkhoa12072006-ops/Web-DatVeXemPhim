<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "kt";
    
    //  private $host = "sql213.infinityfree.com";
    // private $username = "if0_41946342";
    // private $password = "jboW2sVuZgBZIo";
    // private $dbname = "if0_41946342_kt";
    // private $conn;
    
    private $conn;
    
    public function __construct() {
        // Thiết lập múi giờ Việt Nam cho PHP
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
        // Thiết lập múi giờ Việt Nam cho MySQL
        $this->conn->query("SET time_zone = '+07:00'");
    }
    
    // Truy vấn SELECT – trả về mảng dữ liệu
    public function select($sql) {
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    // Thực thi lệnh INSERT, UPDATE, DELETE
    public function execute($sql) {
        return $this->conn->query($sql);
    }
    
    // Lấy ID vừa insert
    public function getInsertId() {
        return $this->conn->insert_id;
    }
    
    // Escape string để tránh SQL injection
    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }
    
    // Hàm hủy kết nối
    public function close() {
        $this->conn->close();
    }
}

// Khởi tạo database global
$db = new Database();

// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra toàn cục nếu tài khoản đang bị khóa
if (isset($_SESSION['tendn'])) {
    $current_user = $_SESSION['tendn'];
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    
    if ($current_script != 'login.php' && $current_script != 'logout.php') {
        $check_status = $db->select("SELECT trangthai FROM nguoidung WHERE tendn = '$current_user'");
        if (!empty($check_status) && $check_status[0]['trangthai'] == 'Bị khóa') {
            session_destroy();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['login_error'] = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.";
            
            // Xử lý path chuyển hướng tùy theo thư mục admin hay thư mục gốc
            $is_admin_dir = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
            if ($is_admin_dir) {
                header('Location: ../login.php');
            } else {
                header('Location: login.php');
            }
            exit;
        }
    }
}
?>
