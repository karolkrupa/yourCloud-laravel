window.App.FilesCollection = Backbone.Collection.extend({
    model: App.FileModel,
    urlRoot: '/api/v1/files',
    views: null,
    lastSortMultipler: 1,
    lastSortAttr: 'data-file-name',
    url: function () {
        let url =  location.origin + '/' + this.urlRoot;
        
        return url;
    },
    
    createViewsList: function () {
        let views = [];

        this.each(function (file) {
            views.push(new App.FileView({model: file}));
        });

        return views;
    },

    render: function(refresh = true) {
        if(refresh) {
            this.each(function(model) {
                model.trigger('remove');
            });
        }

        $(this.createViewsList()).each(function (k, v) {
            v.append();
        });

        this.sortViews();

        $(App.fileContainer).attr('data-files-count', this.models.length || 0);

        return this;
    },

    fetchAndRender: function() {
        let that = this;
        this.fetch({
            success: function (collection, response, options) {
                if(App.files) {
                    App.files.remove(App.files.models);
                }

                that.render(false);

                window.App.files = that;
            },
    
            error: function (collection, response, options) {
                YourCloud.addAlert(response.responseJSON.message, 'danger');
                console.error(JSON.stringify(response));
            }
        });

        return this;
    },

    setParentId: function (id) {
        this.parent_id = id;
    },

    sortViews: function (asc = true) {
        this.sortViewsBy('data-file-name', asc);
    },

    sortViewsBy: function (sortAttr = false, asc = true) {
        let files = $(App.fileContainer).find('tbody tr[data-file-type="1"]');
        let folders = $(App.fileContainer).find('tbody tr[data-file-type="0"]');

        this.lastSortAttr = sortAttr;

        let sortMultipler = asc? 1 : -1;
        this.lastSortMultipler = sortMultipler;

        folders.sort(function(a,b){
            return $(a).attr(sortAttr).localeCompare($(b).attr(sortAttr), {}, {numeric: true})*sortMultipler;
        }).appendTo(App.fileContainer);

        files.sort(function(a, b){
            return $(a).attr(sortAttr).localeCompare($(b).attr(sortAttr), {}, {numeric: true})*sortMultipler;
        }).appendTo(App.fileContainer);
    },

    toggleSortViews: function (sortAttr = false) {
        let negativeMultipler = (this.lastSortMultipler == 1)? false : true;
        if(!sortAttr) {
            this.sortViews(negativeMultipler);
        }else if(sortAttr == this.lastSortAttr) {
            this.sortViewsBy(sortAttr, negativeMultipler);
        }else {
            this.sortViewsBy(sortAttr);
        }
    }
});
