<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm tài khoản</title>

</head>

<body>
    <header>
        <h1>Thêm tài khoản</h1>
    </header>
    <main>
        <section class="content mt-5">
            <div class="container">
                <!-- Form thêm tài khoản -->
                <div class="form-container">
                    <form action="../controller/themtaikhoan.php" method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Vai trò:</label>
                            <select class="form-control" id="role" name="role">
                                <option value="1">Admin</option>
                                <option value="2">Quản lý</option>
                                <option value="3">Nhân viên</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="users">Người dùng:</label>
                            <select class="form-control" id="users" name="users" onchange="updateHiddenField()">
                                <?php
                                    // Kết nối đến cơ sở dữ liệu và lấy danh sách người dùng
                                    include_once('../controller/connect.php');
                                    $dbs = new Database();
                                    $db = $dbs->connect();

                                    // Truy vấn SQL để lấy danh sách nhân viên chưa có tài khoản
                                    $sql = "SELECT maNhanVien, hoTenNhanVien FROM nhan_vien WHERE NOT EXISTS(SELECT * FROM tai_khoan WHERE nhan_vien.maNhanVien = tai_khoan.maNhanVien)";
                                    $result = $db->query($sql);

                                    // Kiểm tra kết quả truy vấn và thêm các tùy chọn vào thẻ select
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

                        <input type="hidden" id="maNhanVien" name="maNhanVien">

                        <!-- Button submit -->
                        <button type="submit" name="submit" class="btn btn-primary">Thêm tài khoản</button>
                    </form>
                </div>
                <!-- Bảng danh sách nhân viên -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã nhân viên</th>
                                <th>Họ và tên</th>
                                <th>Tình trạng có tài khoản</th>
                                <th>Chức vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Kết nối đến cơ sở dữ liệu và lấy danh sách người dùng
                                include_once('../controller/connect.php');
                                $dbs = new Database();
                                $db = $dbs->connect();

                                $search_query = "";
                                if (isset($_GET['search'])) {
                                    $search = $_GET['search'];
                                    $search_query = " AND (maNhanVien LIKE '%$search%' OR hoTenNhanVien LIKE '%$search%')";
                                }

                                $sql = "SELECT * FROM nhan_vien WHERE NOT EXISTS (SELECT * FROM tai_khoan WHERE nhan_vien.maNhanVien = tai_khoan.maNhanVien) $search_query ORDER BY maNhanVien";
                                $result = $dbs->query($sql);
                                $count = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
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
                                    echo "<td>" . $row['chucVu'] . "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
    // Hàm cập nhật giá trị của trường ẩn mã nhân viên khi lựa chọn từ danh sách
    function updateHiddenField() {
        var selectedValue = document.getElementById("users").value;
        document.getElementById("maNhanVien").value = selectedValue;
    }
    // Gọi hàm updateHiddenField() khi trang được tải lần đầu và khi có sự thay đổi trong danh sách người dùng
    document.addEventListener("DOMContentLoaded", function() {
        updateHiddenField(); // Cập nhật giá trị ban đầu
        document.getElementById("users").addEventListener("change",
            updateHiddenField); // Gọi hàm khi có sự thay đổi
    });
    </script>
</body>

</html>