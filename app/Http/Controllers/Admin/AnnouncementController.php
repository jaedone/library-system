<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = DB::table('announcements')
            ->orderBy('is_archived')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('admin.website.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return redirect()->route('admin.website.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);

        $imagePath = $this->storeAnnouncementImage($request);

        $announcementId = DB::table('announcements')->insertGetId([
            'announcement_key' => Str::slug($validated['title']) . '-' . Str::random(6),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'icon' => 'bi-megaphone',
            'image_path' => $imagePath,
            'is_active' => true,
            'is_archived' => false,
            'sort_order' => 0,
            'published_at' => $validated['published_at'] ?? now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully.',
                'item' => $this->findAnnouncement($announcementId),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(int $announcement)
    {
        return redirect()->route('admin.website.index');
    }

    public function edit(int $announcement)
    {
        return redirect()->route('admin.website.index');
    }

    public function update(Request $request, int $announcement)
    {
        $announcementData = DB::table('announcements')
            ->where('id', $announcement)
            ->first();

        abort_if(!$announcementData, 404);

        $validated = $this->validateAnnouncement($request);

        $imagePath = $announcementData->image_path;

        if ($request->hasFile('announcement_image')) {
            $this->deleteStoredFile($imagePath);
            $imagePath = $this->storeAnnouncementImage($request);
        }

        DB::table('announcements')
            ->where('id', $announcement)
            ->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'icon' => 'bi-megaphone',
                'image_path' => $imagePath,
                'is_active' => true,
                'published_at' => $validated['published_at'] ?? now(),
                'updated_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully.',
                'item' => $this->findAnnouncement($announcement),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function archive(Request $request, int $announcement)
    {
        $announcementData = DB::table('announcements')
            ->where('id', $announcement)
            ->first();

        abort_if(!$announcementData, 404);

        $archive = $request->boolean('archive');

        DB::table('announcements')
            ->where('id', $announcement)
            ->update([
                'is_archived' => $archive,
                'updated_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $archive
                    ? 'Announcement archived successfully.'
                    : 'Announcement restored successfully.',
                'item' => $this->findAnnouncement($announcement),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', $archive ? 'Announcement archived successfully.' : 'Announcement restored successfully.');
    }

    public function destroy(Request $request, int $announcement)
    {
        $announcementData = DB::table('announcements')
            ->where('id', $announcement)
            ->first();

        abort_if(!$announcementData, 404);

        DB::table('announcements')
            ->where('id', $announcement)
            ->delete();

        $this->deleteStoredFile($announcementData->image_path);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:2000'],
            'published_at' => ['nullable', 'date'],
            'announcement_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);
    }

    private function findAnnouncement(int $id)
    {
        return DB::table('announcements')
            ->where('id', $id)
            ->first();
    }

    private function storeAnnouncementImage(Request $request): ?string
    {
        if (!$request->hasFile('announcement_image')) {
            return null;
        }

        $path = $request->file('announcement_image')
            ->store('announcement-images', 'public');

        return 'storage/' . $path;
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (!Str::startsWith($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}