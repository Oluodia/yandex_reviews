<?php

namespace App\Services;

use App\Models\User;
use Auth;
use Exception;
use Hash;

class RegistrationService {
	
	public function register(string $login, string $password) {
		try {
			$user = new User();
			$user->login = $login;
			$user->password = Hash::make($password);
			$user->save();

			Auth::login($user);;
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}