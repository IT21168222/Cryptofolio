<?php
	class Utils {
		public $accountFile = "../data/account.json";
		public $holdingsFile = "../data/holdings.json";
		public $settingsFile = "../data/settings.json";

		function verifyPassword($password) {
			$account = json_decode(file_get_contents($this->accountFile), true);
			$hash = $account["password"];
			return password_verify($password, $hash);
		}

		function verifySession($token) {
			$account = json_decode(file_get_contents($this->accountFile), true);
			$valid = $account["token"];
			if($token == $valid) {
				return true;
			}
			return false;
		}

		function verifyPIN($pin) {
			$settings = json_decode(file_get_contents($this->settingsFile), true);
			$valid = $settings["pin"];
			if($settings["shareHoldings"] && $pin == $valid) {
				return true;
			}
			return false;
		}

		function generateAccount() {
			$content = file_get_contents($this->accountFile);
			$current = json_decode($content, true);
			if(!array_key_exists("password", $current) || empty($content)) {
				$password = password_hash("admin", PASSWORD_DEFAULT);
				$account = json_encode(array("password" => $password, "token" => $this->generateToken()));
				file_put_contents($this->accountFile, $account);
			}

			if(empty(file_get_contents($this->settingsFile))) {
				$settings = json_encode(array("shareHoldings" => false, "pin" => "0000", "css" => ""));
				file_put_contents($this->settingsFile, $settings);
			}
		}

		function generateToken() {
			$token = $this->getRandomHex(64);
			$account = json_decode(file_get_contents($this->accountFile), true);
			$account["token"] = $token;
			file_put_contents($this->accountFile, json_encode($account));
			return $token;
		}

		function getRandomHex($bytes) {
			return bin2hex(openssl_random_pseudo_bytes($bytes));
		}
	}
?>