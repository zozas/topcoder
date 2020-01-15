<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class files {
	private $file = NULL;
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
	public function read() {
		return file_get_contents($this->file_name);
	}
	public function write($data) {
		file_put_contents($this->file_name, $data);
	}
	public function contents($dir, $searchname = '') {
		$listDir = array();
		if($handler = opendir($dir)) {
			while (($sub = readdir($handler)) !== FALSE) {
				if ($sub != "." && $sub != ".." && $sub != "Thumb.db") {
					if(is_file($dir."/".$sub)) {
						if ($searchname == "") {
							$listDir[] = $sub;
						} else {
							if (strstr($sub, $searchname)!='')
								$listDir[] = $sub;
						}
					} elseif(is_dir($dir."/".$sub)) {
						$listDir[$sub] = $this->dirlist($dir."/".$sub);
					}
				}
			}   
			closedir($handler);
		}
		return $listDir;   
	}
	public function weight($dir) {
		$handle = opendir($dir);
		$size = 0;
		while ($file = readdir($handle)) {
			if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) {
				$size = $size + filesize($dir.'/'.$file);
			} else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') {
				$size = $size + dir_size($dir.'/'.$file);
			}
		}
		return $size;
	}
	function convert($bytes) {
		$sizes = array('B', 'KB', 'MB', 'GB', 'TB');
		for($i = 0; $bytes >= 1024 && $i < (count($sizes)-1); $bytes /= 1024, $i++);
		return(round($bytes, 2)." ".$sizes[$i]);
	}
}

?>
