<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        $now = now();

        /*
        |----------------------------------------------------
        | 1. Insert Tags
        |----------------------------------------------------
        */
        $tags = ['mobile', 'web', 'desktop'];

        DB::table('tags')->insert(
            collect($tags)->map(function ($tag) use ($now) {
                return [
                    'name' => $tag,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->toArray()
        );

        $tagIds = DB::table('tags')->pluck('id')->toArray();

  
        $total = 1000;
        $chunkSize = 100;

        $locales = ['en', 'ur', 'ar'];

        for ($i = 0; $i < $total; $i += $chunkSize) {

            $translations = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $translations[] = [
                    'key' => 'key_' . Str::random(10),
                    'value' => 'value_' . Str::random(20),
                    'locale' => $locales[array_rand($locales)],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Insert translations
            DB::table('translations')->insert($translations);

            /*
            |----------------------------------------------------
            | 3. Get inserted IDs safely
            |----------------------------------------------------
            */
            $insertedIds = DB::table('translations')
                ->orderByDesc('id')
                ->limit($chunkSize)
                ->pluck('id')
                ->toArray();

            $insertedIds = array_reverse($insertedIds);

            /*
            |----------------------------------------------------
            | 4. Pivot insert → translation_tag (FIXED)
            |----------------------------------------------------
            */
            $pivot = [];

            foreach ($insertedIds as $translationId) {

                $randomTags = collect($tagIds)->random(rand(1, 2));

                foreach ($randomTags as $tagId) {
                    $pivot[] = [
                        'translation_id' => $translationId,
                        'tag_id' => $tagId,
                    ];
                }
            }

            DB::table('tag_translation')->insert($pivot);
        }
    }
}