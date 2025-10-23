// Function to generate an array of colors
function generateColors(length) {
    var colors = [];
    for (var i = 0; i < length; i++) {
        // Generate random RGB values
        var r = Math.floor(Math.random() * 256);
        var g = Math.floor(Math.random() * 256);
        var b = Math.floor(Math.random() * 256);
        // Construct RGBA string with a low opacity
        var color = 'rgba(' + r + ', ' + g + ', ' + b + ', 0.2)';
        colors.push(color);
    }
    return colors;
}

function reduceAbsenceDataByDate(absenceData) {
    // Initialize an empty dictionary to store reduced values
    var reducedValuesByDate = {};

    // Loop through each item in absenceData
    absenceData.forEach(function (currentValue) {
        // If the date doesn't exist in the reducedValuesByDate dictionary, initialize it with all zeros
        if (!reducedValuesByDate[currentValue.date]) {
            reducedValuesByDate[currentValue.date] = {
                nb_absences: 0,
                justifie: 0,
                non_justifie: 0,
                sum_plages:0
            };
        }

        // Accumulate nb_absences, justifie, and non_justifie values for each date
        reducedValuesByDate[currentValue.date].nb_absences += currentValue.nb_absences;
        reducedValuesByDate[currentValue.date].justifie += currentValue.justifie;
        reducedValuesByDate[currentValue.date].non_justifie += currentValue.non_justifie;
        reducedValuesByDate[currentValue.date].sum_plages += currentValue.sum_plages/60;
    });

    return reducedValuesByDate;
}
function trendChart(chartId, type, labels, nb_absences, absences_justifie, absences_non_justifie) {
    var ctx = document.getElementById(chartId).getContext('2d');
    return new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: __('vocab.absence'),
                data: nb_absences,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'مبرر',
                data: absences_justifie,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 1
            },
            {
                label: 'غير مبرر',
                data: absences_non_justifie,
                backgroundColor: 'rgba(255, 255, 153, 0.7)',
                borderColor: 'rgba(255, 255, 153, 0.7)',
                borderWidth: 1
            }
            ]
        },
    });
}
function plagesTrendChart(chartId, type, labels, sum_plages) {
    var ctx = document.getElementById(chartId).getContext('2d');
    return new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: 'ساعات الغياب',
                data: sum_plages,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            ]
        },
    });
}
function comparingChart(chartId, type, label, labels, data) {
    // Get array of colors
    var backgroundColors = generateColors(labels.length);
    var ctx = document.getElementById(chartId).getContext('2d');
    return new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(color => color.replace('0.2',
                    '1')),
                borderWidth: 1
            }]
        },

        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }

    });
}
function getChartData() {
    $.ajax({
        url: '/chart-absence',
        method: 'GET',
        datatype: 'json',
        data: {
            'classe': $("#classe").val(),
            'student': $("#student").val(),
            'from': $("#from").val(),
            'to': $('#to').val()
        },
        success: function (response) {
            // Access the absenceData from the response
            var absenceData = response.absenceData;

            // Mapping and reducing to sum nb_absences by date
            var reducedValues = reduceAbsenceDataByDate(absenceData);

            var dates = Object.keys(reducedValues);

            var nb_absences = dates.map(function (date) {
                return reducedValues[date].nb_absences;
            });

            var absences_justifie = dates.map(function (date) {
                return reducedValues[date].justifie;
            });

            var absences_non_justifie = dates.map(function (date) {
                return reducedValues[date].non_justifie;
            });

            var sum_plages = dates.map(function (date) {
                return reducedValues[date].sum_plages;
            });

            if (chartOne) {
                chartOne.destroy();
            }
            chartOne = trendChart('trendChart', 'line', dates, nb_absences, absences_justifie, absences_non_justifie);

            // Mapping and reducing to sum nb_absences by classe_name
            var summedAbsencesByClass = absenceData.reduce(function (acc, currentValue) {
                if (!acc[currentValue.classe_name]) {
                    acc[currentValue.classe_name] = 0;
                }
                acc[currentValue.classe_name] += currentValue.nb_absences;
                return acc;
            }, {});
            // Get keys array (class names)
            var classNames = Object.keys(summedAbsencesByClass);

            // Get values array (sum of absences)
            var absenceTotals = Object.values(summedAbsencesByClass);


            if (chartTwo) {
                chartTwo.destroy();
            }
            chartTwo = comparingChart('classesAbsenceChart', 'pie', __('vocab.compare_sections'), classNames, absenceTotals);


            var summedAbsencesByStudent = absenceData.reduce(function (acc, currentValue) {
                if (!acc[currentValue.firstName + ' ' + currentValue.lastName]) {
                    acc[currentValue.firstName + ' ' + currentValue.lastName] = 0;
                }
                acc[currentValue.firstName + ' ' + currentValue.lastName] += currentValue.nb_absences;
                return acc;
            }, {});

             // Get keys array
             var studentNames = Object.keys(summedAbsencesByStudent);

             // Get values array
             var studentsAbsences = Object.values(summedAbsencesByStudent);
            if (chartThree) {
                chartThree.destroy();
            }
            chartThree = comparingChart('studentsAbsenceChart', 'polarArea', __('vocab.students'), studentNames, studentsAbsences);
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
    $.ajax({
        url: '/chart-absence',
        method: 'GET',
        datatype: 'json',
        data: {
            'classe': $("#classe").val(),
            'student': $("#student").val(),
            'from': $("#from").val(),
            'to': $('#to').val()
        },
        success: function (response) {
            // Access the absenceData from the response
            var absenceData = response.absenceData;

            // Mapping and reducing to sum nb_absences by date
            var reducedValues = reduceAbsenceDataByDate(absenceData);

            var dates = Object.keys(reducedValues);

            var nb_absences = dates.map(function (date) {
                return reducedValues[date].nb_absences;
            });

            var absences_justifie = dates.map(function (date) {
                return reducedValues[date].justifie;
            });

            var absences_non_justifie = dates.map(function (date) {
                return reducedValues[date].non_justifie;
            });

            var sum_plages = dates.map(function (date) {
                return reducedValues[date].sum_plages;
            });

            if (chartOne) {
                chartOne.destroy();
            }
            chartOne = trendChart('trendChart', 'bar', dates, nb_absences, absences_justifie, absences_non_justifie);


            if (chartTwo) {
                chartTwo.destroy();
            }
            chartTwo = plagesTrendChart('plagesTrendChart', 'bar', dates, sum_plages);

        },
        error: function (error) {
            console.log(error);
        }
    });
}
