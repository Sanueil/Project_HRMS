<?php
session_start();
// Kết nối đến cơ sở dữ liệu
include_once('connect.php');

$message = '';

$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem biểu mẫu đã được gửi chưa
if(isset($_POST['submit'])) {
    // Lấy dữ liệu từ biểu mẫu
    $maNhanVien = $_POST['maNhanVien'];
    $hoTenNhanVien = $_POST['hoTenNhanVien'];
    $diaChi = $_POST['diaChi'];
    $soDienThoai = $_POST['soDienThoai'];
    $email = $_POST['email'];
    $chucVu = $_POST['chucVu'];
    $ngaySinh = $_POST['ngaySinh'];
    $gioiTinh = $_POST['gioiTinh'];

    // Xử lý tải lên hình ảnh
    $target_dir = "../controller/uploads/";
    $target_file = $target_dir . basename($_FILES["hinhAnh"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra và tạo thư mục uploads nếu không tồn tại
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Kiểm tra kích thước tệp
    if ($_FILES["hinhAnh"]["size"] > 500000) {
        $_SESSION['message'] = "Xin lỗi, tệp hình ảnh quá lớn.";
        $uploadOk = 0;
    }

    // Cho phép các định dạng hình ảnh nhất định
    $allowed_formats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_formats)) {
        $_SESSION['message'] = "Xin lỗi, chỉ các tệp JPG, JPEG, PNG & GIF được chấp nhận.";
        $uploadOk = 0;
    }

    // Kiểm tra xem $uploadOk có bị lỗi không
    if ($uploadOk == 0) {
        $_SESSION['message'] = "Xin lỗi, tệp hình ảnh của bạn không được tải lên.";
    } else {
        // Nếu mọi thứ đều ổn, thử tải lên tệp
        if (move_uploaded_file($_FILES["hinhAnh"]["tmp_name"], $target_file)) {
            $_SESSION['message'] = "Tệp ". htmlspecialchars(basename($_FILES["hinhAnh"]["name"])). " đã được tải lên.";

            // Kiểm tra xem mã nhân viên đã tồn tại chưa
            $query_check_duplicate = "SELECT COUNT(*) AS total FROM nhan_vien WHERE maNhanVien = '$maNhanVien'";
            $result_check_duplicate = mysqli_query($db, $query_check_duplicate);
            $row_check_duplicate = mysqli_fetch_assoc($result_check_duplicate);
            $total = $row_check_duplicate['total'];

            if ($total > 0) {
                $_SESSION['message'] = "Mã nhân viên đã tồn tại trong cơ sở dữ liệu.";
            } else {
                // Thêm dữ liệu vào cơ sở dữ liệu
                $query = "INSERT INTO nhan_vien (maNhanVien, hoTenNhanVien, diaChi, soDienThoai, email, chucVu, ngaySinh, hinhAnh, gioiTinh)
                        VALUES ('$maNhanVien', '$hoTenNhanVien', '$diaChi', '$soDienThoai', '$email', '$chucVu', '$ngaySinh', '$target_file', '$gioiTinh')";
                if(mysqli_query($db, $query)) {
                    // Hiển thị thông báo thành công
                    $_SESSION['message'] = "Thêm người dùng thành công.";
                    header("Location: ../admin/home.php?user=admin&table=addUser");
                    exit();
                } else {
                    $_SESSION['message'] = "Lỗi: Không thể thêm người dùng.";
                }
            }
        } else {
            $_SESSION['message'] = "Xảy ra lỗi khi tải lên tệp.";
        }
    }
}
header("Location: ../admin/home.php?user=admin&table=addUser");
exit();