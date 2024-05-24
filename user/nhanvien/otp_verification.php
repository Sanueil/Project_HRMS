<!-- otp_verification.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP</title>
</head>

<body>
    <h2>Nhập số điện thoại để nhận mã OTP</h2>
    <form method="POST" action="verify_otp.php">
        <label for="phone_number">Số điện thoại:</label>
        <input type="text" id="phone_number" name="phone_number" required>
        <input type="hidden" name="qr_code" value="<?php echo $_POST['qr_code']; ?>">
        <button type="submit">Gửi OTP</button>
    </form>
</body>

</html>