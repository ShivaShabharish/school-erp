<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currentYear = AcademicYear::current();

        $query = Announcement::with('author')
            ->where('academic_year_id', $currentYear?->id);

        $category = $request->get('category');
        if ($category) {
            $query->where('category', $category);
        }

        $announcements = $query->orderByDesc('is_pinned')
            ->orderByDesc('publish_date')
            ->take(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $announcements->map(fn($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'content' => $a->content,
                'category' => $a->category,
                'target_type' => $a->target_type,
                'publish_date' => $a->publish_date,
                'is_pinned' => (bool) $a->is_pinned,
                'author' => $a->author?->name ?? 'Head of Institution',
            ]),
        ]);
    }
}
