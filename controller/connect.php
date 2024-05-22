<?php
class Database {
    private $conn;

    // Phương thức kết nối đến cơ sở dữ liệu
    public function connect() {
        $this->conn = new mysqli("localhost", "root", "", "hrms");
        if ($this->conn->connect_error) {
            die("Kết nối không thành công: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    // Phương thức thực thi truy vấn
    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            die("Lỗi truy vấn: " . $this->conn->error);
        }
        return $result;
    }

    // Phương thức đóng kết nối
    public function close() {
        $this->conn->close();
    }

    public function paginate($sql, $records_per_page, $current_page) {
        $offset = ($current_page - 1) * $records_per_page;
        $sql .= " LIMIT $offset, $records_per_page";
        $result = $this->query($sql);
        return $result;
    }

    public function resetAutoIncrement($tableName) {
        $sql = "ALTER TABLE $tableName AUTO_INCREMENT = 1";
        if (!$this->conn->query($sql)) {
            die("Lỗi truy vấn: " . $this->conn->error);
        }
    }

    public function getError() {
        return $this->conn->error;
    }

    // Phương thức mới để gọi resetAutoIncrement
    public function resetChamCongAutoIncrement() {
        $this->resetAutoIncrement('cham_cong');
    }
}

// Tạo đối tượng của lớp Database và sử dụng các phương thức
$db = new Database();
$conn = $db->connect();
$db->resetChamCongAutoIncrement();
$db->close();
?>