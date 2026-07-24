<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bannars;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    // =========================================================================
    // Hero Bannar
    // =========================================================================

    public function heroIndex()
    {
        $bannars = Bannars::where('status', 1)->get();
        return view('backend.bannars.heroBannar.index', compact('bannars'));
    }

    public function heroCreate()
    {
        return view('backend.bannars.heroBannar.create');
    }

    public function heroEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.heroBannar.edit', compact('bannars'));
    }

    public function heroStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 1;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('heroBannar.index');
    }

    public function heroUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('heroBannar.index');
    }

    public function heroDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('heroBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('heroBannar.index');
    }

    // =========================================================================
    // Middle Bannar
    // =========================================================================

    public function middleIndex()
    {
        $bannars = Bannars::where('status', 2)->get();
        return view('backend.bannars.middleBannar.index', compact('bannars'));
    }

    public function middleCreate()
    {
        return view('backend.bannars.middleBannar.create');
    }

    public function middleEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.middleBannar.edit', compact('bannars'));
    }

    public function middleStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 2;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('middleBannar.index');
    }

    public function middleUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('middleBannar.index');
    }

    public function middleDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('middleBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('middleBannar.index');
    }

    // =========================================================================
    // Monthly Bannar
    // =========================================================================

    public function monthlyIndex()
    {
        $bannars = Bannars::where('status', 3)->get();
        return view('backend.bannars.monthlyBannar.index', compact('bannars'));
    }

    public function monthlyCreate()
    {
        return view('backend.bannars.monthlyBannar.create');
    }

    public function monthlyEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.monthlyBannar.edit', compact('bannars'));
    }

    public function monthlyStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 3;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('monthlyBannar.index');
    }

    public function monthlyUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('monthlyBannar.index');
    }

    public function monthlyDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('monthlyBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('monthlyBannar.index');
    }

    // =========================================================================
    // Last Bannar
    // =========================================================================

    public function lastIndex()
    {
        $bannars = Bannars::where('status', 4)->get();
        return view('backend.bannars.lastBannar.index', compact('bannars'));
    }

    public function lastCreate()
    {
        return view('backend.bannars.lastBannar.create');
    }

    public function lastEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.lastBannar.edit', compact('bannars'));
    }

    public function lastStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 4;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('lastBannar.index');
    }

    public function lastUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('lastBannar.index');
    }

    public function lastDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('lastBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('lastBannar.index');
    }

    // =========================================================================
    // Status toggle (AJAX)
    // =========================================================================

    public function status(Request $request)
    {
        $banner = Bannars::find($request->id);
        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Banner not found']);
        }
        $banner->status = $request->status;
        $banner->save();
        Cache::forget('all_banners');
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    // =========================================================================
    // Private helper
    // =========================================================================

    /**
     * Fill banner fields from request based on banner_type.
     */
    private function fillBanner(Bannars $bannars, Request $request): void
    {
        $type = $request->input('banner_type', 'simple');
        $bannars->banner_type = $type;
        $bannars->image       = $request->image;
        $bannars->background_image = $request->background_image;
        $bannars->url         = $request->input('url');

        if ($type === 'product') {
            $bannars->sku           = $request->sku;
            $bannars->product_title = $request->product_title;
            $bannars->price         = $request->price;
            $bannars->vat           = $request->vat;
            $bannars->button_text   = $request->button_text;
            // clear simple-banner fields
            $bannars->title       = null;
            $bannars->description = null;
        } else {
            $bannars->title = $request->title;
            $bannars->badge_text = $request->badge_text;
            $bannars->description = $request->description;
            // clear product-banner fields
            $bannars->sku           = null;
            $bannars->product_title = null;
            $bannars->price         = null;
            $bannars->vat           = null;
            $bannars->button_text   = null;
        }
    }

    public function BestSellerIndex()
    {
        $bannars = Bannars::where('status', 5)->get();
        return view('backend.bannars.bestSellerBanner.index', compact('bannars'));
    }

    public function BestSellerCreate()
    {
        return view('backend.bannars.bestSellerBanner.create');
    }

    public function BestSellerStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 5;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('BestSellerBannar.index');
    }

    public function BestSellerEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.bestSellerBanner.edit', compact('bannars'));
    }

    public function BestSellerUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('BestSellerBannar.index');
    }

    public function BestSellerDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('BestSellerBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('BestSellerBannar.index');
    }

    public function trendingIndex()
    {
        $bannars = Bannars::where('status', 6)->get();
        return view('backend.bannars.trendingBanner.index', compact('bannars'));
    }

    public function trendingCreate()
    {
        return view('backend.bannars.trendingBanner.create');
    }

    public function trendingStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 6;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('trendingBannar.index');
    }

    public function trendingEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.trendingBanner.edit', compact('bannars'));
    }

    public function trendingUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('trendingBannar.index');
    }

    public function trendingDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('trendingBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('trendingBannar.index');
    }

    public function topPickIndex()
    {
        $bannars = Bannars::where('status', 7)->get();
        return view('backend.bannars.topPickBannar.index', compact('bannars'));
    }

    public function topPickCreate()
    {
        return view('backend.bannars.topPickBannar.create');
    }

    public function topPickStore(Request $request)
    {
        $bannars = new Bannars();
        $this->fillBanner($bannars, $request);
        $bannars->status = 7;
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('topPickBannar.index');
    }

    public function topPickEdit($id)
    {
        $bannars = Bannars::findOrFail($id);
        return view('backend.bannars.topPickBannar.edit', compact('bannars'));
    }

    public function topPickUpdate(Request $request)
    {
        $bannars = Bannars::findOrFail($request->id);
        $this->fillBanner($bannars, $request);
        $bannars->save();
        Cache::forget('all_banners');
        return redirect()->route('topPickBannar.index');
    }

    public function topPickDelete($id)
    {
        $bannars = Bannars::find($id);
        if (!$bannars) {
            return redirect()->route('topPickBannar.index');
        }
        $bannars->delete();
        Cache::forget('all_banners');
        return redirect()->route('topPickBannar.index');
    }
}
