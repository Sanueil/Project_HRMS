<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng</title>
</head>
<style>
td {
    padding: 2rem;
}
</style>

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
    if(isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
    <script>
    showAlert('<?php echo $_SESSION['message']; ?>');
    </script>
    <?php 
        // Sau khi hiển thị thông báo, xóa nó khỏi session để tránh hiển thị lặp lại
        unset($_SESSION['message']);
    ?>
    <?php endif; ?>
</body>

</html>