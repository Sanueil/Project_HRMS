<?php
session_start();
include_once ('../../controller/connect.php');

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['nhanvien_user'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: ../../login.php");
    exit;
}
// Lưu URL trang hiện tại vào session trước khi chuyển hướng
$_SESSION['current_url'] = $_SERVER['REQUEST_URI'];

// Sau đó, bạn có thể sử dụng $_SESSION['current_url'] để lấy URL của trang hiện tại

$username = $_SESSION['nhanvien_user']['username'];

// Tạo URL đầy đủ với username được truyền vào
$url = "home.php?user=nhanvien&username=" . urlencode($username);

$dbs = new Database();
$db = $dbs->connect();

// Sử dụng Prepared Statement để bảo vệ câu truy vấn khỏi SQL Injection
$query = "SELECT hinhAnh FROM nhan_vien WHERE maNhanVien = '$username'";
$result_anh = mysqli_query($db, $query);

if ($result_anh) {
    $row = mysqli_fetch_assoc($result_anh);
    // Kiểm tra xem cột hinhAnh có giá trị không rỗng
    if (!empty($row['hinhAnh'])) {
        // Sử dụng ảnh từ cơ sở dữ liệu
        $logo_url = '../../controller/' . $row['hinhAnh'];
    } else {
        // Nếu không có giá trị, sử dụng ảnh mặc định
        $logo_url = "../../controller/uploads/thiên hà.jpg";
    }
} else {
    // Xử lý lỗi nếu truy vấn không thành công
    echo "Lỗi: " . mysqli_error($db);
}
// Sử dụng Prepared Statement để bảo vệ câu truy vấn khỏi SQL Injection
$query = "SELECT COUNT(*) as user_count FROM tai_khoan WHERE maNhanVien = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Kiểm tra trạng thái của tài khoản
if ($row['user_count'] == 0) {
    // Lưu thông báo vào biến phiên
    $_SESSION['account_message'] = "Tài khoản không tồn tại hoặc đã bị xóa.";

    // Chuyển hướng người dùng đến trang đăng nhập sau một khoảng thời gian
    $_SESSION['redirect'] = true;
}


// Hiển thị thông báo nếu có
if (isset($_SESSION['account_message'])) {
    // Hiển thị thông báo bằng JavaScript trước khi chuyển hướng
    echo '<script type="text/javascript">';
    echo 'alert("' . $_SESSION['account_message'] . '");';
    echo 'window.location.href = "../../login.php";'; // Chuyển hướng sau khi hiển thị thông báo
    echo '</script>';

    // Xóa thông báo sau khi đã hiển thị
    unset($_SESSION['account_message']);

    // Ngăn không cho mã HTML phía dưới được thực thi
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Employee Dashboard</title>
    <link href="../../resorce/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <style>
    /* Default avatar size */
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Container for the avatar */
    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        transition: width 0.3s, height 0.3s;
    }

    /* Smaller avatar size when sidebar is collapsed */
    .mini-sidebar .avatar {
        width: 50px;
        height: 50px;
    }
    </style>
</head>

<body>
    <div id="main-wrapper">
        <div class="nav-header">
            <div class="brand-logo">
                <a>
                    <span class="brand-title">
                    </span>
                </a>
            </div>
        </div>
        <div class="header">
            <div class="header-content clearfix">
                <div class="nav-control">
                    <div class="hamburger">
                        <span class="toggle-icon"><i class="icon-menu"></i></span>
                    </div>
                </div>
                <div class="text-center">
                    <h2 class="pt-3">HRMS-2N </h2>
                </div>
            </div>
        </div>
        <div class="nk-sidebar">
            <div class="nk-nav-scroll">
                <div class="avatar mt-4" style="margin-left: auto; margin-right: auto;">
                    <img src="<?php echo $logo_url ?>" alt="Avatar">
                </div>
                <ul class="metismenu" id="menu">
                    <br> <br>
                    <li>
                        <a href="<?php echo $url; ?>">
                            <i class="icon-home menu-icon"></i><span class="nav-text">Trang chủ</span>
                        </a>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="fa fa-home menu-icon"></i><span class="nav-text">Dashboard</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo $url; ?>&table=chamCongQR"><i class="fa fa-qrcode menu-icon"></i>
                                    <span class="nav-text">Chấm công bằng mã
                                        QR</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $url; ?>&table=profile">
                            <i class="fa fa-user menu-icon"></i><span class="nav-text">Hồ sơ</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $url; ?>&table=setting">
                            <i class="fa fa-cog menu-icon"></i><span class="nav-text">Cài đặt</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="icon-logout menu-icon"></i><span class="nav-text"> Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content-body">
            <div class="container">
                <section class="content" id="main-content">
                    <div id="defaultContent" style="display: none;">
                        <br>
                        <h3>QUẢN LÝ NHÂN SỰ: NHÂN VIÊN </h3>
                        <p>Chào mừng đến với Bảng điều kiển Nhân viên</p>
                        <!-- Hiển thị thông báo mới nhất -->
                        <h2>Thông báo mới nhất</h2>
                        <ul class="list-group">
                            <?php
                            // Kết nối đến cơ sở dữ liệu
                            // Truy vấn để lấy thông báo mới nhất từ cơ sở dữ liệu
                            $query = "SELECT * FROM thong_bao ORDER BY ngay_tao DESC LIMIT 1";
                            $result = $db->query($query);

                            // Kiểm tra xem có thông báo nào không
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                echo "<li class='list-group-item'>";
                                echo "<h5 class='mb-1'>" . $row['tieu_de'] . "</h5>";
                                echo "<p class='mb-1'>" . $row['noi_dung'] . "</p>";
                                echo "<small class='text-muted'>" . $row['ngay_tao'] . "</small>";
                                echo "</li>";
                            } else {
                                echo "<li class='list-group-item'>Không có thông báo nào.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- Nội dung của từng danh mục sẽ được hiển thị ở đây -->
                    <div border="1" class="data-table" id="chamCongQR" style="display: none;">
                        <div id="chamCongQR">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'chamCongQR') {
                                include_once 'chamCongQR.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </div>
                    <table border="1" class="data-table" id="profile" style="display: none;">
                        <div id="profile">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'profile') {
                                include_once 'profile.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" id="setting" style="display: none;">
                        <div id="setting">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'setting') {
                                include_once 'setting.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>
                </section>
            </div>
        </div>



        <script>
        $(document).ready(function() {
            $('.toggle-icon').click(function() {
                $('body').toggleClass('mini-sidebar');
            });
        });
        </script>