<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <style>
    .send-email-button {
        background-color: #4CAF50;
        /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        transition: background-color 0.3s ease;
    }

    .send-email-button:hover {
        background-color: #45a049;
    }

    .employee-details {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .employee-details p {
        margin: 10px 0;
    }

    .employee-details img {
        display: block;
        margin: 10px auto;
    }
    </style>
</head>

<body>

    <div class="employee-details">
        <?php
    include_once ('../controller/connect.php');
    require "../controller/PHPMailer-6.2.0/src/PHPMailer.php";
    require "../controller/PHPMailer-6.2.0/src/Exception.php";
    require "../controller/PHPMailer-6.2.0/src/OAuth.php";
    require "../controller/PHPMailer-6.2.0/src/POP3.php";
    require "../controller/PHPMailer-6.2.0/src/SMTP.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Sử dụng lớp Database để kết nối đến cơ sở dữ liệu
    $dbs = new Database();
    $db = $dbs->connect();

    // Hàm hiển thị thông tin chi tiết của một nhân viên
    function displayEmployeeDetail($employee_id, $db)
    {
        // Kiểm tra xem có mã nhân viên được truyền qua không
        if (isset($employee_id)) {
            // Truy vấn thông tin chi tiết của nhân viên
            $sql = "SELECT * FROM nhan_vien WHERE maNhanVien = '$employee_id'";
            $result = mysqli_query($db, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $hoTenNhanVien = $row['hoTenNhanVien'];
                $diaChi = $row['diaChi'];
                $soDienThoai = $row['soDienThoai'];
                $email = $row['email'];
                $chucVu = $row['chucVu'];
                $ngaySinh = $row['ngaySinh'];
                $gioiTinh = $row['gioiTinh'];
                $hinhAnh = $row['hinhAnh'];
                $maNhanVien = $row['maNhanVien'];

                $qrFileName = $maNhanVien . '_' . str_replace(' ', '_', $hoTenNhanVien) . '.png';
                // Tạo chuỗi chứa đầy đủ thông tin của nhân viên
                $employee_info = "Mã Nhân Viên: $maNhanVien - ";
                $employee_info .= "Họ Tên: $hoTenNhanVien - ";
                $employee_info .= "Địa Chỉ: $diaChi - ";
                $employee_info .= "Số Điện Thoại: $soDienThoai - ";
                $employee_info .= "Email: $email - ";
                $employee_info .= "Chức Vụ: $chucVu - ";
                $employee_info .= "Ngày Sinh: $ngaySinh - ";
                $employee_info .= "Giới Tính: $gioiTinh\n";
                // Mã hóa thông tin nhân viên để truyền vào URL của ảnh mã QR
                $qr_code_data = urlencode($employee_info);

                // Hiển thị thông tin chi tiết của nhân viên
                echo "<p><strong>Mã Nhân Viên:</strong> $maNhanVien</p>";
                echo "<p><strong>Họ Tên:</strong> $hoTenNhanVien</p>";
                echo "<p><strong>Địa Chỉ:</strong> $diaChi</p>";
                echo "<p><strong>Số Điện Thoại:</strong> $soDienThoai</p>";
                echo "<p><strong>Email:</strong> $email</p>";
                echo "<p><strong>Chức Vụ:</strong> $chucVu</p>";
                echo "<p><strong>Ngày Sinh:</strong> $ngaySinh</p>";
                echo "<p><strong>Giới Tính:</strong> $gioiTinh</p>";
                $query = "UPDATE nhan_vien SET maQR = '$qrFileName' WHERE maNhanVien = '$maNhanVien'";
                $validQRCodesResult = $db->query($query);
                // Hiển thị ảnh mã QR chứa đầy đủ thông tin của nhân viên
                echo "<p><strong>Mã QR:</strong></p>";
                echo "<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$qr_code_data' alt='QR Code'>";

                // Form để gửi email
                echo '<form method="post">';
                echo '<input type="hidden" name="maNhanVien" value="' . $maNhanVien . '">';
                echo '<input type="hidden" name="recipient_email" value="' . $email . '">';
                echo '<input type="hidden" name="employee_info" value="' . htmlspecialchars($employee_info) . '">';
                echo '<button type="submit" name="send_email" class="send-email-button">Gửi thông tin qua email</button>';
                echo '</form>';
            } else {
                echo "<p class='text-danger'>Không tìm thấy người dùng.</p>";
            }
        } else {
            echo "<p class='text-danger'>Không có mã nhân viên được cung cấp.</p>";
        }
    }

    // Xử lý gửi email nếu form được submit
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_email'])) {
        // Thu thập dữ liệu từ form
        $maNhanVien = $_POST['maNhanVien'];
        $recipient_email = $_POST['recipient_email'];
        $employee_info = $_POST['employee_info'];

        // Tạo một PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Cài đặt thông tin SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Địa chỉ SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'nguyenlp02@gmail.com'; // Email của bạn
            $mail->Password = 'wkta vkjh mbfh vlut'; // Mật khẩu email của bạn
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPDebug = 2; // Bật debug output

            // Cài đặt thông tin email
            $mail->setFrom('nguyenlp02@gmail.com', 'QLNS-2N'); // Địa chỉ email người gửi
            $mail->addAddress($recipient_email); // Địa chỉ email người nhận

            // Định dạng email
            $mail->isHTML(true);
            $mail->Subject = 'Thông tin nhân viên'; // Tiêu đề email
            $mail->Body = nl2br($employee_info); // Nội dung email

            // Gửi email
            $mail->send();
            $_SESSION['message'] = 'Email đã được gửi thành công.';
        } catch (Exception $e) {
            $_SESSION['message'] = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
        }

        // Hiển thị thông tin phản hồi
        echo "<p>{$_SESSION['message']}</p>";
    }

    // Sử dụng hàm để hiển thị thông tin chi tiết của nhân viên
    if (isset($_POST['maNhanVien'])) {
        $id = $_POST['maNhanVien'];
        displayEmployeeDetail($id, $db);
    }
    ?>
    </div>

</body>

</html>