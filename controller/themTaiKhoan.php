<?php
session_start();
include_once('connect.php');

$message = '';

// Nếu biến session đã được thiết lập, tức là tài khoản đã được tạo
if(isset($_SESSION['account_created'])) {
    $message = "Tài khoản đã được tạo. Không thể thêm tài khoản mới.";
}

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Nếu dữ liệu đã được gửi đi từ biểu mẫu
if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Giả sử vai trò được chọn từ một danh sách thả xuống
    $maNhanVien = $_POST['maNhanVien']; // Lấy giá trị mã nhân viên từ trường ẩn

    // Kiểm tra trùng username của nhân viên
    $check_username_sql = "SELECT * FROM tai_khoan WHERE username = '$username'";
    $check_username_result = $dbs->query($check_username_sql);
    if ($check_username_result->num_rows > 0) {
        $message = "Tên người dùng đã tồn tại.";
    } else {
        // Thực hiện truy vấn INSERT để thêm tài khoản mới vào cơ sở dữ liệu
        $sql = "INSERT INTO tai_khoan (username, password, maNhanVien, id_phan_quyen) 
                VALUES ('$username', '$password', '$maNhanVien', '$role')"; //Thêm mã nhân viên vào câu lệnh SQL
        $result = $dbs->query($sql);
        if($result) {
            $message = "Thêm tài khoản thành công.";
            $_SESSION['account_created'] = true; // Đánh dấu rằng tài khoản đã được tạo
        } else {
            $message = "Lỗi khi thêm tài khoản: " . $dbs->getError();
        }
    }
    // Đóng kết nối
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo</title>
    <script>
    function showAlert(message, redirectTo) {
        alert(message);
        window.location.href = redirectTo;
    }
    </script>
</head>

<body>
    <?php if(!empty($message)): ?>
    <script>
    showAlert('<?php echo $message; ?>', "../admin/home.php?user=admin&table=add");
    </script>
    <?php endif; ?>
</body>

</html>