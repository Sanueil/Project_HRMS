<?php
// Kết nối đến cơ sở dữ liệu
include_once('connect.php');
$dbs = new Database();
$db = $dbs->connect();

// Thiết lập tiêu đề trả về JSON
header('Content-Type: application/json');

// Kiểm tra kết nối cơ sở dữ liệu
if ($db->connect_error) {
    echo json_encode(['error' => 'Kết nối thất bại: ' . $db->connect_error]);
    exit;
}

// Hàm để lấy dữ liệu tổng hợp số lần chấm công của nhân viên từ cơ sở dữ liệu
function getAggregatedWorkTimeData($db)
{
    // Truy vấn cơ sở dữ liệu để tổng hợp số lần chấm công của mỗi nhân viên
    $query = "SELECT maNhanVien, COUNT(*) as soLanChamCong FROM cham_cong GROUP BY maNhanVien";
    $result = $db->query($query);

    // Mảng để lưu trữ dữ liệu tổng hợp
    $aggregatedData = array(
        'maNhanVien' => array(),
        'soLanChamCong' => array()
    );

    // Kiểm tra kết quả truy vấn và lưu dữ liệu vào mảng
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $aggregatedData['maNhanVien'][] = $row['maNhanVien'];
            $aggregatedData['soLanChamCong'][] = $row['soLanChamCong'];
        }
    }

    return $aggregatedData;
}

// Lấy dữ liệu tổng hợp và trả về dưới dạng JSON
$aggregatedData = getAggregatedWorkTimeData($db);
echo json_encode($aggregatedData);

?>