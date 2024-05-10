<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống chấm công bằng mã QR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

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

.attendance-container {
    height: 90%;
    width: 90%;
    border-radius: 20px;
    padding: 40px;
    background-color: rgba(255, 255, 255, 0.8);
}

.attendance-container>div {
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius: 10px;
    padding: 30px;
}

.attendance-container>div:last-child {
    width: 64%;
    margin-left: auto;
}
</style>

<body>
    <div class="main">

        <div class="attendance-container row">
            <div class="qr-container col-4">
                <div class="scanner-con">
                    <h5 class="text-center">Dùng mã QR của bạn để chấm công</h5>
                    <video id="interactive" class="viewport" width="100%">
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form action="../../controller/endpoint/add_attendance.php" method="POST">
                        <h4 class="text-center">Mã QR đã được quét!</h4>
                        <input type="hidden" id="detected-qr-code" name="qr_code">
                        <button type="submit" class="btn btn-dark form-control">Chấp nhận chấm công</button>
                    </form>
                </div>
            </div>

            <div class="attendance-list">
                <h4>Danh sách các nhân viên</h4>
                <div class="table-container table-responsive">
                    <table class="table text-center table-sm" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Mã nhân viên</th>
                                <th scope="col">Họ và tên</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Thời gian chấm công</th>
                                <th scope="col">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT cc.maChamCong, nv.maNhanVien, nv.hoTenNhanVien, pb.tenPhongBan, cc.thoiGianChamCong, cc.trangThai
                                FROM cham_cong cc
                                LEFT JOIN nhan_vien nv ON cc.maNhanVien = nv.maNhanVien
                                LEFT JOIN nhan_vien_phong_ban nvpb ON nv.maNhanVien = nvpb.maNhanVien
                                LEFT JOIN phong_ban pb ON nvpb.maPhongBan = pb.maPhongBan";

                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    $attendanceID = $row["maChamCong"];
                                    $employeeCode = $row["maNhanVien"];
                                    $employeeName = $row["hoTenNhanVien"];
                                    $employeePosition = $row["tenPhongBan"];
                                    $time = $row["thoiGianChamCong"];
                                    $status = $row["trangThai"];
                                    ?>
                                    <tr>
                                        <th scope="row"><?= $attendanceID ?></th>
                                        <td><?= $employeeCode ?></td>
                                        <td><?= $employeeName ?></td>
                                        <td><?= $employeePosition ?></td>
                                        <td><?= $time ?></td>
                                        <td><?= $status ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "Lỗi truy vấn: " . $stmt->error;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- instascan Js -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>
    let scanner;

    function startScanner() {
        scanner = new Instascan.Scanner({
            video: document.getElementById('interactive')
        });

        scanner.addListener('scan', function(content) {
            $("#detected-qr-code").val(content);
            console.log(content);
            scanner.stop();
            document.querySelector(".qr-detected-container").style.display = '';
            document.querySelector(".scanner-con").style.display = 'none';
        });

        Instascan.Camera.getCameras()
            .then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                    alert('No cameras found.');
                }
            })
            .catch(function(err) {
                console.error('Camera access error:', err);
                alert('Camera access error: ' + err);
            });
    }

    document.addEventListener('DOMContentLoaded', startScanner);

    function deleteAttendance(id) {
        if (confirm("Bạn có muốn loại bỏ chấm công này không?")) {
            window.location = "./endpoint/delete_attendance.php?attendance=" + id;
        }
    }
    </script>
</body>

</html>