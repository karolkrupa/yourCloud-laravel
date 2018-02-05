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

require('./breadcrumb');
require('./folder');





