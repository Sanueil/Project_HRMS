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

        if ($result_luong) {
            // Lưu URL trước đó vào session
            $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];

            // Xác định vai trò và tên người dùng hiện tại
            $role = isset($_SESSION['quanly_user']['username']) ? 'quanly' : (isset($_SESSION['nhanvien_user']['username']) ? 'nhanvien' : '');
            $username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : (isset($_SESSION['nhanvien_user']['username']) ? $_SESSION['nhanvien_user']['username'] : '');

            // Tạo URL đầy đủ với vai trò và tên người dùng
            $url = "home.php?user=" . $role . "&username=" . $username . "&table=createLuong";

            // Tạo đường link đầy đủ
            $redirectUrl = "../user/" . $role . "/" . $url;


            // Hiển thị thông báo và chuyển hướng sau khi thêm lương
            echo "<script>alert('Lương đã được tạo thành công!');</script>";
            echo "<script>window.location.href = '$redirectUrl';</script>";
            exit();
        } else {
            // Hiển thị thông báo lỗi
            $message = "Có lỗi xảy ra khi thêm lương.";
        }
    } catch (mysqli_sql_exception $exception) {
        // Nếu có lỗi, rollback giao dịch SQL
        $db->rollback();
        $message = "Có lỗi xảy ra khi tạo lương: " . $exception->getMessage();
    }

    // Hiển thị thông báo lỗi và quay trở lại trang trước đó
    echo "<script>alert('$message'); window.history.back();</script>";
}
?>