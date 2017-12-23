var file_event = {
    on_click: function (e) {
        $(e.target).parents('tr').toggleClass('active');
    },

}

$('#file-list').click(file_event.on_click);