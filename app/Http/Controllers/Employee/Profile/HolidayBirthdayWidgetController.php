<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PublicHoliday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HolidayBirthdayWidgetController extends Controller
{
    public function index(Request $request)
    {
        $tz    = config('app.timezone', 'Europe/Berlin');
        $today = Carbon::now($tz)->startOfDay();

        $days  = (int) ($request->integer('days') ?: 30);
        $days  = max(7, min(120, $days));
        $until = $today->copy()->addDays($days)->endOfDay();

        $country = strtoupper((string) ($request->get('country') ?: config('app.holidays_country', 'DE')));
        $state   = $request->get('state'); // optional
        $city    = $request->get('city');  // optional

        $holidays  = $this->getUpcomingPublicHolidays($today, $until, $country, $state, $city);
        $birthdays = $this->getUpcomingEmployeeBirthdays($today, $until);

        return response()->json([
            'range' => [
                'from' => $today->toDateString(),
                'to'   => $until->toDateString(),
                'days' => $days,
            ],
            'filters' => [
                'country' => $country,
                'state'   => $state,
                'city'    => $city,
            ],
            'holidays'  => $holidays,
            'birthdays' => $birthdays,
        ]);
    }

    private function getUpcomingPublicHolidays(Carbon $from, Carbon $to, string $country, ?string $state, ?string $city): array
    {
        $q = PublicHoliday::query()
            ->where('country', $country)
            ->whereDate('end_date', '>=', $from->toDateString())
            ->whereDate('start_date', '<=', $to->toDateString());

        if ($state) $q->where('state', $state);
        if ($city)  $q->where('city', $city);

        $rows = $q->orderBy('start_date')->limit(20)->get();

        $out = [];
        foreach ($rows as $h) {
            $start = Carbon::parse($h->start_date)->startOfDay();
            $end   = Carbon::parse($h->end_date)->startOfDay();

            $daysUntilStart = $from->diffInDays($start, false);
            $isToday        = $from->betweenIncluded($start, $end);
            $lengthDays     = $start->diffInDays($end) + 1;

            $out[] = [
                'id'          => $h->id,
                'name'        => (string) $h->name,
                'comment'     => (string) ($h->comment ?? ''),
                'start_date'  => $start->toDateString(),
                'end_date'    => $end->toDateString(),
                'length_days' => $lengthDays,
                'city'        => $h->city,
                'state'       => $h->state,
                'country'     => $h->country,
                'days_until'  => $daysUntilStart,
                'is_today'    => $isToday,
            ];
        }

        return $out;
    }

    private function getUpcomingEmployeeBirthdays(Carbon $today, Carbon $until): array
    {
        $emps = Employee::query()
            ->whereNotNull('dob')
            ->where('status', 'Active')
            ->select(['id', 'title', 'name', 'midname', 'lastname', 'dob', 'image'])
            ->get();

        $items = [];

        foreach ($emps as $e) {
            $dob = Carbon::parse($e->dob)->startOfDay();

            $next = $today->copy()->setMonth($dob->month)->setDay($dob->day);
            if ($next->lt($today)) $next->addYear();

            if ($next->gt($until)) continue;

            $fullName = trim(implode(' ', array_filter([
                $e->title,
                $e->name,
                $e->midname,
                $e->lastname,
            ])));

            $daysUntil = $today->diffInDays($next, false);

            $items[] = [
                'id'         => $e->id,
                'name'       => $fullName !== '' ? $fullName : ("Employee #{$e->id}"),
                'dob'        => $dob->toDateString(),
                'next_date'  => $next->toDateString(),
                'days_until' => $daysUntil,
                'is_today'   => $daysUntil === 0,
                'avatar_url' => $this->resolveEmployeeImageUrl($e->image),
            ];
        }

        usort($items, fn ($a, $b) => $a['days_until'] <=> $b['days_until']);

        return array_slice($items, 0, 10);
    }

    /**
     * FIX: Your employee images are in /public/images/employee (NOT storage).
     * Accepts:
     * - full url (http/https) -> returns as-is
     * - absolute public path "/images/employee/x.jpg" -> asset(...)
     * - filename "x.jpg" -> asset("images/employee/x.jpg")
     * - "images/employee/x.jpg" -> asset(...)
     * - storage paths "public/..." or "storage/..." only if they actually exist on the public disk
     */
    private function resolveEmployeeImageUrl(?string $path): ?string
    {
        $p = trim((string) $path);
        if ($p === '') return null;

        // 1) absolute URL
        if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://')) {
            return $p;
        }

        // normalize slashes
        $p = ltrim(str_replace('\\', '/', $p), '/');

        // 2) if it already points to public images folder
        if (str_starts_with($p, 'images/employee/')) {
            return asset($p);
        }

        // 3) if only filename, assume /public/images/employee/<file>
        if (!str_contains($p, '/')) {
            return asset('images/employee/' . $p);
        }

        // 4) if dev accidentally stores "/images/employee/x.jpg"
        if (str_starts_with($p, 'images/')) {
            return asset($p);
        }

        /**
         * 5) Only use Storage::url if this is truly a stored file.
         *    Otherwise you get wrong "/storage/..." URLs.
         */
        $storageLike = str_starts_with($p, 'public/')
            || str_starts_with($p, 'storage/')
            || str_starts_with($p, 'uploads/')
            || str_starts_with($p, 'employee/'); // if you ever put them on disk paths

        if ($storageLike) {
            // for "storage/xxx" strip leading "storage/" to check disk("public")
            $diskPath = str_starts_with($p, 'storage/') ? substr($p, 8) : $p;
            $diskPath = str_starts_with($diskPath, 'public/') ? substr($diskPath, 7) : $diskPath;

            try {
                if (Storage::disk('public')->exists($diskPath)) {
                    return Storage::url($diskPath);
                }
            } catch (\Throwable $e) {
                // ignore and fallback to asset
            }
        }

        // 6) final fallback: treat as public relative path
        return asset($p);
    }
}
