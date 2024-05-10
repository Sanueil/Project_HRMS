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
    public function resetAutoIncrement($table) {
        // Thiết lập lại số tự động tăng (auto_increment) trong bảng
        $sql_alter = "ALTER TABLE $table AUTO_INCREMENT = 1";
        if ($this->conn->query($sql_alter) === TRUE) {
            echo "Đã thiết lập lại số tự động tăng trong bảng $table.";
        } else {
            die("Lỗi khi thiết lập lại số tự động tăng: " . $this->conn->error);
        }
    }
    
    public function getError() {
        return $this->conn->error;
    }
}


?>