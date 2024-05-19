<?php
session_start();
// Kết nối đến cơ sở dữ liệu
include_once('connect.php');

$message = '';

$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra biểu mẫu đã được gửi chưa
if(isset($_POST['submit'])) {
    // Lấy dữ liệu từ biểu mẫu
    $maNhanVien = $_POST['maNhanVien'];
    $hoTenNhanVien = $_POST['hoTenNhanVien'];
    $diaChi = $_POST['diaChi'];
    $soDienThoai = $_POST['soDienThoai'];
    $email = $_POST['email'];
    $chucVu = $_POST['chucVu'];
    $ngaySinh = $_POST['ngaySinh'];
    $gioiTinh = $_POST['gioiTinh'];

    // Tạo tên file QR
    $qrFileName = $maNhanVien . '_' . str_replace(' ', '_', $hoTenNhanVien) . '.png';

    // Lưu tên file QR vào cơ sở dữ liệu
    $query = "INSERT INTO nhan_vien (maNhanVien, hoTenNhanVien, diaChi, soDienThoai, email, chucVu, ngaySinh, gioiTinh, maQR)
            VALUES ('$maNhanVien', '$hoTenNhanVien', '$diaChi', '$soDienThoai', '$email', '$chucVu', '$ngaySinh', '$gioiTinh', '$qrFileName')";
    if(mysqli_query($db, $query)) {
        $_SESSION['message'] = "Thêm người dùng thành công.";
        // Tạo mã QR và lưu file vào thư mục
        $qrData = "Mã Nhân Viên: $maNhanVien\nHọ Tên: $hoTenNhanVien\nĐịa Chỉ: $diaChi\nSố Điện Thoại: $soDienThoai\nGiới Tính: $gioiTinh\nEmail: $email\nChức Vụ: $chucVu\nNgày Sinh: $ngaySinh";
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
        file_put_contents("../controller/uploads/$qrFileName", file_get_contents($apiUrl));
        
        header("Location: ../admin/home.php?user=admin&table=addUser");
        exit();
    } else {
        $_SESSION['message'] = "Lỗi: Không thể thêm người dùng.";
    }
}
header("Location: ../admin/home.php?user=admin&table=addUser");
exit();
?>