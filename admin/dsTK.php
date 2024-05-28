<?php
include_once ('../controller/connect.php');

// Khởi tạo một đối tượng Database
$dbs = new Database();
$db = $dbs->connect();
// Số lượng bản ghi trên mỗi trang
$records_per_page = 5;

// Lấy số trang hiện tại từ tham số truyền vào
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Truy vấn cơ sở dữ liệu để lấy số lượng tài khoản
$count_query = "SELECT COUNT(*) AS total FROM tai_khoan";
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
    // Viết truy vấn SQL để xóa bản ghi dựa trên ID
    $sql = "DELETE FROM tai_khoan WHERE username = $id";

    // Thực thi truy vấn
    if ($db->query($sql) === TRUE) {
        // Hiển thị thông báo xóa thành công bằng hộp thoại JavaScript
        echo "<script>alert('Xóa tài khoản thành công.');</script>";
        echo "<script>window.location.href = '$url'users';</script>";
    } else {
        // Hiển thị thông báo lỗi bằng hộp thoại JavaScript
        echo "<script>alert('Lỗi: " . $db->error . "');</script>";
    }
}

if (isset($_GET['username'])) {
    // Lấy ID từ tham số truyền vào
    $id = $_GET['username'];
    // Gọi hàm xóa bản ghi
    deleteRecord($id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
</head>
<style>
/* Thêm mã CSS cho modal */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 5px;
}

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
</style>

<body>
    <div class="container">
        <h2 class="mt-5">Danh sách tài khoản</h2>
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
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Phân quyền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_query = " WHERE username LIKE '%$search%' OR id_phan_quyen LIKE '%$search%'";
                    }
                    // Truy vấn cơ sở dữ liệu để lấy danh sách tài khoản
                    $sql = "SELECT *
                FROM tai_khoan tk
                JOIN phan_quyen pq ON tk.id_phan_quyen = pq.id $search_query";
                    $result = $dbs->paginate($sql, $records_per_page, $current_page);
                    $count = ($current_page - 1) * $records_per_page;
                    while ($account = $result->fetch_assoc()) {
                        // Kiểm tra và ẩn tài khoản của admin
                        $count++;
                        if ($account['id_phan_quyen'] !== '1') {
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $account['username'] . "</td>";
                            // Thêm một thẻ <span> để chứa mật khẩu và gắn id để dễ dàng thay đổi nội dung
                            echo "<td class='password-cell' onclick=\"showPassword('" . $account['username'] . "')\"><span id='password_" . $account['username'] . "'>*******</span><span id='actual_password_" . $account['username'] . "' style='display:none'>" . $account['password'] . "</span></td>";
                            echo "<td>" . $account['vai_tro'] . "</td>";
                            echo "<td><button type='button' data-toggle='modal' data-target='#changePasswordModal' onclick='openPasswordModal(\"" . $account['username'] . "\")' class='btn btn-primary btn-sm'>Sửa</button>
                            <button type='button' onclick=\"confirmDelete(" . $account['username'] . ")\" class='btn btn-danger btn-sm'>Xóa</button>
                            </td>
                        </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            <div class="pagination">
                <ul class="pagination">
                    <?php
                    // Hiển thị các nút điều hướng
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item'><a class='page-link' href='home.php?user=admin&table=users&page=$i'>$i</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- Modal for changing password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog"
        aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="passwordForm" method="POST" action="../controller/new_pass.php" class="modal-body">
                    <input type="hidden" name="username" id="username" value="">
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <div class="password-input input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-eye" id="togglePassword"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Lắng nghe sự kiện khi người dùng nhấp vào biểu tượng
        document.getElementById("togglePassword").addEventListener("click", function() {
            var passwordInput = document.getElementById("new_password");
            // Kiểm tra loại của input mật khẩu
            if (passwordInput.type === "password") {
                // Nếu là "password", chuyển thành "text" để hiển thị mật khẩu
                passwordInput.type = "text";
                // Thay đổi biểu tượng sang mắt không che
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                // Ngược lại, chuyển lại thành "password" để ẩn mật khẩu
                passwordInput.type = "password";
                // Thay đổi biểu tượng sang mắt che
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    });

    function openPasswordModal(username) {
        document.getElementById("changePasswordModal").style.display = "block";
        document.getElementById("username").value = username;
    }


    function showPassword(id) {
        var passwordSpan = document.querySelector("#password_" + id);
        var actualPasswordSpan = document.querySelector("#actual_password_" + id);
        if (passwordSpan && actualPasswordSpan) {
            if (passwordSpan.textContent === "*******") {
                passwordSpan.textContent = actualPasswordSpan.textContent;
            } else {
                passwordSpan.textContent = "*******";
            }
        }
    }

    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa tài khoản này không?")) {
            // Redirect or perform delete action here
            var urlWithId = window.location.href + "&username=" + id;
            window.location.href = urlWithId;
        }
    }
    </script>
</body>

</html>