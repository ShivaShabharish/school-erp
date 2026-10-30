<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $target = $request->get('target', 'all');
        $category = $request->get('category');
        $search = $request->get('search');

        $query = Announcement::with(['author', 'schoolClass', 'section'])
            ->where('academic_year_id', $currentYear?->id);

        if ($target && $target !== 'all') {
            $query->where('target_type', $target);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderByDesc('is_pinned')
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        return view('communication.announcements.index', compact(
            'announcements',
            'classes',
            'currentYear',
            'target',
            'category'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'category' => ['required', 'in:general,academic,urgent,event,sports'],
            'target_type' => ['required', 'in:all,class,staff,parents'],
            'school_class_id' => ['nullable', 'required_if:target_type,class', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'publish_date' => ['required', 'date'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $announcement = Announcement::create([
            ...$validated,
            'is_pinned' => !empty($validated['is_pinned']),
            'author_id' => auth()->id(),
        ]);

        AuditLog::record('announcement_published', $announcement, null, [
            'title' => $announcement->title,
            'target' => $announcement->target_type,
            'category' => $announcement->category,
        ]);

        return redirect()->route('admin.communication.announcements.index')
            ->with('success', "Notice '{$announcement->title}' published successfully.");
    }
}
