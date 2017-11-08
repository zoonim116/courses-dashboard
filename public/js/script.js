$(document).ready(function() {
    var table = $('#courses-list').DataTable( {
        "ajax": {
            url: "/course/all",
            dataSrc: ''
        },
        "columns": [
            { data: 'id' },
            { data: 'name' },
            { data: 'category' },
            { data: 'author' }
        ],
        "columnDefs": [ {
            "targets": -1,
            // "data": null,
            "defaultContent": "<button>Edit</button>"
        } ]
    } );

    $('div.dataTables_filter input').addClass('form-control');
    $('div.dataTables_length select').addClass('form-control');

} );