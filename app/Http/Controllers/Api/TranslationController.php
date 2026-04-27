<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\Tag;
use OpenApi\Attributes as OA;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationRepository $repo,
        private TranslationService $service
    ) {}

    #[OA\Get(
        path: '/api/translations',
        summary: 'Get translations list',
        description: 'Search and filter translations',
        tags: ['Translations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'key',
                in: 'query',
                required: false,
                description: 'Filter by translation key',
                schema: new OA\Schema(type: 'string', example: 'auth.login')
            ),
            new OA\Parameter(
                name: 'locale',
                in: 'query',
                required: false,
                description: 'Filter by locale',
                schema: new OA\Schema(type: 'string', example: 'en')
            ),
            new OA\Parameter(
                name: 'tag',
                in: 'query',
                required: false,
                description: 'Filter by tag name',
                schema: new OA\Schema(type: 'string', example: 'mobile')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Translations fetched successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'key', type: 'string', example: 'auth.login'),
                            new OA\Property(property: 'locale', type: 'string', example: 'en'),
                            new OA\Property(property: 'value', type: 'string', example: 'Login')
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            )
        ]
    )]
    public function index(Request $request)
    {
        return $this->repo->search($request->all());
    }

    #[OA\Post(
        path: '/api/translations',
        summary: 'Create translation',
        description: 'Create a new translation and optionally attach tags',
        tags: ['Translations'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['key', 'locale', 'value'],
                properties: [
                    new OA\Property(
                        property: 'key',
                        type: 'string',
                        example: 'home.title'
                    ),
                    new OA\Property(
                        property: 'locale',
                        type: 'string',
                        example: 'en'
                    ),
                    new OA\Property(
                        property: 'value',
                        type: 'string',
                        example: 'Welcome'
                    ),
                    new OA\Property(
                        property: 'tags',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['homepage', 'web']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Translation created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'key', type: 'string', example: 'home.title'),
                        new OA\Property(property: 'locale', type: 'string', example: 'en'),
                        new OA\Property(property: 'value', type: 'string', example: 'Welcome'),
                        new OA\Property(
                            property: 'tags',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'homepage')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The key field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: [
                                'key' => ['The key field is required.'],
                                'locale' => ['The locale field is required.'],
                                'value' => ['The value field is required.']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
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
    #[OA\Get(
        path: '/api/translations/{id}',
        summary: 'Get translation details',
        description: 'Get single translation with tags',
        tags: ['Translations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Translation ID',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Translation fetched successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'key', type: 'string', example: 'home.title'),
                        new OA\Property(property: 'locale', type: 'string', example: 'en'),
                        new OA\Property(property: 'value', type: 'string', example: 'Welcome'),
                        new OA\Property(
                            property: 'tags',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'homepage')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 404,
                description: 'Translation not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\Translation] 1')
                    ]
                )
            )
        ]
    )]
    public function show($id)
    {
        $translation = Translation::with('tags')->findOrFail($id);
    
        return response()->json($translation);
    }

    #[OA\Put(
        path: '/api/translations/{id}',
        summary: 'Update translation',
        description: 'Update translation and optionally sync tags',
        tags: ['Translations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Translation ID',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'key',
                        type: 'string',
                        example: 'home.title'
                    ),
                    new OA\Property(
                        property: 'locale',
                        type: 'string',
                        example: 'en'
                    ),
                    new OA\Property(
                        property: 'value',
                        type: 'string',
                        example: 'Welcome updated'
                    ),
                    new OA\Property(
                        property: 'tags',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['homepage', 'mobile']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Translation updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'key', type: 'string', example: 'home.title'),
                        new OA\Property(property: 'locale', type: 'string', example: 'en'),
                        new OA\Property(property: 'value', type: 'string', example: 'Welcome updated'),
                        new OA\Property(
                            property: 'tags',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'homepage')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 404,
                description: 'Translation not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'No query results for model [App\\Models\\Translation] 1'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'The key field is required.'
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: [
                                'key' => ['The key field is required.'],
                                'locale' => ['The locale field is required.'],
                                'value' => ['The value field is required.']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
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

    #[OA\Get(
        path: '/api/export',
        summary: 'Export translations',
        description: 'Export translations by locale',
        tags: ['Translations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'query',
                required: true,
                description: 'Locale to export',
                schema: new OA\Schema(type: 'string', example: 'en')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Translations exported successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    example: [
                        'auth.login' => 'Login',
                        'auth.logout' => 'Logout',
                        'home.title' => 'Welcome'
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'The locale field is required.'
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: [
                                'locale' => ['The locale field is required.']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function export(Request $request)
    {
        return $this->service->export($request->locale);
    }
}