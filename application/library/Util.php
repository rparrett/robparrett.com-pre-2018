<?php

class Util {
	public static function base64url_decode($plainText) {
		$base64 = strtr($plainText, '-_','+/');
		return base64_decode($base64);
	}

	public static function base64url_encode($plainText) {
		$base64 = base64_encode($plainText);
		$base64url = rtrim(strtr($base64, '+/', '-_'), '=');
		return $base64url;
	}

	public static function time_ago($time) {
		$now = time();

		if (!$time) {
			$ago = "unknown hours";
		} else {
			$elapsed = $now - $time;
			
			$num = $elapsed;

			if ($elapsed < 60) {
				$ago = $elapsed . " second";
			} elseif ($elapsed < 60 * 60) {
				$num = floor($elapsed / 60);

				$ago = $num . " minute";
			} elseif ($elapsed < 60 * 60 * 24) {
				$num = floor($elapsed / 60 / 60);

				$ago = $num . " hour";
			} else {
				$num = floor($elapsed / 60 / 60 / 24);

				$ago = $num . " day";
			}

			if ($num > 1) {
				$ago .= "s";
			}
		}

		return $ago;
	}
}
