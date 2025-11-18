<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Services\RegistrationService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function __construct(private RegistrationService $registrationService){}

    public function index() {
        return inertia('auth/Registration');
    }

    public function store(RegistrationRequest $request) {
        try {
            $this->registrationService->register($request->login, $request->password);
            return redirect()->route('reviews');
        } catch(Exception $e) {
            return $e->getMessage() . 'пошел наухй';
        }
    }
}
