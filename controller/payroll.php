<?php
session_start();
include_once('connect.php');

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem dữ liệu đã được gửi đi từ form chưa
if (isset($_POST['submit'])) {
     // Lấy dữ liệu từ form 
     $maNhanVien = $_POST['maNhanVien'];
     $mucLuongCoBan = $_POST['mucLuongCoBan']; 
     $phuCap = $_POST['phuCap']; 
     $thueThuNhapCaNhan = $_POST['thueThuNhapCaNhan'];
     $tongLuong = $_POST['tongLuong']; 
     $ngayThanhToan = $_POST['ngayThanhToan'];
     $baoHiem = $_POST['baoHiem'];
     $maPhongBan = $_POST['maPhongBan'];
     $maLuong = $_POST['maLuong'];
     $thuong = $_POST['thuong'];
 
     // Chuyển đổi định dạng tiền cho các trường cần thiết
     $mucLuongCoBan = str_replace(',', '', $mucLuongCoBan);
     $phuCap = str_replace('.', '', $phuCap);
     $tongLuong = str_replace(',', '', $tongLuong);

    // Thực hiện giao dịch SQL
    $db->begin_transaction();
    try {
        // Thêm dữ liệu vào bảng luong
        $sql_luong = "INSERT INTO luong (maLuong, maNhanVien, mucLuongCoBan, phuCap, thuong, thueThuNhapCaNhan, tongLuong, ngayThanhToan, baoHiem) VALUES ('$maLuong', '$maNhanVien', '$mucLuongCoBan', '$phuCap', '$thuong', '$thueThuNhapCaNhan', '$tongLuong', '$ngayThanhToan', '$baoHiem')";
        $result_luong = $db->query($sql_luong);

        // Hoàn thành giao dịch SQL
        $db->commit();
        $message = "Lương đã được tạo thành công!";
    } catch (mysqli_sql_exception $exception) {
        // Nếu có lỗi, rollback giao dịch SQL
        $db->rollback();
        $message = "Có lỗi xảy ra khi tạo lương: " . $exception->getMessage();
    }

    // Lưu URL trước đó vào session
    $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
    $username = $_SESSION['user']['username'];

    // Kiểm tra và xác định trang cần chuyển hướng dựa trên user và URL trước đó
    if (isset($_SESSION['previous_url'])) {
        if (strpos($_SESSION['previous_url'], 'user=admin') !== false) {
            // Chuyển hướng đến trang quản lý của admin
            $redirect_url = "../admin/home.php?user=admin&table=createLuong";
        } else {
            // Nếu không thể xác định user, chuyển hướng đến trang mặc định
            $redirect_url = "home.php";
        }
    } else {
        // Nếu không có URL trước đó, chuyển hướng đến trang mặc định
        $redirect_url = "home.php";
    }

    // Hiển thị thông báo và chuyển hướng sau khi thêm phòng ban
    echo "<script>alert('$message'); window.location.href = '$redirect_url';</script>";
}
?>