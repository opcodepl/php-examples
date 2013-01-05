<?php 

class Login_model extends CI_Model {
	
	public $login;
	public $password;
	
	function __construct() {
		parent::__construct();
	}
	
	function checkLogin() {
		$this->db->select('count(users_id) as "howMany"');
		$this->db->from('users');
		$this->db->where('login', $this->login);
		$this->db->where('password', $this->password);
		
		return $this->db->get()->row()->howMany;
	}
	
	function updatePassword() {
		$this->db->set('password', $this->password);
		
		$this->db->where('login', $this->login);
		
		$this->db->update('users');
		
		return $this->db->affected_rows();
	}
	
	
}

?>