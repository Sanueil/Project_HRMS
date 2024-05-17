<?php
include ('../conn/conn.php');

if (isset($_GET['attendance'])) {
    $attendance = $_GET['attendance'];

    try {

        $query = "DELETE FROM cham_cong WHERE maChamCong = :attendance";

        $employee = $conn->prepare($query);
        $employee->bindParam(':attendance', $attendance);
        $query_execute = $employee->execute();

        if ($query_execute) {
            echo "
                <script>
                    alert('Xóa chấm công thành công!');
                    window.location.href = 'http://localhost/qr-code-attendance-system/index.php';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Xóa chấm công thất bại!');
                    window.location.href = 'http://localhost/qr-code-attendance-system/index.php';
                </script>
            ";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>