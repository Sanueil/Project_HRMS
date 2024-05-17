<?php
include ('../conn/conn.php');

if (isset($_GET['employee'])) {
    $employeeId = $_GET['employee']; // Đổi tên biến này thành $employeeId hoặc tên khác phù hợp

    try {
        // Xóa bản ghi có id là $employeeId
        $query_delete = "DELETE FROM nhan_vien WHERE idNhanVien = :employeeId";
        $deleteEmployee = $conn->prepare($query_delete);
        $deleteEmployee->bindParam(':employeeId', $employeeId);
        $query_delete_execute = $deleteEmployee->execute();

        if ($query_delete_execute) {

            echo "
                <script>
                    alert('Xóa nhân viên thành công!');
                    window.location.href = 'http://localhost/qr-code-attendance-system/masterlist.php';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Xóa nhân viên thất bại!');
                    window.location.href = 'http://localhost/qr-code-attendance-system/masterlist.php';
                </script>
            ";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>