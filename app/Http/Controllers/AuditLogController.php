<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $event = $request->query('event');
        $model = $request->query('model');

        $logs = AuditLog::query()
            ->with('user')
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('label', 'like', "%{$search}%")
                        ->orWhere('event', 'like', "%{$search}%")
                        ->orWhere('auditable_type', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($event, fn ($query, string $event) => $query->where('event', $event))
            ->when($model, fn ($query, string $model) => $query->where('auditable_type', $model))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,
            'search' => $search,
            'selectedEvent' => $event,
            'selectedModel' => $model,
            'events' => AuditLog::query()
                ->select('event')
                ->distinct()
                ->orderBy('event')
                ->pluck('event', 'event')
                ->map(fn (string $event) => str($event)->replace('_', ' ')->headline()->toString()),
            'models' => AuditLog::query()
                ->select('auditable_type')
                ->distinct()
                ->orderBy('auditable_type')
                ->pluck('auditable_type'),
        ]);
    }
}
