window.App.FilesCollection = Backbone.Collection.extend({
    model: App.FileModel,
    urlRoot: '/api/v1/files',
    views: null,
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

        return this;
    },

    fetchAndRender: function() {
        let that = this;
        this.fetch({
            success: function () {
                if(App.files) {
                    App.files.remove(App.files.models);
                }

                that.render(false);

                window.App.files = that;
            },
    
            error: function () {
                alert('Cant load files');
            }
        });

        return this;
    },

    setParentId: function (id) {
        this.parent_id = id;
    },

    sortViews: function (asc = true) {
        let files = $('#file-list tbody tr[data-file-type="1"]');
        let folders = $('#file-list tbody tr[data-file-type="0"]');
        const fileContainer = '#file-list tbody';
        const fileNameAttr = 'data-file-name';

        let sortMultipler = asc? 1 : -1;

        folders.sort(function(a,b){
            return $(a).attr(fileNameAttr).localeCompare($(b).attr(fileNameAttr), {}, {numeric: true})*sortMultipler;
        }).appendTo(fileContainer);

        files.sort(function(a, b){
            return $(a).attr(fileNameAttr).localeCompare($(b).attr(fileNameAttr), {}, {numeric: true})*sortMultipler;
        }).appendTo(fileContainer);

        this.lastSort = asc;
    }
});