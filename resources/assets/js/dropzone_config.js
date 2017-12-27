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
    success: function(file, data) {
        $(file.previewElement).find('.progress-bar').addClass('bg-success');

        setTimeout(function (file) {
            $(file.previewElement).fadeOut(800, function () {
                $(this).remove();
            });
        }, 3000, file);

        file = FileList.addFileToList(data).addClass('active');

        YourCloud.srollTo(file);
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
};

window.enable_dropzonejs = function(container = '#content') {
    $(container).dropzone(dropzonejs_config);
};
