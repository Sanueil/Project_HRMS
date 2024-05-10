<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá và thưởng phạt</title>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Đánh giá và thưởng phạt cho nhân viên</h1>
        <form>
            <div class="form-group">
                <label for="maNhanVien">Mã nhân viên:</label>
                <input type="text" class="form-control" id="maNhanVien" name="maNhanVien"
                    placeholder="Nhập mã nhân viên">
            </div>
            <div class="form-group">
                <label for="tenNhanVien">Tên nhân viên:</label>
                <input type="text" class="form-control" id="tenNhanVien" name="tenNhanVien"
                    placeholder="Nhập tên nhân viên">
            </div>
            <div class="form-group">
                <label for="danhGia">Đánh giá:</label>
                <select class="form-control" id="danhGia" name="danhGia">
                    <option value="tich_cuc">Tích cực</option>
                    <option value="trung_binh">Trung bình</option>
                    <option value="kem">Kém</option>
                </select>
            </div>
            <div class="form-group">
                <label for="lyDoPhat">Lý do phạt:</label>
                <textarea class="form-control" id="lyDoPhat" name="lyDoPhat" rows="3"
                    placeholder="Nhập lý do phạt"></textarea>
            </div>
            <div class="form-group">
                <label for="mucThuongPhat">Mức thưởng/phạt:</label>
                <input type="number" class="form-control" id="mucThuongPhat" name="mucThuongPhat"
                    placeholder="Nhập mức thưởng/phạt">
            </div>
            <button type="submit" class="btn btn-primary">Xác nhận</button>
        </form>
    </div>

</body>

</html>