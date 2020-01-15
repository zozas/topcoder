<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class scrapper {
	private $page = NULL;
	// Read mode :
	// HTML			Return full html
	// STRIPPED		Strip all html tags
	// LINKS		Return array of links
	// <...>		Strip all html tags except <...>
	public function get($page, $read_mode='') {
		$this->page = $page;
		$opts = array(
			'http' =>
				array(
					'method' => 'GET',
					'ignore_errors' => '1',
					'header'  => 'User-agent: '.$_SERVER['HTTP_USER_AGENT'],
					'request_fulluri' => True
			)
		);
		$context = stream_context_create($opts);
		$scrap_result = file_get_contents($page, false, $context);
		if ($read_mode == "HTML") {
			return $scrap_result;
		} else if ($read_mode == "STRIPPED") {
			return strip_tags($scrap_result);
		} else if ($read_mode == "LINKS") {
			$regex = "((https?|ftp)\:\/\/)?";
			$regex = $regex."([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";
			$regex = $regex."([a-z0-9-.]*)\.([a-z]{2,4})";
			$regex = $regex."(\:[0-9]{2,5})?";
			$regex = $regex."(\/([a-z0-9+\$_-]\.?)+)*\/?";
			$regex = $regex."(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?";
			$regex = $regex."(#[a-z_.-][a-z0-9+\$_.-]*)?";
			$matches = array();
			$pattern = "/$regex/";
			preg_match_all($pattern, $scrap_result, $matches); 
			$matches_links = array();
			$j = 0;
			$max = sizeof($matches[0]);
			for($i = 0; $i < $max;$i++)
			{
				if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $matches[0][$i])) {
					$matches_links[$j] = $matches[0][$i];
					$j = $j + 1;
				}
			}
			unset($matches);
			return $matches_links;
		} else {
			return strip_tags($scrap_result, $read_mode);
		}

	}
}

?>
