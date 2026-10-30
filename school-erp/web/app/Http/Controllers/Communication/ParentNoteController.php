<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ParentNote;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentNoteController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $query = ParentNote::with(['student.schoolClass', 'student.section', 'parent', 'staff']);

        if (auth()->user()->isParent()) {
            $query->where('parent_id', auth()->id());
        } elseif (auth()->user()->isTeacher()) {
            // Find teacher staff record
            $staff = Staff::where('user_id', auth()->id())->first();
            if ($staff) {
                $query->where('staff_id', $staff->id);
            }
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $notes = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $pendingCount = ParentNote::where('status', 'pending')->count();
        $teachers = Staff::where('designation', 'like', '%Teacher%')->get();
        $students = Student::where('status', 'active')->orderBy('first_name')->take(50)->get();

        return view('communication.notes.index', compact(
            'notes',
            'pendingCount',
            'teachers',
            'students',
            'status'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'staff_id' => ['nullable', 'exists:staff,id'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string'],
        ]);

        $note = ParentNote::create([
            ...$validated,
            'parent_id' => auth()->id(),
            'status' => 'pending',
        ]);

        AuditLog::record('parent_note_created', $note, null, [
            'student_id' => $note->student_id,
            'subject' => $note->subject,
        ]);

        return redirect()->route('admin.communication.notes.index')
            ->with('success', "Note sent to school administration successfully.");
    }

    public function reply(ParentNote $note, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reply' => ['required', 'string'],
        ]);

        $note->update([
            'reply' => $validated['reply'],
            'replied_at' => now(),
            'status' => 'replied',
        ]);

        AuditLog::record('parent_note_replied', $note, null, [
            'note_id' => $note->id,
            'subject' => $note->subject,
        ]);

        return redirect()->route('admin.communication.notes.index')
            ->with('success', "Response dispatched to parent.");
    }
}
