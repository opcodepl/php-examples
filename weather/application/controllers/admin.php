<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	private $salt = 'dg;H43-pQ23';
	
	private function check_login() {
		if($this->session->userdata('cms_logged_in') != 1) 
			redirect('admin/cities');
	}
	
	public function __construct() {
		parent::__construct();
		
		$this->load->model('login_model');
		$this->load->model('weather_model');
		$this->load->model('config_model');
	}

	//login page
	function index() {
		if($this->session->userdata('cms_logged_in') == 1) 
			redirect('admin/cities');

		$this->load->view('admin/login_view');
	}
	
	//check if user exists and provided password is correct
	function login() {
		session_start();
		
		if($this->session->userdata('cms_logged_in') == 1) 
			redirect('admin/cities');
		
		if($this->input->post('login') == false || $this->input->post('password') == false) {
			$this->session->set_flashdata('err', 'Zły login lub hasło. Spróbuj ponownie.');
			redirect('admin');
		}
		
		
		$this->login_model->login = $this->input->post('login');
		$this->login_model->password = md5($this->salt.$this->input->post('password'));

		if(!$this->login_model->checkLogin()) {
			$this->session->set_flashdata('err', 'Zły login lub hasło. Spróbuj ponownie.');
			redirect('admin');
		}
		
		$this->session->set_userdata('cms_logged_in', '1');
		
		redirect('admin/cities');
	}
	
	//logout from admin panel
	function logout() {
		session_start();
		
		if($this->session->userdata('cms_logged_in') != 1) {
			$this->session->set_flashdata('err', 'Musisz być zalogowany, żeby móc oglądać tą stronę.');
			redirect('admin');
		}
		
		$this->session->unset_userdata('cms_logged_in');
		
		$this->session->set_flashdata('msg', 'Zostałeś wylogowany.');
			redirect('admin');
	}
	
	
	//main admin page: list of cities, form to type a new city name
	function cities() {
		$this->check_login();
		
		$header['menu'] = 'cities';
		$data['cities'] = $this->weather_model->getCities();
		
		$location = $this->session->flashdata('location');
		if($location) $location = simplexml_load_string($location);
		
		$data['location'] = $location;
		$data['i'] = 0;
		
		$this->load->view('admin/header_view', $header);
		$this->load->view('admin/cities_view', $data);
		$this->load->view('admin/footer_view');
	}
	
	//check if city exists in web service
	function cities_check() {
		$this->check_login();
		
		$city = htmlspecialchars($this->input->post('city'));
		
		if(!$city) {
			$this->session->set_flashdata('err', 'Podaj nazwę miasta.');
			redirect('admin/cities');
		}
		
		$config = $this->config_model->getConfig();
		
		$this->load->library('weather', array(
			'soap_source'	=> $config->soap_source, 
			'timeout'		=> $config->timeout)
		);
		
		try {
			@$this->weather->connect(); //silent because of network errors, which causes exit/die
		} catch (Exception $e) {
           	$this->session->set_flashdata('err', 'Błąd: '.$e->getMessage());
           	redirect('admin/cities'); 
		}
		
		$this->weather->setCity($city);
		
		try {
			$xml = $this->weather->getCity();
		} catch (Exception $e) {
           	$this->session->set_flashdata('err', 'Błąd: '.$e->getMessage());
           	redirect('admin/cities'); 
		}
		
		if(!$xml) {
			$this->session->set_flashdata('err', 'Nie znaleziono miasta '.$city);
			redirect('admin/cities');
		}
		
		$location = $xml->Location;
		
		$this->session->set_flashdata('location', $location->asXML());
		$this->session->set_flashdata('city', $city);
		redirect('admin/cities');
		
	}
	
	//add city to database
	function city_add() {
		$this->check_login();
		
		$city = $this->input->post('city');
		$location = $this->input->post('location');
		
		$this->load->library('weather', array(
			'soap_source'	=> $this->config_model->soap_source, 
			'timeout'		=> $this->config_model->timeout)
		);
		
		try {
			@$this->weather->connect();
		} catch (Exception $e) {
           	$this->session->set_flashdata('err', 'Błąd: '.$e->getMessage());
           	redirect('admin/cities'); 
		}
		
		$this->weather->setCity($this->input->post('city'));
	
		try {
			$xml = $this->weather->getCity();
		} catch (Exception $e) {
           	$this->session->set_flashdata('err', 'Błąd: '.$e->getMessage());
           	redirect('admin/cities'); 
		}
			
		$this->weather_model->city = $city;
		
		if(!$xml) {
			$this->session->set_flashdata('err', 'Występił błąd. Spróbuj ponownie.');
			redirect('admin/cities');
		}
		
		$this->weather_model->location = strip_tags($xml->Location->asXML());
		$this->weather_model->time = strip_tags($xml->Time->asXML());
		$this->weather_model->wind = strip_tags($xml->Wind->asXML());
		$this->weather_model->visibility = strip_tags($xml->Visibility->asXML());
		$this->weather_model->temperature = strip_tags($xml->Temperature->asXML());
		$this->weather_model->dewpoint = strip_tags($xml->DewPoint->asXML());
		$this->weather_model->relativehumidity = strip_tags($xml->RelativeHumidity->asXML());
		$this->weather_model->pressure = strip_tags($xml->Pressure->asXML());
		
		if($this->weather_model->checkCity()) {
			$this->session->set_flashdata('err', 'Już istnieje miasto o podanej nazwie. Podaj inną nazwę.');
			redirect('admin/cities');
		}
		
		if($this->weather_model->checkLocation()) {
			$this->session->set_flashdata('err', 'Już istnieje miasto o podanej lokalizacji. Podaj nazwę innego miasta..');
			redirect('admin/cities');
		}
		
		if($this->weather_model->add())
			$this->session->set_flashdata('msg', 'Miasto zostało dodane');
		else
			$this->session->set_flashdata('err', 'Wystąpił błąd. Proszę spróbować ponownie.');

		redirect('admin/cities');
	}
	
	//change name of city
	function edit() {
		if(!preg_match('/^[0-9]+$/', $this->uri->segment(3))) {
			$this->session->set_flashdata('err', 'Nie istnieje miasto o podanym numerze ID. Spróbuj ponownie.');
			redirect('admin/cities');
		}
		
		$this->weather_model->weather_id = $this->uri->segment(3);
		
		//check if exists
		$city = $this->weather_model->getCity();
		
		if(!$city) {
			$this->session->set_flashdata('err', 'Nie istnieje strona o podanym numerze ID. Spróbuj ponownie.');
			redirect('admin/cities');
		}
		
		$header['menu'] = 'cities';
		$data['city'] = $city;
		
		$this->load->view('admin/header_view', $header);
		$this->load->view('admin/edit_view', $data);
		$this->load->view('admin/footer_view');
	}
	
	//save changes
	function save() {
		$this->weather_model->city = trim(htmlspecialchars($this->input->post('city')));
		$this->weather_model->weather_id = htmlspecialchars($this->input->post('weather_id'));
		
		$this->session->set_flashdata('city', $this->weather_model->city);
		
		if(!preg_match('/^[0-9]+$/', $this->weather_model->weather_id)) {
			$this->session->set_flashdata('err', 'Nie istnieje miasto o podanym numerze ID. Spróbuj ponownie.');
			redirect('admin/cities');
		}
		
		if(strlen($this->weather_model->city) < 1) {
			$this->session->set_flashdata('err', 'Podaj nazwę miasta');
			redirect('admin/edit/'.$this->weather_model->weather_id);
		}
		
		if($this->weather_model->checkCity()) {
			$this->session->set_flashdata('err', 'Już istnieje miasto o podanej nazwie. Podaj inną nazwę.'.$this->db->last_query());
			redirect('admin/edit/'.$this->weather_model->weather_id);
		}
		
		if($this->weather_model->save()) {
			$this->session->set_flashdata('msg', 'Zmiany zostały zapisane.');
			redirect('admin/cities');
		}
		
		$this->session->set_flashdata('err', 'Wystąpił błąd w czasie zapisu zmian. Spróbuj ponownie.');
		
		redirect('admin/edit/'.$this->weather_model->weather_id);
	}
	
	//confirm city removal
	function confirm_remove() {
		if(!preg_match('/^[0-9]+$/', $this->uri->segment(3))) {
			$this->session->set_flashdata('err', 'Nie istnieje miasto o podanym ID');
			redirect('admin/cities');
		}
	
		$this->weather_model->weather_id = $this->uri->segment(3);
	
		$city = $this->weather_model->getCity();
	
		if(!$city) {
			$this->session->set_flashdata('err', 'Nie istnieje miasto o podanym ID');
			redirect('admin/cities');
		}
	
		$data['city'] = $city;
	
		$header['menu'] = 'cities';
	
		$this->load->view('admin/header_view', $header);
		$this->load->view('admin/remove_view', $data);
		$this->load->view('admin/footer_view');
	}
	
	//remove city from database
	function remove() {
		if(!preg_match('/^[0-9]+$/', $this->input->post('weather_id'))) {
			$this->session->set_flashdata('err', 'Nie istnieje miasto o podanym ID');
			redirect('admin/cities');
		}
	
		$this->weather_model->weather_id = $this->input->post('weather_id');
		$city = $this->weather_model->getCity();
	
		if(!$city) {
			$this->session->set_flashdata('err', 'Nie istnieje miasto o podanym ID');
			redirect('admin/cities');
		}
	
		switch($this->input->post('delete')) {
			case '0':
				$this->session->set_flashdata('msg', 'Miasto nie zostało usunięte');
				redirect('admin/cities');
				break;
			case '1':
				if($this->weather_model->delete())
					$this->session->set_flashdata('msg', 'Miasto zostało usunięte.');
				else
					$this->session->set_flashdata('err', 'Wystąpił błąd w czasie usuwania miasta. Spróbuj ponownie.');
	
				redirect('admin/cities');
				break;
			default:
				$this->session->set_flashdata('err', 'Wystąpił błąd w czasie usuwania miasta. Spróbuj ponownie.');
				redirect('admin/cities');
		}
	
		redirect('admin/cities');
	}
	
	//config page, with web service link and timeout
	function config() {
		$header['menu'] = 'config';
		$data['config'] = $this->config_model->getConfig();
		
		$this->load->view('admin/header_view', $header);
		$this->load->view('admin/config_view', $data);
		$this->load->view('admin/footer_view');
	}
	
	function save_config() {
		$this->config_model->soap_source = prep_url(trim(htmlspecialchars($this->input->post('soap_source'))));
		$this->config_model->timeout = htmlspecialchars($this->input->post('timeout'));
		
		$this->session->set_flashdata('soap_source', $this->config_model->soap_source);
		$this->session->set_flashdata('timeout', $this->config_model->timeout);
		
		//validates url
		if(!filter_var($this->config_model->soap_source, FILTER_VALIDATE_URL)) {
			$this->session->set_flashdata('err', 'Podany adres url wydaje się być niepoprawny. Spróbuj ponownie.');
			redirect('admin/config');
		}
		
		if(!preg_match('/^[0-9]+$/', $this->config_model->timeout)) {
			$this->session->set_flashdata('err', 'Błędna wartość timeout. Podaj wartość w sekundach.');
			redirect('admin/config');
		}
		
		if(strlen($this->config_model->soap_source) < 11) {
			$this->session->set_flashdata('err', 'Podaj adres usługi sieciowej.');
			redirect('admin/config');
		}
		
		if($this->config_model->timeout < 1) {
			$this->session->set_flashdata('err', 'Minimalny timeout to 1 sekunda.');
			redirect('admin/config');
		}
		
		if($this->config_model->saveConfig()) {
			$this->session->set_flashdata('msg', 'Zmiany zostały zapisane.');
			redirect('admin/config');
		}
		
		$this->session->set_flashdata('err', 'Wystąpił błąd w czasie zapisu zmian. Spróbuj ponownie.');
		
		redirect('admin/config');
	}
	
}
