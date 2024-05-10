<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Lương</title>
</head>

<body>
    <h1>Tạo Lương</h1>
    <form action="../controller/payroll.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group" style="display: none;">
                    <label for="maLuong">Mã lương:</label>
                    <input class="form-control" type="text" id="maLuong" name="maLuong" readonly>
                </div>

                <div class="form-group">
                    <label for="maNhanVien">Mã nhân viên:</label>
                    <select class="form-control" id="maNhanVien" name="maNhanVien" required
                        onchange="fillEmployeeData()">
                        <option value="">Chọn mã nhân viên</option>
                        <?php
                                // Thực hiện truy vấn SQL để lấy danh sách mã nhân viên có tài khoản
                                $sql = "
                                    SELECT nv.maNhanVien, nv.hoTenNhanVien, pb.maPhongBan
                                    FROM nhan_vien nv
                                    JOIN tai_khoan tk ON nv.maNhanVien = tk.maNhanVien
                                    JOIN nhan_vien_phong_ban pb ON nv.maNhanVien = pb.maNhanVien
                                    WHERE tk.username IS NOT NULL
                                    AND nv.maNhanVien NOT IN (
                                        SELECT maNhanVien FROM luong
                                    ); 
                                    ";
                                $result = $p->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    // Hiển thị tên nhân viên trong danh sách chọn
                                    echo '<option value="' . $row['maNhanVien'] . '" data-maphongban="' . $row['maPhongBan'] . '">' . $row['maNhanVien'] . ' - ' . $row['hoTenNhanVien'] . '</option>';
                                }
                                ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mucLuongCoBan">Mức lương cơ bản:</label>
                    <div class="input-group">
                        <input class="form-control" type="text" id="mucLuongCoBan" name="mucLuongCoBan"
                            placeholder="VD:10000000" required>
                        <div class="input-group-append">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phuCap">Phụ cấp:</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="phuCap" name="phuCap" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="display: none;">
                    <label for="thuong">Thưởng:</label>
                    <div class="input-group">
                        <input class="form-control" type="number" id="thuong" name="thuong" value="0" required>
                        <div class="input-group-append">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="thueThuNhapCaNhan">Thuế thu nhập cá nhân:</label>
                    <div class="input-group">
                        <input class="form-control" id="thueThuNhapCaNhan" name="thueThuNhapCaNhan" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tongLuong">Tổng lương:</label>
                    <div class="input-group">
                        <input class="form-control" type="text" id="tongLuong" name="tongLuong" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ngayThanhToan">Ngày thanh toán:</label>
                    <input type="date" class="form-control" id="ngayThanhToan" name="ngayThanhToan" required>
                </div>

                <div class="form-group">
                    <label for="baoHiem">Bảo hiểm:</label>
                    <select class="form-control" id="baoHiem" name="baoHiem" required>
                        <option value="1">Có</option>
                        <option value="0">Không</option>
                    </select>
                </div>

                <div class="form-group" style="display: none;">
                    <label for="maPhongBan">Mã phòng ban:</label>
                    <div class="form-group">
                        <input class="form-control" type="text" id="maPhongBan" name="maPhongBan" readonly>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary" name="submit">Tạo Lương</button>
            </div>
        </div>
    </form>

    <!-- Script JavaScript -->
    <script>
    // Hàm để tính toán phụ cấp và thuế thu nhập cá nhân khi biết mức lương cơ bản
    function calculateAllowanceAndIncomeTax() {
        var basicSalary = document.getElementById("mucLuongCoBan").value;
        var allowance = 0;
        var taxRate = 0;

        // Phân loại phụ cấp dựa vào mức lương cơ bản
        if (basicSalary <= 10000000) {
            allowance = 500000;
        } else if (basicSalary <= 12000000) {
            allowance = 600000;
        } else if (basicSalary <= 15000000) {
            allowance = 700000;
        } else if (basicSalary <= 20000000) {
            allowance = 800000;
        }

        // Phân loại thuế thu nhập cá nhân dựa vào mức lương cơ bản
        if (basicSalary <= 60000000) {
            taxRate = 5;
        } else if (basicSalary <= 120000000) {
            taxRate = 10;
        } else if (basicSalary <= 216000000) {
            taxRate = 15;
        } else if (basicSalary <= 384000000) {
            taxRate = 20;
        } else if (basicSalary <= 624000000) {
            taxRate = 25;
        } else if (basicSalary <= 960000000) {
            taxRate = 30;
        } else {
            taxRate = 35;
        }

        // Hiển thị phụ cấp
        document.getElementById("phuCap").value = allowance.toLocaleString();

        // Hiển thị % thuế thu nhập cá nhân dựa trên mức lương cơ bản và thuế suất
        document.getElementById("thueThuNhapCaNhan").value = taxRate;
    }

    /// Hàm tính tổng lương
    function calculateTotalSalary() {
        var baoHiemRate = 10;
        var basicSalary = parseFloat(document.getElementById("mucLuongCoBan").value.replace(/,/g, ''));
        var allowance = parseFloat(document.getElementById("phuCap").value.replace(/,/g, ''));
        var bonus = parseFloat(document.getElementById("thuong").value.replace(/,/g, ''));
        var taxRate = parseFloat(document.getElementById("thueThuNhapCaNhan").value.replace('%', ''));

        // Tính toán tổng lương theo công thức đã cho
        var totalSalary = basicSalary + allowance + bonus - (taxRate / 100 * basicSalary) - (baoHiemRate / 100 *
            basicSalary);

        // Hiển thị tổng lương với dấu phẩy
        document.getElementById("tongLuong").value = numberWithCommas(totalSalary);
    }

    // Hàm thêm dấu phẩy vào số
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Hàm điền dữ liệu nhân viên
    function fillEmployeeData() {
        var select = document.getElementById("maNhanVien");
        var selectedOption = select.options[select.selectedIndex];
        var selectedEmployee = selectedOption.value;
        document.getElementById("maLuong").value = selectedEmployee;
    }

    // Gọi hàm khi trang đã được tải hoàn tất (document ready)
    document.addEventListener("DOMContentLoaded", function() {
        calculateAllowanceAndIncomeTax();
        document.getElementById("mucLuongCoBan").addEventListener("change", calculateAllowanceAndIncomeTax);
        document.getElementById("thueThuNhapCaNhan").addEventListener("change", calculateTotalSalary);
        document.getElementById("phuCap").addEventListener("change", calculateTotalSalary);
        document.getElementById("mucLuongCoBan").addEventListener("change", calculateTotalSalary);
        document.getElementById("maNhanVien").addEventListener("change", fillEmployeeData);
    });
    </script>
</body>

</html>