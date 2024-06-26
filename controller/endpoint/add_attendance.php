<?php
session_start();
include ("../connect.php");

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['qr_code'])) {
        $username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : (isset($_SESSION['nhanvien_user']['username']) ? $_SESSION['nhanvien_user']['username'] : '');
        $qrCode = $_POST['qr_code'];

        // Phân tách chuỗi thành mảng các phần tử dựa trên dấu "-"
        $qrParts = explode("-", $qrCode);
        // Mảng để lưu trữ thông tin được trích xuất
        $info = [];

        // Lặp qua các phần tử
        foreach ($qrParts as $part) {
            // Tìm vị trí của dấu ":" trong phần tử
            $colonPosition = strpos($part, ':');

            // Kiểm tra xem dấu ":" có tồn tại trong phần tử hay không
            if ($colonPosition !== false) {
                // Phân tách phần tử thành key và value dựa trên dấu ":"
                $key = trim(substr($part, 0, $colonPosition));
                $value = trim(substr($part, $colonPosition + 1));

                // Lưu thông tin vào mảng kết quả
                $info[$key] = $value;
            }
        }

        $maNhanVien = $info['MÃ£ NhÃ¢n ViÃªn'];
        $hoTen = str_replace(' ', '_', $info['Há» TÃªn']);
        $qrFilename = $maNhanVien . "_" . $hoTen . ".png";
        function redirectWithError($errorMessage)
        {
            $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];

            $role = isset($_SESSION['quanly_user']['username']) ? 'quanly' : (isset($_SESSION['nhanvien_user']['username']) ? 'nhanvien' : '');
            $username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : (isset($_SESSION['nhanvien_user']['username']) ? $_SESSION['nhanvien_user']['username'] : '');
            $url = "home.php?user=" . $role . "&username=" . $username . "&table=chamCongQR";

            $redirectUrl = "../../user/" . $role . "/" . $url;
            echo "<script>alert('$errorMessage');</script>";
            echo "<script>window.location.href = '$redirectUrl';</script>";
            exit();
        }

        if ($maNhanVien !== $username) {
            redirectWithError('Mã nhân viên không khớp!');
        }

        $validQRCodesQuery = "SELECT maQR FROM nhan_vien";
        $validQRCodesResult = $db->query($validQRCodesQuery);

        $validQRCodes = [];
        while ($row = $validQRCodesResult->fetch_assoc()) {
            $validQRCodes[] = $row['maQR'];
        }

        if (!in_array($qrFilename, $validQRCodes)) {
            redirectWithError('Mã QR không hợp lệ!');
        }

        // Lấy mã nhân viên từ mã QR
        $selectEmployeeIdQuery = "SELECT maNhanVien FROM nhan_vien WHERE maQR = ?";
        $stmt = $conn->prepare($selectEmployeeIdQuery);
        $stmt->bind_param("s", $qrFilename);
        $stmt->execute();
        $employeeIdResult = $stmt->get_result()->fetch_assoc();

        if ($employeeIdResult) {
            $employeeID = $employeeIdResult['maNhanVien'];

            // Thời gian hiện tại
            $current_time = time();

            date_default_timezone_set('Asia/Ho_Chi_Minh');

            // Thời gian bắt đầu và kết thúc chấm công đúng giờ (08h00 - 08h10)
            $onTimeStart = strtotime(date("Y-m-d 08:00:00"));
            $onTimeEnd = strtotime(date("Y-m-d 08:10:00"));

            // Thời gian bắt đầu và kết thúc chấm công tan ca (21h06 - 21h09)
            $overtimeStart = strtotime(date("Y-m-d 17:00:00"));
            $overtimeEnd = strtotime(date("Y-m-d 17:10:00"));

            // Thời gian bắt đầu và kết thúc thời gian tăng ca (21h10 - 21h15)
            $nightShiftStart = strtotime(date("Y-m-d 18:30:00"));
            $nightShiftEnd = strtotime(date("Y-m-d 20:40:00"));

            // Trạng thái chấm công
            $attendanceStatus = "";

            // Kiểm tra xem nhân viên đã chấm công trong buổi đó chưa
            $attendanceCheckQuery = "SELECT * FROM cham_cong WHERE maNhanVien = ? AND DATE(thoiGianChamCong) = CURDATE() AND trangThai = ?";

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

            $stmt = $conn->prepare($attendanceCheckQuery);
            $stmt->bind_param("ss", $employeeID, $attendanceStatus);
            $stmt->execute();
            $attendanceCheckResult = $stmt->get_result()->fetch_assoc();

            if ($attendanceCheckResult) {
                // Lưu URL trước đó vào session
                $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];

                // Tạo URL đầy đủ với ID được truyền vào
                $role = isset($_SESSION['quanly_user']['username']) ? 'quanly' : (isset($_SESSION['nhanvien_user']['username']) ? 'nhanvien' : '');
                $username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : (isset($_SESSION['nhanvien_user']['username']) ? $_SESSION['nhanvien_user']['username'] : '');
                $url = "home.php?user=" . $role . "&username=" . $username . "&table=chamCongQR";

                $redirectUrl = "../../user/" . $role . "/" . $url;
                echo "<script>alert('Nhân viên đã chấm công trong buổi này!');</script>";
                echo "<script>window.location.href = '$redirectUrl';</script>";
                exit();
            }

            // Chấm công mới với thời gian vào và trạng thái
            $addAttendanceQuery = "INSERT INTO cham_cong (maNhanVien, thoiGianChamCong, trangThai) VALUES (?, NOW(), ?)";
            $stmt = $conn->prepare($addAttendanceQuery);
            $stmt->bind_param("ss", $employeeID, $attendanceStatus);

            if ($stmt->execute()) {
                // Lưu URL trước đó vào session
                $_SESSION['previous_url'] = $_SERVER['HTTP_REFERER'];

                // Tạo URL đầy đủ với ID được truyền vào
                $role = isset($_SESSION['quanly_user']['username']) ? 'quanly' : (isset($_SESSION['nhanvien_user']['username']) ? 'nhanvien' : '');
                $username = isset($_SESSION['quanly_user']['username']) ? $_SESSION['quanly_user']['username'] : (isset($_SESSION['nhanvien_user']['username']) ? $_SESSION['nhanvien_user']['username'] : '');
                $url = "home.php?user=" . $role . "&username=" . $username . "&table=chamCongQR";

                $redirectUrl = "../../user/" . $role . "/" . $url;
                header("Location: $redirectUrl");
                exit();
            } else {
                echo "Error:" . $db->getError();
            }
        } else {
            echo "Không tìm thấy nhân viên có mã QR tương ứng.";
            exit();
        }
    } else {
        echo "Mã QR không được cung cấp.";
        exit();
    }

    $stmt->close();
    $db->close();
}
?>