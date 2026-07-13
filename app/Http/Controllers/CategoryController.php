<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\CategoryTranslation;
use App\Utility\CategoryUtility;
use Illuminate\Support\Str;
use Cache;
use Log;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_product_categories'])->only('index');
        $this->middleware(['permission:add_product_category'])->only('create');
        $this->middleware(['permission:edit_product_category'])->only('edit');
        $this->middleware(['permission:delete_product_category'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = $request->search;

        $categoriesQuery = Category::orderBy('order_level', 'desc');

        // Parent Category Filter
        $parent_id = $request->parent_id;
        $child_id  = $request->child_id;   // sub-category of parent
        $grandchild_id = $request->grandchild_id; // sub-child of child

        if ($sort_search) {
            $categoriesQuery->where('name', 'like', '%' . $sort_search . '%');
        }

        // Apply hierarchical filtering
        if ($grandchild_id) {
            $categoriesQuery->where('parent_id', $grandchild_id);
        } elseif ($child_id) {
            $categoriesQuery->where('parent_id', $child_id);
        } elseif ($parent_id) {
            // Show direct children of parent + the parent itself (optional)
            $categoriesQuery->where(function ($q) use ($parent_id) {
                $q->where('parent_id', $parent_id)
                    ->orWhere('id', $parent_id);
            });
        }

        $categories = $categoriesQuery->paginate(15);

        // Load data for dropdowns
        $parentCategories = Category::whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->orderBy('name')
            ->get();

        // If a parent is selected, load its direct children
        $childCategories = [];
        if ($parent_id) {
            $childCategories = Category::where('parent_id', $parent_id)
                ->orderBy('name')
                ->get();
        }

        // If a child is selected, load its sub-children (grandchildren)
        $grandchildCategories = [];
        if ($child_id) {
            $grandchildCategories = Category::where('parent_id', $child_id)
                ->orderBy('name')
                ->get();
        }

        return view('backend.product.categories.index', compact(
            'categories',
            'sort_search',
            'parentCategories',
            'childCategories',
            'grandchildCategories',
            'parent_id',
            'child_id',
            'grandchild_id'
        ));
    }

    public function getChildren(Request $request)
    {
        $parentId = $request->input('parent_id');   // or $request->post('parent_id')

        if (!$parentId) {
            return response()->json([]);
        }

        $children = Category::where('parent_id', $parentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($children);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Recursively propagate a color to all nested children of a category.
     */
    private function propagateColorToChildren(int $categoryId, string $color): void
    {
        Category::where('parent_id', $categoryId)->each(function ($child) use ($color) {
            $child->color = $color;
            $child->save();
            $this->propagateColorToChildren($child->id, $color);
        });
    }

    private function propagateLiteColorToChildren(int $categoryId, string $liteColor): void
    {
        Category::where('parent_id', $categoryId)->each(function ($child) use ($liteColor) {
            $child->lite_color = $liteColor;
            $child->save();
            $this->propagateLiteColorToChildren($child->id, $liteColor);
        });
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $category = new Category();

        $category->fill([
            'name' => $request->name,
            'color' => $request->color,
            'lite_color' => $request->lite_color,
            'tagline' => $request->tagline,
            'short_description' => $request->short_description,
            'overview' => $request->overview,
            'our_range' => $request->our_range,
            'why_us' => $request->why_us,
            'faqs' => $request->faqs ? json_encode($request->faqs) : null,
            'content_description' => $request->content_description,
            'order_level' => $request->order_level ?? 0,
            'digital' => $request->digital,
            'banner' => $request->banner,
            'icon' => $request->icon,
            'cover_image' => $request->cover_image,
            'background_image' => $request->background_image,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'commision_rate' => $request->commision_rate,
        ]);

        // ALT fields
        $category->banner_alt = $request->banner_alt ? json_encode($request->banner_alt) : null;
        $category->icon_alt = $request->icon_alt ? json_encode($request->icon_alt) : null;
        $category->cover_image_alt = $request->cover_image_alt ? json_encode($request->cover_image_alt) : null;

        // Parent handling
        if ($request->parent_id != "0") {
            $parent = Category::find($request->parent_id);

            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Invalid parent category']);
            }

            $category->parent_id = $parent->id;
            $category->level = $parent->level + 1;
        } else {
            $category->level = 0;
            $category->parent_id = 0;
        }

        // Slug
        $slugSource = $request->slug ?? $request->name;
        $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $slugSource)) . '-' . Str::random(5);

        $category->save();

        // Propagate color to all nested children
        if ($request->color) {
            $this->propagateColorToChildren($category->id, $request->color);
        }

        // Propagate lite_color to all nested children
        if ($request->lite_color) {
            $this->propagateLiteColorToChildren($category->id, $request->lite_color);
        }

        // Attributes sync
        if ($request->has('filtering_attributes')) {
            $category->attributes()->sync($request->filtering_attributes);
        }

        // Translation
        CategoryTranslation::updateOrCreate(
            [
                'lang' => env('DEFAULT_LANGUAGE'),
                'category_id' => $category->id
            ],
            [
                'name' => $request->name
            ]
        );

        flash(translate('Category has been inserted successfully'))->success();

        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)
            ->where('digital', $category->digital)
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=', $category->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('backend.product.categories.edit', compact('category', 'categories', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $category->name = $request->name;
        }
        if ($request->order_level != null) {
            $category->order_level = $request->order_level;
        }
        $category->digital = $request->digital;
        $category->content_description = $request->content_description;
        $category->short_description = $request->short_description;
        $category->overview = $request->overview;
        $category->our_range = $request->our_range;
        $category->why_us = $request->why_us;
        $category->faqs = $request->faqs ? json_encode($request->faqs) : null;
        $category->banner = $request->banner;
        $category->icon = $request->icon;
        $category->color = $request->color;
        $category->lite_color = $request->lite_color;
        $category->tagline = $request->tagline;
        $category->cover_image = $request->cover_image;
        $category->background_image = $request->background_image;


        $category->banner_alt = !empty($request->banner_alt) ? json_encode($request->banner_alt) : [];
        $category->icon_alt = !empty($request->icon_alt) ? json_encode($request->icon_alt) : [];
        $category->cover_image_alt = !empty($request->cover_image_alt) ? json_encode($request->cover_image_alt) : [];


        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $previous_level = $category->level;

        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;

            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1;
        } else {
            $category->parent_id = 0;
            $category->level = 0;
        }

        if ($category->level > $previous_level) {
            Log::info('category level changed to downl');
            CategoryUtility::move_level_down($category->id);
        } elseif ($category->level < $previous_level) {
            Log::info('category level changed to up');
            CategoryUtility::move_level_up($category->id);
        }

        if ($request->slug != null) {
            $category->slug = strtolower($request->slug);
        } else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }


        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $category->save();

        // Propagate color to all nested children
        if ($request->color) {
            $this->propagateColorToChildren($category->id, $request->color);
        }

        // Propagate lite_color to all nested children
        if ($request->lite_color) {
            $this->propagateLiteColorToChildren($category->id, $request->lite_color);
        }

        $category->attributes()->sync($request->filtering_attributes);

        $category_translation = CategoryTranslation::firstOrNew(['lang' => $request->lang, 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();

        Cache::forget('featured_categories');
        flash(translate('Category has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->attributes()->detach();

        // Category Translations Delete
        foreach ($category->category_translations as $key => $category_translation) {
            $category_translation->delete();
        }

        foreach (Product::where('category_id', $category->id)->get() as $product) {
            $product->category_id = null;
            $product->save();
        }

        CategoryUtility::delete_category($id);
        Cache::forget('featured_categories');

        flash(translate('Category has been deleted successfully'))->success();
        return redirect()->route('categories.index');
    }

    public function updateFeatured(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->featured = $request->status;
        $category->save();
        Cache::forget('featured_categories');
        return response()->json(1);
    }
    public function sellerStatus(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->best_seller = $request->status;
        $category->save();
        return response()->json(1);
    }

    public function updateSaveBig(Request $request)
    {
        $category = Category::findOrFail($request->id);

        // Only enforce the limit when turning ON
        if ($request->status == 1) {
            $current = Category::where('save_big', 1)
                ->where('id', '!=', $category->id) // exclude self (re-saving same one is fine)
                ->count();

            if ($current >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only 3 categories can be marked as Save Big at a time.'
                ], 422);
            }
        }

        $category->save_big = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $categories = Category::where('parent_id', $category->id)->get();
        foreach ($categories as $cat) {
            $cat->status = $request->status;
            $cat->save();
        }
        $category->status = $request->status;
        $category->save();
        Cache::forget('featured_categories');
        return response()->json(1);
    }

    public function categoriesByType(Request $request)
    {
        $categories = Category::where('parent_id', 0)
            ->where('digital', $request->digital)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.categories.categories_option', compact('categories'));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:5120'
        ]);

        $file     = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path     = public_path('uploads/editor');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $filename);

        return response()->json([
            'url' => asset('public/uploads/editor/' . $filename)
        ]);
    }
}
