    app.config(['$routeProvider', function($routeProvider) {
        $routeProvider.
        //Vehicle Make
        when('/vehicle-pkg/vehicle-make/list', {
            template: '<vehicle-make-list></vehicle-make-list>',
            title: 'Vehicle Makes',
        }).
        when('/vehicle-pkg/vehicle-make/add', {
            template: '<vehicle-make-form></vehicle-make-form>',
            title: 'Add Vehicle Make',
        }).
        when('/vehicle-pkg/vehicle-make/edit/:id', {
            template: '<vehicle-make-form></vehicle-make-form>',
            title: 'Edit Vehicle Make',
        }).
        when('/vehicle-pkg/vehicle-make/card-list', {
            template: '<vehicle-make-card-list></vehicle-make-card-list>',
            title: 'Vehicle Make Card List',
        });
    }]);

    app.config(['$routeProvider', function($routeProvider) {
        $routeProvider.
        //Vehicle Model
        when('/vehicle-pkg/vehicle-model/list', {
            template: '<vehicle-model-list></vehicle-model-list>',
            title: 'Vehicle Models',
        }).
        when('/vehicle-pkg/vehicle-model/add', {
            template: '<vehicle-model-form></vehicle-model-form>',
            title: 'Add Vehicle Model',
        }).
        when('/vehicle-pkg/vehicle-model/edit/:id', {
            template: '<vehicle-model-form></vehicle-model-form>',
            title: 'Edit Vehicle Model',
        }).
        when('/vehicle-pkg/vehicle-model/card-list', {
            template: '<vehicle-model-card-list></vehicle-model-card-list>',
            title: 'Vehicle Model Card List',
        });
    }]);

    app.config(['$routeProvider', function($routeProvider) {
        $routeProvider.
        //Vehicle Segment
        when('/vehicle-pkg/vehicle-segment/list', {
            template: '<vehicle-segment-list></vehicle-segment-list>',
            title: 'Vehicle Segments',
        }).
        when('/vehicle-pkg/vehicle-segment/add', {
            template: '<vehicle-segment-form></vehicle-segment-form>',
            title: 'Add Vehicle Segment',
        }).
        when('/vehicle-pkg/vehicle-segment/edit/:id', {
            template: '<vehicle-segment-form></vehicle-segment-form>',
            title: 'Edit Vehicle Segment',
        });
    }]);