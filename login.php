<!DOCTYPE HTML>
<html>

<head>
    <title>HRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="resorce/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <style>
    body {
        background-color: #232526;
        color: white;
        font-family: 'Montserrat', sans-serif;
        display: flex;
        justify-content: center;
        /* Căn giữa theo chiều dọc */
        height: 100vh;
        /* Chiều cao của màn hình */
        margin: 0;
        /* Loại bỏ margin */
    }

    .container {
        align-self: center;
        /* Căn giữa theo chiều dọc */
    }

    .login-form {
        max-width: 400px;
        margin-left: 350px;
        /* Đặt kích thước tối đa cho form */
    }
    </style>
</head>

<body class="bg-primary d-flex align-items-center justify-content-center">
    <!-- Thêm class d-flex, align-items-center và justify-content-center -->
    <div class="container">
        <div class="login-form bg-dark p-4 rounded-lg">
            <!-- Thêm rounded-lg để làm cho form trông tròn hơn -->
            <h2 class="text-white mb-4">Đăng nhập</h2>
            <form id="loginForm" action="controller/login.php" method="POST">
                <div class="form-group">
                    <label for="username" class="text-white">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter Username"
                        required>
                </div>
                <div class="form-group">
                    <label for="password" class="text-white">Password:</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Enter Password" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePasswordVisibility()">
                                <i class="fa fa-eye" id="eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="page" value="user">
                <button type="submit" name="submit" class="btn btn-primary btn-block">Đăng nhập</button>
            </form>
            <div class="text-center mt-3">
                <a href="index.php" class="text-white">Trở về màn hình chính</a>
            </div>
        </div>
    </div>
</body>

<script>
function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.getElementById("eye");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    var usernameInput = document.getElementById("username");
    var passwordInput = document.getElementById("password");

    usernameInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            passwordInput.focus();
            event.preventDefault();
        }
    });

    passwordInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            document.getElementById("loginForm").submit();
            event.preventDefault();
        }
    });
});
</script>

</html>