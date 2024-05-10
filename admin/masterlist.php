<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống chấm công bằng mã QR</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Data Table -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

    * {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.15) 0%, rgba(0, 0, 0, 0.15) 100%), radial-gradient(at top center, rgba(255, 255, 255, 0.40) 0%, rgba(0, 0, 0, 0.40) 120%) #989898;
        background-blend-mode: multiply, multiply;
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-size: cover;
    }

    .main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 91.5vh;
    }

    .employee-container {
        height: 90%;
        width: 90%;
        border-radius: 20px;
        padding: 40px;
        background-color: rgba(255, 255, 255, 0.8);
    }

    .employee-container>div {
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        border-radius: 10px;
        padding: 30px;
        height: 100%;
    }

    .title {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    table.dataTable thead>tr>th.sorting,
    table.dataTable thead>tr>th.sorting_asc,
    table.dataTable thead>tr>th.sorting_desc,
    table.dataTable thead>tr>th.sorting_asc_disabled,
    table.dataTable thead>tr>th.sorting_desc_disabled,
    table.dataTable thead>tr>td.sorting,
    table.dataTable thead>tr>td.sorting_asc,
    table.dataTable thead>tr>td.sorting_desc,
    table.dataTable thead>tr>td.sorting_asc_disabled,
    table.dataTable thead>tr>td.sorting_desc_disabled {
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="main">

        <div class="employee-container">
            <div class="employee-list">
                <div class="title">
                    <h4>Danh sách nhân viên</h4>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#addEmployeeModal">Thêm nhân
                        viên</button>
                </div>
                <hr>
                <div class="table-container table-responsive">
                    <table class="table text-center table-sm" id="employeeTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Mã nhân viên</th>
                                <th scope="col">Họ và tên</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Điều chỉnh</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $employee = $db->prepare("SELECT nv.*, pb.tenPhongBan
                                FROM nhan_vien nv
                                LEFT JOIN nhan_vien_phong_ban nvpb ON nv.maNhanVien = nvpb.maNhanVien
                                LEFT JOIN phong_ban pb ON nvpb.maPhongBan = pb.maPhongBan;
                                -- WHERE nv.maQR IS NOT NULL AND nv.maQR != '';                            
                                ");
                            $employee->execute();
                            $result = $employee->get_result();
                            $count = 0;
                            foreach ($result as $row) {
                                $count++;
                                $employeeID = $count;
                                $employeeCode = $row["maNhanVien"];
                                $employeeName = $row["hoTenNhanVien"];
                                $employeePosition = $row["tenPhongBan"];
                                $qrCode = $row["maQR"];
                                ?>
                                <tr>
                                    <th scope="row" id="employeeID-<?= $employeeID ?>"><?= $employeeID ?></th>
                                    <td id="employeeCode-<?= $employeeID ?>"><?= $employeeCode ?></td>
                                    <td id="employeeName-<?= $employeeID ?>"><?= $employeeName ?></td>
                                    <td id="employeePosition-<?= $employeeID ?>"><?= $employeePosition ?></td>
                                    <td>
                                        <div class="action-button">
                                            <button class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#qrCodeModal<?= $employeeID ?>"><img
                                                    src="https://cdn-icons-png.flaticon.com/512/1341/1341632.png" alt=""
                                                    width="16"></button>

                                            <!-- QR Modal -->
                                            <div class="modal fade" id="qrCodeModal<?= $employeeID ?>" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Mã QR của <?= $employeeName ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $qrCode ?>"
                                                                alt="" width="300">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-secondary btn-sm"
                                                onclick="updateEmployee(<?= $employeeID ?>)">&#128393;</button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteEmployee(<?= $employeeID ?>)">&#10006;</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addEmployeeModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="addEmployee" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployee">Thêm nhân viên</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="./endpoint/add_employee.php" method="POST">
                        <div class="form-group">
                            <label for="employeeName">Họ và tên đầy đủ:</label>
                            <input type="text" class="form-control" id="employeeName" name="hoTenNhanVien">
                        </div>

                        <div class="form-group">
                            <label for="employeeCode">Mã nhân viên:</label>
                            <input type="text" class="form-control" id="employeeCode" name="maNhanVien"
                                placeholder="Tên phòng ban + mã">
                        </div>

                        <div class="form-group">
                            <label for="employeePosition">Phòng ban:</label>
                            <input type="text" class="form-control" id="employeePosition" name="phongBan">
                        </div>
                        <button type="button" class="btn btn-secondary form-control qr-generator"
                            onclick="generateQrCode()">Tạo mã QR</button>

                        <div class="qr-con text-center" style="display: none;">
                            <input type="hidden" class="form-control" id="generatedCode" name="maQR">
                            <p>Lưu ảnh mã QR của bạn.</p>
                            <img class="mb-4" src="" id="qrImg" alt="">
                        </div>
                        <div class="modal-footer modal-close" style="display: none;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-dark">Thêm vào danh sách</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateEmployeeModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="updateEmployee" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateEmployee">Chỉnh sửa thông tin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="./endpoint/update_employee.php" method="POST">
                        <input type="hidden" class="form-control" id="updateEmployeeId" name="idNhanVien">
                        <div class="form-group">
                            <label for="updateEmployeeName">Họ và tên đầy đủ:</label>
                            <input type="text" class="form-control" id="updateEmployeeName" name="hoTenNhanVien">
                        </div>

                        <div class="form-group">
                            <label for="updateEmployeeCode">Mã nhân viên:</label>
                            <input type="text" class="form-control" id="updateEmployeeCode" name="maNhanVien">
                        </div>

                        <div class="form-group">
                            <label for="updateEmployeePosition">Phòng ban:</label>
                            <input type="text" class="form-control" id="updateEmployeePosition" name="phongBan">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-dark">Sửa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>

    <script>
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            "lengthMenu": [1, 2, 3], // Chọn số lượng bản ghi hiển thị trên mỗi trang
            "pageLength": 3 // Số lượng bản ghi mặc định hiển thị trên mỗi trang
        });
    });

    function updateEmployee(id) {
        $("#updateEmployeeModal").modal("show");

        let updateEmployeeId = $("#employeeID-" + id).text();
        let updateEmployeeCode = $("#employeeCode-" + id).text();
        let updateEmployeeName = $("#employeeName-" + id).text();
        let updateEmployeePosition = $("#employeePosition-" + id).text();

        $("#updateEmployeeId").val(updateEmployeeId);
        $("#updateEmployeeCode").val(updateEmployeeCode);
        $("#updateEmployeeName").val(updateEmployeeName);
        $("#updateEmployeePosition").val(updateEmployeePosition);
    }

    function deleteEmployee(id) {
        if (confirm("Bạn có muốn xóa nhân viên này không?")) {
            window.location = "./endpoint/delete_employee.php?employee=" + id;
        }
    }

    function generateRandomCode(length) {
        const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        let randomString = '';

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            randomString += characters.charAt(randomIndex);
        }

        return randomString;
    }

    function generateQrCode() {
        const qrImg = document.getElementById('qrImg');

        let text = generateRandomCode(10);
        $("#generatedCode").val(text);

        if (text === "") {
            alert("Vui lòng nhập văn bản để tạo mã QR.");
            return;
        } else {
            const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}`;

            qrImg.src = apiUrl;
            document.getElementById('employeeCode').style.pointerEvents = 'none';
            document.getElementById('employeeName').style.pointerEvents = 'none';
            document.getElementById('employeePosition').style.pointerEvents = 'none';
            document.querySelector('.modal-close').style.display = '';
            document.querySelector('.qr-con').style.display = '';
            document.querySelector('.qr-generator').style.display = 'none';
        }
    }
    </script>

</body>

</html>