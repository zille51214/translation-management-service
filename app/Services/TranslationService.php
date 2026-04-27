<?php 

namespace App\Services;

use App\Repositories\TranslationRepository;
use Illuminate\Support\Facades\Cache;


class TranslationService
{
    public function export($locale)
    {
        return Cache::remember("translations_$locale", 60, function () use ($locale) {
            return app(TranslationRepository::class)->export($locale);
        });
    }
}