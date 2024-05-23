<?php
session_start();

require "../controller/PHPMailer-6.2.0/src/PHPMailer.php";
require "../controller/PHPMailer-6.2.0/src/Exception.php";
require "../controller/PHPMailer-6.2.0/src/OAuth.php";
require "../controller/PHPMailer-6.2.0/src/POP3.php";
require "../controller/PHPMailer-6.2.0/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kiểm tra xem form đã được submit chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Thu thập dữ liệu từ form
    $maNhanVien = $_POST['maNhanVien'];
    $recipient_email = $_POST['recipient_email'];
    $employee_info = $_POST['employee_info'];

    // Tạo một PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Cài đặt        
        // Cài đặt thông tin SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Địa chỉ SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'nguyenlp02@gmail.com'; // Email của bạn
        $mail->Password = 'wkta vkjh mbfh vlut'; // Mật khẩu email của bạn
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Cài đặt thông tin email
        $mail->setFrom('nguyenlp02@gmail.com', 'QLNS-2N'); // Địa chỉ email người gửi
        $mail->addAddress($recipient_email); // Địa chỉ email người nhận

        // Định dạng email
        $mail->isHTML(true);
        $mail->Subject = 'Thông tin nhân viên'; // Tiêu đề email
        $mail->Body = nl2br($employee_info); // Nội dung email, chuyển đổi dòng mới thành <br>

        // Gửi email
        $mail->send();
        $_SESSION['message'] = 'Email đã được gửi thành công.';
    } catch (Exception $e) {
        $_SESSION['message'] = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
    }

    // Chuyển hướng trở lại trang chi tiết nhân viên
    header("Location: employee_detail.php?maNhanVien=$maNhanVien");
    exit;
}
?>