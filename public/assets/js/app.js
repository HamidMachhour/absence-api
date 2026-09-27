/**
 * Generates an array of RGBA colors.
 * @param {number} length The number of colors to generate.
 * @returns {Array<Object>} An array of objects, each with backgroundColor and borderColor.
 */
function generateColors(length) {
    const colors = [];
    for (let i = 0; i < length; i++) {
        // Generate random RGB values
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        // Construct RGBA string with a low opacity
        colors.push({
            backgroundColor: `rgba(${r}, ${g}, ${b}, 0.2)`,
            borderColor: `rgba(${r}, ${g}, ${b}, 1)`
        });
    }
    return colors;
}

/**
 * Creates or updates a chart.
 * @param {string} chartId The ID of the canvas element.
 * @param {Chart} chartInstance The existing chart instance (can be null).
 * @param {string} type The chart type (e.g., 'line', 'bar', 'pie').
 * @param {Array<string>} labels The labels for the chart.
 * @param {Array<Object>} datasets The datasets for the chart.
 * @param {Object} [options={}] Additional chart options.
 * @returns {Chart} The new or updated chart instance.
 */
function createOrUpdateChart(chartId, chartInstance, type, labels, datasets, options = {}) {
    if (chartInstance) {
        chartInstance.destroy();
    }
    const ctx = document.getElementById(chartId).getContext('2d');
    return new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: options
    });
}

/**
 * Reduces data by a given key and sums specified properties.
 * @param {Array<Object>} data The array of data objects.
 * @param {string} key The key to group by.
 * @param {Array<string>} propsToSum The properties to sum.
 * @param {Object} [propModifiers={}] Optional modifiers for properties.
 * @returns {Object} The reduced data.
 */
function reduceData(data, key, propsToSum, propModifiers = {}) {
    return data.reduce((acc, currentValue) => {
        const groupKey = typeof key === 'function' ? key(currentValue) : currentValue[key];
        if (!acc[groupKey]) {
            acc[groupKey] = {};
            propsToSum.forEach(prop => {
                acc[groupKey][prop] = 0;
            });
        }
        propsToSum.forEach(prop => {
            let value = currentValue[prop];
            if (propModifiers[prop]) {
                value = propModifiers[prop];
            }
            acc[groupKey][prop] += value;
        });
        return acc;
    }, {});
}

