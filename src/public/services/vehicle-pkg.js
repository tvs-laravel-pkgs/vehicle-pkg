app.factory('VehicleSvc', function(RequestSvc) {

    var model = 'vehicle';

    return {
        index: function(params) {
            return RequestSvc.get('/api/' + model + '/index', params);
        },
        read: function(id) {
            return RequestSvc.get('/api/' + model + '/read/' + id);
        },
        saveFromFormData: function(params) {
            return RequestSvc.post('/api/' + model + '/save-from-form-data', params);
        },
        saveFromNgData: function(params) {
            return RequestSvc.post('/api/' + model + '/save-from-ng-data', params);
        },
        remove: function(params) {
            return RequestSvc.post('api/' + model + '/delete', params);
        },
        options: function(params) {
            return RequestSvc.get('/api/' + model + '/options', params);
        },
    };

});