<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['admin_user'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: ../admin/login.php");
    exit;
}
include_once "../controller/connect.php";
$p = new Database();
$db = $p->connect();
// Lưu URL trang hiện tại vào session trước khi chuyển hướng
$_SESSION['current_url'] = $_SERVER['REQUEST_URI'];

$table = isset($_SESSION['admin_user']['table']);

// Tạo URL đầy đủ với username được truyền vào
$url = "home.php??user=admin&table=" . urlencode($table);
?>
<!DOCTYPE html>
<html lang="en">
<style>
.dashboard {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    /* Chia cột thành 3 phần bằng nhau */
    grid-gap: 20px;
    /* Khoảng cách giữa các cột */
    height: 100vh;
}

.left-column,
.central-section,
.right-section {
    background-color: #f2f2f2;
    padding: 20px;
    border-radius: 5px;
    width: 250px;
    height: 150px;
    background: white;
    margin: 20px 10px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

@media screen and (max-width: 768px) {
    .dashboard {
        grid-template-columns: 1fr;
        /* Khi độ rộng màn hình nhỏ hơn 768px, chỉ hiển thị một cột */
    }
}

h3 {
    color: #333;
    /* Màu chữ */
    font-size: 24px;
    /* Kích thước chữ */
    margin-bottom: 10px;
    /* Khoảng cách dưới */
}

/* CSS cho đoạn văn */
p {
    color: #666;
    /* Màu chữ */
    font-size: 16px;
    /* Kích thước chữ */
    line-height: 1.5;
    /* Độ cao dòng */
}
</style>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="../resorce/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
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
                    <h2 class="pt-3"> Admin Dashboard </h2>
                </div>

            </div>
        </div>
        <div class="nk-sidebar">
            <div class="nk-nav-scroll">
                <ul class="metismenu" id="menu">
                    <br> <br>
                    <li>
                        <a href="../admin/home.php?user=admin">
                            <i class="icon-home menu-icon"></i><span class="nav-text">Trang chủ</span>
                        </a>
                    </li>


                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="fa fa-address-card-o menu-icon"></i><span class="nav-text">Người dùng</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="../admin/home.php?user=admin&table=addUser"> <i
                                        class="icon-plus menu-icon"></i><span class="nav-text">Thêm người
                                        dùng</span></a></li>
                            <li><a href="../admin/home.php?user=admin&table=dsUsers"> <i
                                        class="fa fa-tasks menu-icon"></i><span class="nav-text">Danh sách người
                                        dùng</span></a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="../admin/home.php?user=admin&table=department">
                            <i class="fa fa-building"></i><span class="nav-text">Danh mục phòng ban</span>
                        </a>
                    </li>

                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="fa fa-address-card-o menu-icon"></i><span class="nav-text">Tài khoản</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="../admin/home.php?user=admin&table=add"> <i
                                        class="icon-plus menu-icon"></i><span class="nav-text">Thêm tài khoản</span></a>
                            </li>
                            <li><a href="../admin/home.php?user=admin&table=users"> <i
                                        class="fa fa-tasks menu-icon"></i><span class="nav-text">Danh sách tài
                                        khoản</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="../admin/home.php?user=admin&table=listqr">
                            <i class="fa fa-tasks menu-icon"></i><span class="nav-text">Danh sách QR</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/home.php?user=admin&table=thongbao">
                            <i class="fa fa-bell"></i><span class="nav-text">Quản lý thông báo</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/home.php?user=admin&table=setting">
                            <i class="fa fa-cog menu-icon"></i><span class="nav-text">Cài đặt</span>
                        </a>
                    </li>
                    <li>
                        <a href="../admin/logout.php">
                            <i class="icon-logout menu-icon"></i><span class="nav-text"> Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content-body">
            <div class="container">
                <section class="content" id="main-content">
                    <br>
                    <div id="defaultContent" style="display: none;">
                        <h3>Chào mừng bạn đến với Bảng điều khiển Quản trị viên!</h3>
                        <div class="dashboard">
                            <div class="central-section">
                                <!-- Content for the central section goes here -->
                                <h3>Tổng số nhân viên</h3><br>
                                <p>
                                <h1><?php
                                // Get the total number of employees
                                $sql = "SELECT COUNT(*) AS total_employees FROM nhan_vien";
                                $result = $p->query($sql);
                                $row = $result->fetch_assoc();
                                $total_employees = $row['total_employees'];
                                echo $total_employees;
                                ?></h1>
                                </p>
                            </div>
                            <div class="left-column">
                                <!-- Content for the left column goes here -->
                                <h3>Tổng số phòng ban</h3>
                                <h1><?php 
                                // Get the total number of departments
                                $sql1 = "SELECT COUNT(*) AS total_departments FROM phong_ban";
                                $result1 = $p->query($sql1);
                                $row1 = $result1->fetch_assoc();
                                $total_departments = $row1['total_departments'];
                                echo $total_departments;
                                ?> </h1>
                            </div>
                            <div class="left-column">
                                <!-- Content for the left column goes here -->
                                <h3>Tổng số tài khoản</h3>
                                <h1><?php 
                                // Get the total number of departments
                                $sql2 = "SELECT COUNT(*) AS total_accounts FROM tai_khoan";
                                $result2 = $p->query($sql2);
                                $row2 = $result2->fetch_assoc();
                                $total_accounts = $row2['total_accounts'];
                                echo $total_accounts;
                                ?> </h1>
                            </div>
                        </div>
                    </div>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="themNguoiDung">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'addUser') {
                                include_once 'addNguoiDung.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="xemDS">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'dsUsers') {
                                include_once 'nguoiDung.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="addDepartment">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'department') {
                                include_once 'loaiPhongBan.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="addAccountFormContainer">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'add') {
                                include_once 'themtaikhoan.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="showAccounts">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'users') {
                                include_once 'dsTK.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="qlthongbao">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'thongbao') {
                                include_once 'thongbao.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="listqr">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'listqr') {
                                include_once 'masterlist.php';
                            } ?>
                        </div>
                    </table>
                    <table border="1" class="data-table" style="display: none;">
                        <div id="caiDat">
                            <?php
                            if (isset($_GET['table']) && $_GET['table'] === 'setting') {
                                include_once 'setting.php';
                            } ?>
                        </div>
                    </table>
                </section>
            </div>
        </div>

        <!-- row -->