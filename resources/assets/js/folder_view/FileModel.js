window.App.FileModel = Backbone.Model.extend({
    urlRoot: '/api/v1/file',

    safeSave: function(attributes = {}, options = {}) {
        options.success = function(model, response, options) {
            App.files.sortViews();

            if(options.afterSuccess) {
                options.afterSuccess(model, response, options);
            }
        }

        options.error = function(model, response, options) {
            YourCloud.addAlert(response.responseJSON['message'], 'warning');
            model.fetch();

            if(options.afterError) {
                options.afterSuccess(model, response, options);
            }
        }

        return this.save(attributes, options); 
    },

    downloadFile: function() {
        location.replace('/download/' + this.attributes.id);
    }
});


let createNewFile = function() {
    let newFile = new App.FileModel({name: 'New File', parent_id: App.currentDirId, type: 1});

    if(newFile.safeSave().statusText == 'OK') {
        App.files.push(newFile);
    }
}