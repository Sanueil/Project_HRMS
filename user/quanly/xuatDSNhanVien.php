<?php

// Khởi tạo kết nối đến cơ sở dữ liệu
$dbs = new Database();
$db = $dbs->connect();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['quanly_user'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: ../../login.php");
    exit;
}

// Lấy dữ liệu nhân viên từ cơ sở dữ liệu
$query = "SELECT * FROM nhan_vien";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách nhân viên</title>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

    <!-- DataTables JavaScript -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js">
    </script>

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
    </style>
</head>

<body>
    <div class="container">
        <div class="employee-list">
            <h2 class="text-center mt-4">Danh sách nhân viên</h2>
            <div class="table-container table-responsive">
                <table class="table text-center table-sm" id="employeeTable">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Mã nhân viên</th>
                            <th scope="col">Họ và tên</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Email</th>
                            <th scope="col">Chức vụ</th>
                            <th scope="col">Ngày sinh</th>
                            <th scope="col">Giới tính</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $count . "</td>";
                                echo "<td>" . $row['maNhanVien'] . "</td>";
                                echo "<td>" . $row['hoTenNhanVien'] . "</td>";
                                echo "<td>" . $row['diaChi'] . "</td>";
                                echo "<td>" . $row['soDienThoai'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['chucVu'] . "</td>";
                                echo "<td>" . $row['ngaySinh'] . "</td>";
                                echo "<td>" . $row['gioiTinh'] . "</td>";
                                echo "</tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='9'>Không có nhân viên</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <button class="btn btn-primary" id="exportExcel">Xuất Excel</button>
            </div>
        </div>
    </div>

    <!-- Script for DataTable initialization and Export Excel -->
    <script>
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            "paging": true,
            "pageLength": 4,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": false
        });

        function exportToExcel() {
            var rows = $('#employeeTable').DataTable().rows().data();
            var data = [];
            var headerRow = ["STT", "Mã nhân viên", "Họ và tên", "Địa chỉ", "Số điện thoại", "Email", "Chức vụ",
                "Ngày sinh", "Giới tính"
            ];
            data.push(headerRow);

            rows.each(function(rowData, index) {
                var row = [];
                row.push(index + 1);
                row.push(rowData[1]);
                row.push(rowData[2]);
                row.push(rowData[3]);
                row.push(rowData[4]);
                row.push(rowData[5]);
                row.push(rowData[6]);
                row.push(rowData[7]);
                row.push(rowData[8]);
                data.push(row);
            });

            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(data);

            XLSX.utils.book_append_sheet(wb, ws, "Employees");

            XLSX.writeFile(wb, "employee_data.xlsx");
        }

        document.getElementById("exportExcel").addEventListener("click", exportToExcel);
    });
    </script>
</body>

</html>