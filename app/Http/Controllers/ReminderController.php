<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Reminder;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(Company $company): View
    {
        return view('reminders.index', ['company' => $company, 'reminders' => $company->reminders()->orderByRaw('COALESCE(next_due_at, due_at) asc')->paginate(40)]);
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'due_at' => ['required', 'date'],
            'recurrence' => ['required', 'in:none,daily,weekly,monthly,quarterly,yearly,custom'],
            'custom_interval_days' => ['nullable', 'integer', 'min:1', 'max:3650', 'required_if:recurrence,custom'],
            'email_notification' => ['sometimes', 'boolean'],
        ]);
        $reminder = $company->reminders()->create($data + [
            'assigned_to' => $request->user()->id,
            'next_due_at' => $data['due_at'],
            'email_notification' => $request->boolean('email_notification'),
            'active' => true,
        ]);
        AuditLogger::record('reminder.created', $reminder, [], $reminder->toArray(), $company->id);
        return back()->with('status', 'Przypomnienie zapisane.');
    }

    public function complete(Request $request, Company $company, Reminder $reminder): RedirectResponse
    {
        abort_unless($reminder->company_id === $company->id, 404);
        $old = $reminder->toArray();
        $completedAt = now();
        $reminder->completions()->create(['completed_by' => $request->user()->id, 'completed_at' => $completedAt, 'notes' => $request->input('notes')]);
        $next = $reminder->calculateNextDue($reminder->next_due_at ?: $reminder->due_at ?: $completedAt);
        $reminder->update(['next_due_at' => $next, 'active' => $next !== null]);
        AuditLogger::record('reminder.completed', $reminder, $old, $reminder->fresh()->toArray(), $company->id);
        return back()->with('status', $next ? 'Zadanie wykonane. Wyliczono kolejny termin.' : 'Zadanie wykonane.');
    }
}
