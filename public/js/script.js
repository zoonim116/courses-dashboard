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

    $(document).on('change', '#course-cover', function (e) {
        var file = this.files[0];
        var fd = new FormData();
        fd.append("img", file);
        fd.append("course_id", "Groucho");
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/course/upload', true);

        xhr.upload.onprogress = function(e) {
            document.querySelector('.progress').style.display = 'block';
            if (e.lengthComputable) {
                var percentComplete = (e.loaded / e.total) * 100;
                percentComplete = Math.round(percentComplete);
                document.querySelector('.progress-bar').style.width = percentComplete + '%';
                console.log(percentComplete + '% uploaded');
            }
        };
        xhr.onload = function() {
            if (this.status == 200) {
                var resp = JSON.parse(this.response);
                console.log('Server got:', resp);
                document.querySelector('.update-thumbnail img').src = resp.url;
                document.querySelector('.progress').style.display = 'none';
            };
        };
        xhr.send(fd);

        return false;
    });

    function upload(file) {

        var xhr = new XMLHttpRequest();
        var sBoundary = "---------------------------" + Date.now().toString(16);
        // обработчик для закачки
        xhr.upload.onprogress = function(event) {
            console.log(event.loaded + ' / ' + event.total);
        }

        // обработчики успеха и ошибки
        // если status == 200, то это успех, иначе ошибка
        xhr.onload = xhr.onerror = function() {
            if (this.status == 200) {
                console.log("success");
            } else {
                console.log("error " + this.status);
            }
        };

        xhr.open("POST", "/course/upload", true);
        xhr.setRequestHeader("Content-Type", "multipart\/form-data; boundary=" + sBoundary);
        xhr.sendAsBinary();
        xhr.send(file);

    }
});