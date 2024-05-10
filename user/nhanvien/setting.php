<?php
// Lấy thông tin người dùng từ session
$username = $_SESSION['nhanvien_user']['username'];
$password = $_SESSION['nhanvien_user']['password'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt</title>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Cài đặt</h1>
        <form action="save_settings.php" method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Tên đăng nhập:</label>
                <div class="input-group">
                    <input type="text" class="form-control-sm" id="username" name="username" disabled
                        value="<?php echo $username; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu:</label>
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