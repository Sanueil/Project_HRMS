<?php
include_once('connect.php');

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

function displayAttendanceByEmployee($employee_id, $db) {
    $attendance = array();

    // Thực hiện truy vấn để lấy thông tin chấm công của nhân viên
    $sql = "SELECT maNhanVien,maChamCong, ngayChamCong, thoiGianVao, thoiGianRa, trangThai 
            FROM cham_cong 
            WHERE maNhanVien = '$employee_id'";

    $result = $db->query($sql);

    // Kiểm tra kết quả truy vấn và trả về một mảng chứa thông tin chấm công
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $attendance[] = array(
                'maNhanVien' => $row['maNhanVien'],
                'maChamCong' => $row['maChamCong'],
                'ngayChamCong' => $row['ngayChamCong'],
                'thoiGianVao' => $row['thoiGianVao'],
                'thoiGianRa' => $row['thoiGianRa'],
                'trangThai' => $row['trangThai']
            );
        }
    }

    return $attendance;
}

// Sử dụng hàm để lấy thông tin chấm công của nhân viên và hiển thị nó
if(isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];
    $attendance = displayAttendanceByEmployee($employee_id, $db);

    // Hiển thị thông tin chấm công của nhân viên
    if (!empty($attendance)) {
        echo '<table class="table table-striped">
        <thead class="thead-dark">';
        echo '<tr><th scope="col">Mã chấm công</th><th scope="col">Ngày</th><th scope="col">Giờ Đến</th><th scope="col">Giờ Ra</th><th scope="col">Trạng Thái</th></tr></thead>';
        foreach ($attendance as $record) {
            echo '<tr>';
            echo '<td>' . $record['maChamCong'] . '</td>';
            echo '<td>' . $record['ngayChamCong'] . '</td>';
            echo '<td>' . $record['thoiGianVao'] . '</td>';
            echo '<td>' . $record['thoiGianRa'] . '</td>';
            echo '<td>' . $record['trangThai'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Không có dữ liệu chấm công cho nhân viên này.</p>';
    }
}
?>