<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class session {
	public function __construct() {
		if(!isset($_SESSION)) {
			session_start();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function erase_session() {
		setcookie(session_name(), NULL, 0, "/");
		session_destroy();
		session_unset();
	}
	public function set($key, $value) {
		return $_SESSION[$key] = $value;
	}
	public function exist($key) {
		if(isset($_SESSION[$key])) {
			return TRUE;
		}
		return FALSE;
	}
	public function delete($key) {
		unset($_SESSION[$key]);
	}
	public function get($key) {
		if(!isset($_SESSION[$key])) {
			return FALSE;
		}
		return $_SESSION[$key];
	}
}

?>
