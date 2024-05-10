<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phê Duyệt Bảng Chấm Công Nhân Viên</title>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Phê Duyệt Bảng Chấm Công Nhân Viên</h1>
        <div class="departments">
            <h2>Danh sách phê duyệt</h2>
            <form method="GET" action="home.php" class="mb-3">
                <div class="input-group input-group-sm">
                    <input type="hidden" name="user" value="<?php echo $_GET['user']; ?>">
                    <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm...">
                    <button type="submit" class="btn btn-primary">Tìm</button>
                </div>
            </form>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>Mã nhân viên</th>
                        <th>Tên Nhân Viên</th>
                        <th>Trạng Thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dữ liệu chấm công sẽ được thêm vào đây từ phía server -->
                    <?php
                    $search_query = "";
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_query = " WHERE maNhanVien LIKE '%$search%' OR tenPhongBan LIKE '%$search%'";
                    }
                    // Truy vấn cơ sở dữ liệu để lấy dữ liệu chấm công
                    $query = "SELECT cc.*, nv.hoTenNhanVien 
                        FROM cham_cong cc 
                        INNER JOIN nhan_vien_phong_ban nvpb ON cc.maNhanVien = nvpb.maNhanVien 
                        INNER JOIN nhan_vien nv ON cc.maNhanVien = nv.maNhanVien
                        ORDER BY nv.maNhanVien 
                        ";
                    $result = mysqli_query($db, $query);

                    // Hiển thị dữ liệu trên trang web
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['maNhanVien'] . "</td>";
                            echo "<td>" . $row['hoTenNhanVien'] . "</td>";
                            echo "<td>" . $row['trangThai'];
                             // Kiểm tra nếu nhân viên chưa có trạng thái, hiển thị nút phê duyệt và từ chối
                             if ($row['trangThai'] == '') {
                                echo "<form action='../../controller/updateTrangThai.php' method='post'>";
                                echo "<input type='hidden' name='maNhanVien' value='" . $row['maNhanVien'] . "'>";
                                echo "<button type='submit' name='approve' class='btn btn-success'>Phê duyệt</button>";
                                echo "<button type='submit' name='reject' class='btn btn-danger'>Từ chối</button>";
                                echo "</form></td>";
                            } else {
                                // Nếu nhân viên đã có trạng thái, không hiển thị nút phê duyệt và từ chối
                                echo "<td></td>";
                            }
                            echo "<td><button type='button' class='btn btn-primary' 
                            data-toggle='modal' data-target='#attendanceModal'>Xem chi tiết</button>";

                           
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Không có dữ liệu chấm công</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-labelledby="attendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Thông tin chấm công của nhân viên</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="attendanceInfo">
                    <!-- Nội dung để hiển thị thông tin chấm công -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>