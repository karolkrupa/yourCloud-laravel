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
        let fileId = $(event.target).parents('tr').attr('data-file-id');

        // Configuration specified by selected view
        contextMenu.find('button[data-action="newFile"]').attr('disabled', !App.currentDirConfig.createFile);
        contextMenu.find('button[data-action="newFolder"]').attr('disabled', !App.currentDirConfig.createFolder);

        if(typeof  fileId !== 'undefined') {
            ContextMenu.fileId = fileId;
            contextMenu.data('file-id', fileId);
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', false);
            contextMenu.find('button[data-action="copy"]').attr('disabled', false);
            contextMenu.find('button[data-action="rename"]').attr('disabled', false);
            contextMenu.find('button[data-action="tag"]').attr('disabled', false);
            contextMenu.find('button[data-action="deleteFile"]').attr('disabled', false);
            contextMenu.find('button[data-action="share"]').attr('disabled', false);
        }else {
            ContextMenu.fileId = false;
            contextMenu.find('button[data-action="downloadFile"]').attr('disabled', true);
            contextMenu.find('button[data-action="copy"]').attr('disabled', true);
            contextMenu.find('button[data-action="rename"]').attr('disabled', true);
            contextMenu.find('button[data-action="tag"]').attr('disabled', true);
            contextMenu.find('button[data-action="deleteFile"]').attr('disabled', true);
            contextMenu.find('button[data-action="share"]').attr('disabled', true);
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
    
        App.files.get(fileId).downloadFile();
    },

    deleteFile: function (contextMenuBtn, event) {
        let fileId = $('#file-context-menu').data('file-id');
        

        App.files.get(fileId).destroy({
            error: function(model, response, options) {
                YourCloud.addAlert(response.responseJSON.message, 'warning');
                App.files.push(model);
                App.files.render();
            }
        });
    },

    newFile: function(contextMenuBtn, event) {
        let model = App.files.add({
            type: 1,
            name: 'New File',
            size: '-',
            updated_at: '-',
        });

        App.files.render();
        model.trigger('showRenameField');
    },

    newFolder: function(contextMenuBtn, event) {
        let model = App.files.add({
            type: 0,
            name: 'New Folder',
            size: '-',
            updated_at: '-',
        });

        App.files.render();
        model.trigger('showRenameField');
    },

    rename: function (contextMenuBtn, event) {
        let fileId = $('#file-context-menu').data('file-id');

        App.files.get(fileId).trigger('showRenameField');
    },

    tag: function (contextMenuBtn, event) {
        let tagId = $(contextMenuBtn).data('tag-id');
        let file = App.files.get(this.fileId);

        if(file.attributes.tag_id == tagId) {
            tagId = 0;
        }

        file.attributes.tag_id = tagId;
        file.safeSave();
        file.trigger('change');
    },
    
    share: function (contextMenuBtn, event) {
        App.shareModalView.render(App.files.get(this.fileId));
        App.shareModalView.show();
    }
};

window.ContextMenu = ContextMenu;

$('html').on('click', ContextMenu.hideMenu);

$('#content').on('contextmenu', ContextMenu.onOpen);

$('#file-context-menu button').click(ContextMenu.selectOption);

