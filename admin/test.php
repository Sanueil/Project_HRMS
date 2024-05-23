<?php
include_once("../controller/CLASS/clsMailer.php");
$mail = new cPHPMailer();
?>

<div class="modal fade" id="send" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Thông tin phản hồi</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="xemchitiet"></div>
                <form id="addUserForm" action="" method="POST" enctype="multipart/form-data"
                    onsubmit="return validateForm()">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="maNhanVien">Mã Nhân Viên:</label>
                                <input type="text" class="form-control" id="maNhanVien" name="maNhanVien" required
                                    pattern="[0-9]+" title="Vui lòng nhập số">
                                <div class="error-message" id="maNhanVienError">Vui lòng nhập Mã Nhân Viên.</div>
                            </div>
                            <div class="form-group">
                                <label for="hoTenNhanVien">Họ Tên Nhân Viên:</label>
                                <input type="text" class="form-control" id="hoTenNhanVien" name="hoTenNhanVien" required
                                    pattern="[A-Za-z ]+"
                                    title="Vui lòng nhập tên không dấu và cách ra, ví dụ: Nguyen Van A">
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
                                    pattern="0[0-9]{9}"
                                    title="Vui lòng nhập số điện thoại bắt đầu từ số 0 và có 10 chữ số">
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
                                <input type="file" class="form-control-file" id="hinhAnh" name="hinhAnh"
                                    accept="image/*" required>
                                <div class="error-message" id="hinhAnhError">Vui lòng chọn Hình Ảnh.</div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="maQR" name="maQR">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-secondary" onclick="generateQrCode()">Tạo mã
                                    QR</button>
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
            </div>
        </div>
    </div>
</div>

<?php
    if (isset($_POST['submit'])) {
        $maNhanVien = $_POST['maNhanVien'];
        $hoTenNhanVien = $_POST['hoTenNhanVien'];
        $diaChi = $_POST['diaChi'];
        $soDienThoai = $_POST['soDienThoai'];
        $gioiTinh = $_POST['gioiTinh'];
        $email = $_POST['email'];
        $chucVu = $_POST['chucVu'];
        $ngaySinh = $_POST['ngaySinh'];
        $maQR = $_POST['maQR'];

        $mail->send_mail_phanhoi($maNhanVien, $hoTenNhanVien, $diaChi, $soDienThoai, $gioiTinh, $email, $chucVu, $ngaySinh, $maQR);
    }
?>