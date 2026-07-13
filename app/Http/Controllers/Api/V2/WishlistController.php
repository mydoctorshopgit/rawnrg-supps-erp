<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\WishlistCollection;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{

    public function index()
    {
        $userId = auth()->id();

        $wishlists = Wishlist::where('user_id', $userId)
            ->whereHas('product') // ensures product exists (replaces manual whereIn logic)
            ->latest()
            ->get();

        return new WishlistCollection($wishlists);
    }

    public function store(Request $request)
    {
        Wishlist::updateOrCreate(
            ['user_id' => $request->user_id, 'product_id' => $request->product_id]
        );
        return response()->json(['message' => translate('Product is successfully added to your wishlist')], 201);
    }

    public function destroy($id)
    {
        try {
            Wishlist::destroy($id);
            return response()->json(['result' => true, 'message' => translate('Product is successfully removed from your wishlist')], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 200);
        }
    }

    public function add(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $userId = auth()->id();

                // Get existing wishlist item (single query)
                $wishlist = Wishlist::where([
                    'product_id' => $request->product_id,
                    'user_id'    => $userId
                ])->first();

                // If already exists
                if ($wishlist) {
                    return response()->json([
                        'message' => translate('Product present in wishlist'),
                        'is_in_wishlist' => true,
                        'product_id' => (int) $request->product_id,
                        'wishlist_id' => (int) $wishlist->id
                    ], 200);
                }

                // Create new wishlist entry
                $wishlist = Wishlist::create([
                    'user_id'    => $userId,
                    'product_id' => $request->product_id
                ]);

                return response()->json([
                    'message' => translate('Product added to wishlist'),
                    'is_in_wishlist' => true,
                    'product_id' => (int) $request->product_id,
                    'wishlist_id' => (int) $wishlist->id
                ], 200);
            });
        } catch (\Throwable $e) {

            Log::error('Wishlist Add Error: ' . $e->getMessage(), [
                'product_id' => $request->product_id,
                'user_id'    => auth()->id(),
            ]);

            return response()->json([
                'message' => translate('Something went wrong'),
                'is_in_wishlist' => false
            ], 500);
        }
    }

    public function remove(Request $request)
    {
        $userId = auth()->id();

        // Single query to check existence
        $wishlist = Wishlist::where([
            'product_id' => $request->product_id,
            'user_id'    => $userId
        ])->first();

        // If not found
        if (!$wishlist) {
            return response()->json([
                'message' => translate('Product in not in wishlist'),
                'is_in_wishlist' => false,
                'product_id' => (int) $request->product_id,
                'wishlist_id' => 0
            ], 200);
        }

        // Delete existing record
        $wishlist->delete();

        return response()->json([
            'message' => translate('Product is removed from wishlist'),
            'is_in_wishlist' => false,
            'product_id' => (int) $request->product_id,
            'wishlist_id' => 0
        ], 200);
    }

    public function isProductInWishlist(Request $request)
    {
        $userId = auth()->id();

        // Single query instead of count + first
        $wishlist = Wishlist::where([
            'product_id' => $request->product_id,
            'user_id'    => $userId
        ])->first();

        if ($wishlist) {
            return response()->json([
                'message' => translate('Product present in wishlist'),
                'is_in_wishlist' => true,
                'product_id' => (int) $request->product_id,
                'wishlist_id' => (int) $wishlist->id
            ], 200);
        }

        return response()->json([
            'message' => translate('Product is not present in wishlist'),
            'is_in_wishlist' => false,
            'product_id' => (int) $request->product_id,
            'wishlist_id' => 0
        ], 200);
    }
}
