<?php
include("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_name'], $_POST['course_section'])) {
        $studentName = $_POST['student_name'];
        $studentCourse = $_POST['course_section'];
        $generatedCode = $_POST['generated_code'];

        try {

            $stmt = $conn->prepare("INSERT INTO tbl_student (student_name, course_section, generated_code) VALUES (:student_name, :course_section, :generated_code)");
            
            $stmt->bindParam(":student_name", $studentName, PDO::PARAM_STR); 
            $stmt->bindParam(":course_section", $studentCourse, PDO::PARAM_STR);
            $stmt->bindParam(":generated_code", $generatedCode, PDO::PARAM_STR);

            $stmt->execute();

            header("Location: ../../admin/home.php?user=admin&table=listqr");

            exit();
        } catch (PDOException $e) {
            echo "Error:" . $e->getMessage();
        }

    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                window.location.href = '../../admin/home.php?user=admin&table=listqr';
            </script>
        ";
    }
}
?>