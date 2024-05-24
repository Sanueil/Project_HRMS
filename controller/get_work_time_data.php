<?php
// Kết nối đến cơ sở dữ liệu
include_once ('connect.php');
$dbs = new Database();
$db = $dbs->connect();

// Hàm để lấy dữ liệu thời gian làm của nhân viên từ cơ sở dữ liệu
function getWorkTimeData($db)
{
    // Truy vấn cơ sở dữ liệu để lấy dữ liệu thời gian làm
    $query = "SELECT * FROM cham_cong";
    $result = mysqli_query($db, $query);

    // Mảng để lưu trữ dữ liệu thời gian làm
    $workTimeData = array();

    // Kiểm tra kết quả truy vấn và lưu dữ liệu vào mảng
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $workTimeData[] = array(
                'maChamCong' => $row['maChamCong'],
                'maNhanVien' => $row['maNhanVien'],
                'thoiGianChamCong' => $row['thoiGianChamCong'],
                'trangThai' => $row['trangThai'],
                // 'maQR' => $row['maQR']
            );
        }
    }

    return $workTimeData;
}
// Lấy dữ liệu thời gian làm và trả về dưới dạng JSON
$workTimeData = getWorkTimeData($db);
echo json_encode($workTimeData);
?>