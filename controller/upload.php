<?php
session_start();
// Kết nối đến cơ sở dữ liệu
include_once ('connect.php');

$message = '';

$dbs = new Database();
$db = $dbs->connect();

if (isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Lấy đường dẫn tạm thời và tên file hình ảnh
    $fileTmpName = $file['tmp_name'];
    $fileName = basename($file['name']);

    // Đường dẫn mới của hình ảnh
    $newImagePath = 'uploads/' . $fileName;

    // Di chuyển hình ảnh vào thư mục uploads
    if (move_uploaded_file($fileTmpName, $newImagePath)) {
        // Kiểm tra xem người dùng đã đăng nhập với vai trò "quanly" hay "nhanvien"
        if (isset($_SESSION['quanly_user']['username'])) {
            $maNhanVien = $_SESSION['quanly_user']['username'];
        } elseif (isset($_SESSION['nhanvien_user']['username'])) {
            $maNhanVien = $_SESSION['nhanvien_user']['username'];
        } else {
            echo "Lỗi: Không tìm thấy thông tin người dùng từ session.";
            exit();
        }

        // Cập nhật đường dẫn của hình ảnh trong trường hinhAnh
        $query = "UPDATE nhan_vien SET hinhAnh = '$newImagePath' WHERE maNhanVien = '$maNhanVien'";
        if (mysqli_query($db, $query)) {
            // Trả về đường dẫn của hình ảnh mới
            echo $newImagePath;
        } else {
            echo "Lỗi: Không thể cập nhật đường dẫn hình ảnh.";
        }
    } else {
        echo "Lỗi: Không thể di chuyển tệp hình ảnh.";
    }
} else {
    echo "Lỗi: Không tìm thấy tệp hình ảnh được tải lên.";
}

?>