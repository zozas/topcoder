<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class ini {
	private $file = NULL;
	private $data = array();
	public function open($file) {
		$this->file = $file;
		if($file != '') {
			if(is_readable($file)) {
				$this->file = $file;
				return(TRUE);
			} else {
				return(FALSE);
			}
		} else {
			return(FALSE);
		}
	}
	public function read() {
		$this->data = parse_ini_file(realpath($this->file), TRUE);
		if ($this->data == FALSE) {
			return(FALSE);
		} else {
			return(TRUE);
		}
	}
	public function write() {
		$content = NULL;
		foreach ($this->data as $section => $data) {
			$content = $content.'['.$section.']'.PHP_EOL;
			foreach ($data as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $v) {
						$content = $content.$key.'[] = '.(is_numeric($v) ? $v : '"'.$v.'"').PHP_EOL;
					}
				} elseif (empty($val)) {
					$content = $content.$key.' = '.PHP_EOL;
				} else {
					$content = $content.$key.' = '.(is_numeric($val) ? $val : '"'.$val.'"').PHP_EOL;
				}
			}
			$content = $content.PHP_EOL;
		}
		return (($handle = fopen($this->file, 'w')) && fwrite($handle, ";<?php".PHP_EOL.";die();".PHP_EOL.";/*".PHP_EOL.trim($content).PHP_EOL.";*/".PHP_EOL.";?>".PHP_EOL) && fclose($handle)) ? TRUE : FALSE;
	}
	public function exist($section, $key = NULL) {
		if ($key != NULL ) {
			if (isset($this->data[$section][$key])) {
				return(TRUE);
			} else {
				return(FALSE);
			}
		} else {
			if (isset($this->data[$section])) {
				return(TRUE);
			} else {
				return(FALSE);
			}
		}
	}
	public function get($section, $key) {
		if (isset($this->data[$section][$key])) {
			return $this->data[$section][$key];
		} else {
			return(FALSE);
		}
	}
	public function set($section, $key, $value) {
		if (isset($this->data[$section][$key])) {
			$this->data[$section][$key] = $value;
			return(TRUE);
		} else {
			$this->data[$section][$key] = $value;
			return(FALSE);
		}
	}
	public function delete($section, $key = NULL) {
		if ($key != NULL ) {
			if (isset($this->data[$section][$key])) {
				unset($this->data[$section][$key]);
				return(TRUE);
			} else {
				return(FALSE);
			}
		} else {			
			if (isset($this->data[$section])) {
				unset($this->data[$section]);
				return(TRUE);
			} else {
				return(FALSE);
			}
		}
	}
}

?>
