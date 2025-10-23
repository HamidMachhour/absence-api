let chartOne, chartTwo, chartThree;

$(document).ready(function () {
    var fromDate = new Date();
    fromDate.setDate(fromDate.getDate() - 7); // Subtract 7 days from current date

    var toDate = new Date();

    // Format date to "YYYY-MM-DD" required by input type="date"
    var formattedFromDate = fromDate.toISOString().substr(0, 10);
    var formattedToDate = toDate.toISOString().substr(0, 10);
    /*
    document.getElementById('from').value = formattedFromDate;
    document.getElementById('to').value = formattedToDate;
    */

    $('#btn_filter').on('click', function () {
        getChartData();
    });

    $('#classe').change(function(){
        fetchStudents($('#classe').val());
    });
    getChartData();
});
