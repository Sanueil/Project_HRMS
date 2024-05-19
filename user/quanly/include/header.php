<?php
session_start();
include_once ('../../controller/connect.php');

// Khởi tạo kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['quanly_user'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: ../../login.php");
    exit;
}

// Lưu URL trang hiện tại vào session trước khi chuyển hướng
$_SESSION['current_url'] = $_SERVER['REQUEST_URI'];

// Lấy username của người dùng từ session
$username = $_SESSION['quanly_user']['username'];

// Nếu người dùng là quản lý, tạo URL đầy đủ với username được truyền vào
$url = "home.php?user=quanly&username=" . urlencode($username);

// Sử dụng Prepared Statement để bảo vệ câu truy vấn khỏi SQL Injection
$query = "SELECT hinhAnh FROM nhan_vien WHERE maNhanVien = '$username'";
$result_anh = mysqli_query($db, $query);

if ($result_anh) {
    if (mysqli_num_rows($result_anh) > 0) {
        $row = mysqli_fetch_assoc($result_anh);
        $logo_url = '../' . $row['hinhAnh'];
    } else {
        // Xử lý khi không có kết quả trả về, ví dụ: sử dụng ảnh mặc định
        $logo_url = "../../controller/uploads/thiên hà.jpg";
    }
} else {
    // Xử lý lỗi nếu truy vấn không thành công
    echo "Lỗi: " . mysqli_error($db);
}
?>
<!DOCTYPE html>
<html lang="en">
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

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Manager Dashboard</title>
    <link href="../../resorce/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
</head>
<style>
.avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
}
</style>

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
                    <h2 class="pt-3"> Quản lý nhân sự </h2>
                </div>

            </div>
        </div>
        <div class="nk-sidebar">
            <div class="nk-nav-scroll">
                <div class="avatar mt-4 " style="margin-left: auto; margin-right: auto;">
                    <img src="<?php echo $logo_url ?>" alt="Avatar" class="avatar">
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
                            <li><a href="<?php echo $url; ?>&table=department"><i
                                        class="fa fa-sitemap menu-icon"></i><span class="nav-text">Quản lý các phòng
                                        ban</span></a></li>
                            <li><a href="<?php echo $url; ?>&table=chamCongQR"><i class="fa fa-qrcode menu-icon"></i>
                                    <span class="nav-text">Chấm công bằng mã
                                        QR</span></a></li>
                            <li><a href="<?php echo $url; ?>&table=pheDuyet"><i
                                        class="fa fa-check-circle menu-icon"></i><span class="nav-text">Phê duyệt bảng
                                        chấm công nhân
                                        viên</span></a></li>
                            <li><a href="<?php echo $url; ?>&table=luong"><i class="fa fa-money menu-icon"></i><span
                                        class="nav-text">Quản lý
                                        lương</span></a></li>
                            <li><a href="<?php echo $url; ?>&table=baoCao"><i
                                        class="fa fa-bar-chart menu-icon"></i><span class="nav-text">Báo cáo và thống
                                        kê</span></a></li>
                            <li><a href="<?php echo $url; ?>&table=danhGia"><i class="fa fa-star menu-icon"></i><span
                                        class="nav-text">Đánh giá nhân
                                        viên</span></a></li>
                            <li><a href="#"><i class="fa fa-users menu-icon"></i><span class="nav-text">Nhập/Xuất danh
                                        sách nhân viên</span></a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="<?php echo $url; ?>&table=profile">
                            <i class="fa fa-user menu-icon"></i><span class="nav-text">Hồ sơ</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $url; ?>&table=listqr">
                            <i class="fa fa-tasks menu-icon"></i><span class="nav-text">Danh sách QR</span>
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
                        <h3>QUẢN LÝ NHÂN SỰ: QUẢN LÝ </h3>
                        <p>Chào mừng đến với Bảng điều kiển Quản lý</p>
                        <!-- Hiển thị thông báo mới nhất -->
                        <h2>Thông báo mới nhất</h2>
                        <ul class="list-group">
                            <?php
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
                    <table border="1" class="data-table" id="department" style="display: none;">
                        <div id="department">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'department') {
                                include_once 'phongBan.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>
                    <div border="1" class="data-table" id="chamCongQR" style="display: none;">
                        <div id="chamCongQR">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'chamCongQR') {
                                include_once 'chamCongQR.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </div>
                    <table border="1" class="data-table" id="pheDuyet" style="display: none;">
                        <div id="pheDuyet">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'pheDuyet') {
                                include_once 'pheDuyet.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" id="luong" style="display: none;">
                        <div id="luong">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'luong') {
                                include_once 'luong.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>

                    <table border="1" class="data-table" id="baoCao" style="display: none;">
                        <div id="baoCao">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'baoCao') {
                                include_once 'baoCao.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" id="danhGia" style="display: none;">
                        <div id="danhGia">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'danhGia') {
                                include_once 'danhGia.php'; // Bao gồm biểu mẫu 
                            } ?>
                        </div>
                    </table>
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