// require('../FileModel');

let fileEventCallbacks = require('./FileEvents');

window.App.FileView = Backbone.View.extend({
    attributes: {
        role: 'link',
    },
    tagName: 'tr',
    parent: '#file-table tbody',
    template: _.template(require('../../templates/FileView/FileView.html')),
    renameFieldTemplate: _.template(require('../../templates/FileView/renameField.html')),
    className: 'file-view',

    events: {
        'click': fileEventCallbacks.click,
        'dblclick': fileEventCallbacks.dblClick,
        'click #file-favorite-btn': fileEventCallbacks.favoriteBtnClick,
        'click .file-rename button[data-action="cancel"]': 'render',
        'click .file-rename button[data-action="save"]': fileEventCallbacks.renameSave,
        'contextmenu': App.contextMenu.show,
    },

    initialize: function () {
        this.model.on('change', this.render, this);
        this.model.on('remove', this.remove, this);
        this.model.on('showRenameField', this.showRenameField, this);
        this.on('remove', fileEventCallbacks.change);

        return this;
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));

        this.setIcon();

        this.setAttributes();

        return this;
    },

    append: function () {
        let parent = $(this.parent);

        parent.append(this.render().$el);

        return this;
    },

    setIcon: function () {
        let icon = '';

        if(this.model.attributes.type == 0) {
            icon = 'fa-folder';
        }else {
            icon = 'fa-file';
        }

        this.$el.find('.file-icon i').first().addClass(icon);

        return this;
    },

    showRenameField: function() {
        this.$el.find('.file-name').html(this.renameFieldTemplate(this.model.toJSON()));
        this.$el.addClass('active-static');

        return this;
    },

    setAttributes: function () {
        this.$el.attr('data-tag-id', this.model.attributes.tag_id);
        this.$el.attr('data-file-id', this.model.attributes.id);
        this.$el.attr('data-file-type', this.model.attributes.type);
        this.$el.attr('data-file-name', this.model.attributes.name);
        this.$el.attr('data-file-size', this.model.attributes.size);
        this.$el.attr('data-file-last-modify', this.model.attributes.updated_at);
        this.$el.attr('data-favorite', this.model.attributes.favorite? 'true' : 'false');
        this.$el.attr('data-link-share', this.model.attributes.share_link? 'true': 'false');
        if(this.model.attributes.share_users) {
            this.$el.attr('data-user-share', (this.model.attributes.share_users.length > 0)? 'true' : 'false');
        }
        
        return this;
    },

    remove: function () {
        this.trigger('remove', this);
        return Backbone.View.prototype.remove.apply(this, arguments);
    }

});
