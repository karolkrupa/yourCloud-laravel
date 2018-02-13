window.App.ShareModalView = Backbone.View.extend({
    model: false,
    template: _.template(require('../templates/ShareModalView.html')),
    attributes: {
        id: 'fileSharingModal',
        class: 'modal fade',
    },

    events: {
        'click button[data-action="cancel"]': 'hide',
        'click td.remove-sharing': 'removeUser',
        'click button[data-action="shareLinkToggle"]': 'shareLinkToggle',
        'click button[data-action="findUser"]': 'findUser',
        'click .users-list li': 'selectUser'

    },

    initialize: function() {
        this.$el.appendTo('body');
    },

    render: function(model) {
        this.model = model;

        let data = {
            data: this.model.toJSON(),
            localization: App.config.localizationArray,
        };

        this.$el.html(this.template(data));

        return this;
    },

    show: function(model = null) {
        if(model != null) {
            this.render(model);
        }

        this.$el.show().addClass('show');
    },

    hide: function() {
        this.$el.hide().removeClass('show');
    },

    removeUser: function(event) {
        let userId = $(event.target).parents('tr').attr('data-user-id');
        let that = this;

        $(this.model.attributes.share_users).each(function(k,v) {
            if(v.id == userId) {
                delete that.model.attributes.share_users[k];
                return false;
            }
        });

        this.model.safeSave();
        this.render(this.model);
    },

    shareLinkToggle: function(event) {
        if(! this.model.attributes.share_link) {
            this.model.attributes.share_link = true;
        }else {
            this.model.attributes.share_link = false;
        }
        
        let that = this;
        this.model.safeSave({}, {
            afterSuccess: function() {
                that.render(that.model);
            }
        });
    },

    findUser: function(event) {
        let userName = this.$el.find('input.user-search').val();
        let usersList = this.$el.find('.users-list');

        $.get('/api/v1/user/find/'+ userName).done(function(response) {
            usersList.find('ul').empty();

            if(response.length > 0) {
                usersList.show();

                $(response).each(function(k,v) {
                    $('<li>').html(v.name).attr('data-user-id', v.id).appendTo(usersList.find('ul'));
                });
            }else {
                usersList.hide();
            }
        }).fail(function(response) {
            YourCloud.addAlert(response.responseJSON.message, 'warning');
        });
    },

    selectUser: function(event) {
        let userId = $(event.target).attr('data-user-id');
        let userName = $(event.target).html();
        this.$el.find('.users-list').hide();

        this.model.attributes.share_users.push(
            {
                name: userName,
                id: userId
            }
        );

        let that = this;
        this.model.safeSave({}, {
            afterSuccess: function() {
                that.render(that.model);
            }
        });
    }
});

window.App.shareModalView = new App.ShareModalView();
