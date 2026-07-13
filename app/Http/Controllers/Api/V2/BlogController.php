<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BlogCollection;
use App\Http\Resources\V2\BlogDetailCollection;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * GET /v2/blogs
     * Optional query params: ?search=keyword&category_id=1&per_page=10
     */
    public function index(Request $request)
    {
        $query = Blog::with('category')
            ->where('status', 1);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('short_description', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = (int) $request->get('per_page', 10);
        $blogs   = $query->latest()->paginate($perPage);

        return (new BlogCollection($blogs))->additional([
            'pagination' => [
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
                'per_page'     => $blogs->perPage(),
                'total'        => $blogs->total(),
            ],
        ]);
    }

    /**
     * GET /v2/blogs/{slug}
     * Accepts either a slug (string) or numeric id
     */
    public function show($slug)
    {
        $blog = is_numeric($slug)
            ? Blog::with('category')->where('status', 1)->findOrFail($slug)
            : Blog::with('category')->where('status', 1)->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'status'  => 200,
            'data'    => new BlogDetailCollection($blog),
        ]);
    }
}
