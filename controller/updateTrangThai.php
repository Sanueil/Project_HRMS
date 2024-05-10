<?php
session_start();
include_once('connect.php');
$dbs = new Database();
$db = $dbs->connect();

if(isset($_POST['approve']) || isset($_POST['reject'])){
    $id = $_POST['maNhanVien'];
    $trangThaiMoi = isset($_POST['approve']) ? 'Duyệt' : 'Từ chối';

    // Cập nhật trạng thái trong cơ sở dữ liệu
    $updateQuery = "UPDATE cham_cong SET trangThai = '$trangThaiMoi' WHERE maNhanVien = $id";
    $result = $db->query($updateQuery);

    // Kiểm tra kết quả của truy vấn và hiển thị thông báo tương ứng
    if ($result) {
        $message = "Trạng thái đã được cập nhật!";
    } else {
        $message = "Có lỗi xảy ra khi cập nhật trạng thái!";
    }
} else {
    $message = "Không có hành động được thực hiện!";
}

// Hiển thị thông báo và chuyển hướng sau khi cập nhật trạng thái
echo "<script>alert('$message'); window.location.href = 'url_muon_chuyen_huong.php';</script>";
?>