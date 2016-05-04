<?php
namespace Mmv\Model;

class MmvDetail
{
    public $mmv_id;
    public $make_id;
    public $model_id;
	public $variant_id;
	public $segment;
    public $type;
    public $bod_style;
	public $start_year;
	public $end_year;
    public $model_discontunued;
    public $noofcylinders;
	public $engine_cc;
	public $power;
    public $fuel_type;
    public $transmission_type;
	public $ground_clearance;
	public $seating_capacity;
    public $boot_space_ltrs;
    public $alloy_wheels;
	public $rear_wash_wiper;
	public $front_fog_lights;
    public $electric_mirrors;
    public $electric_foldable_mirrors;
	public $auto_headlamps;
	public $rain_sensing_wipers;
    public $aircondition;
    public $rear_ac_vents;
	public $power_window;
	public $auto_down_power_windows;
    public $anti_pinch_power_windows;
    public $defogger_front;
	public $leather_seats;
	public $power_seats;
    public $driver_seat_height_adjust;
    public $sun_roof;
	public $keyless_entry;
	public $keyless_start;
    public $cruise_control;
    public $chilled_glovebox;
	public $central_locking;
	public $abs;
    public $traction_control;
    public $electronic_stability_control;
	public $airbags;
	public $immobilizer;
    public $Childsafetylocks;
    public $reversing_camera;
	public $cupholders;
	public $remotefuellid;
    public $tachometer;
    public $gear_shift_indicators;
	public $power_steering;
	public $bluetooth_connectivity;
    public $audio_system;
    public $front_speakers;
	public $rear_speakers;
	public $steeringmountedaudiocontrols;
    public $default_image;
    public $created_on;
	public $created_by;

