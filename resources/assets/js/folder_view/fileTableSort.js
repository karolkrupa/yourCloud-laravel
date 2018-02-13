$(App.fileContainer).find('thead [data-sort-by]').click(function (event) {
    let el = $(event.target);
    let sortBy = 'data-' + el.attr('data-sort-by');
    App.files.toggleSortViews(sortBy);

    let sortType = (App.files.lastSortMultipler === 1)? 'caret-up' : 'caret-down';

    el.find('[data-fa-processed]').attr('data-icon', sortType);

    $(App.fileContainer).find('thead [data-sort-by]').find('[data-fa-processed]').addClass('invisible');
    el.find('[data-fa-processed]').removeClass('invisible');
});
