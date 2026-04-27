<?php 

namespace App\Repositories;

use App\Models\Translation;

class TranslationRepository
{
    public function search($filters)
    {
        return Translation::query()
            ->when($filters['key'] ?? null, fn($q, $key) =>
                $q->where('key', 'like', "%$key%")
            )
            ->when($filters['locale'] ?? null, fn($q, $locale) =>
                $q->where('locale', $locale)
            )
            ->when($filters['tag'] ?? null, function ($q, $tag) {
                $q->whereHas('tags', fn($q) => $q->where('name', $tag));
            })
            ->with('tags')
            ->paginate(50);
    }

    public function export($locale)
    {
        return Translation::where('locale', $locale)
            ->pluck('value', 'key');
    }
}