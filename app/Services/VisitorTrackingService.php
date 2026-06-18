<?php

namespace App\Services;

use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitorTrackingService
{
    public function track(Request $request): void
    {
        if (! $this->shouldTrack($request)) {
            return;
        }

        $now = now();
        $visitedOn = $now->copy()->startOfDay();
        $path = '/'.ltrim($request->path(), '/');

        $ip = (string) ($request->ip() ?: 'unknown');
        $userAgent = mb_substr((string) ($request->userAgent() ?: 'unknown'), 0, 500);

        $log = VisitorLog::query()->firstOrNew([
            'visitor_hash' => $this->hash("{$ip}|{$userAgent}"),
            'visited_on' => $visitedOn,
        ]);

        if (! $log->exists) {
            $log->fill([
                'ip_hash' => $this->hash($ip),
                'user_agent_hash' => $this->hash($userAgent),
                'first_path' => $path,
                'last_path' => $path,
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'visit_count' => 1,
            ])->save();

            return;
        }

        $log->forceFill([
            'last_path' => $path,
            'last_seen_at' => $now,
            'visit_count' => $log->visit_count + 1,
        ])->save();
    }

    /**
     * @return array{today_unique: int, month_unique: int, total_unique: int, today_page_views: int}
     */
    public function publicStats(): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        return [
            'today_unique' => VisitorLog::query()->whereDate('visited_on', $today)->count(),
            'month_unique' => VisitorLog::query()->whereDate('visited_on', '>=', $monthStart)->count(),
            'total_unique' => VisitorLog::query()->distinct('visitor_hash')->count('visitor_hash'),
            'today_page_views' => (int) VisitorLog::query()->whereDate('visited_on', $today)->sum('visit_count'),
        ];
    }

    /**
     * @return array{unique_visitors: array<int, int>, page_views: array<int, int>}
     */
    public function currentYearMonthlyStats(): array
    {
        $year = (int) now()->year;
        $uniqueVisitors = array_fill(1, 12, 0);
        $pageViews = array_fill(1, 12, 0);

        VisitorLog::query()
            ->whereYear('visited_on', $year)
            ->get(['visited_on', 'visitor_hash', 'visit_count'])
            ->groupBy(fn (VisitorLog $log): int => Carbon::parse($log->visited_on)->month)
            ->each(function ($logs, int $month) use (&$uniqueVisitors, &$pageViews): void {
                $uniqueVisitors[$month] = $logs->pluck('visitor_hash')->unique()->count();
                $pageViews[$month] = (int) $logs->sum('visit_count');
            });

        return [
            'unique_visitors' => array_values($uniqueVisitors),
            'page_views' => array_values($pageViews),
        ];
    }

    private function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        $userAgent = mb_strtolower((string) $request->userAgent());

        if ($userAgent === '') {
            return false;
        }

        foreach (['bot', 'crawler', 'spider', 'slurp', 'curl', 'wget', 'uptime', 'monitor'] as $needle) {
            if (str_contains($userAgent, $needle)) {
                return false;
            }
        }

        return true;
    }

    private function hash(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('app.key'));
    }
}
