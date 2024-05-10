<?php
session_start();
include_once ('connect.php');

$message = '';

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem trang hiện tại là trang của admin hay không
$is_admin_page = isset($_POST['page']) && $_POST['page'] === 'admin';

// Nếu dữ liệu đã được gửi đi từ biểu mẫu
if (isset($_POST['submit'])) {
    $department_name = $_POST['tenPhongBan'];
    $department_description = $_POST['moTa'];
    $department_phone = $_POST['soDienThoai'];
    $department_email = $_POST['email'];
    $maNhanVien = isset($_POST['maNhanVien']) ? $_POST['maNhanVien'] : '';
    $user = isset($_GET['user']) ? $_GET['user'] : '';

    // Kiểm tra trường tên phòng ban có được điền không và không chứa ký tự đặc biệt
    if (preg_match('/[!@#$%^&*()\-_=+\[\]{}\\\\|;:\'"`,.<>\/?]/', $department_name)) {
        $message = "Tên phòng ban không được chứa ký tự đặc biệt!";
    } else {
        // Kiểm tra trùng tên phòng ban trong cơ sở dữ liệu
        $query_check_duplicate = "SELECT COUNT(*) AS total FROM phong_ban WHERE tenPhongBan = '$department_name'";
        $result_check_duplicate = mysqli_query($db, $query_check_duplicate);

        if ($result_check_duplicate) {
            $total = mysqli_fetch_assoc($result_check_duplicate)['total'];

            if ($total > 0) {
                $message = "Tên phòng ban đã tồn tại trong cơ sở dữ liệu!";
            } else {
                // Thực hiện truy vấn dựa trên trang hiện tại và có mã nhân viên hay không
                $sql = "INSERT INTO phong_ban (tenPhongBan, moTa, soDienThoai, email" . ($is_admin_page || empty($maNhanVien) ? "" : ", maNhanVien") . ") VALUES ('$department_name', '$department_description', '$department_phone', '$department_email'" . ($is_admin_page || empty($maNhanVien) ? "" : ", '$maNhanVien'") . ")";

                // Thực hiện truy vấn
                $result = $dbs->query($sql);
                if ($result) {
                    $message = "Phòng ban $department_name đã được thêm vào cơ sở dữ liệu!";
                } else {
                    $message = "Có lỗi xảy ra khi thêm phòng ban!";
                }
            }
        } else {
            $message = "Có lỗi xảy ra khi kiểm tra trùng lặp tên phòng ban!";
        }
    }

    // Lưu URL trước đó vào session
    $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
    $username = isset($_SESSION['admin_user']) ? $_SESSION['admin_user'] : (isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : '');
    $username = is_string($username) ? $username : '';
    $role = isset($_SESSION['admin_user']) ? 'admin' : (isset($_SESSION['quanly_user']['username']) ? 'quanly' : '');

    // Kiểm tra và xác định trang cần chuyển hướng dựa trên user và URL trước đó
    if (isset($_SESSION['previous_url'])) {
        if (strpos($_SESSION['previous_url'], 'user=quanly') !== false) {
            // Chuyển hướng đến trang quản lý của user quản lý
            $redirect_url = "../user/quanly/home.php?user=quanly&username=" . $username . "&table=department";
        } elseif (strpos($_SESSION['previous_url'], 'user=admin') !== false) {
            // Chuyển hướng đến trang quản lý của admin
            $redirect_url = "../admin/home.php?user=admin&table=department";
        } else {
            // Nếu không phải là quản lý hoặc admin, chuyển hướng đến trang chính
            $redirect_url = "../../home.php?user=" . $role . "&username=" . $username;
        }
    } else {
        // Nếu không có URL trước đó, chuyển hướng đến trang chính
        $redirect_url = "../../home.php?user=" . $role . "&username=" . $username;
    }

    // Hiển thị thông báo và chuyển hướng sau khi cập nhật thành công
    echo "<script>alert('$message'); window.location.href = '$redirect_url';</script>";
    exit();

}
?>