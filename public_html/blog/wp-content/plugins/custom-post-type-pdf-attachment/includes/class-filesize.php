<?php
class CPTA_File_Size {

	function get_file_size($url) {
		$size = $this->get_file_size_from_header($url);
		return $this->file_size_convert($size);
	}

	function get_file_size_from_header($url) {
		$data = get_headers($url, true);
		if (isset($data['Content-Length'])) {
			return (int) $data['Content-Length'];
		} else {
			return 0;
		}
	}
	
	function file_size_convert($bytes){
		if(!$bytes){
			return;
		}
		
		$bytes = floatval($bytes);
			$arBytes = array(
				0 => array(
					"UNIT" => "TB",
					"VALUE" => pow(1024, 4)
				),
				1 => array(
					"UNIT" => "GB",
					"VALUE" => pow(1024, 3)
				),
				2 => array(
					"UNIT" => "MB",
					"VALUE" => pow(1024, 2)
				),
				3 => array(
					"UNIT" => "KB",
					"VALUE" => 1024
				),
				4 => array(
					"UNIT" => "B",
					"VALUE" => 1
				),
			);
	
		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				$result = strval(round($result, 2))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
}
