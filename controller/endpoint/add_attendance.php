<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qr_code'])) {
        $qrCode = $_POST['qr_code'];

        // Lấy danh sách các mã QR hợp lệ từ cơ sở dữ liệu
        $validQRCodesQuery = $conn->prepare("SELECT maQR FROM nhan_vien");
        $validQRCodesQuery->execute();
        $validQRCodesResult = $validQRCodesQuery->fetchAll(PDO::FETCH_COLUMN);

        // Kiểm tra xem mã QR được cung cấp có trong danh sách các mã QR hợp lệ không
        if (!in_array($qrCode, $validQRCodesResult)) {
            echo "<script>alert('Mã QR không hợp lệ!');</script>";
            echo "<script>window.location.href = 'http://localhost/qr-code-attendance-system/index.php';</script>";
            exit(); // Dừng thực thi script
        }

        // Lấy mã nhân viên từ mã QR
        $selectEmployeeId = $conn->prepare("SELECT idNhanVien FROM nhan_vien WHERE maQR = :qrCode");
        $selectEmployeeId->bindParam(":qrCode", $qrCode, PDO::PARAM_STR);
        $selectEmployeeId->execute();
        $employeeIdResult = $selectEmployeeId->fetch();

        if ($employeeIdResult) {
            $employeeID = $employeeIdResult['idNhanVien'];

            // Thời gian hiện tại
            $current_time = time();

            date_default_timezone_set('Asia/Ho_Chi_Minh');

            // Thời gian bắt đầu và kết thúc chấm công đúng giờ (08h00 - 08h10)
            $onTimeStart = strtotime(date("Y-m-d 08:00:00"));
            $onTimeEnd = strtotime(date("Y-m-d 08:10:00"));

            // Thời gian bắt đầu và kết thúc chấm công tan ca (17h00 - 17h10)
            $overtimeStart = strtotime(date("Y-m-d 17:00:00"));
            $overtimeEnd = strtotime(date("Y-m-d 17:10:00"));

            // Thời gian bắt đầu và kết thúc thời gian tăng ca (vd: 18h00 - 23h10)
            $nightShiftStart = strtotime(date("Y-m-d 18:00:00"));
            $nightShiftEnd = strtotime(date("Y-m-d 23:10:00"));

            // Trạng thái chấm công
            $attendanceStatus = "";

            // Kiểm tra xem nhân viên đã chấm công trong buổi đó chưa
            $attendanceCheckQuery = $conn->prepare("SELECT * FROM cham_cong WHERE idNhanVien = :idNhanVien AND DATE(thoiGianChamCong) = CURDATE()");
            $attendanceCheckQuery->bindParam(":idNhanVien", $employeeID, PDO::PARAM_STR);
            $attendanceCheckQuery->execute();
            $attendanceCheckResult = $attendanceCheckQuery->fetch();

            if ($attendanceCheckResult) {
                echo "<script>alert('Nhân viên đã chấm công trong buổi này!');</script>";
                echo "<script>window.location.href = 'http://localhost/qr-code-attendance-system/index.php';</script>";
                exit();
            }

            // Sửa đổi điều kiện để ghi nhận "Đúng giờ", "Tăng ca" hoặc "Vào trễ", "Quá giờ"
            if ($current_time >= $onTimeStart && $current_time < $onTimeEnd) { 
                $attendanceStatus = "Đúng giờ";
            } elseif ($current_time >= $overtimeStart && $current_time <= $overtimeEnd) {
                $attendanceStatus = "Tan ca";
            } elseif ($current_time >= $nightShiftStart && $current_time <= $nightShiftEnd) {
                $attendanceStatus = "Tăng ca";
            } else {
                if ($current_time > $overtimeEnd) {
                    $attendanceStatus = "Quá giờ";
                } else {
                    $attendanceStatus = "Vào trễ";
                }
            }

            // Chấm công mới với thời gian vào và trạng thái
            try {
                $addAttendance = $conn->prepare("INSERT INTO cham_cong (idNhanVien, thoiGianChamCong, trangThai) VALUES (:idNhanVien, NOW(), :trangThai)");
                $addAttendance->bindParam(":idNhanVien", $employeeID, PDO::PARAM_STR);
                $addAttendance->bindParam(":trangThai", $attendanceStatus, PDO::PARAM_STR);
                $addAttendance->execute();

                header("Location: http://localhost/qr-code-attendance-system/index.php");
                exit();
            } catch (PDOException $e) {
                echo "Error:" . $e->getMessage();
            }
        } else {
            echo "Không tìm thấy nhân viên có mã QR tương ứng.";
            exit();
        }
    } else {
        echo "Mã QR không được cung cấp.";
        exit();
    }
}
?>