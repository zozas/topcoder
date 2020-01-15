<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class template {
	protected $file;
	protected $values = array();
	public function open($file) {
		$this->file = $file;
		if($file != '') {
			if(is_readable($file)) {
				$this->file = $file;
				return(TRUE);
			} else {
				return(FALSE);
			}
		}
	}
	public function set($key, $value) {
		$this->values[$key] = $value;
	}
	public function get() {
		if (!file_exists($this->file)) {
			die($this->file);
		}
		$output = file_get_contents($this->file);
		foreach ($this->values as $key => $value) {
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}
		return $output;
	}
}		

?>
