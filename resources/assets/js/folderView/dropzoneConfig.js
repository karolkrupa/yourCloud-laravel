'use strict';

window.App.dropzonejsConfig = {
    url: '/api/v1',
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

        if(App.config.debug) {
            console.info('[DropzoneJS] Recived file: '+ JSON.stringify(data));
        }

        App.files.add(data);
        App.files.render();
        App.files.sortViews();

        YourCloud.srollTo($('[data-file-id="'+ data.id +'"]'));
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
