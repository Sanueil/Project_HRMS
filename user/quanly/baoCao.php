<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo hiệu suất và thống kê thời gian làm</title>
    <style>
    /* Lớp overlay */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        /* Màu đen với độ trong suốt 50% */
        z-index: 8;
        /* Z-index nhỏ hơn form để nó nằm dưới form */
        display: none;
        /* Ẩn mặc định */
    }
    </style>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Báo cáo hiệu suất và thống kê thời gian làm của nhân viên</h1>
        <!-- Filter form -->
        <div class="row align-items-center">
            <div class="col-auto">
                <form id="filterForm">
                    <div class="form-row align-items-center">
                        <label for="dateFilter" class="col-auto">Chọn ngày:</label>
                        <div class="col-auto">
                            <input type="date" id="dateFilter" name="dateFilter" class="form-control mr-2"
                                style="max-width: 200px;">
                        </div>
                        <div class="col-auto">
                            <button type="button" onclick="filterWorkTime()" class="btn btn-primary">Lọc</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col"></div> <!-- Thêm một cột trống để căn chỉnh nút "Xuất biểu đồ" sang phải -->
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="exportChartButton" onclick="exportChart()">Xuất biểu
                    đồ</button>
            </div>
        </div>
    </div>

    <!-- Canvas for displaying work time chart -->
    <canvas id="workTimeChart"></canvas>

    <!-- Report Modal -->
    <div class="modal" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Báo cáo hiệu suất và thống kê thời gian làm</h5>
                    <button type="button" class="close" onclick="closeReportModal()">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your report content goes here -->
                    <p>Thêm nội dung báo cáo ở đây...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeReportModal"
                        onclick="closeReportModal()">Đóng</button>
                    <!-- Button for exporting chart -->
                    <button type="button" class="btn btn-primary" id="exportChartButton" onclick="exportChart()">Xuất
                        biểu đồ</button>
                </div>
            </div>
        </div>
    </div>
    <div id="overlay" class="overlay"></div>

    <!-- Script -->
    <script>
    // Gọi hàm để lấy dữ liệu và vẽ biểu đồ khi trang được tải
    getWorkTimeDataAndDrawChart();

    // Thiết lập ngày mặc định cho bộ lọc
    setDefaultDateFilter();

    function closeReportModal() {
        document.getElementById("reportModal").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }

    function getWorkTimeDataAndDrawChart() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "../../controller/get_work_time_data.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var data = JSON.parse(xhr.responseText);
                drawWorkTimeChart(data);
            }
        };
        xhr.send();
    }

    function drawWorkTimeChart(data) {
        var ctx = document.getElementById('workTimeChart').getContext('2d');
        var workTimeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.dates, // Sử dụng dữ liệu ngày từ server
                datasets: [{
                    label: 'Số lượng nhân viên chấm công',
                    data: data.numEmployees, // Sử dụng dữ liệu số lượng nhân viên từ server
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Số lượng nhân viên'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Ngày'
                        }
                    }
                }
            }
        });
    }

    function filterWorkTime() {
        var dateFilter = document.getElementById("dateFilter").value;
        // Gọi hàm để lấy dữ liệu và vẽ biểu đồ với bộ lọc đã chọn
        getFilteredWorkTimeDataAndDrawChart(dateFilter);
    }

    function getFilteredWorkTimeDataAndDrawChart(dateFilter) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "../../controller/get_filtered_work_time_data.php?date=" + dateFilter, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var data = JSON.parse(xhr.responseText);
                drawWorkTimeChart(data);
            }
        };
        xhr.send();
    }

    function setDefaultDateFilter() {
        // Thiết lập ngày mặc định là ngày hôm nay cho bộ lọc
        var today = new Date();
        var defaultDate = today.toISOString().split('T')[0]; // Lấy định dạng yyyy-mm-dd
        document.getElementById("dateFilter").value = defaultDate;
    }

    function exportChart() {
        var canvas = document.getElementById('workTimeChart');
        var currentDate = new Date().toISOString().slice(0, 10); // Lấy ngày hiện tại, định dạng yyyy-mm-dd
        var fileName = 'work_time_chart_' + currentDate + '.png'; // Tạo tên file với thời gian
        var url = canvas.toDataURL('image/png');
        var link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    </script>
</body>

</html>