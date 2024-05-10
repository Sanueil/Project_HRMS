<?php
// Khởi tạo một đối tượng Database
$dbs = new Database();
$db = $dbs->connect();
// Số lượng bản ghi trên mỗi trang
$records_per_page = 10;

// Lấy số trang hiện tại từ tham số truyền vào
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Truy vấn cơ sở dữ liệu để lấy số lượng tài khoản
$count_query = "SELECT COUNT(*) AS total FROM luong";
$count_result = $dbs->query($count_query);
$row = mysqli_fetch_assoc($count_result);
$total_records = $row['total'];
// Tính toán số trang
$total_pages = ceil($total_records / $records_per_page);

// Tính toán offset để lấy bản ghi từ cơ sở dữ liệu dựa trên số trang hiện tại
$offset = ($current_page - 1) * $records_per_page;
// Hàm xóa bản ghi dựa trên ID
function deleteRecord($id)
{
    global $db;
    // Viết truy vấn SQL để xóa bản ghi dựa trên ID
    $sql = "DELETE FROM luong WHERE maLuong = $id";

    // Thực thi truy vấn
    if ($db->query($sql) === TRUE) {
        // Hiển thị thông báo xóa thành công bằng hộp thoại JavaScript
        echo "<script>alert('Xóa bản ghi thành công!');</script>";
        echo "<script>window.location.href = 'home.php?user=admin&table=dsLuong';</script>";
        exit;
    } else {
        // Hiển thị thông báo lỗi bằng hộp thoại JavaScript
        echo "<script>alert('Lỗi: " . $db->error . "');</script>";
    }
}

// Kiểm tra xem có yêu cầu xóa bản ghi không
if (isset($_GET['maLuong'])) {
    // Lấy ID từ tham số truyền vào
    $id = $_GET['maLuong'];
    // Gọi hàm xóa bản ghi
    deleteRecord($id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Lương</title>
</head>
<style>
th,
td {
    text-align: center;
}
</style>

<body>
    <div class="container">
        <h2 class="mt-5">Danh Sách Lương</h2>
        <form method="GET" action="home.php" class="mb-3">
            <div class="input-group input-group-sm">
                <input type="hidden" name="user" value="<?php echo $_GET['user']; ?>">
                <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm...">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Mã Nhân Viên</th>
                        <th scope="col">Mức Lương</th>
                        <th scope="col">Phụ Cấp</th>
                        <th scope="col">Thuế Thu Nhập</th>
                        <th scope="col">Tổng Lương</th>
                        <th scope="col">Ngày Thanh Toán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_query = " WHERE maNhanVien LIKE '%$search%'";
                    }
                    // Truy vấn cơ sở dữ liệu để lấy danh sách hợp đồng
                    $sql = "SELECT * FROM luong $search_query";
                    $result = $dbs->paginate($sql, $records_per_page, $current_page);
                    $count = ($current_page - 1) * $records_per_page;
                    // Hiển thị dữ liệu từ cơ sở dữ liệu vào bảng HTML
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $count++;
                            echo "<tr onclick='confirmDelete(" . $row['maLuong'] . ")'>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $row['maNhanVien'] . "</td>";
                            echo "<td>" . number_format($row['mucLuongCoBan']) . "</td>";
                            echo "<td>" . number_format($row['phuCap']) . "</td>";
                            echo "<td>" . number_format($row['thueThuNhapCaNhan'], 0) . " %" . "</td>";
                            echo "<td>" . number_format($row['tongLuong']) . "</td>";
                            echo "<td>" . $row['ngayThanhToan'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>Không có dữ liệu</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="pagination">
                <ul class="pagination">
                    <?php
                    // Hiển thị các nút điều hướng
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item'><a class='page-link' href='home.php?user=admin&table=dsUsers&page=$i'>$i</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</body>
<script>
function confirmDelete(id) {
    if (confirm("Bạn có chắc chắn muốn xóa bản ghi này không?")) {
        // Redirect or perform delete action here
        var urlWithId = window.location.href + "&maLuong=" + id;
        window.location.href = urlWithId;
    }
}
</script>

</html>