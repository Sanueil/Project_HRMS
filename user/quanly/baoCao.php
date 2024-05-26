<!DOCTYPE html>
<html lang="vi">

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
        z-index: 8;
        display: none;
    }

    /* Spinner khi đang tải dữ liệu */
    .spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        width: 3rem;
        height: 3rem;
        z-index: 9;
        border: 0.4rem solid #f3f3f3;
        border-top: 0.4rem solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: none;
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }
    </style>
    <!-- Bao gồm thư viện Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Báo cáo hiệu suất và thống kê thời gian làm của nhân viên</h1>
        <!-- Form lọc -->
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

    <!-- Canvas để hiển thị biểu đồ thời gian làm việc -->
    <canvas id="workTimeChart"></canvas>

    <!-- Modal báo cáo -->
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
                    <!-- Nội dung báo cáo -->
                    <p>Thêm nội dung báo cáo ở đây...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeReportModal"
                        onclick="closeReportModal()">Đóng</button>
                    <!-- Nút xuất biểu đồ -->
                    <button type="button" class="btn btn-primary" id="exportChartButtonModal"
                        onclick="exportChart()">Xuất biểu đồ</button>
                </div>
            </div>
        </div>
    </div>
    <div id="overlay" class="overlay"></div>
    <div class="spinner" id="loadingSpinner"></div>

    <!-- Script -->
    <script>
    // Gọi hàm để lấy dữ liệu và vẽ biểu đồ khi trang được tải
    getAggregatedWorkTimeDataAndDrawChart();

    function showLoadingSpinner() {
        document.getElementById("loadingSpinner").style.display = "block";
    }

    function hideLoadingSpinner() {
        document.getElementById("loadingSpinner").style.display = "none";
    }

    function getAggregatedWorkTimeDataAndDrawChart() {
        showLoadingSpinner();
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "../../controller/get_aggregated_work_time_data.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                hideLoadingSpinner();
                if (xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    drawWorkTimeChart(data);
                } else {
                    alert("Không thể lấy dữ liệu. Vui lòng thử lại sau.");
                }
            }
        };
        xhr.send();
    }

    function drawWorkTimeChart(data) {
        var ctx = document.getElementById('workTimeChart').getContext('2d');
        var workTimeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.maNhanVien, // Sử dụng dữ liệu mã nhân viên từ server
                datasets: [{
                    label: 'Số lần chấm công',
                    data: data.soLanChamCong, // Sử dụng dữ liệu số lần chấm công từ server
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lần chấm công'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mã nhân viên'
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
        showLoadingSpinner();
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "../../controller/get_filtered_work_time_data.php?date=" + dateFilter, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                hideLoadingSpinner();
                if (xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    drawWorkTimeChart(data);
                } else {
                    alert("Không thể lấy dữ liệu lọc. Vui lòng thử lại sau.");
                }
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