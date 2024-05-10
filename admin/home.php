<?php 
require_once "include/header.php";
?>
<script>
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

    if (tableParam === 'users') {
        showAccounts('accountTable');
    } else if (tableParam === 'department') {
        showAccounts('addAccountTable');
    } else if (tableParam === 'dsUsers') {
        showAccounts('dsUsersTable');
    } else if (tableParam === 'addUser') {
        showAccounts('addUser');
    } else if (tableParam === 'add') {
        showAccounts('addAccountTable');
    } else if (tableParam === 'createLuong') {
        showAccounts('createLuong');
    } else if (tableParam === 'dsLuong') {
        showAccounts('dsLuong');
    } else if (tableParam === 'thongbao') {
        showAccounts('thongbao');
    } else if (tableParam === 'listqr') {
        showAccounts('listqr');
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