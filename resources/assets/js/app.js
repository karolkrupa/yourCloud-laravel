/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./yourCloud');

$.ajaxSetup({
    headers:
        { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

_.templateSettings = {
    interpolate: /\{\{=(.+?)\}\}/g,
    evaluate: /\{\{(.+?)\}\}/g,
};

window.App = {
    config: {
        localizationArray: {}
    },
    getConfig: function () {
        // Config loading
        $.ajax('/api/v1/config', {
            async: false,
            dataType: 'json'
        }).done(function(data) {
            App.config = data;
        }).fail(function (data) {
            YourCloud.addAlert("Can't load configuration", 'danger');
            console.error(JSON.stringify(data));
        });
    }
};




