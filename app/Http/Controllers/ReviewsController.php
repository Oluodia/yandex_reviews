<?php

namespace App\Http\Controllers;

use App\Services\ReviewsService;
use Cookie;
use Exception;
use Illuminate\Http\Request;
use Auth;

class ReviewsController extends Controller
{

    public function __construct(private ReviewsService $reviewsService) {}

    public function reviews(Request $request) {
        $url = $request->input('url', Cookie::get('yandex_link'));
        if(!$url) {
            $reviews = null;
            $summary = null;
        } else {
            $parseReviews = $this->reviewsService->parseReviews($url);

            $reviews = $parseReviews['reviews'];
            $summary = $parseReviews['summary'];
        }

        return inertia('reviews/Home', [
            'logoUrl' => asset('images/logo.png'),
            'userName' => Auth::user()->login,
            'yandex_link' => $url,
            'reviews' => $reviews,
            'summary' => $summary
        ]);
    }

    public function settings() {
        return inertia('reviews/Setting', [
            'logoUrl' => asset('images/logo.png'),
            'userName' => Auth::user()->login,
        ]);
    }

    public function setUrl(Request $request) {

        try {
            $request->validate([
                'link' => 'required|active_url'
            ]);

            $cookie = cookie('yandex_link', $request->link);

            return redirect()->route('reviews')->cookie($cookie);
        } catch(Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
