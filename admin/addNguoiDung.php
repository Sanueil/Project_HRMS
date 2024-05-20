<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
    td {
        padding: 2rem;
    }
    </style>
</head>

<body>
    <h1>Thêm Người Dùng</h1>
    <form action="../controller/addNguoiDung.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="maNhanVien">Mã Nhân Viên:</label>
                    <input type="text" class="form-control" id="maNhanVien" name="maNhanVien" required>
                </div>
                <div class="form-group">
                    <label for="hoTenNhanVien">Họ Tên Nhân Viên:</label>
                    <input type="text" class="form-control" id="hoTenNhanVien" name="hoTenNhanVien" required>
                </div>
                <div class="form-group">
                    <label for="diaChi">Địa Chỉ:</label>
                    <input type="text" class="form-control" id="diaChi" name="diaChi" required>
                </div>
                <div class="form-group">
                    <label for="soDienThoai">Số Điện Thoại:</label>
                    <input type="text" class="form-control" id="soDienThoai" name="soDienThoai" required>
                </div>
                <div class="form-group">
                    <label for="gioiTinh">Giới Tính:</label>
                    <select class="form-control" id="gioiTinh" name="gioiTinh" required>
                        <option value="">Chọn giới tính</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="chucVu">Chức Vụ:</label>
                    <input type="text" class="form-control" id="chucVu" name="chucVu" required>
                </div>
                <div class="form-group">
                    <label for="ngaySinh">Ngày Sinh:</label>
                    <input type="date" class="form-control" id="ngaySinh" name="ngaySinh" required>
                </div>
                <div class="form-group">
                    <label for="hinhAnh">Hình Ảnh:</label>
                    <input type="file" class="form-control-file" id="hinhAnh" name="hinhAnh" accept="image/*" required>
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

    <script>
    function showAlert(message) {
        alert(message);
    }
    </script>
    <?php
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
    <script>
    showAlert('<?php echo $_SESSION['message']; ?>');
    </script>
    <?php
        unset($_SESSION['message']);
        ?>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <script>
    function generateQrCode() {
        const maNhanVien = document.getElementById('maNhanVien').value;
        const hoTenNhanVien = document.getElementById('hoTenNhanVien').value;
        const diaChi = document.getElementById('diaChi').value;
        const soDienThoai = document.getElementById('soDienThoai').value;
        const gioiTinh = document.getElementById('gioiTinh').value;
        const email = document.getElementById('email').value;
        const chucVu = document.getElementById('chucVu').value;
        const ngaySinh = document.getElementById('ngaySinh').value;

        const qrData = `Mã Nhân Viên: ${maNhanVien} -
                            Họ Tên: ${hoTenNhanVien} -
                            Địa Chỉ: ${diaChi} -
                            Số Điện Thoại: ${soDienThoai} -
                            Giới Tính: ${gioiTinh} -
                            Email: ${email} -
                            Chức Vụ: ${chucVu} -
                            Ngày Sinh: ${ngaySinh}`;

        if (maNhanVien && hoTenNhanVien && diaChi && soDienThoai && gioiTinh && email && chucVu && ngaySinh) {
            const apiUrl =
                `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrData)}`;
            document.getElementById('qrImg').src = apiUrl;
            document.getElementById('generatedCode').value = qrData;
            document.querySelector('.qr-con').style.display = '';
        } else {
            alert("Vui lòng nhập đầy đủ thông tin để tạo mã QR.");
        }
    }
    </script>
</body>

</html>