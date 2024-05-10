<?php
session_start();
include_once('connect.php');
$dbs = new Database();
$db = $dbs->connect();
if(isset($_POST['submit'])){
    $id = $_POST['maLuong'];
    $salary = $_POST['salary'];
    $phuCap = $_POST['phuCap'];
    $bonus = $_POST['bonus'];
    $penalty = $_POST['penalty'];
    $tax = $_POST['tax'];
    // Kiểm tra xem liệu có thay đổi dữ liệu so với dữ liệu hiện tại không
    $selectQuery = "SELECT phuCap, thuong, phat FROM luong WHERE maLuong = $id";
    $currentData = $db->query($selectQuery)->fetch_assoc();
    // Kiểm tra xem các trường phụ cấp, thưởng và phạt đã được điền vào hay chưa
    if ($currentData['phuCap'] == $phuCap && $currentData['thuong'] == $bonus && $currentData['phat'] == $penalty) {
        // Nếu không có thay đổi, hiển thị thông báo và chuyển hướng về trang trước
        $message = "Không có thay đổi dữ liệu để cập nhật!";
        echo "<script>alert('$message'); window.history.back();</script>";
        exit();
    }
    // Thực hiện các phép tính cần thiết để tính toán tổng lương
    $totalSalary = $salary - ($salary * ($tax / 100)) + $phuCap + $bonus - $penalty;
    $updateQuery = "UPDATE luong SET phuCap = '$phuCap', thuong = '$bonus', phat = '$penalty', tongLuong = '$totalSalary' WHERE maLuong = $id";
    $result = $db->query($updateQuery);

    // Kiểm tra kết quả của truy vấn và hiển thị thông báo tương ứng
    if ($result) {
        $message = "Thông tin lương đã được cập nhật!";
    } else {
        $message = "Có lỗi xảy ra khi cập nhật thông tin lương!";
    }
    // Lưu URL trước đó vào session
    $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
    $username = $_SESSION['user']['username'];
     // Tạo URL đầy đủ với ID được truyền vào
    $url = "home.php?user=quanly&username".$username;
    // Kiểm tra và xác định trang cần chuyển hướng dựa trên URL trước đó
    if (isset($_SESSION['previous_url'])) {
        if (strpos($_SESSION['previous_url'], 'user=quanly') !== false) {
            // Chuyển hướng đến trang quản lý của user quản lý với bảng là "luong"
            $redirect_url = "../user/quanly/".$url."&table=luong";
        } else {
            // Nếu không thể xác định user, chuyển hướng đến trang mặc định
            $redirect_url = "home.php";
        }
    } else {
        // Nếu không có URL trước đó, chuyển hướng đến trang mặc định
        $redirect_url = "home.php";
    }

    // Hiển thị thông báo và chuyển hướng sau khi cập nhật thông tin lương
    echo "<script>alert('$message'); window.location.href = '$redirect_url';</script>";
}