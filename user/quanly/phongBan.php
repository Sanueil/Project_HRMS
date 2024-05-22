<?php

// Số lượng bản ghi trên mỗi trang
$records_per_page = 4;

// Lấy số trang hiện tại từ tham số truyền vào
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

// Truy vấn cơ sở dữ liệu để lấy số lượng bản ghi
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
    global $db, $url;
    // Sử dụng prepared statement để ngăn chặn SQL Injection
    $stmt = $db->prepare("DELETE FROM phong_ban WHERE maPhongBan = ?");
    $stmt->bind_param("i", $id);
    
    // Thực thi truy vấn
    if ($stmt->execute()) {
        // Hiển thị thông báo xóa thành công bằng hộp thoại JavaScript
        echo "<script>alert('Xóa phòng ban thành công.');</script>";
        echo "<script>window.location.href = '$url&table=department';</script>";
    } else {
        // Hiển thị thông báo lỗi bằng hộp thoại JavaScript
        echo "<script>alert('Lỗi: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

if (isset($_GET['maPhongBan'])) {
    // Lấy ID từ tham số truyền vào
    $id = (int)$_GET['maPhongBan'];
    // Gọi hàm xóa bản ghi
    deleteRecord($id);
}
?>

<script>
function confirmDelete(id) {
    if (confirm("Bạn có chắc chắn muốn xóa phòng ban này không?")) {
        // Redirect or perform delete action here
        var urlWithId = window.location.href.split('?')[0] + "?maPhongBan=" + id;
        window.location.href = urlWithId;
    }
}

function updateMaPhongBan() {
    // Lấy giá trị của phòng ban được chọn
    var selectedPhongBan = document.getElementById("users").value;
    // Gán giá trị của phòng ban vào trường input ẩn
    document.getElementById("maPhongBan").value = selectedPhongBan;
    console.log("Mã phòng ban được chọn là: " + selectedPhongBan); // Thêm dòng này để kiểm tra giá trị
}
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phòng ban và chức vụ</title>
    <style>
    .modal {
        overflow: auto;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Quản lý phòng ban</h1>
        <button class="btn btn-success mb-3" id="addButton" data-toggle="modal" data-target="#addDepartmentModal">Thêm
            phòng ban</button>
        <div class="departments">
            <h2>Danh sách phòng ban</h2>
            <form method="GET" action="home.php" class="mb-3">
                <div class="input-group input-group-sm">
                    <input type="hidden" name="user" value="<?php echo $_GET['user']; ?>">
                    <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm...">
                    <button type="submit" class="btn btn-primary">Tìm</button>
                </div>
            </form>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tên phòng ban</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if (isset($_GET['search'])) {
                        $search = $db->real_escape_string($_GET['search']);
                        $search_query = " WHERE tenPhongBan LIKE '%$search%'";
                    }
                    // Truy vấn cơ sở dữ liệu để lấy danh sách phòng ban
                    $sql = "SELECT * FROM phong_ban $search_query LIMIT $records_per_page OFFSET $offset";
                    $result = $db->query($sql);
                    if ($result && $result->num_rows > 0) {
                        $count = ($current_page - 1) * $records_per_page;
                        while ($row = $result->fetch_assoc()) {
                            $count++;
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . htmlspecialchars($row['tenPhongBan']) . "</td>";
                            echo "<td>
                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#addEmployeeModal'>Thêm</button>
                                <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#editDepartmentModal_" . $row['maPhongBan'] . "'>Sửa</button>
                                <button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row['maPhongBan'] . ")'>Xóa</button>
                                <button class='btn btn-secondary btn-sm' data-toggle='modal' data-target='#dsEmployeeModal' onclick='openDsEmployeeModal(" . $row['maPhongBan'] . ")'>Xem thêm</button>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Không có phòng ban nào.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Hiển thị phân trang -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php
                    for ($page = 1; $page <= $total_pages; $page++) {
                        echo '<li class="page-item"><a class="page-link" href="home.php?user=' . $_GET['user'] . '&table=' . $_GET['table'] . '&page=' . $page . '">' . $page . '</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
</body>
<!-- Form thêm phòng ban -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="addDepartmentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">Thêm phòng ban</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../../controller/addPhongBan.php" method="POST" class="modal-body">
                <div class="form-group">
                    <label for="department_name">Tên phòng ban:</label>
                    <input type="text" class="form-control" id="department_name" name="tenPhongBan" required>
                </div>
                <div class="form-group">
                    <label for="department_description">Mô tả:</label>
                    <textarea class="form-control" id="department_description" name="moTa"></textarea>
                </div>
                <div class="form-group">
                    <label for="department_phone">Số điện thoại:</label>
                    <input type="tel" class="form-control" id="department_phone" name="soDienThoai">
                </div>
                <div class="form-group">
                    <label for="department_email">Email:</label>
                    <input type="email" class="form-control" id="department_email" name="email">
                </div>
                <input type="hidden" name="maNhanVien" value="<?php echo $username; ?>">
                <input type="hidden" name="ngayTao" value="">
                <input type="hidden" name="ngayChinhSua" value="">
                <button type="submit" class="btn btn-primary" name="submit">Thêm phòng ban</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </form>
        </div>
    </div>
</div>
<!-- Form model thêm nhân viên vào phòng ban -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Thêm nhân viên vào phòng ban</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="modal-body" method="post" action="../../controller/themNhanVienPB.php">
                <div class="form-group">
                    <input type="hidden" name="maPhongBan" id="maPhongBan" value="">
                    <label for="employee_name">Chọn nhân viên:</label>
                    <select class="form-control" id="employee_name" name="users" onchange="updateMaPhongBan()">
                        <?php
                        $sql = "SELECT maNhanVien, hoTenNhanVien FROM nhan_vien WHERE maNhanVien NOT IN (SELECT maNhanVien FROM nhan_vien_phong_ban) AND chucVu = 'Nhân viên'";
                        $result = $db->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row["maNhanVien"] . '">' . $row["hoTenNhanVien"] . '</option>';
                            }
                        } else {
                            echo '<option value="0">Vui lòng chọn nhân viên</option>';
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="submit">Thêm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </form>
        </div>
    </div>
</div>
<!-- Danh sách phòng ban -->
<?php
$sql = "SELECT * FROM phong_ban";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    ?>
<!-- Modal sửa thông tin phòng ban -->
<div class="modal fade" id="editDepartmentModal_<?php echo $row['maPhongBan']; ?>" tabindex="-1" role="dialog"
    aria-labelledby="editDepartmentModalLabel_<?php echo $row['maPhongBan']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel_<?php echo $row['maPhongBan']; ?>">Sửa thông tin
                    phòng ban</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="modal-body" method="post" action="../../controller/editPhongBan.php">
                <input type="hidden" id="department_id" name="department_id" value="<?php echo $row['maPhongBan']; ?>">
                <div class="form-group">
                    <label for="tenPhongBan">Tên phòng ban:</label>
                    <input type="text" class="form-control" id="tenPhongBan" name="tenPhongBan"
                        value="<?php echo $row['tenPhongBan']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="moTa">Mô tả:</label>
                    <textarea class="form-control" id="moTa" name="moTa"><?php echo $row['moTa']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="soDienThoai">Số điện thoại:</label>
                    <input type="tel" class="form-control" id="soDienThoai" name="soDienThoai"
                        value="<?php echo $row['soDienThoai']; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo $row['email']; ?>">
                </div>
                <input type="hidden" name="maNhanVien" value="<?php echo $username; ?>">
                <input type="hidden" name="ngayTao" value="<?php echo $row['ngayTao']; ?>">
                <input type="hidden" name="ngayChinhSua" value="<?php echo date("Y-m-d H:i:s"); ?>">
                <button type="submit" class="btn btn-primary" name="submit">Lưu</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </form>
        </div>
    </div>
</div>
<?php
}
?>
<script>
function openDsEmployeeModal(maPhongBan) {
    // Gọi hàm để lấy danh sách nhân viên dựa trên mã phòng ban
    getEmployeesByDepartment(maPhongBan);
}

function getEmployeesByDepartment(departmentId) {
    // Tạo một đối tượng XMLHttpRequest
    var xhr = new XMLHttpRequest();

    // Thiết lập hàm xử lý khi nhận được phản hồi từ máy chủ
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Cập nhật nội dung của modal với danh sách nhân viên trả về
            document.getElementById("employeeList").innerHTML = xhr.responseText;
        }
    };

    // Gửi yêu cầu Ajax để lấy danh sách nhân viên từ file PHP
    xhr.open("POST", "../../controller/ds_nv_pb.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("get_department_id=" + departmentId);
}
</script>

<!-- Modal "Danh sách nhân viên phòng ban" -->
<div class="modal fade" id="dsEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="dsEmployeeModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dsEmployeeModal">Danh sách nhân viên phòng ban</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="employeeList">
                <!-- Employee list will be loaded here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

</html>