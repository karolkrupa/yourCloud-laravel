window.App.Router = Backbone.Router.extend({
    routes: {
        'files(?*query)': 'main',
        'files/favorites(?*query)': 'favorites',
        'files/tag/:id(?*query)': 'tag',
        'files/sharedforme': 'shareForMe',
        'files/sharedbyme': 'shareByMe'
    },

    getFromQuery: function(parameter, query) {
        let parameterValue = null;

        if(query == null) {
            return parameterValue;
        }

        if(parameter.charAt(parameter.length-1) != '=') {
            parameter += '=';
        }

        let parameterPos = query.indexOf(parameter);
        
        if(parameterPos > -1) {
            let valueStart = parameterPos + parameter.length;

            let valueEnd = query.indexOf('&', valueStart);
            valueEnd = (valueEnd < 1)? query.length : valueEnd;

            parameterValue = query.substr(valueStart, valueEnd);
        }

        return parameterValue;
    },

    takeRederict: function(event) {
        event.preventDefault();

        App.router.navigate($(this).attr('href'), {trigger: true});
    },

    main: function(query) {
        let dirId = this.getFromQuery('dirId', query) || 0;

        App.dropzone.enable();
        App.dropzone.options.url = App.fileUploadApiUrl + dirId;

            $('#left-menu a').removeClass('active');
        $('#left-menu a[href="/files"]').addClass('active');

        window.App.loadFolder(dirId, 'files');

        App.currentDirConfig.fileCreating(true);
    },

    favorites: function(query) {
        App.dropzone.disable();

        $('#left-menu a').removeClass('active');
        $('#left-menu a[href="/files/favorites"]').addClass('active');

        window.App.loadFolder(null, 'files/favorites');

        App.currentDirConfig.fileCreating(false);
    },

    tag: function(id, query) {
        App.dropzone.disable();

        if(id == null) {
            return this.main(query);
        }

        $('#left-menu a').removeClass('active');
        $('#left-menu a[href="/files/tag/'+ id +'"]').addClass('active');

        window.App.loadFolder(null, 'files/tag/' + id);

        App.currentDirConfig.fileCreating(false);
    },

    shareForMe: function() {
        App.dropzone.disable();

        $('#left-menu a').removeClass('active');
        $('#left-menu a[href="/files/sharedforme"]').addClass('active');

        window.App.loadFolder(null, 'files/shareforme');

        App.currentDirConfig.fileCreating(false);
    },

    shareByMe: function() {
        App.dropzone.disable();

        $('#left-menu a').removeClass('active');
        $('#left-menu a[href="/files/sharedbyme"]').addClass('active');

        window.App.loadFolder(null, 'files/sharebyme');

        App.currentDirConfig.fileCreating(false);
    }
});

window.App.router = new App.Router;
Backbone.history.start({pushState: true});

// Menu bindings
$('#left-menu a').click(App.router.takeRederict);
