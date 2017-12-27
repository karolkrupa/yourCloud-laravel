FileListEvents = {
    onFileClick: function (event) {
        el = $(event.target).parents('tr');

        if(! el.hasClass('active-static')) {
            if(el.hasClass('active')) {
                FileList.selectAllCheckbox.uncheck();
            }

            el.toggleClass('active');
        }

        if(typeof el.data('click-time') == 'undefined') {
            el.data('click-time', (new Date()).getTime());
        }else if(el.data('click-time') >= (new Date()).getTime()-350) {
            // File action after duble click

            if(el.data('file-type') == 1) { // File
                // download_link = window.location.href + '?download_file=' + el.data("file-id");
                // window.location.replace(download_link);
            }else { // Folder
                window.location.href += '/' + el.data('file-name');
            }
        }else {
            el.data('click-time', (new Date()).getTime());
        }
    },
};

FileList.selectAllCheckbox.onClick = function () {
    var checkbox = $(this);

    if(checkbox.prop('checked')) {
        $('#file-list tbody tr').addClass('active');
    }else {
        $('#file-list tbody tr').removeClass('active');
    }
};

FileList.events = FileListEvents;

$('#file-list tbody').click(FileList.events.onFileClick);

$('#checkbox-select-all input').click(FileList.selectAllCheckbox.onClick);