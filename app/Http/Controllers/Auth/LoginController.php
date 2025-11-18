<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{

    public function __construct(private LoginService $loginService) {}

    public function index() {
        return inertia('auth/Login');
    }

    public function store(LoginRequest $request) {
        try {
            $this->loginService->login($request->login, $request->password);
            return redirect()->route('reviews');
        } catch(Exception $e) {
            return back()->withErrors([
                'login' => $e->getMessage()
            ]);
        }
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}
