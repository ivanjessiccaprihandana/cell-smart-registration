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
        $activeProgramNames = $programs
            ->pluck('name')
            ->map(fn (string $name) => str($name)->lower()->toString());

        $homeClassCards = HomeClass::active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(function (HomeClass $homeClass) use ($activeProgramNames) {
                $configuredProgramNames = collect($homeClass->sub_programs ?? [])
                    ->pluck('program_name')
                    ->filter()
                    ->map(fn (string $name) => str($name)->lower()->toString());

                $subPrograms = collect($homeClass->sub_programs ?? [])
                    ->filter(function (array $subProgram) use ($activeProgramNames) {
                        $programName = $subProgram['program_name'] ?? null;

                        return !$programName || $activeProgramNames->contains(str($programName)->lower()->toString());
                    })
                    ->values()
                    ->all();

                if ($homeClass->quota_program_name && !$activeProgramNames->contains(str($homeClass->quota_program_name)->lower()->toString())) {
                    $homeClass->quota_program_name = null;
                    $homeClass->quota_label = null;
                }

                $homeClass->sub_programs = $subPrograms;
                $homeClass->features = collect($homeClass->features ?? [])
                    ->filter(function (string $feature) use ($configuredProgramNames, $activeProgramNames) {
                        $featureName = str($feature)->lower()->toString();

                        return !$configuredProgramNames->contains($featureName)
                            || $activeProgramNames->contains($featureName);
                    })
                    ->values()
                    ->all();

                return $homeClass;
            })
            ->filter(fn (HomeClass $homeClass) => count($homeClass->sub_programs ?? []) > 0)
            ->values();

        return view('home', [
            'landingPrograms' => $programs,
            'landingProgramsByName' => $programs->keyBy('name'),
            'homeClassCards' => $homeClassCards,
        ]);
    }
}
