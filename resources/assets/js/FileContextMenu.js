'use strict';

// Context Menu object
let ContextMenu = {
    fileId: false,

    // On open context menu
    onOpen: function (event) {
        event.preventDefault();

        let contextMenu = $('#file-context-menu');

        let posX = event.pageX - $(contextMenu).parent().position().left;
        let posY = event.pageY - $(contextMenu).parent().position().top;
        let menuHeight = $(contextMenu).height();

        if((posY + menuHeight + 50) > $(document).height()) {
            posY -= menuHeight;
        }

        let file = $(event.target).parents('tr');
        let fileId = $(event.target).parents('tr').data('file-id');

        if(typeof  fileId !== 'undefined') {
            ContextMenu.fileId = fileId;
            contextMenu.data('file-id', fileId);
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', false);
            contextMenu.find('button[data-action="copy"]').attr('disabled', false);
            contextMenu.find('button[data-action="rename"]').attr('disabled', false);
            contextMenu.find('button[data-action="tag"]').attr('disabled', false);
            contextMenu.find('button[data-action="deleteFile"]').attr('disabled', false);
        }else {
            ContextMenu.fileId = false;
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', true);
            contextMenu.find('button[data-action="copy"]').attr('disabled', true);
            contextMenu.find('button[data-action="rename"]').attr('disabled', true);
            contextMenu.find('button[data-action="tag"]').attr('disabled', true);
            contextMenu.find('button[data-action="deleteFile"]').attr('disabled', true);
        }

        // Tag submenu
        let tagId = file.attr('data-tag-id');
        if(tagId) {
            contextMenu.find('#tag-context-menu [data-tag-id]').removeClass('active');
            contextMenu.find('#tag-context-menu [data-tag-id="'+ tagId +'"]').addClass('active');
        }else {
            contextMenu.find('#tag-context-menu [data-tag-id]').removeClass('active');
        }

        contextMenu.css('top', posY).css('left', posX);
        contextMenu.show();
    },

    hideMenu: function() {
        $('#file-context-menu').hide();
    },

    selectOption: function (event) {
        let action = $(this).data('action');
        ContextMenu[action](this, event);
    },

    downloadFile: function (contextMenuBtn, event) {
        let fileId = $('#file-context-menu').data('file-id');
        FileList.downloadFile(fileId)
    },

    deleteFile: function (contextMenuBtn, event) {
        let fileId = $('#file-context-menu').data('file-id');
        FileList.deleteFile(fileId);
    },

    newFile: function(contextMenuBtn, event) {
        FileList.createFile();
    },

    newFolder: function(contextMenuBtn, event) {
        FileList.createFolder();
    },

    rename: function (contextMenuBtn, event) {
        let fileId = $('#file-context-menu').data('file-id');

        FileList.renameFile(fileId);
    },

    tag: function (contextMenuBtn, event) {
        let tagId = $(contextMenuBtn).data('tag-id');
        let file = $('#file-list tbody tr[data-file-id="'+ this.fileId +'"]');

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
            YourCloud.addAlert(data.responseJSON.error, 'warning');
        });
    }
};

window.ContextMenu = ContextMenu;

$('html').on('click', ContextMenu.hideMenu);

$('#content').on('contextmenu', ContextMenu.onOpen);

$('#file-context-menu button').click(ContextMenu.selectOption);

