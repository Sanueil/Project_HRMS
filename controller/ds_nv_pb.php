<?php
include_once ('connect.php');

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

function displayEmployeesByDepartment($department_id, $db)
{
    $employees = array();

    // Thực hiện truy vấn để lấy danh sách nhân viên trong phòng ban tương ứng
    $sql = "SELECT maNhanVien, hoTenNhanVien 
            FROM nhan_vien 
            WHERE maNhanVien IN (
                SELECT maNhanVien 
                FROM nhan_vien_phong_ban 
                WHERE maPhongBan = $department_id
            )
            AND chucVu = 'Nhân viên'";

    $result = $db->query($sql);

    // Kiểm tra kết quả truy vấn và trả về một mảng chứa thông tin nhân viên
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = array(
                'maNhanVien' => $row['maNhanVien'],
                'hoTenNhanVien' => $row['hoTenNhanVien']
            );
        }
    }

    return $employees;
}

// Sử dụng hàm để lấy danh sách nhân viên và hiển thị nó
if (isset($_POST['get_department_id'])) {
    $department_id = $_POST['get_department_id'];
    $employees = displayEmployeesByDepartment($department_id, $db);

    // Hiển thị danh sách nhân viên
    if (!empty($employees)) {
        echo '<table class="table table-striped">
        <thead class="thead-dark">';
        echo '<tr><th scope="col">Mã nhân viên</th><th scope="col">Họ tên nhân viên</th>
        <th scope="col">Thao tác</th></tr></thead>';
        foreach ($employees as $employee) {
            echo '<tr>';
            echo '<td>' . $employee['maNhanVien'] . '</td>';
            echo '<td>' . $employee['hoTenNhanVien'] . '</td>';
            echo '<td><button class="btn btn-danger btn-sm" onclick="removeEmployee(' . $employee['maNhanVien'] . ',' . $department_id . ')">Xóa</button></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Không có nhân viên nào trong phòng ban này.</p>';
    }
}

// Hàm xử lý xóa nhân viên khỏi phòng ban
if (isset($_POST['maNhanVien']) && isset($_POST['maPhongBan'])) {
    $maNhanVien = $_POST['maNhanVien'];
    $maPhongBan = $_POST['maPhongBan'];

    // Thực hiện truy vấn để xóa nhân viên khỏi phòng ban
    $sql = "DELETE FROM nhan_vien_phong_ban WHERE maNhanVien = $maNhanVien AND maPhongBan = $maPhongBan";
    if ($db->query($sql) === TRUE) {
        echo "Xóa nhân viên thành công!";
    } else {
        echo "Lỗi khi xóa nhân viên: " . $db->error;
    }
}
?>