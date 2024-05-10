<?php 
require_once "include/header.php";
?>
<!-- JavaScript -->
<script>
function toggleDropdown(event) {
    event.preventDefault(); // Ngăn chặn sự kiện mặc định của liên kết
    var target = event.target;
    var submenu = target.nextElementSibling;
    target.classList.toggle("active"); // Thêm hoặc loại bỏ class active cho liên kết được nhấp
    submenu.classList.toggle("active"); // Thêm hoặc loại bỏ class active cho submenu tương ứng
}

function showAccounts(tableIdToShow) {
    // Ẩn tất cả các bảng trước khi hiển thị bảng được chọn
    var tables = document.getElementsByClassName("data-table");
    for (var i = 0; i < tables.length; i++) {
        tables[i].style.display = "none";
    }

    // Hiển thị bảng được chọn
    var tableToShow = document.getElementById(tableIdToShow);
    if (tableToShow) {
        tableToShow.style.display = "table";
    }
}

document.addEventListener("DOMContentLoaded", function() {
    // Ẩn tất cả các bảng với lớp "data-table" ban đầu
    var tables = document.getElementsByClassName("data-table");
    for (var i = 0; i < tables.length; i++) {
        tables[i].style.display = "none";
    }

    // Kiểm tra tham số 'table' từ URL và hiển thị bảng tương ứng
    var urlParams = new URLSearchParams(window.location.search);
    var tableParam = urlParams.get('table');

    if (tableParam === 'department') {
        showAccounts('department');
    } else if (tableParam === 'chamCongQR') {
        showAccounts('chamCongQR');
    } else if (tableParam === 'pheDuyet') {
        showAccounts('pheDuyet');
    } else if (tableParam === 'luong') {
        showAccounts('luong');
    } else if (tableParam === 'baoCao') {
        showAccounts('baoCao');
    } else if (tableParam === 'danhGia') {
        showAccounts('danhGia');
    } else if (tableParam === 'profile') {
        showAccounts('profile');
    } else if (tableParam === 'add') {
        showAccounts('addAccountTable');
    } else if (tableParam === 'setting') {
        showAccounts('setting');
    } else {
        showAccounts('defaultContent');
    }
});
</script>
<?php 
require_once "include/footer.php";
?>