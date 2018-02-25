window.App = $.extend(window.App, {
    dropzone: new Dropzone('#content', App.dropzonejsConfig),
    selectAllCheckbox: $('#checkbox-select-all'),
    fileContainer: '#file-table',
    fileClass: '.file-view',
    fileUploadApiUrl: '/api/v1/file/upload/',
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
});

require('./selectAllFiles');
require('./breadcrumb');
require('./folder');

require('./ContextMenu');
require('./FileModel');
require('./FileView/FileView');
require('./FilesCollection');
require('./Router');
require('./ShareModalView');





