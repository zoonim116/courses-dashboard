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
    });

    $('.update-thumbnail').on('click', function () {
        $('#course-cover').trigger('click');
    });

    function upload(file) {

        var xhr = new XMLHttpRequest();

        // обработчик для закачки
        xhr.upload.onprogress = function(event) {
            log(event.loaded + ' / ' + event.total);
        }

        // обработчики успеха и ошибки
        // если status == 200, то это успех, иначе ошибка
        xhr.onload = xhr.onerror = function() {
            if (this.status == 200) {
                log("success");
            } else {
                log("error " + this.status);
            }
        };

        xhr.open("POST", "/courses/upload", true);
        xhr.send(file);

    }
});