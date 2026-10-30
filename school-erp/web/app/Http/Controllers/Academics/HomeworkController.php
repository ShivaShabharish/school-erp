<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Homework;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeworkController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Staff::with('user')->where('status', 'active')->where('department', 'Teaching')->get();

        $selectedClassId = $request->get('class_id', $classes->first()->id ?? null);

        $query = Homework::with(['schoolClass', 'section', 'subject', 'teacher.user'])->latest('id');

        if ($selectedClassId) {
            $query->where('school_class_id', $selectedClassId);
        }

        $assignments = $query->paginate(15)->withQueryString();

        return view('academics.homework.index', compact(
            'classes',
            'subjects',
            'teachers',
            'selectedClassId',
            'assignments'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'staff_id' => ['required', 'exists:staff,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'assigned_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:assigned_date'],
        ]);

        $hw = Homework::create([
            ...$validated,
            'status' => 'active',
        ]);

        AuditLog::record('homework_assigned', $hw, null, [
            'class_id' => $hw->school_class_id,
            'title' => $hw->title,
        ]);

        return redirect()->route('admin.academics.homework.index', ['class_id' => $validated['school_class_id']])
            ->with('success', 'Homework task assigned to class roster successfully.');
    }
}
