'use strict';

var FileList = {
    fileInputNameTemplate: '<div class="input-group">\n' +
    '                        <input type="text" class="form-control" placeholder="name">\n' +
    '                        <span class="input-group-btn">\n' +
    '                            <button class="btn btn-secondary" type="button" data-action="cancel">\n' +
    '                                <i class="fas fa-times"></i>\n' +
    '                            </button>\n' +
    '                            <button class="btn btn-secondary" type="button" data-action="save">\n' +
    '                                <i class="fas fa-check"></i>\n' +
    '                            </button>\n' +
    '                        </span>\n' +
    '                    </div>',

    sort: function (asc = true) {
        var files = $('#file-list tbody tr[data-file-type="1"]');
        var folders = $('#file-list tbody tr[data-file-type="0"]');
        const fileContainer = '#file-list tbody';
        const fileNameAttr = 'data-file-name';

        var multipler = asc? 1 : -1;

        folders.sort(function(a,b){
            return $(a).attr(fileNameAttr).localeCompare($(b).attr(fileNameAttr), {}, {numeric: true})*multipler;
        }).appendTo(fileContainer);

        files.sort(function(a, b){
            return $(a).attr(fileNameAttr).localeCompare($(b).attr(fileNameAttr), {}, {numeric: true})*multipler;
        }).appendTo(fileContainer);

        this.lastSort = asc;
    },

    lastSort: null,

    sortToggle: function() {
        this.sort(this.lastSort*-1);
    },

    renameFile: function (fileIdOrFile) {
        var file;
        var inputFileName = $(FileList.fileInputNameTemplate);

        if(typeof fileIdOrFile === 'object') {
            file = fileIdOrFile;
        }else {
            file = $('#file-list tbody tr[data-file-id="'+ fileIdOrFile +'"]');
        }

        inputFileName.find('input').val(file.data('file-name'));
        file.toggleClass('active-static');

        file.find('.file-name').html(inputFileName);

        YourCloud.srollTo(file);
    },

    renameFileExec: function (event) {
        if($(event.target).is('button') || $(event.target).parents('button').is('button')) { // Is event on button
            var btn = ($(event.target).is('button'))? $(event.target) : $(event.target).parents('button');
            var file = $(btn.parents('tr'));
            var newName;
            var fileId = file.data('file-id');

            if(btn.data('action') == 'save') {
                newName = file.find('input').val();
            }else {
                newName = file.attr('data-file-name');
                file.find('.file-name').html(newName);
                file.removeClass('active-static');
                return;
            }

            $.post(window.location.href, {rename_file: newName, file_id: fileId}).done(function (data) {
                file.remove();
                file = FileList.addFileToList(data).addClass('active');

                YourCloud.srollTo(file);

            }).fail(function (data) {
                YourCloud.addAlert(data.responseJSON.error, 'warning');
            });
        }
    },

    addFileToList: function (file) {
        var newFile = $('#file-list tbody tr.yc-template').clone();
        const fileContainer = '#file-list tbody';
        const fileTemplateClass = 'yc-template';
        const fileIcon = 'fa-file';
        const folderIcon = 'fa-folder';
        newFile.find('.file-name').html(file['name']);

        if(file['type'] == 0) {
            file['size_normalized'] = "-";
        }

        newFile.find('.file-size').html(file['size_normalized']);
        newFile.find('.file-updated-at').html(file['updated_at']);

        newFile.attr('data-file-id', file['id']);
        newFile.attr('data-parent-id', file['parent_id']);
        newFile.attr('data-file-type', file['type']);
        newFile.attr('data-file-name', file['name']);
        newFile.attr('data-file-size', file['size']);
        newFile.attr('data-file-updated-at', file['updated_at']);

        newFile.appendTo(fileContainer);

        newFile.removeClass(fileTemplateClass);

        if(file['type'] == '1') {
            newFile.find('[data-fa-processed]').addClass(fileIcon);
        }else {
            newFile.find('[data-fa-processed]').addClass(folderIcon);
        }

        FileList.sort();

        return newFile;
    },
    
    createFile: function () {
        $.post(window.location.href, {new_file: "New File", file_content: ''}).done(function (data) {
            var fileId = FileList.addFileToList(data).attr('data-file-id');

            FileList.renameFile(fileId);
        })
    },
    
    createFolder: function () {
        $.post(window.location.href, {new_folder: "New Folder"}).done(function (data) {
            var fileId = FileList.addFileToList(data).attr('data-file-id');

            FileList.renameFile(fileId);
        })
    },

    downloadFile: function (fileIdOrFile = false) {
        var selectedFiles = $('#file-list tbody tr.active');
        var uri = '?';

        if (selectedFiles.length > 0) {
            let i = 0;

            selectedFiles.each(function (el) {
                uri += 'download_file['+ i +']=' + $(this).data('file-id') + "&";
                i++;
            });
        }else if(fileIdOrFile) {
            var id;
            if(typeof fileIdOrFile === 'object') {
                id = $(fileIdOrFile).data('file-id');
            }else {
                id = fileIdOrFile;
            }

            uri = '?download_file=' + id;
        }

        window.location.href += uri;
    },

    selectAllCheckbox: {
        check: function () {
            $('#checkbox-select-all input').prop('checked', true);
        },

        uncheck: function () {
            $('#checkbox-select-all input').prop('checked', false);
        }
    },
};

$("#file-list tbody").click(FileList.renameFileExec);

window.FileList = FileList;

FileList.sort();





//  Quicksort
// function partition(el, p, r) {
//     var val = el.eq(p).attr('data-file-name');
//     var i = p;
//     var j = r;
//     var b;
//     var element;
//     // alert();
//     while(true) {
//         while(el.eq(j).attr('data-file-name').localeCompare(val, {}, {numeric: true}) == 1) {
//             // alert(el.eq(j).attr('data-file-name')+ '>' + val);
//             // alert(j);
//             j--;
//         }
//
//         while(el.eq(i).attr('data-file-name').localeCompare(val, {}, {numeric: true}) == -1) {
//             i++;
//         }
//
//         if(i < j) {
//             b = el.eq(i).attr('data-file-name');
//             el.eq(i).attr('data-file-name', el.eq(j).attr('data-file-name'));
//             el.eq(i).find('.file-name').html(el.eq(j).attr('data-file-name'));
//             el.eq(j).attr('data-file-name', b);
//             el.eq(j).find('.file-name').html(b);
//
//             i++;
//             j--;
//         }else {
//             return j;
//         }
//     }
// }
//
// function quicsort(el, p, r) {
//     var q;
//     if(p < r) {
//         q = partition(el, p, r);
//         quicsort(el, p, q);
//         quicsort(el, q+1, r);
//     }
// }
//
// var el = $('#file-list tbody tr');
// var p = el.first();
// var r = el.last();

// quicsort(el, 0, el.length-1);
