<?php
// Kết nối đến cơ sở dữ liệu
include_once('connect.php');

$message = '';

$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra hành động được gửi từ trang danh sách người dùng
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    if($action == "sua") {
        // Kiểm tra xem biểu mẫu đã được gửi chưa
        if(isset($_POST['submit'])) {
            // Lấy dữ liệu từ biểu mẫu và xử lý cập nhật người dùng
            $maNhanVien = $_POST['maNhanVien'];
            $hoTenNhanVien = $_POST['hoTenNhanVien'];
            $diaChi = $_POST['diaChi'];
            $soDienThoai = $_POST['soDienThoai'];
            $email = $_POST['email'];
            $chucVu = $_POST['chucVu'];
            $ngaySinh = $_POST['ngaySinh'];
            $gioiTinh = $_POST['gioiTinh'];

            // Cập nhật dữ liệu vào cơ sở dữ liệu
            $query = "UPDATE nhan_vien 
                      SET hoTenNhanVien = '$hoTenNhanVien', diaChi = '$diaChi', soDienThoai = '$soDienThoai', 
                          email = '$email', chucVu = '$chucVu', ngaySinh = '$ngaySinh', gioiTinh = '$gioiTinh'
                      WHERE maNhanVien = '$maNhanVien'";
            if(mysqli_query($db, $query)) {
                // Kiểm tra xem có bản ghi nào bị ảnh hưởng hay không
                if(mysqli_affected_rows($db) > 0) {
                    // Hiển thị thông báo thành công nếu có bản ghi được cập nhật
                    $message = "Cập nhật thông tin người dùng thành công.";
                } else {
                    $message = "Không có gì được thay đổi.";
                }
            } else {
                $message = "Lỗi: Không thể cập nhật thông tin người dùng.";
            }
        }
    } elseif($action == "xoa") {
        // Kiểm tra xem mã nhân viên đã được truyền qua không
        if(isset($_GET['maNhanVien'])) {
            // Xóa người dùng khỏi cơ sở dữ liệu
            $maNhanVien = $_GET['maNhanVien'];
            $query = "DELETE FROM nhan_vien WHERE maNhanVien = '$maNhanVien'";
            if(mysqli_query($db, $query)) {
                // Hiển thị thông báo thành công
                $message = "Xóa người dùng thành công.";
            } else {
                $message = "Lỗi: Không thể xóa người dùng.";
            }
        } else {
            $message = "Lỗi: Không có mã nhân viên được cung cấp.";
        }
    }
} else {
    $message = "Lỗi: Hành động không được xác định.";
}

// Hiển thị thông báo và chuyển hướng
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo</title>
    <script>
    function showAlert(message) {
        alert(message);
        window.location.href = "../admin/home.php?user=admin&table=dsUsers";
    }
    </script>
</head>

<body>

    <script>
    showAlert('<?php echo $message; ?>');
    </script>
</body>

</html>