<?php
$dbs = new Database();
$db = $dbs->connect();
// Kiểm tra xem người dùng có yêu cầu thêm mới thông báo không
if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $tieu_de = $_POST['tieu_de'];
    $noi_dung = $_POST['noi_dung'];

    // Kiểm tra xem tiêu đề và nội dung có được cung cấp không
    if (!empty($tieu_de) && !empty($noi_dung)) {
        // Thực hiện truy vấn để thêm thông báo mới vào cơ sở dữ liệu
        $query = "INSERT INTO thong_bao (tieu_de, noi_dung) VALUES ('$tieu_de', '$noi_dung')";
        $result = $p->query($query);

        if ($result) {
            // Nếu thêm thành công, chuyển hướng về trang quản lý thông báo
            header("Location: home.php?user=admin&table=thongbao");
            exit;
        } else {
            // Nếu có lỗi xảy ra, hiển thị thông báo lỗi
            $error_message = "Đã có lỗi xảy ra khi thêm thông báo. Vui lòng thử lại sau.";
        }
    } else {
        // Nếu tiêu đề hoặc nội dung trống, hiển thị thông báo lỗi
        $error_message = "Vui lòng nhập đầy đủ tiêu đề và nội dung của thông báo.";
    }
}
function deleteRecord($id)
{
    global $db;
    // Viết truy vấn SQL để xóa bản ghi dựa trên ID
    $sql = "DELETE FROM thong_bao WHERE id = $id";

    // Thực thi truy vấn
    if ($db->query($sql) === TRUE) {
        // Hiển thị thông báo xóa thành công bằng hộp thoại JavaScript
        echo "<script>alert('Xóa thông báo thành công.');</script>";
        echo "<script>window.location.href = 'home.php?user=admin&table=thongbao';</script>";
        exit;
    } else {
        // Hiển thị thông báo lỗi bằng hộp thoại JavaScript
        echo "<script>alert('Lỗi: " . $db->error . "');</script>";
    }
}

if (isset($_GET['id'])) {
    // Lấy ID từ tham số truyền vào
    $id = $_GET['id'];
    // Gọi hàm xóa bản ghi
    deleteRecord($id);
}
// Lấy danh sách các thông báo từ cơ sở dữ liệu
$query = "SELECT * FROM thong_bao";
$result = $p->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thông báo</title>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Quản lý thông báo</h1>
        <!-- Form thêm mới thông báo -->
        <form method="POST">
            <div class="mb-3">
                <label for="tieu_de" class="form-label">Tiêu đề:</label>
                <!-- Thêm gợi ý vào tiêu đề -->
                <input type="text" class="form-control" id="tieu_de" name="tieu_de" required
                    placeholder="Nhập tiêu đề thông báo...">
            </div>
            <div class="mb-3">
                <label for="noi_dung" class="form-label">Nội dung:</label>
                <!-- Thêm gợi ý vào nội dung -->
                <textarea class="form-control" id="noi_dung" name="noi_dung" required
                    placeholder="Nhập nội dung thông báo..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Thêm mới</button>
        </form>

        <?php if (isset($error_message)) { ?>
        <p class="text-danger"><?php echo $error_message; ?></p>
        <?php } ?>

        <!-- Danh sách thông báo -->
        <h2 class="mt-5">Danh sách thông báo</h2>
        <ul class="list-group">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <li class="list-group-item">
                <h3><?php echo $row['tieu_de']; ?></h3>
                <p><?php echo $row['noi_dung']; ?></p>
                <!-- Thêm nút xóa -->
                <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-danger btn-sm">Xóa</button>
            </li>
            <?php } ?>
        </ul>
    </div>
</body>
<script>
function confirmDelete(id) {
    if (confirm("Bạn có chắc chắn muốn xóa thông báo này không?")) {
        // Redirect or perform delete action here
        var urlWithId = window.location.href + "&id=" + id;
        window.location.href = urlWithId;
    }
}
</script>

</html>