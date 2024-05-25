<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Data Table CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

    <style>
    #employeeTable {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    #employeeTable th,
    #employeeTable td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    #employeeTable th {
        background-color: #343a40;
        color: white;
    }

    #employeeTable tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #exportExcel {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #exportExcel:hover {
        background-color: #0056b3;
    }

    #employeeTable img {
        max-width: 100px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="employee-list">
            <br>
            <div class="table-container table-responsive">
                <table class="table text-center table-sm" id="employeeTable">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Mã nhân viên</th>
                            <th scope="col">Họ và tên</th>
                            <th scope="col">Mã QR</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        <?php
                        // Kết nối cơ sở dữ liệu
                        $dbs = new Database();
                        $db = $dbs->connect();

                        // Kiểm tra kết nối
                        if ($db->connect_error) {
                            die("Connection failed: " . $db->connect_error);
                        }

                        $employeeQuery = $db->query("SELECT * FROM nhan_vien");

                        if ($employeeQuery->num_rows > 0) {
                            $count = 1;
                            while ($row = $employeeQuery->fetch_assoc()) {
                                $hoTenNhanVien = $row['hoTenNhanVien'];
                                $maNhanVien = $row['maNhanVien'];
                                $diaChi = $row['diaChi'];
                                $soDienThoai = $row['soDienThoai'];
                                $email = $row['email'];
                                $chucVu = $row['chucVu'];
                                $ngaySinh = $row['ngaySinh'];
                                $gioiTinh = $row['gioiTinh'];
                                $qrFileName = $maNhanVien . '_' . str_replace(' ', '_', $hoTenNhanVien) . '.png';
                                // Tạo chuỗi chứa đầy đủ thông tin của nhân viên
                                $employee_info = "Mã Nhân Viên: $maNhanVien\n";
                                $employee_info .= "Họ Tên: $hoTenNhanVien\n";
                                $employee_info .= "Địa Chỉ: $diaChi\n";
                                $employee_info .= "Số Điện Thoại: $soDienThoai\n";
                                $employee_info .= "Email: $email\n";
                                $employee_info .= "Chức Vụ: $chucVu\n";
                                $employee_info .= "Ngày Sinh: $ngaySinh\n";
                                $employee_info .= "Giới Tính: $gioiTinh\n";
                                // Mã hóa thông tin nhân viên để truyền vào URL của ảnh mã QR
                                $qr_code_data = urlencode($employee_info);
                                ?>
                        <tr>
                            <th scope="row"><?= $count ?></th>
                            <td><?= $maNhanVien ?></td>
                            <td><?= $hoTenNhanVien ?></td>
                            <td data-qr-code-data="<?= $qr_code_data ?>">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $qr_code_data ?>"
                                    alt="">
                            </td>

                        </tr>

                        <?php
                            $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>Không có nhân viên</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Xuất Excel Button -->
                <button class="btn btn-primary" id="exportExcel">Xuất Excel</button>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <!-- DataTable JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <!-- FileSaver.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js"></script>
    <!-- xlsx.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <!-- ExcelJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.1.1/exceljs.min.js"></script>

    <!-- Script for DataTable initialization -->
    <script>
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            "paging": true, // Cho phép phân trang
            "pageLength": 4, // Số lượng nhân viên trên mỗi trang
            "lengthChange": false, // Ẩn chức năng thay đổi số lượng bản ghi trên mỗi trang
            "searching": false, // Ẩn ô tìm kiếm
            "ordering": false, // Ẩn sắp xếp
            "info": false // Ẩn thông tin số bản ghi
        });
    });

    function exportToExcel() {
        var rows = document.querySelectorAll("#employeeTable tbody tr");
        var data = [];
        var headerRow = ["Mã nhân viên", "Họ và tên", "Mã QR"];
        data.push(headerRow);

        rows.forEach(function(row) {
            var rowData = [];
            row.querySelectorAll("td").forEach(function(cell, index) {
                if (index === 2) {
                    var qrCodeData = cell.dataset.qrCodeData;
                    var qrImageLink = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" +
                        qrCodeData;
                    rowData.push(qrImageLink); // Thêm đường link của ảnh QR code vào mảng dữ liệu
                } else {
                    rowData.push(cell.innerText);
                }
            });
            data.push(rowData);
        });

        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(data);

        XLSX.utils.book_append_sheet(wb, ws, "Employees");

        XLSX.writeFile(wb, "employee_data.xlsx");
    }

    document.getElementById("exportExcel").addEventListener("click", exportToExcel);
    </script>
</body>

</html>