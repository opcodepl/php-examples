<?php 

class Weather_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	public $weather_id = false;
	public $city;
	public $location;
	public $time;
	public $wind;
	public $visibility;
	public $temperature;
	public $dewpiont;
	public $relativehumidity;
	public $pressure;	
	
	function checkCity() {
		$this->db->select('count(weather_id) as howMany');
		$this->db->from('weather');
		$this->db->where('city', $this->city);
		
		if($this->weather_id)
			$this->db->where('weather_id !=', $this->weather_id);
		
		$q = $this->db->get();
		
		return $q->row()->howMany;
	}
	
	function checkLocation() {
		$this->db->select('count(weather_id) as howMany');
		$this->db->from('weather');
		$this->db->where('location', $this->location);
		
		if($this->weather_id)
			$this->db->where('weather_id !=', $this->weather_id);
		
		$q = $this->db->get();
		
		return $q->row()->howMany;
	}
	
	function add() {
		$this->db->set('city', $this->city);
		$this->db->set('location', $this->location);
		$this->db->set('time', $this->time);
		$this->db->set('wind', $this->wind);
		$this->db->set('visibility', $this->visibility);
		$this->db->set('temperature', $this->temperature);
		$this->db->set('dewpoint', $this->dewpoint);
		$this->db->set('relativehumidity', $this->relativehumidity);
		$this->db->set('pressure', $this->pressure);
		
		$this->db->insert('weather');
		
		return $this->db->affected_rows();
	}
	
	function updateWeather() {
		$this->db->set('location', $this->location);
		$this->db->set('time', $this->time);
		$this->db->set('wind', $this->wind);
		$this->db->set('visibility', $this->visibility);
		$this->db->set('temperature', $this->temperature);
		$this->db->set('dewpoint', $this->dewpoint);
		$this->db->set('relativehumidity', $this->relativehumidity);
		$this->db->set('pressure', $this->pressure);
	
		$this->db->where('weather_id', $this->weather_id);
		
		$this->db->update('weather');
	}
	
	function getCities() {
		$this->db->select('weather_id, city, location');
		$this->db->from('weather');
		
		$this->db->order_by('city');
		
		$q = $this->db->get();
		
		if(!$q->num_rows())
			return false;
		
		return $q->result();
	}
	
	function getCity() {
		$this->db->select('weather_id, city, location');
		$this->db->from('weather');
		$this->db->where('weather_id', $this->weather_id);
		
		$q = $this->db->get();
		
		if(!$q->num_rows())
			return false;
		
		return $q->row();
	}
	
	function save() {
		$this->db->set('city', $this->city);
		$this->db->where('weather_id', $this->weather_id);
		
		$this->db->update('weather');
		
		return $this->db->affected_rows();
	}
	
	function delete() {
		$this->db->where('weather_id', $this->weather_id);
		
		$this->db->delete('weather');
		
		return $this->db->affected_rows();
	}
	
	function getWeather() {
		$this->db->select('*');
		$this->db->from('weather');
		$this->db->where('weather_id', $this->weather_id);
		
		$q = $this->db->get();
		
		if(!$q->num_rows())
			return 0;
		
		return $q->row();
	}
}