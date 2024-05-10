<?php
session_start();
include_once('connect.php');

// Kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

if(isset($_POST['submit'])) {
    $maNhanVien = $_POST['maNhanVien'];
    $hoTenNhanVien = $_POST['hoTenNhanVien'];
    $diaChi = $_POST['diaChi'];
    $soDienThoai = $_POST['soDienThoai'];
    $email = $_POST['email'];
    $chucVu = $_POST['chucVu'];
    $ngaySinh = $_POST['ngaySinh'];
    $gioiTinh = $_POST['gioiTinh'];
    
    // Kiểm tra xem thông tin đã được chỉnh sửa hay không
    $query_check = "SELECT * FROM nhan_vien WHERE maNhanVien='$maNhanVien' AND hoTenNhanVien='$hoTenNhanVien' AND diaChi='$diaChi' AND soDienThoai='$soDienThoai' AND email='$email' AND chucVu='$chucVu' AND ngaySinh='$ngaySinh' AND gioiTinh='$gioiTinh'";
    $result_check = $dbs->query($query_check);
    $num_rows = mysqli_num_rows($result_check);
    
    if($num_rows > 0) {
        // Không có sự thay đổi, không cần cập nhật
        echo "<script>alert('Không có thay đổi nào được thực hiện.'); history.go(-1);</script>";
        exit();
    }
    
    // Cập nhật thông tin nhân viên vào cơ sở dữ liệu
    $query = "UPDATE nhan_vien SET hoTenNhanVien='$hoTenNhanVien', diaChi='$diaChi', soDienThoai='$soDienThoai', email='$email', chucVu='$chucVu', ngaySinh='$ngaySinh', gioiTinh='$gioiTinh' WHERE maNhanVien='$maNhanVien'";
    $result = $dbs->query($query);

    if($result) {
        // Lưu URL trước đó vào session
        $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];
        $username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : (isset($_SESSION['nhanvien_user']['username']) ? $_SESSION['nhanvien_user']['username'] : '');
        
        // Tạo URL đầy đủ với ID được truyền vào
        $role = isset($_SESSION['quanly_user']['username']) ? 'quanly' : (isset($_SESSION['nhanvien_user']['username']) ? 'nhanvien' : '');
        $url = "home.php?user=".$role."&username=".$username;
        // Kiểm tra và xác định trang cần chuyển hướng dựa trên user và URL trước đó
        if (isset($_SESSION['previous_url']) && strpos($_SESSION['previous_url'], 'user=quanly') !== false) {
            // Chuyển hướng đến trang quản lý của user quản lý
            $redirect_url = "../user/quanly/".$url."&table=profile";
        } elseif (isset($_SESSION['previous_url']) && strpos($_SESSION['previous_url'], 'user=nhanvien') !== false) {
            // Chuyển hướng đến trang nhân viên của user nhân viên
            $redirect_url = "../user/nhanvien/".$url."&table=profile";
        } else {
            // Nếu không phải là quản lý hoặc nhân viên, chuyển hướng đến trang chính
            $redirect_url = "../../home.php?user=".$role."&username=".$username;
        }

        // Hiển thị thông báo và chuyển hướng sau khi cập nhật thành công
        echo "<script>alert('Cập nhật thông tin nhân viên thành công'); window.location.href = '$redirect_url';</script>";
        exit();
    } else {
        // Nếu có lỗi xảy ra, hiển thị thông báo lỗi
        echo "<script>alert('Có lỗi xảy ra khi cập nhật thông tin nhân viên: " . $dbs->getError() . "');</script>";
    }
}

?>