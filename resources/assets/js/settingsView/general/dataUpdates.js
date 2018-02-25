$('#update-full-name').click(function (event) {
    let fullName = $('#full-name');
    $.post('/api/v1/user/update/fullName', {full_name: fullName.val()}).done(function (response) {
        if(App.config.debug) {
            console.info('[dataUpdates] Recived: '+ JSON.stringify(response));
        }

        fullName.val(response.new_full_name);
        YourCloud.addAlert(response.message, 'success');
    }).fail(function (response) {
        if(App.config.debug) {
            console.error('[dataUpdates] Recived: '+ JSON.stringify(response));
        }

        YourCloud.addAlert(response.responseJSON.message, 'warning');
    });
})

$('#update-language').click(function (event) {
    let lang = $('#selected-language');
    $.post('/api/v1/user/update/language', {lang: lang.val()}).done(function (response) {
        if(App.config.debug) {
            console.info('[dataUpdates] Recived: '+ JSON.stringify(response));
        }

        // lang.val(response.new_full_name);
        YourCloud.addAlert(response.message, 'success');
        setTimeout(function () {
            location.reload();
        }, 3000);

    }).fail(function (response) {
        if(App.config.debug) {
            console.error('[dataUpdates] Recived: '+ JSON.stringify(response));
        }

        YourCloud.addAlert(response.responseJSON.message, 'warning');
    });
})

$('#update-password').click(function (event) {
    let password = $('#password');
    let password_repeat = $('#password-repeat');
    $.post('/api/v1/user/update/password', {password: password.val(), password_confirmation: password_repeat.val()}).done(function (response) {
        if(App.config.debug) {
            console.info('[dataUpdates] Recived: '+ JSON.stringify(response));
        }

        password.val('');
        password_repeat.val('');
        YourCloud.addAlert(response.message, 'success');
    }).fail(function (response) {
        if(App.config.debug) {
            console.error('[dataUpdates] Recived: '+ JSON.stringify(response));
        }

        YourCloud.addAlert(response.responseJSON.message, 'warning');
    });
})