    public function exchangeArray($data)
    {
        $this->mmv_id		= (isset($data['mmv_id'])) ? $data['mmv_id'] : null;
		$this->make_id		= (isset($data['make_id'])) ? $data['make_id'] : null;
		$this->model_id		= (isset($data['model_id'])) ? $data['model_id'] : null;
		$this->variant_id		= (isset($data['variant_id'])) ? $data['variant_id'] : null;
		$this->segment		= (isset($data['segment'])) ? $data['segment'] : null;
		$this->type		= (isset($data['type'])) ? $data['type'] : null;
		$this->bod_style		= (isset($data['bod_style'])) ? $data['bod_style'] : null;
		$this->start_year		= (isset($data['start_year'])) ? $data['start_year'] : null;
		$this->end_year		= (isset($data['end_year'])) ? $data['end_year'] : null;
		$this->model_discontunued		= (isset($data['model_discontunued'])) ? $data['model_discontunued'] : null;
		$this->noofcylinders		= (isset($data['noofcylinders'])) ? $data['noofcylinders'] : null;
		$this->engine_cc		= (isset($data['engine_cc'])) ? $data['engine_cc'] : null;
		$this->power		= (isset($data['power'])) ? $data['power'] : null;
		$this->fuel_type		= (isset($data['fuel_type'])) ? $data['fuel_type'] : null;
		$this->transmission_type		= (isset($data['transmission_type'])) ? $data['transmission_type'] : null;
		$this->ground_clearance		= (isset($data['ground_clearance'])) ? $data['ground_clearance'] : null;
		$this->seating_capacity		= (isset($data['seating_capacity'])) ? $data['seating_capacity'] : null;
		$this->boot_space_ltrs		= (isset($data['boot_space_ltrs'])) ? $data['boot_space_ltrs'] : null;
		$this->alloy_wheels		= (isset($data['alloy_wheels'])) ? $data['alloy_wheels'] : null;
		$this->rear_wash_wiper		= (isset($data['rear_wash_wiper'])) ? $data['rear_wash_wiper'] : null;
		$this->front_fog_lights		= (isset($data['front_fog_lights'])) ? $data['front_fog_lights'] : null;
		$this->electric_mirrors		= (isset($data['electric_mirrors'])) ? $data['electric_mirrors'] : null;
		$this->electric_foldable_mirrors		= (isset($data['electric_foldable_mirrors'])) ? $data['electric_foldable_mirrors'] : null;
		$this->auto_headlamps		= (isset($data['auto_headlamps'])) ? $data['auto_headlamps'] : null;
		$this->rain_sensing_wipers		= (isset($data['rain_sensing_wipers'])) ? $data['rain_sensing_wipers'] : null;
		$this->aircondition		= (isset($data['aircondition'])) ? $data['aircondition'] : null;
		$this->rear_ac_vents		= (isset($data['rear_ac_vents'])) ? $data['rear_ac_vents'] : null;
		$this->power_window		= (isset($data['power_window'])) ? $data['power_window'] : null;
		$this->auto_down_power_windows		= (isset($data['auto_down_power_windows'])) ? $data['auto_down_power_windows'] : null;
		$this->anti_pinch_power_windows		= (isset($data['anti_pinch_power_windows'])) ? $data['anti_pinch_power_windows'] : null;
		$this->defogger_front		= (isset($data['defogger_front'])) ? $data['defogger_front'] : null;
		$this->leather_seats		= (isset($data['leather_seats'])) ? $data['leather_seats'] : null;
		$this->power_seats		= (isset($data['power_seats'])) ? $data['power_seats'] : null;
		$this->driver_seat_height_adjust		= (isset($data['driver_seat_height_adjust'])) ? $data['driver_seat_height_adjust'] : null;
		$this->sun_roof		= (isset($data['sun_roof'])) ? $data['sun_roof'] : null;
		$this->keyless_entry		= (isset($data['keyless_entry'])) ? $data['keyless_entry'] : null;
		$this->keyless_start		= (isset($data['keyless_start'])) ? $data['keyless_start'] : null;
		$this->cruise_control		= (isset($data['cruise_control'])) ? $data['cruise_control'] : null;
		$this->chilled_glovebox		= (isset($data['chilled_glovebox'])) ? $data['chilled_glovebox'] : null;
		$this->central_locking		= (isset($data['central_locking'])) ? $data['central_locking'] : null;
		$this->abs		= (isset($data['abs'])) ? $data['abs'] : null;
		$this->traction_control		= (isset($data['traction_control'])) ? $data['traction_control'] : null;
		$this->electronic_stability_control		= (isset($data['electronic_stability_control'])) ? $data['electronic_stability_control'] : null;
		$this->airbags		= (isset($data['airbags'])) ? $data['airbags'] : null;
		$this->immobilizer		= (isset($data['immobilizer'])) ? $data['immobilizer'] : null;
		$this->Childsafetylocks		= (isset($data['Childsafetylocks'])) ? $data['Childsafetylocks'] : null;
		$this->reversing_camera		= (isset($data['reversing_camera'])) ? $data['reversing_camera'] : null;
		$this->cupholders		= (isset($data['cupholders'])) ? $data['cupholders'] : null;
		$this->remotefuellid		= (isset($data['remotefuellid'])) ? $data['remotefuellid'] : null;
		$this->tachometer		= (isset($data['tachometer'])) ? $data['tachometer'] : null;
		$this->gear_shift_indicators		= (isset($data['gear_shift_indicators'])) ? $data['gear_shift_indicators'] : null;
		$this->power_steering		= (isset($data['power_steering'])) ? $data['power_steering'] : null;
		$this->bluetooth_connectivity		= (isset($data['bluetooth_connectivity'])) ? $data['bluetooth_connectivity'] : null;
		$this->audio_system		= (isset($data['audio_system'])) ? $data['audio_system'] : null;
		$this->front_speakers		= (isset($data['front_speakers'])) ? $data['front_speakers'] : null;
		$this->rear_speakers		= (isset($data['rear_speakers'])) ? $data['rear_speakers'] : null;
		$this->steeringmountedaudiocontrols		= (isset($data['steeringmountedaudiocontrols'])) ? $data['steeringmountedaudiocontrols'] : null;
		$this->default_image		= (isset($data['default_image'])) ? $data['default_image'] : null;
		$this->created_on		= (isset($data['created_on'])) ? $data['created_on'] : null;
		$this->created_by		= (isset($data['created_by'])) ? $data['created_by'] : null;
    }
}