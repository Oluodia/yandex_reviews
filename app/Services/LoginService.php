<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Hash;
use Auth;

class LoginService {
	public function login(string $login, string $password) {
		try {
			$user = User::where('login', $login)->first();

			if($user && Hash::check($password, $user->password)) {
				Auth::login($user);
			} else {
				throw new Exception('Логин или пароль неправильны');
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}