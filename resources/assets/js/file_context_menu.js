'use strict';

let context_menu = {
    fileId: false,

    onOpen: function (event) {
        event.preventDefault();

        var contextMenu = $('#file-context-menu');

        var posX = event.pageX - $(contextMenu).parent().position().left;
        var posY = event.pageY - $(contextMenu).parent().position().top;
        var menuHeight = $(contextMenu).height();

        if((posY + menuHeight + 50) > $(document).height()) {
            posY -= menuHeight;
        }

        var file = $(event.target).parents('tr');
        var fileId = $(event.target).parents('tr').data('file-id');

        if(typeof  fileId !== 'undefined') {
            context_menu.fileId = fileId;
            contextMenu.data('file-id', fileId);
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', false);
            contextMenu.find('button[data-action="copy"]').attr('disabled', false);
            contextMenu.find('button[data-action="rename"]').attr('disabled', false);
            contextMenu.find('button[data-action="tag"]').attr('disabled', false);
            contextMenu.find('button[data-action="deleteFile"]').attr('disabled', false);
        }else {
            context_menu.fileId = false;
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', true);
            contextMenu.find('button[data-action="copy"]').attr('disabled', true);
            contextMenu.find('button[data-action="rename"]').attr('disabled', true);
            contextMenu.find('button[data-action="tag"]').attr('disabled', true);
            contextMenu.find('button[data-action="deleteFile"]').attr('disabled', true);
        }

        // Tag submenu
        var tagId = file.attr('data-tag-id');
        if(tagId) {
            contextMenu.find('#tag-context-menu [data-tag-id]').removeClass('active');
            contextMenu.find('#tag-context-menu [data-tag-id="'+ tagId +'"]').addClass('active');
        }else {
            contextMenu.find('#tag-context-menu [data-tag-id]').removeClass('active');
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
        context_menu[action](this, event);
    },

    downloadFile: function (contextMenuBtn, event) {
        var fileId = $('#file-context-menu').data('file-id');
        FileList.downloadFile(fileId)
    },

    deleteFile: function (contextMenuBtn, event) {
        var fileId = $('#file-context-menu').data('file-id');
        FileList.deleteFile(fileId);
    },

    newFile: function(contextMenuBtn, event) {
        FileList.createFile();
    },

    newFolder: function(contextMenuBtn, event) {
        FileList.createFolder();
    },

    rename: function (contextMenuBtn, event) {
        var fileId = $('#file-context-menu').data('file-id');

        FileList.renameFile(fileId);
    },
    
    tag: function (contextMenuBtn, event) {
        let tagId = $(contextMenuBtn).data('tag-id');
        var file = $('#file-list tbody tr[data-file-id="'+ this.fileId +'"]');

        $.post(window.location.href, {tag_file: this.fileId, tag_id: tagId}).done(function (data) {
            $('#tag-context-menu [data-tag-id]').removeClass('active');

            if(tagId == file.attr('data-tag-id')) { // Removing tag
                file.find('.file-icon .fa-circle').attr('data-tag-id', 'null');
                file.find('.file-icon .fa-circle').data('tag-id', 'null');
                tagId = 'null';
            }else {
                file.find('.file-icon .fa-circle').attr('data-tag-id', tagId);
                file.find('.file-icon .fa-circle').data('tag-id', tagId);
                $('#tag-context-menu [data-tag-id="'+ tagId +'"]').addClass('active');
            }

            if($('#left-menu [data-overlap="tags"] li a.active').length > 0) {
                file.remove();
            }

            file.attr('data-tag-id', tagId);
            file.data('data-tag-id', tagId);
        }).fail(function (data) {
            alert(data.responseJSON.error);
        });
    }
};

$('html').on('click', context_menu.hideMenu);

$('#content').on('contextmenu', context_menu.onOpen);

$('#file-context-menu button').click(context_menu.selectOption);

