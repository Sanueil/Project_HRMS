<?php
include_once('../controller/connect.php');

// Kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Truy vấn để lấy tài khoản có vai trò là admin
$query = "SELECT * FROM tai_khoan WHERE id_phan_quyen = '1'";
$result = mysqli_query($db, $query);

// Khai báo các biến để lưu thông tin tài khoản admin
$username = "";
$password = "";

// Kiểm tra xem có kết quả trả về không
if ($result && mysqli_num_rows($result) > 0) {
    // Lấy dữ liệu từ kết quả
    $admin_account = mysqli_fetch_assoc($result);
    // Gán thông tin tài khoản admin cho các biến
    $username = $admin_account['username'];
    $password = $admin_account['password'];
}

// Đóng kết nối
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
</head>

<body>
    <header class="container mt-3 mb-3">
        <h1>Admin Settings</h1>
    </header>
    <div class="container">
        <form action="save_settings.php" method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <div class="input-group">
                    <input type="text" class="form-control-sm" id="username" name="username" disabled
                        value="<?php echo $username; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control-sm" id="password" name="password" disabled
                        value="<?php echo $password; ?>">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                        onclick="togglePasswordVisibility()">&#128065;</button>
                </div>
            </div>

            <script>
            function togglePasswordVisibility() {
                var passwordField = document.getElementById("password");
                var toggleIcon = document.getElementById("togglePassword");

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    toggleIcon.innerHTML = "&#128065;";
                } else {
                    passwordField.type = "password";
                    toggleIcon.innerHTML = "&#128065;";
                }
            }
            </script>
        </form>
    </div>
</body>

</html>