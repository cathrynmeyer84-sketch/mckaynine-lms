<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\Resource;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $categories = $resources->pluck('category')->filter()->unique()->sort()->values();

        return view('handler.resources.index', compact('resources', 'categories'));
    }

    public function show(Resource $resource)
    {
        abort_unless($resource->is_published, 404);
        return view('handler.resources.show', compact('resource'));
    }
}
