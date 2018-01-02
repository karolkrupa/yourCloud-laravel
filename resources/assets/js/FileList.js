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
    },

    lastSort: null,

    sortToggle: function() {
        this.sort(this.lastSort*-1);
    },

    renameFile: function (fileIdOrFile) {
        let file;
        let inputFileName = $(FileList.fileInputNameTemplate);

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
        // Is event on button
        if($(event.target).is('button') || $(event.target).parents('button').is('button')) {
            let btn = ($(event.target).is('button'))? $(event.target) : $(event.target).parents('button');
            let file = $(btn.parents('tr'));
            let newName;
            let fileId = file.data('file-id');

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

    addFileToList: function (fileAttr) {
        let newFile = $('#file-list tbody tr.yc-template').clone();
        const fileContainer = '#file-list tbody';
        const fileTemplateClass = 'yc-template';
        const fileIcon = 'fa-file';
        const folderIcon = 'fa-folder';

        // Setting displayed folder size
        if(fileAttr['type'] == 0) {
            fileAttr['size_normalized'] = "-";
        }

        // File Name
        newFile.find('.file-name').html(fileAttr['name']);
        // File size
        newFile.find('.file-size').html(fileAttr['size_normalized']);
        // Last modify
        newFile.find('.file-updated-at').html(fileAttr['updated_at']);

        // Activate favorite button
        if(fileAttr['favorite']) {
            newFile.find('.favorite-btn').addClass('active');
        }

        // Setting file attrubutes
        newFile.attr('data-file-id', fileAttr['id']);
        newFile.attr('data-parent-id', fileAttr['parent_id']);
        newFile.attr('data-file-type', fileAttr['type']);
        newFile.attr('data-file-name', fileAttr['name']);
        newFile.attr('data-file-size', fileAttr['size']);
        newFile.attr('data-file-updated-at', fileAttr['updated_at']);

        // Appendig to file container
        newFile.appendTo(fileContainer);

        // Setting file icon
        if(fileAttr['type'] == '1') {
            newFile.find('.file-icon [data-fa-processed]').first().addClass(fileIcon);
        }else {
            newFile.find('.file-icon [data-fa-processed]').first().addClass(folderIcon);
        }

        // Remove template class
        newFile.removeClass(fileTemplateClass);

        // Sorting files
        FileList.sort();

        return newFile;
    },

    createFile: function () {
        $.post(window.location.href, {new_file: "New File", file_content: ''}).done(function (data) {
            let fileId = FileList.addFileToList(data).attr('data-file-id');

            FileList.renameFile(fileId);
        })
    },

    createFolder: function () {
        $.post(window.location.href, {new_folder: "New Folder"}).done(function (data) {
            let fileId = FileList.addFileToList(data).attr('data-file-id');

            FileList.renameFile(fileId);
        })
    },

    downloadFile: function (fileIdOrFile = false) {
        let selectedFiles = $('#file-list tbody tr.active');
        let uri = '?';

        if (selectedFiles.length > 0) {
            let i = 0;

            selectedFiles.each(function (el) {
                uri += 'download_file['+ i +']=' + $(this).data('file-id') + "&";
                i++;
            });
        }else if(fileIdOrFile) {
            let id;
            if(typeof fileIdOrFile === 'object') {
                id = $(fileIdOrFile).data('file-id');
            }else {
                id = fileIdOrFile;
            }

            uri = '?download_file=' + id;
        }

        window.location.href += uri;
    },

    deleteFile: function (fileIdOrFile) {
        let fileId;
        let file;

        if(typeof  fileIdOrFile === 'object') {
            fileId = $(fileIdOrFile).attr('data-file-id');
            file = fileIdOrFile;
        }else {
            fileId = fileIdOrFile;
            file = $('#file-list tbody tr[data-file-id="'+ fileId +'"]')
        }

        $.post(window.location.href, {delete_file: fileId}).done(function (data) {
            file.remove();
        }).fail(function (data) {
            YourCloud.addAlert(data.responseJSON.error, 'warning');
        });
    },

    selectAllCheckbox: {
        check: function () {
            const selectAllcheckobx = '#checkbox-select-all input';
            $(selectAllcheckobx).prop('checked', true);
        },

        uncheck: function () {
            const selectAllcheckobx = '#checkbox-select-all input';
            $(selectAllcheckobx).prop('checked', false);
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