function getChartAbsenceData() {
    $.ajax({
        url: '/chart-absence',
        method: 'GET',
        datatype: 'json',
        data: {
            'classe': $("#classe").val() || null,
            'student': $("#student").val() || null,
            'from': $("#from").val() || null,
            'to': $('#to').val() || null
        },
        success: function (response) {
            // Access the absenceData from the response
            const absenceData = response.absenceData;

            // Mapping and reducing to sum nb_absences by date
            const reducedByDate = reduceData(absenceData, 'date', ['nb_absences', 'justifie', 'non_justifie', 'sum_plages'], { sum_plages: val => val / 60 });
            const dates = Object.keys(reducedByDate);
            const trendDatasets = [
                { label: VOCAB['absences'], data: dates.map(date => reducedByDate[date].nb_absences), backgroundColor: 'rgba(54, 162, 235, 0.2)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 },
                { label: VOCAB['absence_justified'], data: dates.map(date => reducedByDate[date].justifie), backgroundColor: 'rgba(75, 192, 192, 0.2)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 },
                { label: VOCAB['absence_not_justified'], data: dates.map(date => reducedByDate[date].non_justifie), backgroundColor: 'rgba(255, 99, 132, 0.2)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }
            ];
            chartOne = createOrUpdateChart('trendChart', chartOne, 'line', dates, trendDatasets);

            // Mapping and reducing to sum nb_absences by classe_name
            const reducedByClass = reduceData(absenceData, 'classe_name', ['nb_absences']);
            const classNames = Object.keys(reducedByClass);
            const absenceTotalsByClass = classNames.map(name => reducedByClass[name].nb_absences);
            const classColors = generateColors(classNames.length);
            const classDataset = [{ label: VOCAB['compare_sections'], data: absenceTotalsByClass, backgroundColor: classColors.map(c => c.backgroundColor), borderColor: classColors.map(c => c.borderColor), borderWidth: 1 }];
            chartTwo = createOrUpdateChart('classesAbsenceChart', chartTwo, 'pie', classNames, classDataset);

            // Mapping and reducing to sum nb_absences by student
            const reducedByStudent = reduceData(absenceData, item => `${item.firstName} ${item.lastName}`, ['nb_absences']);
            const studentNames = Object.keys(reducedByStudent);
            const absenceTotalsByStudent = studentNames.map(name => reducedByStudent[name].nb_absences);
            const studentColors = generateColors(studentNames.length);
            const studentDataset = [{ label: VOCAB['students'], data: absenceTotalsByStudent, backgroundColor: studentColors.map(c => c.backgroundColor), borderColor: studentColors.map(c => c.borderColor), borderWidth: 1 }];
            chartThree = createOrUpdateChart('studentsAbsenceChart', chartThree, 'polarArea', studentNames, studentDataset);
        },
        error: function (error) {
            console.log(error);
        }
    });
}
 
// Define the function to fetch students based on the selected class
function fetchStudents(classeId) {
    if (classeId) {
        // Make AJAX request to fetch students for the selected class
        $.ajax({
            url: '/get-students/' + classeId, // Replace with your route for fetching students
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                // Clear previous options
                $('#student').empty();
                // Add default empty option
                $('#student').append('<option value=""></option>');
                // Add students as options
                $.each(response.students, function (key, value) {
                    $('#student').append('<option value="' + value.student_id + '">' + value.firstName + ' ' + value.lastName + '</option>');
                });
            },
            error: function (error) {
                console.log(error);
            }
        });
    } else {
        // If no class is selected, clear the students dropdown
        $('#student').empty();
        $('#student').append('<option value=""></option>');
    }
}

function getStudentChartData() {
    const ajaxOptions = {
        url: '/chart-absence',
        method: 'GET',
        dataType: 'json',
        data: {
            'classe': $("#classe").val() || null,
            'student': $("#student").val() || null,
            'from': $("#from").val() || null,
            'to': $('#to').val() || null
        }
    };

    $.ajax(ajaxOptions).done(function(response) {
        const absenceData = response.absenceData;
        const reducedByDate = reduceData(absenceData, 'date', ['nb_absences', 'justifie', 'non_justifie', 'sum_plages'], { sum_plages: val => val / 60 });
        const dates = Object.keys(reducedByDate);

        const trendDatasets = [
            { label: VOCAB['absences'], data: dates.map(date => reducedByDate[date].nb_absences), backgroundColor: 'rgba(54, 162, 235, 0.2)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 },
            { label: VOCAB['absence_justified'], data: dates.map(date => reducedByDate[date].justifie), backgroundColor: 'rgba(75, 192, 192, 0.2)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 },
            { label: VOCAB['absence_not_justified'], data: dates.map(date => reducedByDate[date].non_justifie), backgroundColor: 'rgba(255, 99, 132, 0.2)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }
        ];
        chartOne = createOrUpdateChart('trendChart', chartOne, 'bar', dates, trendDatasets);

        const plagesDataset = [{ label: VOCAB['plages'], data: dates.map(date => reducedByDate[date].sum_plages), backgroundColor: 'rgba(255, 159, 64, 0.2)', borderColor: 'rgba(255, 159, 64, 1)', borderWidth: 1 }];
        chartTwo = createOrUpdateChart('plagesTrendChart', chartTwo, 'bar', dates, plagesDataset);
    }).fail(function(error) {
        console.log(error);
    });
}

function getChartMisconductData() {
    const ajaxOptions = {
        url: '/chart-report',
        method: 'GET',
        dataType: 'json',
        data: {
            'classe': $("#classe").val() || null,
            'student': $("#student").val() || null,
            'from': $("#from").val() || null,
            'to': $('#to').val() || null
        }
    };

    $.ajax(ajaxOptions).done(function(response) {
        const reportData = response.reportData;

        // 1. Trend chart by date and status
        const reducedByDate = reportData.reduce((acc, item) => {
            if (!acc[item.date]) {
                acc[item.date] = { total: 0 };
            }
            acc[item.date].total += item.nb_reports;
            acc[item.date][item.status] = (acc[item.date][item.status] || 0) + item.nb_reports;
            return acc;
        }, {});

        const dates = Object.keys(reducedByDate).sort();
        const statuses = [...new Set(reportData.map(item => item.status))];
        const statusColors = generateColors(statuses.length);

        const trendDatasets = statuses.map((status, index) => ({
            label: VOCAB[status] || status,
            data: dates.map(date => reducedByDate[date][status] || 0),
            backgroundColor: statusColors[index].backgroundColor,
            borderColor: statusColors[index].borderColor,
            borderWidth: 1
        }));

        chartFour = createOrUpdateChart('reportTrendChart', chartFour, 'line', dates, trendDatasets);

        // 2. Comparison chart by class
        const reducedByClass = reduceData(reportData, 'classe_name', ['nb_reports']);
        const classNames = Object.keys(reducedByClass);
        const reportTotalsByClass = classNames.map(name => reducedByClass[name].nb_reports);
        const classColors = generateColors(classNames.length);
        const classDataset = [{
            label: VOCAB['compare_sections'], // Assuming VOCAB is available
            data: reportTotalsByClass,
            backgroundColor: classColors.map(c => c.backgroundColor),
            borderColor: classColors.map(c => c.borderColor),
            borderWidth: 1
        }];
        chartFive = createOrUpdateChart('classesReportsChart', chartFive, 'pie', classNames, classDataset);

        // 3. Comparison chart by student
        const reducedByStudent = reduceData(reportData, item => `${item.firstName} ${item.lastName}`, ['nb_reports']);
        const studentNames = Object.keys(reducedByStudent);
        const reportTotalsByStudent = studentNames.map(name => reducedByStudent[name].nb_reports);
        const studentColors = generateColors(studentNames.length);
        const studentDataset = [{ label: VOCAB['students'], data: reportTotalsByStudent, backgroundColor: studentColors.map(c => c.backgroundColor), borderColor: studentColors.map(c => c.borderColor), borderWidth: 1 }];
        chartSix = createOrUpdateChart('studentsReportsChart', chartSix, 'polarArea', studentNames, studentDataset);

    }).fail(function(error) {
        console.log(error);
    });
}
