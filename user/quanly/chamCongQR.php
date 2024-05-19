<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống chấm công bằng mã QR</title>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

    * {
        margin: 0;
        padding: 0;
        /* font-family: 'Poppins', sans-serif; */
    }

    body {
        background: #f3f3f9;
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
        width: 100%;
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
        width: 100%;
        margin-left: auto;
    }

    .attendance-list {
        width: 100%;
        /* Set table width to 100% of its container */
    }

    th,
    td {
        white-space: nowrap;
        /* Prevent text wrapping */
        overflow: hidden;
        /* Hide overflowing content */
        text-overflow: ellipsis;
        /* Optionally truncate text with ellipsis */
    }

    .table-sm th,
    .table-sm td {
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
        /* Adjust font size if needed */
        line-height: 1.5;
        /* Adjust line height for better spacing */
    }
    </style>
</head>

<body>
    <div class="main">

        <div class="attendance-container row">
            <div class="qr-container col-3">
                <div class="scanner-con">
                    <h5 class="text-center">Quét mã QR ở đây</h5>
                    <video id="interactive" class="viewport" width="100%">
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form action="../../controller/endpoint/add_attendance.php" method="POST">
                        <h4 class="text-center">Mã QR đã được quét!</h4>
                        <input type="hidden" id="detected-qr-code" name="qr_code">
                        <button type="submit" class="btn btn-dark form-control">Chấp nhận</button>
                    </form>
                </div>
            </div>

            <div class="attendance-list col-9">
                <h4>Danh sách chấm công</h4>
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
                                <!-- <th scope="col">Điều chỉnh</th> -->
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

    // function deleteAttendance(id) {
    //     if (confirm("Bạn có muốn loại bỏ chấm công này không?")) {
    //         window.location = "./endpoint/delete_attendance.php?attendance=" + id;
    //     }
    // }

    document.addEventListener('DOMContentLoaded', function() {
        // Lấy giờ hiện tại
        var currentTime = new Date();
        var currentHour = currentTime.getHours();
        var currentMinute = currentTime.getMinutes();

        // Lấy phần tử video của camera
        var videoElement = document.getElementById('interactive');
        var scannerContainer = document.querySelector('.scanner-con');

        // Kiểm tra xem có đến thời gian mở camera không
        if ((currentHour >= 8 && currentHour < 17) || (currentHour === 17 && currentMinute <= 10) || (
                currentHour >= 18 && currentHour < 23) || (currentHour === 23 && currentMinute <= 10)) {
            // Nếu trong khoảng thời gian cho phép mở camera, hiển thị camera và bắt đầu quét mã QR
            startScanner();
        } else {
            // Nếu không phải thời gian mở camera, ẩn camera
            videoElement.style.display = 'none';

            // Hiển thị thông báo
            var notificationElement = document.createElement('div');
            notificationElement.classList.add('alert', 'alert-warning', 'text-center');
            notificationElement.innerHTML =
                '<strong>Thông báo:</strong> Hiện không phải là thời gian để chấm công. Thời gian chấm công từ 8h00 - 17h10 và từ 18h00 - 23h10.';
            scannerContainer.appendChild(notificationElement);
        }
    });
    </script>
</body>

</html>