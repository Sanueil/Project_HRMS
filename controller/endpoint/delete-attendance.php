<?php
include ('../connect.php');

if (isset($_GET['attendance'])) {
    $attendance = $_GET['attendance'];

    try {

        $query = "DELETE FROM tbl_attendance WHERE tbl_attendance_id = '$attendance'";

        $stmt = $conn->prepare($query);

        $query_execute = $stmt->execute();

        if ($query_execute) {
            echo "
                <script>
                    alert('Attendance deleted successfully!');
                    window.location.href = '../../admin/home.php?user=admin';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Failed to delete attendance!');
                    window.location.href = '../../admin/home.php?user=admin';
                </script>
            ";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>