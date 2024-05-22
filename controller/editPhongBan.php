<?php
session_start();
include_once('connect.php');

$message = '';
$updated = false; // Biến này để kiểm tra xem đã có cập nhật hay không

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem trang hiện tại là trang của admin hay không
$is_admin_page = isset($_POST['page']) && $_POST['page'] === 'admin';

if(isset($_POST['submit'])) {
    $department_id = isset($_POST['department_id']) ? $_POST['department_id'] : '';
    $new_department_name = $_POST['tenPhongBan'];
    $new_department_description = $_POST['moTa'];
    $new_department_phone = $_POST['soDienThoai'];
    $new_department_email = $_POST['email'];
    $maNhanVien = isset($_POST['maNhanVien']) ? $_POST['maNhanVien'] : '';
    $user = isset($_GET['user']) ? $_GET['user'] : '';

    // Lấy thông tin phòng ban hiện tại từ cơ sở dữ liệu
    $sql_select = "SELECT * FROM phong_ban WHERE maPhongBan = $department_id";
    $result_select = $dbs->query($sql_select);
    $current_department = $result_select->fetch_assoc();

    // Kiểm tra xem thông tin mới có khác với thông tin hiện tại không
    if ($current_department['tenPhongBan'] != $new_department_name || 
        $current_department['moTa'] != $new_department_description ||
        $current_department['soDienThoai'] != $new_department_phone ||
        $current_department['email'] != $new_department_email) {
        // Thực hiện truy vấn để cập nhật thông tin phòng ban
        $sql = "UPDATE phong_ban SET tenPhongBan = '$new_department_name', moTa = '$new_department_description'
        , soDienThoai = '$new_department_phone', email = '$new_department_email', ngayChinhSua = NOW() WHERE maPhongBan = $department_id";
        $result = $dbs->query($sql);
        if ($result) {
            $updated = true; // Đánh dấu là đã cập nhật thành công
            $message = "Thông tin phòng ban đã được cập nhật!";
        } else {
            $message = "Có lỗi xảy ra khi cập nhật thông tin phòng ban!";
        }
    } else {
        $message = "Không có thay đổi nào được thực hiện!";
    }
}

// Lưu URL trước đó vào session
$_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
$username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : '';
$role = isset($_SESSION['admin_user']) ? 'admin' : (isset($_SESSION['quanly_user']['username']) ? 'quanly' : '');

// Kiểm tra và xác định trang cần chuyển hướng dựa trên user và URL trước đó
if (isset($_SESSION['previous_url'])) {
    if (strpos($_SESSION['previous_url'], 'user=quanly') !== false) {
        // Chuyển hướng đến trang quản lý của user quản lý
        $redirect_url = "../user/quanly/home.php?user=quanly&username=".$username."&table=department";
    } elseif (strpos($_SESSION['previous_url'], 'user=admin') !== false) {
        // Chuyển hướng đến trang quản lý của admin
        $redirect_url = "../admin/home.php?user=admin&table=department";
    } else {
        // Nếu không phải là quản lý hoặc admin, chuyển hướng đến trang chính
        $redirect_url = "../../home.php?user=".$role."&username=".$username;
    }
} else {
    // Nếu không có URL trước đó, chuyển hướng đến trang chính
    $redirect_url = "../../home.php?user=".$role."&username=".$username;
}

// Hiển thị thông báo và chuyển hướng sau khi cập nhật thành công
echo "<script>alert('$message'); window.location.href = '$redirect_url';</script>";
exit();
?>