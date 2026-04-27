<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\Tag;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationRepository $repo,
        private TranslationService $service
    ) {}

    public function index(Request $request)
    {
        return $this->repo->search($request->all());
    }

    public function store(Request $request)
    {
        $translation = Translation::create(
            $request->only(['key','locale','value'])
        );

        if ($request->tags) {
            $tags = Tag::whereIn('name', $request->tags)->pluck('id');
            $translation->tags()->sync($tags);
        }

        return response()->json($translation);
    }

    public function show($id)
    {
        $translation = Translation::with('tags')->findOrFail($id);

        return response()->json($translation);
    }

    public function update(Request $request, $id)
    {
        $translation = Translation::findOrFail($id);

        $translation->update(
            $request->only(['key','locale','value'])
        );

        if ($request->tags) {
            $tags = Tag::whereIn('name', $request->tags)->pluck('id');
            $translation->tags()->sync($tags);
        }

        return response()->json($translation);
    }

    public function export(Request $request)
    {
        return $this->service->export($request->locale);
    }
}