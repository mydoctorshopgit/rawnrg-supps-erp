<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = Partner::orderBy('id','desc')->paginate(10);
        return view('backend.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.partners.create');
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
            'image' => 'required',
        ]);

        $data = $request->except('_token');
        Partner::create($data);

        flash(translate('Partner has been created successfully'))->success();
        return redirect()->route('partners.index');
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
        $partner = Partner::find($id);
        return view('backend.partners.create',compact('partner'));
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
            'image' => 'required',
        ]);

        $data = $request->except('_token','_method');
        Partner::where('id',$id)->update($data);

        flash(translate('Partner has been updated successfully'))->success();
        return redirect()->route('partners.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        if ($partner->image && file_exists(public_path('uploads/editor/' . $partner->image))) {
            unlink(public_path('uploads/editor/' . $partner->image));
        }

        $partner->delete();
        flash(translate('Partner has been deleted successfully'))->success();
        return redirect()->route('partners.index');
    }



    /**
     * Update Status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function change_status(Request $request) {
        $partner = Partner::find($request->id);
        $partner->status = $request->status;
        $partner->save();
        return 1;
    }

}
