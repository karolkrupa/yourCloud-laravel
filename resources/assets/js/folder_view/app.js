window.App = {
    selectAllCheckbox: $('#checkbox-select-all input'),

    currentDir: {},
    currentDirConfig: {
        id: 0,
        apiUrl: 'files',
        createFolder: false,
        createFile: false,

        fileCreating: function (status = null) {
            if (status == null) {
                return this.createFile && this.createFolder;
            }

            this.createFile = status;
            this.createFolder = status;
        },
    },
}


// Select All event
App.selectAllCheckbox.click(function (event) {
    if ($(this).prop('checked')) {
        $('#file-list tbody tr').addClass('active');
    } else {
        $('#file-list tbody tr').removeClass('active');
    }
});

App.createBreadcrumb = function () {
    let parents = App.currentDir.get('parents');
    let template = $('<li class="breadcrumb-item"><a></a></li>');
    let container = $('#breadcrumb ol').empty();

    let currentView = $('#left-menu a.active').clone();
    currentView.find('svg').remove();

    // View item
    template.clone().find('a')
        .attr('href', currentView.attr('href'))
        .html(currentView.html())
        .click(App.router.takeRederict)
        .parent()
        .appendTo(container);

    $(parents).each(function (k, v) {
        let item = template.clone();
        item.find('a')
            .attr('href', '/files?dirId=' + v.id)
            .html(v.name)
            .click(App.router.takeRederict);

        container.append(item);
    });

    if(App.currentDir.get('name')) {
        template.clone().find('a')
        .html(App.currentDir.get('name'))
        .click(App.router.takeRederict)
        .parent()
        .addClass('active')
        .appendTo(container);
    }
    
}


window.App.loadFolder = function (dirId, urlRoot = 'files', withUrlReplace = false) {
    let url = 'api/v1/' + urlRoot;

    App.currentDir.apiUrl = url;
    App.currentDir.id = dirId;

    if (dirId != null) {
        url += '/' + dirId;
    } else {
        dirId = 0;
    }

    App.currentDir = new App.FileModel({ id: dirId });
    App.currentDir.fetch({
        success: App.createBreadcrumb
    });

    let files = new App.FilesCollection();
    files.urlRoot = url;

    files.fetchAndRender();

    url = document.createElement('a');
    url.href = location.href;

    if (withUrlReplace) {
        window.App.router.navigate(url.pathname + '?dirId=' + dirId)
    }
};

window.App.refreshFolder = function () {
    App.loadFolder(App.currentDir.dirId, App.currentDir.apiUrl);
}

require('./FileView');
require('./FilesCollection');
require('./Router');
require('./ShareModalView');
