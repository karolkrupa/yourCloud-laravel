'use strict';

window.dropzonejs_config = {
    url: window.location.href,
    parallelUploads: 1,
    previewsContainer: '#dropzonejs-container',
    previewTemplate: $('#dropzonejs-template').html(),
    clickable: false,
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
    },
    error: function(file, msg) {
        $(file.previewElement).find('.progress-bar').addClass('bg-warning');

        setTimeout(function (file) {
            $(file.previewElement).fadeOut(800, function () {
                $(this).remove();
            });
        }, 5000, file);

        console.error('Dropzonejs upload error. Server response: ' + JSON.stringify(msg));
    },
    success: function(file, msg) {
        $(file.previewElement).find('.progress-bar').addClass('bg-success');

        setTimeout(function (file) {
            $(file.previewElement).fadeOut(800, function () {
                $(this).remove();
            });
        }, 3000, file);

        var element = $('#file-list tbody tr.yc-template').clone();

        element.find('.file-name').html(msg['name']);
        element.find('.file-size').html(msg['size_normalized']);
        element.find('.file-updated-at').html(msg['updated_at']);

        element.attr('data-file-id', msg['id']);
        element.attr('data-parent-id', msg['parent_id']);
        element.attr('data-file-type', msg['type']);
        element.attr('data-file-name', msg['name']);
        element.attr('data-file-size', msg['size']);
        element.attr('data-file-updated-at', msg['updated_at']);

        element.appendTo('#file-list tbody');

        element.removeClass('yc-template');

        if(msg['type'] == '1') {
            element.find('[data-fa-processed]').addClass('fa-file');
        }else {
            element.find('[data-fa-processed]').addClass('fa-folder');
        }
    },
    sending: function(file, xhr) {
        $('#dropzonejs-container .dz-complete').remove();

        $(file.previewElement).removeClass('dropzonejs-template');
    },
    uploadprogress: function(file, progress, byteSent) {
        $(file.previewElement).find('.progress-bar').width(progress + '%');
        $(file.previewElement).find('.progress-bar').attr('aria-valuenow', progress);
        $(file.previewElement).find('.progress-bar').html(progress + '%');
    },
}

window.enable_dropzonejs = function(container = '#content') {
    $(container).dropzone(dropzonejs_config);
}
