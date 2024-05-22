<?php
include_once ('../controller/connect.php');

// Khởi tạo một đối tượng Database
$dbs = new Database();
$db = $dbs->connect();
// Số lượng bản ghi trên mỗi trang
$records_per_page = 4;

// Lấy số trang hiện tại từ tham số truyền vào
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Truy vấn cơ sở dữ liệu để lấy số lượng 
$count_query = "SELECT COUNT(*) AS total FROM phong_ban";
$count_result = $dbs->query($count_query);
$row = mysqli_fetch_assoc($count_result);
$total_records = $row['total'];

// Tính toán số trang
$total_pages = ceil($total_records / $records_per_page);

// Tính toán offset để lấy bản ghi từ cơ sở dữ liệu dựa trên số trang hiện tại
$offset = ($current_page - 1) * $records_per_page;
function deleteRecord($id)
{
    global $db;
    // Viết truy vấn SQL để xóa bản ghi dựa trên ID
    $sql = "DELETE FROM phong_ban WHERE maPhongBan = $id";

    // Thực thi truy vấn
    if ($db->query($sql) === TRUE) {
        // Hiển thị thông báo xóa thành công bằng hộp thoại JavaScript
        echo "<script>alert('Xóa phòng ban thành công.');</script>";
        echo "<script>window.location.href = 'home.php?user=admin&table=department';</script>";
        exit;
    } else {
        // Hiển thị thông báo lỗi bằng hộp thoại JavaScript
        echo "<script>alert('Lỗi: " . $db->error . "');</script>";
    }
}

if (isset($_GET['maPhongBan'])) {
    // Lấy ID từ tham số truyền vào
    $id = $_GET['maPhongBan'];
    // Gọi hàm xóa bản ghi
    deleteRecord($id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form nhập loại phòng ban</title>
</head>

<body style="overflow:scroll;">
    <div class="container">
        <h2>Thêm thông tin phòng ban</h2>
        <form method="post" action="../controller/addPhongBan.php">
            <!-- Form để thêm phòng ban -->
            <input type="text" class="form-group form-control-sm" id="department_name" name="tenPhongBan"
                placeholder="Tên phòng ban" required><br>
            <input type="tel" class="form-group form-control-sm" id="department_phone" name="soDienThoai"
                placeholder="Số điện thoại"><br>
            <input type="email" class="form-group form-control-sm" id="department_email" name="email"
                placeholder="Email"><br>
            <textarea class="form-group form-control-sm" id="department_description" name="moTa"
                placeholder="Mô tả"></textarea><br>
            <button type="submit" class="btn btn-primary btn-sm" name="submit">Thêm</button>
        </form>
    </div>

    <h2 class="mt-5">Danh sách các phòng ban</h2>
    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phòng ban</th>
                    <th>Mô tả</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Ngày tạo</th>
                    <th>Ngày sửa</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <!-- Hiển thị danh sách các phòng ban -->
                <?php
                // Kết nối đến cơ sở dữ liệu và lấy danh sách phòng ban
                $sql = "
                    SELECT * FROM phong_ban
                    ";
                $result = $dbs->paginate($sql, $records_per_page, $current_page);
                $count = ($current_page - 1) * $records_per_page;
                // Hiển thị danh sách phòng ban
                while ($row = $result->fetch_assoc()) {
                    $count++;
                    echo "<tr>";
                    echo "<td>" . $count . "</td>";
                    echo "<td>" . $row['tenPhongBan'] . "</td>";
                    echo "<td>" . $row['moTa'] . "</td>";
                    echo "<td>" . $row['soDienThoai'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['ngayTao'] . "</td>";
                    echo "<td>" . $row['ngayChinhSua'] . "</td>";
                    echo "<td>
                    <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#editDepartmentModal_" . $row['maPhongBan'] . "'>Sửa</button>
                    <button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row['maPhongBan'] . ")'>Xóa</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <!-- Hiển thị nút điều hướng phân trang -->
        <div class="pagination">
            <?php
            // Hiển thị các nút điều hướng
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<li class='page-item'><a class='page-link' href='home.php?user=admin&table=department&page=$i'>$i</a></li>";
            }
            ?>

            </ul>
        </div>
    </div>
</body>
<?php
$query = "SELECT * FROM phong_ban";
$result = $db->query($query);

// Duyệt qua từng dòng kết quả
while ($row_edit = $result->fetch_assoc()) {
    ?>
<div id="editDepartmentModal_<?php echo $row_edit['maPhongBan']; ?>" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="editDepartmentModalLabel_<?php echo $row_edit['maPhongBan']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel_<?php echo $row_edit['maPhongBan']; ?>">Sửa thông
                    tin phòng ban</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form class="modal-body" id="editDepartmentForm_<?php echo $row_edit['maPhongBan']; ?>" method="post"
                action="../controller/editPhongBan.php">
                <input type="hidden" id="department_id" name="department_id"
                    value="<?php echo $row_edit['maPhongBan']; ?>">
                <div class="form-group">
                    <label for="tenPhongBan">Tên phòng ban:</label>
                    <input type="text" class="form-control" id="tenPhongBan" name="tenPhongBan"
                        value="<?php echo $row_edit['tenPhongBan']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="moTa">Mô tả:</label>
                    <textarea class="form-control" id="moTa" name="moTa"><?php echo $row_edit['moTa']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="soDienThoai">Số điện thoại:</label>
                    <input type="tel" class="form-control" id="soDienThoai" name="soDienThoai"
                        value="<?php echo $row_edit['soDienThoai']; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo $row_edit['email']; ?>">
                </div>
                <input type="hidden" name="maNhanVien" value="<?php echo $username; ?>">
                <input type="hidden" name="ngayTao" value="<?php echo $row_edit['ngayTao']; ?>">
                <input type="hidden" name="ngayChinhSua" value="<?php echo date("Y-m-d H:i:s"); ?>">
            </form>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"
                    form="editDepartmentForm_<?php echo $row_edit['maPhongBan']; ?>" name="submit">Lưu</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

<script>
function confirmDelete(id) {
    if (confirm("Bạn có chắc chắn muốn xóa phòng ban này không?")) {
        // Redirect or perform delete action here
        var urlWithId = window.location.href + "&maPhongBan=" + id;
        window.location.href = urlWithId;
    }
}
</script>
<html>