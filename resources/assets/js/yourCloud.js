var YourCloud = {
    alertTemplate: '<div class="alert alert-dismissible fade show text-center" role="alert">\n' +
    '    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
    '        <span aria-hidden="true">&times;</span>\n' +
    '    </button>\n' +
    '</div>',

    srollTo: function (el, time = 1000) {
        $('html, body').animate({
            scrollTop: $(el).offset().top - $(window).height()/2
        }, time);
    },

    addAlert: function (msg, type) {
        let alert = $(this.alertTemplate);
        alert.addClass('alert-'+type);
        alert.prepend(msg);

        alert.appendTo('#alerts-container');
    }
};

window.YourCloud = YourCloud;