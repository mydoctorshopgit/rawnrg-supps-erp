<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientReview;
use Illuminate\Support\Facades\Cache;

class ClientReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = ClientReview::orderBy('id','desc')->paginate(10);
        return view('backend.client-reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.client-reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'role' => 'required|max:255',
            'image' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ]);

        $data = $request->except('_token');
        ClientReview::create($data);

        Cache::forget('client_reviews');
        flash(translate('Review has been created successfully'))->success();
        return redirect()->route('client-reviews.index');
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
    public function edit($id)
    {
        $review = ClientReview::find($id);
        return view('backend.client-reviews.create', compact('review'));
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
         $request->validate([
            'name' => 'required|max:255',
            'role' => 'required|max:255',
            'image' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ]);

        $data = $request->except('_token','_method');
        ClientReview::where('id',$id)->update($data);
        Cache::forget('client_reviews');
        flash(translate('Review has been created successfully'))->success();
        return redirect()->route('client-reviews.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = ClientReview::findOrFail($id);
        if ($review->image && file_exists(public_path('uploads/editor/' . $review->image))) {
            unlink(public_path('uploads/editor/' . $review->image));
        }

        $review->delete();
        Cache::forget('client_reviews');
        flash(translate('Review has been deleted successfully'))->success();
        return redirect()->route('client-reviews.index');
    }
}
