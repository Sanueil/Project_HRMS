<?php
// Kết nối đến cơ sở dữ liệu
include_once('connect.php');
$dbs = new Database();
$db = $dbs->connect();

// Thiết lập tiêu đề trả về JSON
header('Content-Type: application/json');

// Kiểm tra xem tham số 'date' có được cung cấp không
if (!isset($_GET['date']) || empty($_GET['date'])) {
    echo json_encode(['error' => 'Tham số ngày là bắt buộc.']);
    exit;
}

$date = $_GET['date'];

// Hàm để lấy dữ liệu thời gian làm việc của nhân viên từ cơ sở dữ liệu với lọc theo ngày
function getFilteredAggregatedWorkTimeData($db, $date)
{
    // Truy vấn cơ sở dữ liệu để đếm số lần chấm công theo ngày với lọc
    $query = "SELECT DATE(thoiGianChamCong) as date, COUNT(*) as numCheckIns FROM cham_cong WHERE DATE(thoiGianChamCong) = ? GROUP BY DATE(thoiGianChamCong)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Mảng để lưu trữ dữ liệu thời gian làm
    $dates = array();
    $numCheckIns = array();

    // Kiểm tra kết quả truy vấn và lưu dữ liệu vào mảng
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row['date'];
            $numCheckIns[] = $row['numCheckIns'];
        }
    }

    return array('dates' => $dates, 'numCheckIns' => $numCheckIns);
}

// Lấy dữ liệu thời gian làm việc đã được lọc và trả về dưới dạng JSON
$workTimeData = getFilteredAggregatedWorkTimeData($db, $date);
echo json_encode($workTimeData);
?>