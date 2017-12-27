var file_event = {
    on_click: function (e) {
        el = $(e.target).parents('tr');

        if(! el.hasClass('active-static'))
            el.toggleClass('active');

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

        // alert((typeof el.data('click-time') == 'undefined')? 'tak' : 'nie');

    },

}

// $('#file-list').click(file_event.on_click);