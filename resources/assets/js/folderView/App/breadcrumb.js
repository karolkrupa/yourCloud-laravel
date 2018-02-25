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
