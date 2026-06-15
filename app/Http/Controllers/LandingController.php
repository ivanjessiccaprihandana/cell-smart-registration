<?php

namespace App\Http\Controllers;

use App\Models\HomeClass;
use App\Models\Program;

class LandingController extends Controller
{
    public function index()
    {
        $programs = Program::where('status', 'active')
            ->where('name', 'not like', '% - Reguler')
            ->where('name', 'not like', '% - Private')
            ->where('name', 'not like', '% - Conversation')
            ->latest()
            ->get()
            ->each(function (Program $program) {
                $program->registered_users_count = $program->registeredUsersCount();
                $program->remaining_quota = $program->remainingQuota();
                $program->is_full = $program->isFull();
            });
        $homeClassCards = HomeClass::active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('home', [
            'landingPrograms' => $programs,
            'landingProgramsByName' => $programs->keyBy('name'),
            'homeClassCards' => $homeClassCards,
        ]);
    }
}
