<?php
    include_once('../controller/connect.php');
    
    // Khởi tạo một đối tượng Database
    $dbs = new Database();
    $db = $dbs->connect();
    
    // Số lượng bản ghi trên mỗi trang
    $records_per_page = 10;
    
    // Lấy số trang hiện tại từ tham số truyền vào hoặc mặc định là trang đầu tiên
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    
    // Tạo câu truy vấn SQL để lấy số lượng nhân viên
    $count_query = "SELECT COUNT(*) AS total FROM nhan_vien";
    $count_result = $dbs->query($count_query);
    $row = mysqli_fetch_assoc($count_result);
    $total_records = $row['total'];
    
    // Tính toán số trang
    $total_pages = ceil($total_records / $records_per_page);
    
    // Tính toán offset để lấy bản ghi từ cơ sở dữ liệu dựa trên số trang hiện tại
    $offset = ($current_page - 1) * $records_per_page;
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
    <style>
    /* Lớp overlay */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        /* Màu đen với độ trong suốt 50% */
        z-index: 8;
        /* Z-index nhỏ hơn form để nó nằm dưới form */
        display: none;
        /* Ẩn mặc định */
    }

    .modal {
        overflow: auto;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Danh sách người dùng</h2>
        <form method="GET" action="home.php" class="mb-3">
            <div class="input-group input-group-sm">
                <input type="hidden" name="user" value="<?php echo $_GET['user']; ?>">
                <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm...">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </div>
        </form>
        <br>
        <br>
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã nhân viên</th>
                        <th>Họ và tên</th>
                        <th>Tình trạng có tài khoản</th>
                        <th>Xem chi tiết</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_query = " WHERE maNhanVien LIKE '%$search%' OR hoTenNhanVien LIKE '%$search%'";
                    }
    
                    $sql = "
                    SELECT * FROM nhan_vien
                    $search_query
                    ";
                    $result = $dbs->paginate($sql, $records_per_page, $current_page);
                    $count = ($current_page - 1) * $records_per_page;
                    while ($row = $result->fetch_assoc()) {
                        $count++;
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $row['maNhanVien'] . "</td>";
                        echo "<td>" . $row['hoTenNhanVien'] . "</td>";
                        // Truy vấn SQL để kiểm tra sự tồn tại của thông tin tài khoản
                        $account_check_sql = "SELECT * FROM tai_khoan WHERE maNhanVien = '" . $row['maNhanVien'] . "'";
                        $account_check_result = $dbs->query($account_check_sql);
                        $has_account = mysqli_num_rows($account_check_result) > 0;
                        // Hiển thị trạng thái tài khoản dựa trên kết quả kiểm tra
                        echo "<td>" . ($has_account ? 'Có' : 'Không') . "</td>";
                        echo "<td><button class='btn btn-primary' onclick='openUserInfoModal(" . $row['maNhanVien'] . ")'>Xem chi tiết</button></td>";
                        echo "<td>
                        <button class='btn btn-primary btn-sm' onclick='openModal(\"" . $row['maNhanVien'] . "\", \"" . $row['hoTenNhanVien'] . "\", \"" . $row['diaChi'] . "\", \"" . $row['soDienThoai'] . "\", \"" . $row['email'] . "\", \"" . $row['chucVu'] . "\", \"" . $row['ngaySinh'] . "\", \"" . $row['gioiTinh'] . "\")'>Sửa</button>
                        <button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row['maNhanVien'] . ")'>Xóa</button>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Hiển thị nút điều hướng phân trang -->
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

    <div id="myModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Người Dùng</h5>
                    <button type="button" class="close" onclick="closeModal()">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="../controller/editNguoiDung.php?action=sua">
                        <input type="hidden" id="maNhanVien" name="maNhanVien">
                        <!-- Các trường thông tin khác của người dùng -->
                        <div class="form-group">
                            <label for="hoTenNhanVien">Họ và Tên:</label>
                            <input type="text" class="form-control" id="hoTenNhanVien" name="hoTenNhanVien" value="">
                        </div>
                        <div class="form-group">
                            <label for="diaChi">Địa Chỉ:</label>
                            <input type="text" class="form-control" id="diaChi" name="diaChi" value="">
                        </div>
                        <div class="form-group">
                            <label for="soDienThoai">Số Điện Thoại:</label>
                            <input type="text" class="form-control" id="soDienThoai" name="soDienThoai" value="">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" class="form-control" id="email" name="email" value="">
                        </div>
                        <div class="form-group">
                            <label for="chucVu">Chức Vụ:</label>
                            <input type="text" class="form-control" id="chucVu" name="chucVu" value="">
                        </div>
                        <div class="form-group">
                            <label for="ngaySinh">Ngày Sinh:</label>
                            <input type="text" class="form-control" id="ngaySinh" name="ngaySinh" value="">
                        </div>
                        <div class="form-group">
                            <label for="gioiTinh">Giới Tính:</label>
                            <input type="text" class="form-control" id="gioiTinh" name="gioiTinh" value="">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Lưu</button>
                        <button type="button" class="btn btn-secondary" id="closeModal"
                            onclick="closeModal()">Đóng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="overlay" class="overlay"></div>

    <script>
    function openModal(maNhanVien, hoTenNhanVien, diaChi, soDienThoai, email, chucVu, ngaySinh, gioiTinh) {
        document.getElementById('myModal').style.display = 'block';
        document.getElementById("overlay").style.display = "block";

        document.getElementById("maNhanVien").value = maNhanVien;
        document.getElementById("hoTenNhanVien").value = hoTenNhanVien;
        document.getElementById("diaChi").value = diaChi;
        document.getElementById("soDienThoai").value = soDienThoai;
        document.getElementById("email").value = email;
        document.getElementById("chucVu").value = chucVu;
        document.getElementById("ngaySinh").value = ngaySinh;
        document.getElementById("gioiTinh").value = gioiTinh;
    }

    function closeModal() {
        document.getElementById('myModal').style.display = 'none';
        document.getElementById("overlay").style.display = "none";
    }

    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa phòng ban này không?")) {
            // Nếu người dùng chấp nhận, chuyển đến trang xử lý xóa và truyền ID
            window.location.href = "../controller/editNguoiDung.php?action=xoa&maNhanVien=" + id;
        }
    }

    document.getElementById("closeModal").addEventListener("click", closeModal);
    window.addEventListener("click", function(event) {
        if (event.target == document.getElementById("myModal")) {
            closeModal();
        }
    });

    function openUserInfoModal(maNhanVien) {
        document.getElementById("employeeDetailModal").style.display = "block";
        document.getElementById("overlay").style.display = "block";

        showEmployeeDetail(maNhanVien);
    }

    function showEmployeeDetail(employeeId) {
        // Tạo một đối tượng XMLHttpRequest
        var xhr = new XMLHttpRequest();

        // Thiết lập hàm xử lý khi nhận được phản hồi từ máy chủ
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Hiển thị thông tin chi tiết của nhân viên trong modal
                document.getElementById("employeeDetail").innerHTML = xhr.responseText;
            }
        };

        // Gửi yêu cầu Ajax để lấy thông tin chi tiết của nhân viên
        xhr.open("POST", "chiTietNguoiDung.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("maNhanVien=" + employeeId);
    }
    </script>
    <div id="employeeDetailModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi Tiết Người Dùng</h5>
                    <button type="button" class="close" onclick="closeUserInfoModal()">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Thông tin chi tiết người dùng sẽ được hiển thị ở đây -->
                    <div id="employeeDetail"></div>
                </div>
                <button type="button" class="close" id="closeUserInfoModal" onclick="closeUserInfoModal()">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    </div>
    <div id="overlay" class="overlay"></div>

</body>
<script>
function closeUserInfoModal() {
    document.getElementById("employeeDetailModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}
// Lắng nghe sự kiện khi người dùng nhấp vào nút "Đóng" hoặc di chuột ra ngoài modal
document.getElementById("closeUserInfoModal").addEventListener("click", closeUserInfoModal);
window.addEventListener("click", function(event) {
    if (event.target == document.getElementById("employeeDetailModal")) {
        closeUserInfoModal();
    }
});
</script>

</html>