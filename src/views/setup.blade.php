@if(config('vehicle-pkg.DEV'))
    <?php $vehicle_pkg_prefix = '/packages/abs/vehicle-pkg/src';?>
@else
    <?php $vehicle_pkg_prefix = '';?>
@endif


<script type='text/javascript'>

	//Vehicle Segments
	app.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
	    //Vehicle Make
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

    var vehicle_segment_list_template_url = "{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-segment/list.html')}}";
    var vehicle_segment_form_template_url = "{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-segment/form.html')}}";

	//Vehicle Make
	app.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
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


	//Vehicle Makes
    var vehicle_make_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/list.html')}}';
    var vehicle_make_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/form.html')}}';
    var vehicle_make_card_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/card-list.html')}}';
    var vehicle_make_modal_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/partials/vehicle-make-modal-form.html')}}';
</script>


<script type='text/javascript'>

	//Vehicle Models
    var vehicle_model_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/list.html')}}';
    var vehicle_model_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/form.html')}}';
    var vehicle_model_card_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/card-list.html')}}';
    var vehicle_model_modal_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/partials/vehicle-model-modal-form.html')}}';
</script>

