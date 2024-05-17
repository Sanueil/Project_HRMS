<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['maNhanVien'], $_POST['hoTenNhanVien'], $_POST['phongBan'])) {
        $employeeID = $_POST['idNhanVien'];
        $employeeCode = $_POST['maNhanVien'];
        $employeeName = $_POST['hoTenNhanVien'];
        $employeePosition = $_POST['phongBan'];

        try {
            $employee = $conn->prepare("UPDATE nhan_vien SET maNhanVien = :maNhanVien, hoTenNhanVien = :hoTenNhanVien, phongBan = :phongBan WHERE idNhanVien = :idNhanVien");
            
            $employee->bindParam(":idNhanVien", $employeeID, PDO::PARAM_STR); 
            $employee->bindParam(":maNhanVien", $employeeCode, PDO::PARAM_STR); 
            $employee->bindParam(":hoTenNhanVien", $employeeName, PDO::PARAM_STR); 
            $employee->bindParam(":phongBan", $employeePosition, PDO::PARAM_STR);

            $employee->execute();

            header("Location: http://localhost/qr-code-attendance-system/masterlist.php");

            exit();
        } catch (PDOException $e) {
            echo "Error:" . $e->getMessage();
        }

    } else {
        echo "
            <script>
                alert('Vui lòng điền đầy đủ thông tin!');
                window.location.href = 'http://localhost/qr-code-attendance-system/masterlist.php';
            </script>
        ";
    }
}
?>