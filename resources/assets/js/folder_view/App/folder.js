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
