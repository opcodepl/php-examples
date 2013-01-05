<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	private $city;
	
	function __construct() {
		parent::__construct();
		
		$this->load->model('weather_model');
	}
	
	//main page	
	function index()	{
		//get cities list
		$data['cities'] = $this->weather_model->getCities();
		
		$this->load->view('front/header_view');
		$this->load->view('front/main_view', $data);
		$this->load->view('front/footer_view');
	}

	//get weather info from database
	function get_weather_db($weather) {
		$w_arr = array(
			'weather_id'	=> $weather->weather_id,
			'city'			=> $weather->city, 
			'location'		=> $weather->location,
			'time'			=> $weather->time,
			'wind'			=> $weather->wind,
			'visibility'	=> $weather->visibility,
			'temperature'	=> $weather->temperature,
			'dewpoint'		=> $weather->dewpoint,
			'relativehumidity'	=> $weather->relativehumidity,
			'pressure'	=> $weather->pressure
		);
		
		return $w_arr;
	}
	
	//get weather info from web service
	function get_weather_soap() {
		$weather_id = $this->input->post('weather_id');
		
		if(!preg_match('/^[0-9]+$/', $weather_id)) {
			echo 0; exit;
		} else if($weather_id < 0) {
			echo 0; exit;
		}
		
		$this->weather_model->weather_id = $weather_id;
		$weather = $this->weather_model->getWeather();
		
		if(!$weather) {
			echo 0; exit;
		}
		
		//find city name
		//php5.3 - third parameter in strstr
		$city = strstr($weather->location, ',', true);
		$this->city = $city;
		
		$this->load->model('config_model');
		$config = $this->config_model->getConfig();
		
		$this->load->library('weather', array(
			'soap_source'	=> $config->soap_source, 
			'timeout'		=> $config->timeout)
		);
		
		try {
			@$this->weather->connect();
			
			$this->weather->setCity($city);
			
			try {
				$xml = $this->weather->getCity();
			} catch (Exception $e) {
				$weather_arr = $this->get_weather_db($weather);
			}
			
			if(isset($xml)) {
				$weather_arr = array(
						'weather_id'	=> $this->weather_model->weather_id,
						'city'			=> $this->city,
						'location'		=> strip_tags($xml->Location),
						'time'			=> strip_tags($xml->Time),
						'wind'			=> strip_tags($xml->Wind),
						'visibility'	=> strip_tags($xml->Visibility),
						'temperature'	=> strip_tags($xml->Temperature),
						'dewpoint'		=> strip_tags($xml->DewPoint),
						'relativehumidity'	=> strip_tags($xml->RelativeHumidity),
						'pressure'	=> strip_tags($xml->Pressure)
				);
			}
			
		} catch (Exception $e) {
			$weather_arr = $this->get_weather_db($weather);
		}
		
		$data['weather'] = $weather_arr;
		
		$this->weather_model->weather_id = $weather_arr['weather_id'];
		$this->weather_model->location = $weather_arr['location'];
		$this->weather_model->time = $weather_arr['time'];
		$this->weather_model->wind = $weather_arr['wind'];
		$this->weather_model->visibility = $weather_arr['visibility'];
		$this->weather_model->temperature = $weather_arr['temperature'];
		$this->weather_model->dewpoint = $weather_arr['dewpoint'];
		$this->weather_model->relativehumidity = $weather_arr['relativehumidity'];
		$this->weather_model->pressure = $weather_arr['pressure'];
		
		$this->weather_model->updateWeather();
		
		$str = $this->load->view('front/city_view', $data, true);
		echo $str;
	
	}

		
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */