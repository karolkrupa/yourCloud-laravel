App.selectAllCheckbox.bind('check', function () {
    $(this).find('[data-fa-processed]').attr('data-icon', 'check-square');
});

App.selectAllCheckbox.bind('uncheck', function () {
    $(this).find('[data-fa-processed]').attr('data-icon', 'square');
});

// Select All event
App.selectAllCheckbox.click(function (event) {
    let icon = $(this).find('[data-fa-processed]');
    if(icon.attr('data-icon') == 'square') {
        App.selectAllCheckbox.trigger('check');
        $('#file-table tbody tr').addClass('active');
    }else {
        App.selectAllCheckbox.trigger('uncheck');
        $('#file-table tbody tr').removeClass('active');
    }
});
