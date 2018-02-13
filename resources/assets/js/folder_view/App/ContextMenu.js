window.App.ContextMenuView = Backbone.View.extend({
    attributes: {
        id: 'file-context-menu'
    },

    contextMenu: true,
    parent: '#content',
    template: _.template(require('../templates/ContextMenuView.html')),
    model: false,

    events: {
        'click [data-action="newFolder"]': 'newFolder',
        'click [data-action="newFile"]': 'newFile',
        'click [data-action="share"]': 'share',
        'click [data-action="setTag"]': 'setTag',
        'click [data-action="rename"]': 'rename',
        'click [data-action="deleteFile"]': 'deleteFile',
        'click [data-action="downloadFile"]': 'downloadFile'
    },

    initialize: function () {
        this.$el.hide();
        $(this.parent).append(this.$el);
        $(this.parent).on('contextmenu', this.show);
        $(this.parent).on('click', this.hide);
    },

    render: function () {
        let data = {
            localization: App.config.localizationArray
        };

        this.$el.html(this.template(data));

        return this;
    },

    show: function (event) {
        event.preventDefault();
        event.stopPropagation();

        let that = App.contextMenu;
        if(this.contextMenu) {
            that.model = false;
        }else {
            if(typeof this.model !== 'undefined') {
                that.model =  this.model;
            }else {
                that.model = false;
            }
        }

        that.render();
        that.selectTag();

        if(that.model == false) {
            let disabledButtons = [
                'downloadFile',
                'copy',
                'rename',
                'tag',
                'deleteFile',
                'share'
            ];

            that.disableButtons(disabledButtons);
        }

        let posX = event.pageX - that.$el.parent().position().left;
        let posY = event.pageY - that.$el.parent().position().top;
        let menuHeight = that.$el.height();

        if((posY + menuHeight + 50) > $(document).height()) {
            posY -= menuHeight;
        }

        that.$el.css('top', posY).css('left', posX).show();

        return App.contextMenu;
    },

    hide: function () {
        App.contextMenu.$el.hide();

        return App.contextMenu;
    },

    disableButtons: function(list) {
        let that = this;
        $(list).each(function (k, v) {
            that.$el.find('[data-action="' + v + '"]').attr('disabled', true);
        });

        return this;
    },

    selectTag: function () {
        if(!this.model) return;

        let tag_id = this.model.attributes.tag_id;
        if(typeof tag_id !== 'undefined') {
            this.$el.find('#tag-context-menu [data-tag-id]').removeClass('active');
            this.$el.find('#tag-context-menu [data-tag-id="'+ tag_id +'"]').addClass('active');
        }else {
            this.$el.find('#tag-context-menu [data-tag-id]').removeClass('active');
        }
    },

    downloadFile: function () {
        this.model.downloadFile();
    },

    deleteFile: function (event) {
        this.model.destroy({
            error: function(model, response, options) {
                YourCloud.addAlert(response.responseJSON.message, 'warning');
                App.files.push(model);
                App.files.render();
            }
        });
    },

    newFile: function(event) {
        let model = App.files.add({
            type: 1,
            name: App.config.localizationArray.new_file_name,
            size: '-',
            updated_at: '-',
        });

        App.files.render();
        model.trigger('showRenameField');
    },

    newFolder: function(event) {
        let model = App.files.add({
            type: 0,
            name: App.config.localizationArray.new_folder_name,
            size: '-',
            updated_at: '-',
        });

        App.files.render();
        model.trigger('showRenameField');
    },

    rename: function (event) {
        this.model.trigger('showRenameField');
    },

    setTag: function (event) {
        let tagId = $(event.target).data('tag-id');
        let file = this.model;

        if(file.attributes.tag_id == tagId) {
            tagId = 0;
        }

        file.attributes.tag_id = tagId;
        file.safeSave();
        file.trigger('change');
    },

    share: function (event) {
        App.shareModalView.render(this.model);
        App.shareModalView.show();
    }
});

App.contextMenu = new App.ContextMenuView();
