<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagSave;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('pages.admin.tags', ['tags' => $tags]);
    }

    public function edit(Tag $tag): string
    {
        return json_encode(['tag' => $tag]);
    }

    public function store(TagSave $request): RedirectResponse
    {
        Tag::create($request->validated());
        return back()->with(['tags' => Tag::all(), 'success' => __('Tag created successfully')]);
    }

    public function update(TagSave $request, Tag $tag): RedirectResponse
    {
        $data = $request->validated();

        //todo fix log issue, for the first time log is trying to write to file, it fails with file permission error,
        // - but when we run update again, it works
        $logData = array_merge($data, ['id' => $tag->id]);
//        Log::info("Variable Updated: data: " . json_encode($logData) . " updated by: " . auth()->user()->name . ' id: ' . auth()->id() . 'time: ' . now());
        $tag->update($data);
        return back()->with(['tags' => Tag::all(), 'success' => __('Tag updated successfully')]);
    }

    public function destroy(Tag $tag)
    {
        DB::beginTransaction();
        foreach ($tag->taggables() as $taggables) {
            $taggables->delete();
        }
        $tag->delete();
        DB::commit();

        return back()->with(['tags' => Tag::all(), 'success' => __('Tag deleted successfully')]);
    }
}
