<?php
// Bắt đầu phiên làm việc
session_start();

// Kiểm tra và xóa session của quản lý nếu tồn tại
if (isset($_SESSION['admin_user'])) {
    unset($_SESSION['admin_user']);
}

// Đảm bảo rằng trang không được cache bởi trình duyệt
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Chuyển hướng đến trang đăng nhập hoặc bất kỳ trang nào khác mà bạn mong muốn
header("Location: login.php"); // Thay 'login.php' bằng trang đăng nhập mong muốn
exit;