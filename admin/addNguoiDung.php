<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
    <!-- Bootstrap CSS -->
    <style>
    td {
        padding: 2rem;
    }

    .error-message {
        color: red;
        display: none;
    }
    </style>
    <script>
    window.onload = function() {
        <?php if (isset($_SESSION['message'])): ?>
        alert("<?php echo $_SESSION['message']; ?>");
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    };

    function validateForm() {
        let isValid = true;

        const maNhanVien = document.getElementById('maNhanVien');
        const hoTenNhanVien = document.getElementById('hoTenNhanVien');
        const diaChi = document.getElementById('diaChi');
        const soDienThoai = document.getElementById('soDienThoai');
        const gioiTinh = document.getElementById('gioiTinh');
        const email = document.getElementById('email');
        const chucVu = document.getElementById('chucVu');
        const ngaySinh = document.getElementById('ngaySinh');
        const hinhAnh = document.getElementById('hinhAnh');

        // Clear previous error messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => {
            msg.style.display = 'none';
        });
        // Validate Mã Nhân Viên
        if (!maNhanVien.value.match(/^\d+$/)) {
            document.getElementById('maNhanVienError').style.display = 'block';
            isValid = false;
        }

        // Validate Họ Tên Nhân Viên
        if (!hoTenNhanVien.value.match(/^[A-Za-z ]+$/)) {
            document.getElementById('hoTenNhanVienError').style.display = 'block';
            isValid = false;
        }

        // Validate Số Điện Thoại
        if (!soDienThoai.value.match(/^0\d{9}$/)) {
            document.getElementById('soDienThoaiError').style.display = 'block';
            isValid = false;
        }

        // Validate Email
        if (!email.value.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
            document.getElementById('emailError').style.display = 'block';
            isValid = false;
        }

        // Validate Ngày Sinh
        if (!ngaySinh.value) {
            document.getElementById('ngaySinhError').style.display = 'block';
            isValid = false;
        }

        // Validate Hình Ảnh
        if (!hinhAnh.value) {
            document.getElementById('hinhAnhError').style.display = 'block';
            isValid = false;
        }

        return isValid;
    }

    function showAlert(message) {
        alert(message);
    }

    function generateQrCode() {
        const maNhanVien = document.getElementById('maNhanVien').value;
        const hoTenNhanVien = document.getElementById('hoTenNhanVien').value;
        const diaChi = document.getElementById('diaChi').value;
        const soDienThoai = document.getElementById('soDienThoai').value;
        const gioiTinh = document.getElementById('gioiTinh').value;
        const email = document.getElementById('email').value;
        const chucVu = document.getElementById('chucVu').value;
        const ngaySinh = document.getElementById('ngaySinh').value;
        const hinhAnh = document.getElementById('hinhAnh').value;

        const qrData =
            `Mã Nhân Viên: ${maNhanVien} - Họ Tên: ${hoTenNhanVien} - Địa Chỉ: ${diaChi} - Số Điện Thoại: ${soDienThoai} - Giới Tính: ${gioiTinh} - Email: ${email} - Chức Vụ: ${chucVu} - Ngày Sinh: ${ngaySinh}`;

        if (maNhanVien && hoTenNhanVien && diaChi && soDienThoai && gioiTinh && email && chucVu && ngaySinh &&
            hinhAnh) {
            const apiUrl =
                `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrData)}`;
            document.getElementById('qrImg').src = apiUrl;
            document.getElementById('generatedCode').value = apiUrl;
            document.querySelector('.qr-con').style.display = '';
        } else {
            alert("Vui lòng nhập đầy đủ thông tin để tạo mã QR.");
        }
    }
    </script>

</head>

<body>
    <h1>Thêm Người Dùng</h1>
    <form id="addUserForm" action="../controller/addNguoiDung.php" method="POST" enctype="multipart/form-data"
        onsubmit="return validateForm()">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="maNhanVien">Mã Nhân Viên:</label>
                    <input type="text" class="form-control" id="maNhanVien" name="maNhanVien" required pattern="[0-9]+"
                        title="Vui lòng nhập số">
                    <div class="error-message" id="maNhanVienError">Vui lòng nhập Mã Nhân Viên.</div>
                </div>
                <div class="form-group">
                    <label for="hoTenNhanVien">Họ Tên Nhân Viên:</label>
                    <input type="text" class="form-control" id="hoTenNhanVien" name="hoTenNhanVien" required
                        pattern="[A-Za-z ]+" title="Vui lòng nhập tên không dấu và cách ra, ví dụ: Nguyen Van A">
                    <div class="error-message" id="hoTenNhanVienError">Vui lòng nhập Họ Tên Nhân Viên.</div>
                </div>
                <div class="form-group">
                    <label for="diaChi">Địa Chỉ:</label>
                    <input type="text" class="form-control" id="diaChi" name="diaChi" required>
                    <div class="error-message" id="diaChiError">Vui lòng nhập Địa Chỉ.</div>
                </div>
                <div class="form-group">
                    <label for="soDienThoai">Số Điện Thoại:</label>
                    <input type="tel" class="form-control" id="soDienThoai" name="soDienThoai" required
                        pattern="0[0-9]{9}" title="Vui lòng nhập số điện thoại bắt đầu từ số 0 và có 10 chữ số">
                    <div class="error-message" id="soDienThoaiError">Vui lòng nhập Số Điện Thoại.</div>
                </div>
                <div class="form-group">
                    <label for="gioiTinh">Giới Tính:</label>
                    <select class="form-control" id="gioiTinh" name="gioiTinh" required>
                        <option value="">Chọn giới tính</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                    <div class="error-message" id="gioiTinhError">Vui lòng chọn Giới Tính.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                        title="Vui lòng nhập địa chỉ email hợp lệ (vd: example@gmail.com)">
                    <div class="error-message" id="emailError">Vui lòng nhập Email.</div>
                </div>
                <div class="form-group">
                    <label for="chucVu">Chức Vụ:</label>
                    <input type="text" class="form-control" id="chucVu" name="chucVu" required>
                    <div class="error-message" id="chucVuError">Vui lòng nhập Chức Vụ.</div>
                </div>
                <div class="form-group">
                    <label for="ngaySinh">Ngày Sinh:</label>
                    <input type="date" class="form-control" id="ngaySinh" name="ngaySinh" required>
                    <div class="error-message" id="ngaySinhError">Vui lòng nhập Ngày Sinh.</div>
                </div>
                <div class="form-group">
                    <label for="hinhAnh">Hình Ảnh:</label>
                    <input type="file" class="form-control-file" id="hinhAnh" name="hinhAnh" accept="image/*" required>
                    <div class="error-message" id="hinhAnhError">Vui lòng chọn Hình Ảnh.</div>
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control" id="maQR" name="maQR">
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" onclick="generateQrCode()">Tạo mã QR</button>
                </div>
                <div class="qr-con text-center" style="display: none;">
                    <input type="hidden" class="form-control" id="generatedCode" name="maQR">
                    <p>Lưu ảnh mã QR của bạn.</p>
                    <img class="mb-4" src="" id="qrImg" alt="">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Thêm</button>
    </form>
</body>

</html>