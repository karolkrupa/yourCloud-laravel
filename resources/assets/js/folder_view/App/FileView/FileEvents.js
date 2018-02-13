module.exports = {
    change: function (event) {
      $(App.fileContainer).attr(
          'data-files-count',
          $(App.fileContainer).find('tbody tr').length-1
      );
    },

    click: function (event) {
        let el = this.$el;

        // Uncheck select All checkbox
        if(! el.hasClass('active-static')) {
            // Set file highlight
            if(el.hasClass('active')) {
                // Remove highlight
                App.selectAllCheckbox.trigger('uncheck');
            }else {
                // Add highlight
                if($(App.fileContainer).find(App.fileClass).not('.active').length == 1) {
                    // All files are highlighted
                    App.selectAllCheckbox.trigger('check');
                }
            }

            // Add or remove highlight
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
        
        this.$el.removeClass('active-static');
        this.$el.addClass('active');
        App.files.sortViews();
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
