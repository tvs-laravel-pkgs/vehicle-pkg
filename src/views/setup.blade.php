@if(config('vehicle-pkg.DEV'))
    <?php $vehicle_pkg_prefix = '/packages/abs/vehicle-pkg/src';?>
@else
    <?php $vehicle_pkg_prefix = '';?>
@endif


<script type='text/javascript'>

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

