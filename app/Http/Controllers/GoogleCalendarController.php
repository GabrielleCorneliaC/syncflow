<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function redirect(GoogleCalendarService $googleCalendar)
    {
        abort_unless(config('services.google.client_id') && config('services.google.client_secret'), 500, 'Google Calendar credentials belum dikonfigurasi.');

        return redirect()->away($googleCalendar->authorizationUrl());
    }

    public function callback(Request $request, GoogleCalendarService $googleCalendar)
    {
        if ($request->filled('error')) {
            return redirect()->route('personal.index')->with('error', 'Google Calendar gagal dihubungkan.');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $googleCalendar->storeTokensFromCallback($request->user(), $request->string('code')->toString());

        return redirect()->route('personal.index')->with('success', 'Google Calendar berhasil dihubungkan.');
    }
}
