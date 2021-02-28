<?php 

class User {
	private $db, $data, $session_name, $cookieName, $isLoggedIn;

	public function __construct($user_id = null) {
		$this->db = Database::getInstance();
		$this->session_name = Config::get('session.user_session');
		$this->cookieName = Config::get('cookie.cookie_name');

		if(!$user_id) {
			// var_dump($this->session_name);
			if(Session::exists($this->session_name)) {
				$user_id = Session::get($this->session_name);

				if($this->find($user_id)) {
					$this->isLoggedIn = true;

				} else {
					//logout
				}
			}
		} else {
			$this->find($user_id);
		}			
		
	}

	public function create($fields = []) {
		$this->db->insert('users', $fields);
	}

	public function login($email = null, $password = null, $remember = false) {

		if(!$email && !$password && $this->exists()) {
			Session::put($this->session_name, $this->data()->id);
		} else {

			$user = $this->find($email);
			// var_dump($user);
			if($user) {
				// var_dump(password_verify($password, $this->data()->password));
				if(password_verify($password, $this->data()->password)) {

					Session::put($this->session_name, $this->data()->id);
					// return true;

					if($remember) {
						$hash = hash('sha256', uniqid());

						$hashCheck = $this->db->get('user_sessions', ['user_id', '=', $this->data()->id]);

						if(!$hashCheck->count()) {
							$this->db->insert('user_sessions', [
								'user_id' => $this->data()->id,
								'hash' => $hash
							]);
						} else {
							$hash=$hashCheck->first()->hash;
						}

						Cookie::put($this->cookieName, $hash, Config::get('cookie.cookie_expiry'));
					}
					return true;				
				}
				
			}
		}		

		return false;
	}

	public function find($value = null) {
		
		if(is_numeric($value)) {
			$this->data = $this->db->get('users', ['id', '=', $value])->first();
			// var_dump($this->data);
		} else {
			$this->data = $this->db->get('users', ['email', '=', $value])->first();			
		}

		if($this->data) {
			return $this->data;
		}

		return false;
	}

	public function data() {
		return $this->data;
	}

	public function exists() {
		return (!empty($this->data())) ? true : false;
	}

	public function isLoggedIn() {
		return $this->isLoggedIn;
	}

	public function logout() {
		$this->db->delete('user_sessions', ['user_id', '=', $this->data()->id]);
		Session::delete($this->session_name);
		Cookie::delete($this->cookieName);
	}

	public function update($fields = [], $id = null) {

		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		$this->db->update('users', $id, $fields);
	}

	public function hasPermissions($key = null) {

		$group = $this->db->get('groups', ['id', '=', $this->data()->group_id]);

		if($group->count()) {
			$permissons = $group->first()->permissons;
			$permissons = json_decode($permissons, true);

			if($permissons[$key]) {
				return true;
			}
		}

		return false;
	}

	public static function register_date($date) {
		
		$date = new DateTime($date);
		return $date->format('d/m/Y');
	}


	public function make_admin($id) {
		
		$this->update(['group_id' => '2'], $id);		
	}

	public static function delete_user($id) {
		
		$db = Database::getInstance();		
		$db->delete('users', ['id','=', $id]);		
	}
}