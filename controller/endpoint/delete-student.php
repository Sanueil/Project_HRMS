<?php
include ('../connect.php');

if (isset($_GET['student'])) {
    $student = $_GET['student'];

    try {

        $query = "DELETE FROM tbl_student WHERE tbl_student_id = '$student'";

        $stmt = $conn->prepare($query);

        $query_execute = $stmt->execute();

        if ($query_execute) {
            echo "
                <script>
                    alert('Student deleted successfully!');
                    window.location.href = '../../admin/home.php?user=admin&table=listqr';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Failed to delete student!');
                    window.location.href = '../../admin/home.php?user=admin&table=listqr';
                </script>e
            ";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>