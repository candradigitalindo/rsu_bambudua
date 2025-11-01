// Basic Data Table
$(function () {
    $("#basic-datatable").DataTable({
        language: {
            paginate: {
                previous: "<i class='ri-arrow-left-s-line'>",
                next: "<i class='ri-arrow-right-s-line'>",
            },
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
    });
});

// Hide Search and Length
$(function () {
    $("#hideSearchExample").DataTable({
        searching: false,
        lengthChange: false,
        language: {
            paginate: {
                previous: "<i class='ri-arrow-left-s-line'>",
                next: "<i class='ri-arrow-right-s-line'>",
            },
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
    });
});

// Datatable with buttons
$(document).ready(function () {
    // Hanya jalankan jika table dengan id datatable-button ada
    if ($("#datatable-button").length > 0) {
        var table = $("#datatable-button").DataTable({
            lengthChange: false,
            buttons: ["copy", "excel", "pdf", "colvis"],
        });

        table.buttons().container().appendTo("#datatable-button_wrapper .col-md-6:eq(0)");
    }
});
