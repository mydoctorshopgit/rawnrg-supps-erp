<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Category extends Model
{
    // protected $with = ['category_translations'];
    protected $fillable = ['parent_id', 'name', 'content_description', 'short_description', 'overview', 'our_range', 'why_us', 'faqs', 'color', 'lite_color', 'tagline', 'order_level', 'digital', 'banner', 'icon', 'cover_image', 'background_image', 'meta_title', 'meta_description', 'save_big', 'is_top_pick'];
    protected $casts = ['faqs' => 'array'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $category_translation = $this->category_translations->where('lang', $lang)->first();
        return $category_translation != null ? $category_translation->$field : $this->$field;
    }

    public function category_translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function coverImage()
    {
        return $this->belongsTo(Upload::class, 'cover_image');
    }

    public function catIcon()
    {
        return $this->belongsTo(Upload::class, 'icon');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function bannerImage()
    {
        return $this->belongsTo(Upload::class, 'banner');
    }

    public function classified_products()
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function sizeChart()
    {
        return $this->belongsTo(SizeChart::class, 'id', 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getFullSlugAttribute()
    {
        if ($this->parent) {
            return $this->parent->full_slug . '/' . $this->slug;
        }
        return $this->slug;
    }

    public function getAllParents()
    {
        $parents = [];
        $category = $this;

        while ($category->parentCategory) {
            $parents[] = $category->parentCategory;
            $category = $category->parentCategory;
        }

        // Add current category at the end
        $parents = array_reverse($parents);
        $parents[] = $this;

        return $parents;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }



    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('status', 1)
            ->orderBy('order_level', 'desc')
            ->orderBy('name', 'asc');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function bestSellerProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_categories',
            'category_id',
            'product_id'
        )
            ->where('products.best_seller', 1)
            ->with([
                'stocks' => function ($q) {
                    $q->orderBy('price', 'asc');
                },
                'taxes'
            ])
            ->select('products.*')
            ->orderBy('products.created_at', 'desc');
    }

    public function topPickProducts()
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id')
            ->where('products.is_top_pick', 1);
    }
}
