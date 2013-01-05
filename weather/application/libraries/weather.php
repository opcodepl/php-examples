<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * manage connection to web service
 */
class Weather {

	private $city;
	private $client;
	private $soap_source;
	private $timeout;
	
	function __construct($settings_arr) {
		$this->soap_source = $settings_arr['soap_source'];
		$this->timeout = $settings_arr['timeout'];
	}
	
	public function setCity($city) {
		$this->city = htmlspecialchars($city);
	}
	
	public function connect() {
		$this->client = new SoapClient($this->soap_source, array(
			'connection_timeout' => $this->timeout, 
			'cache_wsdl'	=> WSDL_CACHE_NONE, //don't cache
			'exceptions'	=> true
		));
	}
	
	public function getCity() {
		//get weather for CityName
		$xml = $this->client->getWeather(array('CountryName' => '', 'CityName' => $this->city));

		$xml = $xml->GetWeatherResult;
		
		//check if city exists
		if($xml == 'Data Not Found')
			return false;
		
		//load xml
		//header shows utf-16, but the body is utf-8 
		$load = simplexml_load_string(preg_replace('/utf-16/', 'utf-8', $xml));
		
		return $load;
	}
}
