<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin lương của quản lý</title>
    <style>
    .modal {
        overflow: auto;
    }

    th,
    td {
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Thông tin lương của quản lý</h1>
        <div class="info">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>Mã nhân viên</th>
                        <th>Mức lương cơ bản</th>
                        <th>Phụ cấp</th>
                        <th>Thưởng</th>
                        <th>Thuế thu nhập cá nhân</th>
                        <th>Tổng lương</th>
                        <th>Ngày thanh toán</th>
                        <th>Bảo hiểm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM luong";
                    $result = mysqli_query($db, $query);
                    if ($result->num_rows > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Hiển thị thông tin lương của nhân viên
                            echo "<tr>";
                            echo "<td class='edit-luong' data-toggle='modal' data-target='#editSalaryModal'>" . $row['maNhanVien'] . "</td>";
                            echo "<td>" . number_format($row['mucLuongCoBan'], 0, '.', ',') . "</td>";
                            echo "<td>" . number_format($row['phuCap'], 0, '.', ',') . "</td>";
                            echo "<td>" . number_format($row['thuong'], 0, '.', ',') . "</td>";
                            echo "<td>" . number_format($row['thueThuNhapCaNhan'], 0, '.', ',') . " %" . "</td>";
                            echo "<td>" . number_format($row['tongLuong'], 0, '.', ',') . "</td>";
                            echo "<td>" . $row['ngayThanhToan'] . "</td>";
                            echo "<td>" . ($row['baoHiem'] == 1 ? 'Có' : 'Không') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Không tìm thấy thông tin lương cho nhân viên này.</td></tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
    <?php
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    ?>
    <!-- Modal Sửa thông tin lương -->
    <div class="modal fade" id="editSalaryModal" tabindex="-1" role="dialog" aria-labelledby="editSalaryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSalaryModalLabel">Sửa thông tin lương</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editSalaryForm" class="modal-body" method="post" action="../../controller/editLuong.php">
                    <input type="hidden" id="maLuong" name="maLuong" value="<?php echo $row['maLuong']; ?>">
                    <div class="form-group">
                        <label for="salaryInput">Mức lương cơ bản:</label>
                        <input type="text" id="salaryInput" name="salary" class="form-control"
                            value='<?php echo number_format($row['mucLuongCoBan'], 0, '.', ',')?>' readonly>
                    </div>
                    <div class="form-group">
                        <label for="phuCap">Phụ cấp:</label>
                        <input type="text" id="phuCap" name="phuCap" class="form-control form-control-sm"
                            value='<?php echo number_format($row['phuCap'], 0, '.', ',')?>' readonly>
                    </div>
                    <div class="form-group">
                        <label for="bonus">Thưởng:</label>
                        <input type="text" id="bonus" name="bonus" class="form-control form-control-sm"
                            value='<?php echo number_format($row['thuong'], 0, '.', ',')?>' placeholder="VD:100000">
                    </div>
                    <div class="form-group">
                        <label for="tax">Thuế thu nhập cá nhân:</label>
                        <input type="text" id="tax" name="tax" class="form-control"
                            value='<?php echo number_format($row['thueThuNhapCaNhan'], 0, '.', ',')?>%' readonly>
                    </div>
                    <div class="form-group">
                        <label for="insuranceInput">Bảo hiểm:</label>
                        <input type="text" id="insuranceInput" name="insurance" class="form-control"
                            value="<?php echo ($row['baoHiem'] == 1) ? 'Có' : 'Không'; ?>" readonly>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>