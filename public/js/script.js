$(document).ready(function() {
    var courses = $('#courses-list').DataTable({
        "processing": true,
        //"serverSide": true, // recommended to use serverSide when data is more than 10000 rows for performance reasons
        "stateSave": true,
        "ajax": {
            url: "/course/all",
            dataSrc: ''
        },
        "columns": [
            { data: 'id' },
            { data: 'name' },
            { data: 'category' },
            { data: 'author' },
            { data: 'action' },
        ],
        "columnDefs": [ {
            "targets": -1,
            "data": 'action',
            "defaultContent": '<div class="dropdown">\n' +
            '    <button class="btn btn-default btn-xs dropdown-toggle"  type="button" data-toggle="dropdown">Select\n' +
            '    <span class="caret"></span></button>\n' +
            '    <ul class="dropdown-menu" role="menu">\n' +
            '      <li role="presentation"><a role="menuitem" data-action="view" tabindex="-1" href="#">View</a></li>\n' +
            '      <li role="presentation"><a role="menuitem" data-action="edit" tabindex="-1" href="#">Edit</a></li>\n' +
            '    </ul>\n' +
            '  </div>'
        } ]
    });

    $('#courses-list tbody').on( 'click', 'a', function (e) {
        e.preventDefault();
        var data = courses.row( $(this).parents('tr') ).data();
        switch ($(this).attr('data-action')) {
            case 'view' :
                location.href = location.href + 'course/view/' + data.id;
                break;
            case 'edit' :
                location.href = location.href + 'course/edit/' + data.id;
                break;
            default:
                location.href = location.href + 'course/view/' + data.id;
                break;
        }
        return false;

    });

    var lessons = $('#lessons-list').DataTable({
        "processing": true,
        //"serverSide": true, // recommended to use serverSide when data is more than 10000 rows for performance reasons
        "stateSave": true,
        "ajax": {
            url: "/lesson/by-course/" + window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1),
            dataSrc: ''
        },
        "columns": [
            { data: 'id' },
            { data: 'name' },
            { data: 'slides' },
            { data: 'questions' },
            { data: 'action' },
        ],
        "columnDefs": [ {
            "targets": -1,
            "data": 'action',
            "defaultContent": '<div class="dropdown">\n' +
            '    <button class="btn btn-default btn-xs dropdown-toggle"  type="button" data-toggle="dropdown">Select\n' +
            '    <span class="caret"></span></button>\n' +
            '    <ul class="dropdown-menu" role="menu">\n' +
            '      <li role="presentation"><a role="menuitem" data-action="view" tabindex="-1" href="#">View</a></li>\n' +
            '      <li role="presentation"><a role="menuitem" data-action="edit" tabindex="-1" href="#">Edit</a></li>\n' +
            '      <li role="presentation"><a role="menuitem" data-action="delete" tabindex="-1" href="#">Delete</a></li>\n' +
            '    </ul>\n' +
            '  </div>'
        } ]
    });

    $('#lessons-list tbody').on( 'click', 'a', function (e) {
        e.preventDefault();
        var data = lessons.row( $(this).parents('tr') ).data();
        switch ($(this).attr('data-action')) {
            case 'view' :
                location.href = location.origin + '/slides/' + data.id;
                break;
            case 'edit' :
                location.href = location.origin + '/lesson/edit/' + data.id;
                break;
            case 'delete' :
                location.href = location.origin + '/lesson/delete/' + data.id;
                break;
            default:
                location.href = location.origin + '/lesson/view/' + data.id;
                break;
        }
        return false;

    });

    $('.update-thumbnail').on('click', function () {
        $('#course-cover').trigger('click');
    });

    $(document).on('change', '#course-cover', function () {
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
            }
        };
        xhr.onload = function() {
            if (this.status == 200) {
                var resp = JSON.parse(this.response);
                console.log('Server got:', resp);
                document.querySelector('.update-thumbnail img').src = resp.url;
                document.querySelector('.progress').style.display = 'none';
                document.querySelector('[name="img_url"]').value = resp.url;
            };
        };
        xhr.send(fd);
        return false;
    });

    slides = $('#slides-list').DataTable({
        "processing": true,
        //"serverSide": true, // recommended to use serverSide when data is more than 10000 rows for performance reasons
        // "stateSave": true,
        "order": true,
        "rowReorder": true,
        "ajax": {
            url: "/slides/by-lesson/" + window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1),
            dataSrc: ''
        },
        "columns": [
            { data: 'id' },
            { data: 'txt' },
            { data: 'img' },
            { data: 'answer' },
            { data: 'option_1' },
            { data: 'option_2' },
            { data: 'option_3' },
            { data: 'action' },
        ],
        "columnDefs": [ {
            "targets": -1,
            "data": 'action',
            "defaultContent": '<div class="dropdown">\n' +
            '    <button class="btn btn-default btn-xs dropdown-toggle"  type="button" data-toggle="dropdown">Select\n' +
            '    <span class="caret"></span></button>\n' +
            '    <ul class="dropdown-menu" role="menu">\n' +
            '      <li role="presentation"><a role="menuitem" data-action="view" tabindex="-1" href="#">View</a></li>\n' +
            '      <li role="presentation"><a role="menuitem" data-action="edit" tabindex="-1" href="#">Edit</a></li>\n' +
            '      <li role="presentation"><a role="menuitem" data-action="delete" tabindex="-1" href="#">Delete</a></li>\n' +
            '    </ul>\n' +
            '  </div>'
            }
        ]
    });
    new $.fn.dataTable.FixedHeader( slides, {
        headerOffset: 50
    } );

    slides.on( 'row-reorder', function ( e, diff, edit ) {
        var positiions = [];
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = slides.row( diff[i].node ).data();
            positiions.push({'id' : rowData.id, 'new' : diff[i].newPosition, 'old' : diff[i].oldPosition});
        }
        if(positiions.length > 0) {
            $.ajax({
                type: "POST",
                url: '/slides/new-order',
                data: {"data" : positiions},
                success: function (response) {
                    slides.ajax.reload();
                },
            }); 
        }
    } );
});