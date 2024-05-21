<?php
session_start();
// Kết nối đến cơ sở dữ liệu
include_once ('connect.php');

$message = '';

$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra biểu mẫu đã được gửi chưa
if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ biểu mẫu
    $maNhanVien = $_POST['maNhanVien'];
    $hoTenNhanVien = $_POST['hoTenNhanVien'];
    $diaChi = $_POST['diaChi'];
    $soDienThoai = $_POST['soDienThoai'];
    $email = $_POST['email'];
    $chucVu = $_POST['chucVu'];
    $ngaySinh = $_POST['ngaySinh'];
    $gioiTinh = $_POST['gioiTinh'];

    // Kiểm tra nếu hình ảnh đã được tải lên
    if (isset($_FILES["hinhAnh"]) && $_FILES["hinhAnh"]["error"] == 0) {
        // Xử lý tải lên hình ảnh
        $target_dir = "../controller/uploads/";
        $target_file = $target_dir . basename($_FILES["hinhAnh"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra và tạo thư mục uploads nếu không tồn tại
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $_SESSION['message'] = "Không thể tạo thư mục để lưu trữ hình ảnh.";
                header("Location: ../admin/home.php?user=admin&table=addUser");
                exit();
            }
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
            header("Location: ../admin/home.php?user=admin&table=addUser");
            exit();
        } else {
            // Nếu mọi thứ đều ổn, thử tải lên tệp
            if (move_uploaded_file($_FILES["hinhAnh"]["tmp_name"], $target_file)) {
                // Đường dẫn tương đối để lưu vào cơ sở dữ liệu
                $relative_path = "uploads/" . basename($_FILES["hinhAnh"]["name"]);
            } else {
                $_SESSION['message'] = "Xảy ra lỗi khi tải lên tệp.";
                header("Location: ../admin/home.php?user=admin&table=addUser");
                exit();
            }
        }
    } else {
        if ($_FILES["hinhAnh"]["error"] != 0) {
            $_SESSION['message'] = "Lỗi khi tải lên tệp: " . $_FILES["hinhAnh"]["error"];
            header("Location: ../admin/home.php?user=admin&table=addUser");
            exit();
        }
    }

    // Tạo tên file QR
    $qrFileName = $maNhanVien . '_' . str_replace(' ', '_', $hoTenNhanVien) . '.png';
    // Kiểm tra xem maNhanVien đã tồn tại trong cơ sở dữ liệu chưa
    $checkQuery = "SELECT * FROM nhan_vien WHERE maNhanVien = '$maNhanVien'";
    $result = mysqli_query($db, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        // Nếu mã nhân viên đã tồn tại, thông báo lỗi và quay trở lại trang thêm người dùng
        $_SESSION['message'] = "Lỗi: Mã nhân viên '$maNhanVien' đã tồn tại trong cơ sở dữ liệu.";
        header("Location: ../admin/home.php?user=admin&table=addUser");
        exit();
    } else {
        // Lưu tên file QR vào cơ sở dữ liệu
        $query = "INSERT INTO nhan_vien (maNhanVien, hoTenNhanVien, diaChi, soDienThoai, email, chucVu, ngaySinh, gioiTinh, maQR, hinhAnh)
            VALUES ('$maNhanVien', '$hoTenNhanVien', '$diaChi', '$soDienThoai', '$email', '$chucVu', '$ngaySinh', '$gioiTinh', '$qrFileName', '$relative_path')";

        if (mysqli_query($db, $query)) {
            $_SESSION['message'] = "Thêm người dùng thành công.";
            // Tạo mã QR và lưu file vào thư mục
            $qrData = "Mã Nhân Viên: $maNhanVien\nHọ Tên: $hoTenNhanVien\nĐịa Chỉ: $diaChi\nSố Điện Thoại: $soDienThoai\nGiới Tính: $gioiTinh\nEmail: $email\nChức Vụ: $chucVu\nNgày Sinh: $ngaySinh";
            $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
            file_put_contents("../controller/uploads/$qrFileName", file_get_contents($apiUrl));

            header("Location: ../admin/home.php?user=admin&table=addUser");
            exit();
        } else {
            $_SESSION['message'] = "Lỗi: Không thể thêm người dùng.";
            header("Location: ../admin/home.php?user=admin&table=addUser");
            exit();
        }
    }
} else {
    $_SESSION['message'] = "Lỗi: Biểu mẫu không được gửi.";
    header("Location: ../admin/home.php?user=admin&table=addUser");
    exit();
}
?>