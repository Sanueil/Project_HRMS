<?php
include_once ('connect.php');

// Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = checkLogin($username, $password);
    // Kiểm tra xem trang nào đang được truy cập
    if (isset($_POST['page']) && $_POST['page'] === 'admin') {
        // Trang đăng nhập của admin
        if ($user && checkUserRole($user) === 'admin') {
            session_start();
            $_SESSION['admin_user'] = $user; // Lưu thông tin đăng nhập của admin vào session riêng
            header("Location: ../admin/home.php?user=admin");
            exit();
        } else {
            header("Location: ../admin/login.php?error=Invalid username or password");
            exit();
        }
    } else {
        // Trang đăng nhập của người dùng
        if ($user) {
            $role = checkUserRole($user);
            session_start();
            // Kiểm tra xem đã có session nào đang hoạt động cho vai trò này chưa
            if ($role === 'quanly' && isset($_SESSION['quanly_user'])) {
                // Đã có phiên đăng nhập cho quản lý
                header("Location: ../user/quanly/home.php?user=quanly&username=" . $username . "&error=Another session is active for manager");
                exit();
            } elseif ($role === 'nhanvien' && isset($_SESSION['nhanvien_user'])) {
                // Đã có phiên đăng nhập cho nhân viên
                header("Location: ../user/nhanvien/home.php?user=nhanvien&username=" . $username . "&error=Another session is active for employee");
                exit();
            }
            // Lưu thông tin đăng nhập vào session riêng của từng vai trò
            $_SESSION[$role . '_user'] = $user;
            switch ($role) {
                case 'quanly':
                    header("Location: ../user/quanly/home.php?user=quanly&username=" . $username);
                    exit();
                case 'nhanvien':
                    header("Location: ../user/nhanvien/home.php?user=nhanvien&username=" . $username);
                    exit();
                default:
                    // Xóa session và chuyển hướng đến trang đăng nhập tương ứng với vai trò không hợp lệ
                    session_unset();
                    session_destroy();
                    header("Location: ../index.php?error=Invalid username or role");
                    exit();
            }
        } else {
            header("Location: ../index.php?error=Invalid username or password");
            exit();
        }
    }
}


function checkLogin($username, $password)
{
    global $db;
    $query = "SELECT * FROM tai_khoan WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        return $user;
    } else {
        return false;
    }
}

function checkUserRole($user)
{
    global $db;
    $userId = $user['username'];
    $query = "SELECT vai_tro FROM phan_quyen p join tai_khoan t on p.id = t.id_phan_quyen  WHERE username = '$userId'";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['vai_tro']; // Trả về giá trị của cột 'role'
    } else {
        return 'Unknown';
    }
}

?>