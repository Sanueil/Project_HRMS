<?php
include_once ('../controller/connect.php');

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Hàm hiển thị thông tin chi tiết của một nhân viên
function displayEmployeeDetail($employee_id, $db)
{
    // Kiểm tra xem có mã nhân viên được truyền qua không
    if (isset($employee_id)) {
        // Truy vấn thông tin chi tiết của nhân viên
        $sql = "SELECT * FROM nhan_vien WHERE maNhanVien = '$employee_id'";
        $result = mysqli_query($db, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hoTenNhanVien = $row['hoTenNhanVien'];
            $diaChi = $row['diaChi'];
            $soDienThoai = $row['soDienThoai'];
            $email = $row['email'];
            $chucVu = $row['chucVu'];
            $ngaySinh = $row['ngaySinh'];
            $gioiTinh = $row['gioiTinh'];
            $hinhAnh = $row['hinhAnh'];
            $maNhanVien = $row['maNhanVien'];

            $qrFileName = $maNhanVien . '_' . str_replace(' ', '_', $hoTenNhanVien) . '.png';
            // Tạo chuỗi chứa đầy đủ thông tin của nhân viên
            $employee_info = "Mã Nhân Viên: $maNhanVien - ";
            $employee_info .= "Họ Tên: $hoTenNhanVien - ";
            $employee_info .= "Địa Chỉ: $diaChi - ";
            $employee_info .= "Số Điện Thoại: $soDienThoai - ";
            $employee_info .= "Email: $email - ";
            $employee_info .= "Chức Vụ: $chucVu - ";
            $employee_info .= "Ngày Sinh: $ngaySinh - ";
            $employee_info .= "Giới Tính: $gioiTinh\n";
            // Mã hóa thông tin nhân viên để truyền vào URL của ảnh mã QR
            $qr_code_data = urlencode($employee_info);

            // Hiển thị thông tin chi tiết của nhân viên
            echo '<div class="employee-details">';
            echo "<p><strong>Mã Nhân Viên:</strong> $maNhanVien</p>";
            echo "<p><strong>Họ Tên:</strong> $hoTenNhanVien</p>";
            echo "<p><strong>Địa Chỉ:</strong> $diaChi</p>";
            echo "<p><strong>Số Điện Thoại:</strong> $soDienThoai</p>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Chức Vụ:</strong> $chucVu</p>";
            echo "<p><strong>Ngày Sinh:</strong> $ngaySinh</p>";
            echo "<p><strong>Giới Tính:</strong> $gioiTinh</p>";
            $query = "UPDATE nhan_vien SET maQR = '$qrFileName' WHERE maNhanVien = '$maNhanVien'";
            $validQRCodesResult = $db->query($query);
            // Hiển thị ảnh mã QR chứa đầy đủ thông tin của nhân viên
            echo "<p><strong>Mã QR:</strong></p>";
            echo "<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$qr_code_data' alt='QR Code'>";
            echo '</div>';
        } else {
            echo "<p class='text-danger'>Không tìm thấy người dùng.</p>";
        }
    } else {
        echo "<p class='text-danger'>Không có mã nhân viên được cung cấp.</p>";
    }
}

// Sử dụng hàm để hiển thị thông tin chi tiết của nhân viên
if (isset($_POST['maNhanVien'])) {
    $id = $_POST['maNhanVien'];
    displayEmployeeDetail($id, $db);
}
?>