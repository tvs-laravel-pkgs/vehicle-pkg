@if(config('vehicle-pkg.DEV'))
    <?php $vehicle_pkg_prefix = '/packages/abs/vehicle-pkg/src';?>
@else
    <?php $vehicle_pkg_prefix = '';?>
@endif


<script type='text/javascript'>
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

	//Vehicle Makes
    var vehicle_make_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/list.html')}}';
    var vehicle_make_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/form.html')}}';
    var vehicle_make_card_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/card-list.html')}}';
    var vehicle_make_modal_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/partials/vehicle-make-modal-form.html')}}';
</script>
<!-- <script type='text/javascript' src='{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-make/controller.js')}}'></script> -->


<script type='text/javascript'>
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

	//Vehicle Models
    var vehicle_model_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/list.html')}}';
    var vehicle_model_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/form.html')}}';
    var vehicle_model_card_list_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/card-list.html')}}';
    var vehicle_model_modal_form_template_url = '{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/partials/vehicle-model-modal-form.html')}}';
</script>
<!-- <script type='text/javascript' src='{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/controller.js')}}'></script> -->

