'use strict';

let context_menu = {
    onOpen: function (event) {
        event.preventDefault();

        var contextMenu = $('#file-context-menu');

        var posX = event.pageX - $(contextMenu).parent().position().left;
        var posY = event.pageY - $(contextMenu).parent().position().top;
        var menuHeight = $(contextMenu).height();

        if((posY + menuHeight + 50) > $(document).height()) {
            posY -= menuHeight;
        }

        var fileId = $(event.target).parents('tr').data('file-id');

        if(typeof  fileId !== 'undefined') {
            contextMenu.data('file-id', fileId);
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', false);
            contextMenu.find('button[data-action="copy"]').attr('disabled', false);
            contextMenu.find('button[data-action="rename"]').attr('disabled', false);
        }else {
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', true);
            contextMenu.find('button[data-action="copy"]').attr('disabled', true);
            contextMenu.find('button[data-action="rename"]').attr('disabled', true);
        }

        // Disable download on folders
        // var fileType = $(event.target).parents('tr').data('file-type');
        // if(fileType == 0) {
        //     $(selector).find('button[data-action="downloadFile"]').attr('disabled', true);
        // }

        contextMenu.css('top', posY).css('left', posX);
        contextMenu.show();
    },

    hideMenu: function() {
        $('#file-context-menu').hide();
    },

    selectOption: function (event) {
        var action = $(this).data('action');
        context_menu[action](this);
    },

    downloadFile: function (contextMenuBtn) {
        var fileId = $('#file-context-menu').data('file-id');
        FileList.downloadFile(fileId)
    },

    newFile: function(contextMenuBtn) {
        FileList.createFile();
    },

    newFolder: function(contextMenuBtn) {
        FileList.createFolder();
    },

    rename: function (contextMenuBtn) {
        var fileId = $('#file-context-menu').data('file-id');

        FileList.renameFile(fileId);
    },
};

$('html').on('click', context_menu.hideMenu);

$('#content').on('contextmenu', context_menu.onOpen);

$('#file-context-menu button').click(context_menu.selectOption);

