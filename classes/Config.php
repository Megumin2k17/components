<?php 

class Config {

	public static function get($path = null) {
		if($path) {
			$config = $GLOBALS['config'];

			$path=explode('.', $path);

			// var_dump($path); die;
			foreach ($path as $item) {

				if(isset($config[$item])) {
					$config = $config[$item];					
				}
			}

			return $config;
		}

		return false;
	}
}