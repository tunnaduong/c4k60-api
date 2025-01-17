<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BirthdayController extends Controller
{
    /**
     * Handle birthday-related queries.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $input = $request->query('show', '');

        if (!in_array($input, ['all', 'recent', ''])) {
            // Query for a specific user
            $user = User::select('name', 'dayofbirth', 'monthofbirth', 'yearofbirth', 'gender', 'username')
                ->where('username', $input)
                ->first();

            if (!$user) {
                return response()->json(['error' => 'no_birthday_found'], 404);
            }

            $daysLeft = $this->calculateDaysLeft($user->dayofbirth, $user->monthofbirth, $user->yearofbirth);

            return response()->json([
                'name' => $user->name,
                'birthday' => "{$user->dayofbirth}/{$user->monthofbirth}/{$user->yearofbirth}",
                'daysleft' => $daysLeft,
                'gender' => $user->gender,
                'username' => $user->username,
            ], 200);
        }

        // Query all users or recent birthdays
        $users = User::select('name', 'dayofbirth', 'monthofbirth', 'yearofbirth', 'gender', 'username')->get();

        if ($users->isEmpty()) {
            return response()->json(['error' => 'no_birthday_found'], 404);
        }

        $data = $users->map(function ($user) {
            $daysLeft = $this->calculateDaysLeft($user->dayofbirth, $user->monthofbirth, $user->yearofbirth);

            return [
                'name' => $user->name,
                'birthday' => "{$user->dayofbirth}/{$user->monthofbirth}/{$user->yearofbirth}",
                'daysleft' => $daysLeft,
                'gender' => $user->gender,
                'username' => $user->username,
            ];
        })->toArray();

        usort($data, fn($a, $b) => $a['daysleft'] <=> $b['daysleft']);

        if ($input === 'recent' || $input === '') {
            $data = array_slice($data, 0, 5); // Limit to 5 recent birthdays
        }

        return response()->json($data, 200);
    }

    /**
     * Calculate days left for the next birthday.
     *
     * @param int $day
     * @param int $month
     * @param int $year
     * @return int
     */
    private function calculateDaysLeft($day, $month, $year)
    {
        $birthdate = strtotime("$year-$month-$day");
        $currentDate = strtotime(date("Y-m-d"));

        $currentYear = date("Y");
        $nextBirthday = strtotime("$currentYear-$month-$day");

        if ($nextBirthday < $currentDate) {
            $nextBirthday = strtotime(($currentYear + 1) . "-$month-$day");
        }

        $timeDiff = $nextBirthday - $currentDate;

        return round($timeDiff / 86400); // Convert seconds to days
    }
}
