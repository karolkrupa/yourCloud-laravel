module.exports = {
    click: function (event) {
        let el = this.$el;

        // Uncheck select All checkbox
        if(! el.hasClass('active-static')) {
            if(el.hasClass('active')) {
                App.selectAllCheckbox.prop('checked', false);
            }

            el.toggleClass('active');
        }
    },

    dblClick: function(event) {
        if(this.model.attributes.type == 0) {
            App.router.navigate('files?dirId='+ this.model.attributes.id, {trigger: true});
        }
    },

    renameSave: function(event) {
        let newName = this.$el.find('.file-rename input').val();

        this.model.attributes.name = newName;
        this.model.safeSave();
    },

    favoriteBtnClick: function(event) {
        event.stopPropagation();

        if(this.model.attributes.favorite) {
            this.model.attributes.favorite = 0;
        }else {
            this.model.attributes.favorite = 1;
        }

        this.model.safeSave();
    },
};