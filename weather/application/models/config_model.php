<?php 

class Config_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	public $soap_source = 'http://www.webservicex.com/globalweather.asmx?WSDL';
	public $timeout = 1;	
	
	function getConfig() {
		$this->db->select('soap_source, timeout');
		$this->db->from('config');
		
		$q = $this->db->get();
		
		if(!$q->num_rows())
			return false;
		
		return $q->row();
	}
	
	function saveConfig() {
		$this->db->set('soap_source', $this->soap_source);
		$this->db->set('timeout', $this->timeout);
		
		if($this->db->update('config'))
			return true;
		
		return false;
	}
	
	
	
}