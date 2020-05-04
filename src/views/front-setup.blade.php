@if(config('vehicle-pkg.DEV'))
    <?php $vehicle_pkg_prefix = '/packages/abs/vehicle-pkg/src';?>
@else
    <?php $vehicle_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var vehicle_models_voucher_list_template_url = "{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/vehicle-model.html')}}";
</script>
<script type="text/javascript" src="{{asset($vehicle_pkg_prefix.'/public/themes/'.$theme.'/vehicle-pkg/vehicle-model/controller.js')}}"></script>
