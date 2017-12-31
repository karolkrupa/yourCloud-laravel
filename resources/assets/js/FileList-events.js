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
                // Remove get parameters
                let link = window.location.href;
                // let getParameters = link.substr(link.indexOf('?'));
                link = link.substr(0, link.indexOf('?')<1? link.length : link.indexOf('?'));

                window.location.href = link + '/' + el.data('file-name');
            }
        }else {
            el.data('click-time', (new Date()).getTime());
        }
    },

    onFavoriteBtnClick: function (event) {
        event.stopPropagation();

        let file = $(this).parents('tr');
        let fileId = file.data('file-id');

        if(! file.find('.favorite-btn').hasClass('active')) { // Add to favorites
            $.post(window.location.href, {add_favorite_file: fileId}).done(function (data) {
                file.find('.favorite-btn').addClass('active');
                file.find('.favorite-btn')
            }).fail(function (data) {
                YourCloud.addAlert(data.responseJSON.error, 'warning');
            });
        }else { // Remove to favorites
            $.post(window.location.href, {remove_favorite_file: fileId}).done(function (data) {
                file.find('.favorite-btn').removeClass('active');

                if($('#left-menu [data-overlap="favorites"]').hasClass('active')) {
                    file.remove();
                }
            }).fail(function (data) {
                YourCloud.addAlert(data.responseJSON.error, 'warning');
            });
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

$('#file-list tbody .favorite-btn').click(FileList.events.onFavoriteBtnClick);

$('#checkbox-select-all input').click(FileList.selectAllCheckbox.onClick);