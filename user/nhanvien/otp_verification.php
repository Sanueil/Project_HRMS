<?php
session_start();
include_once ('./controller/connect.php'); // Include your database connection class

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qrCode = $_POST['qr_code'];

    // Create an instance of the Database class
    $db = new Database();

    // Check if the database connection was successful
    $conn = $db->connect();
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Prepare the SQL statement
    $query = "SELECT soDienThoai FROM nhan_vien WHERE maQR = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $qrCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $phoneNumber = $row['soDienThoai'];

            // Generate OTP
            $otp = rand(100000, 999999);

            // Save OTP to session for verification later
            $_SESSION['otp'] = $otp;
            $_SESSION['qr_code'] = $qrCode;

            // Send OTP to phone number (use your preferred method to send SMS)
            // For example: sendSms($phoneNumber, "Your OTP is: $otp");

            // Display OTP verification form
            echo '<h4 class="text-center">Xác thực mã OTP</h4>
                  <form action="verify_otp.php" method="POST">
                      <div class="form-group">
                          <label for="otp">Nhập mã OTP:</label>
                          <input type="text" class="form-control" id="otp" name="otp" required>
                      </div>
                      <button type="submit" class="btn btn-dark form-control">Xác nhận</button>
                  </form>';

        } else {
            echo 'QR code không hợp lệ.';
        }

        // Close the statement
        $stmt->close();
    } else {
        // Output an error if the statement couldn't be prepared
        echo 'Statement preparation failed: ' . $db->getError();
    }

    // Close the database connection
    $db->close();
}
?>