app.config(['$routeProvider', function($routeProvider) {

    $routeProvider.
    //Vehicle Segment
    when('/vehicle-pkg/vehicle-segment/list', {
        template: '<vehicle-segment-list></vehicle-segment-list>',
        title: 'Vehicle segments',
    }).
    when('/vehicle-pkg/vehicle-segment/add', {
        template: '<vehicle-segment-form></vehicle-segment-form>',
        title: 'Add Vehicle segment',
    }).
    when('/vehicle-pkg/vehicle-segment/edit/:id', {
        template: '<vehicle-segment-form></vehicle-segment-form>',
        title: 'Edit Vehicle segment',
    });

}]);