<?php
$query = "SELECT * FROM nhan_vien WHERE maNhanVien = '$username'";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ nhân viên</title>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Hồ sơ nhân viên</h1>
        <table class="table mt-4">
            <div class="profile-info">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mã nhân viên:</strong> <?php echo $row['maNhanVien']; ?></p>
                        <p><strong>Họ và tên:</strong> <?php echo $row['hoTenNhanVien']; ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo $row['diaChi']; ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo $row['soDienThoai']; ?></p>
                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                        <p><strong>Chức vụ:</strong> <?php echo $row['chucVu']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Ngày sinh:</strong> <?php echo $row['ngaySinh']; ?></p>
                        <p><strong>Giới tính:</strong> <?php echo $row['gioiTinh']; ?></p>
                        <p><strong>Hình ảnh:</strong></p>
                        <div class="profile-image">
                            <?php if($logo_url != ""): ?>
                            <img src="<?php echo $logo_url; ?>" alt="Hình ảnh nhân viên" class="avatar"
                                id="profileImage">
                            <?php else: ?>
                            <img src="../../controller/uploads/thiên_hà.jpg" alt="Hình ảnh mặc định" id="profileImage">
                            <?php endif; ?>
                        </div>
                        <div class="image-upload">
                            <input type="file" id="imageInput" style="display: none;">
                            <label for="imageInput" class="choose-image">Thay đổi</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                Chỉnh sửa thông tin
            </button>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Chỉnh sửa thông tin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form chỉnh sửa thông tin -->
                    <form action="../../controller/edit_profile.php" method="POST">
                        <div class="form-group">
                            <label for="fullName">Họ và tên:</label>
                            <input type="text" class="form-control" id="fullName" name="hoTenNhanVien"
                                value="<?php echo $row['hoTenNhanVien']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Địa chỉ:</label>
                            <input type="text" class="form-control" id="address" name="diaChi"
                                value="<?php echo $row['diaChi']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber">Số điện thoại:</label>
                            <input type="text" class="form-control" id="phoneNumber" name="soDienThoai"
                                value="<?php echo $row['soDienThoai']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo $row['email']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="position">Chức vụ:</label>
                            <input type="text" class="form-control" id="position" name="chucVu"
                                value="<?php echo $row['chucVu']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="birthDate">Ngày sinh:</label>
                            <input type="date" class="form-control" id="birthDate" name="ngaySinh"
                                value="<?php echo $row['ngaySinh']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="gender">Giới tính:</label>
                            <select class="form-control" id="gender" name="gioiTinh">
                                <option value="Nam" <?php if($row['gioiTinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
                                <option value="Nữ" <?php if($row['gioiTinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
                            </select>
                        </div>
                        <input type="hidden" id="maNhanVien" name="maNhanVien"
                            value="<?php echo $row['maNhanVien']; ?>">
                        <button type="submit" class="btn btn-primary" name="submit">Lưu thay đổi</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>