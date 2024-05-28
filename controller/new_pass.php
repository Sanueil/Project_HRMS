<?php
session_start(); // Bắt đầu hoặc khởi tạo session

// Kết nối đến cơ sở dữ liệu
include_once ('connect.php');

// Kiểm tra xem người dùng có tồn tại không
$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem biểu mẫu đã được gửi chưa
if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ biểu mẫu
    $userId = $_POST['username'];
    $newPassword = $_POST['new_password'];

    $query = "SELECT * FROM tai_khoan WHERE username = '$userId'";
    $result = $dbs->query($query);

    if ($result->num_rows > 0) {
        // Cập nhật mật khẩu của người dùng
        $updateQuery = "UPDATE tai_khoan SET password = '$newPassword' WHERE username = '$userId'";
        if (mysqli_query($db, $updateQuery)) {
            $message = "Mật khẩu đã được cập nhật thành công.";
        } else {
            $message = "Lỗi: Không thể cập nhật mật khẩu.";
        }
    } else {
        $message = "Người dùng không tồn tại.";
    }
    // Lưu URL trước đó vào session
    $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
    $username = $_SESSION['admin_user']['username'];

    // Kiểm tra và xác định trang cần chuyển hướng dựa trên user và URL trước đó
    if (isset($_SESSION['previous_url'])) {
        if (strpos($_SESSION['previous_url'], 'user=admin') !== false) {
            // Chuyển hướng đến trang quản lý của admin
            $redirect_url = "../admin/home.php?user=admin&table=users";
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