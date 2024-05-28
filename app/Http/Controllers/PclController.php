<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Sample;
use App\Models\Status;
use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PclController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pcl/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (!Auth::check()) {
            abort(403);
        }

        $user = User::find(Auth::user()->id);

        $statuses = Status::all();
        $subdistricts = Subdistrict::where(['regency_id' => $user->regency->id])->get();
        return view('pcl/select', ['subdistricts' => $subdistricts, 'statuses' => $statuses]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return view('pcl/edit');
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
        $sample = Sample::find($id);
        $result = $sample->update([
            'status_id' => $request->status,
            'user_id' => Auth::user()->id,
            'is_selected' => $request->status == 9 || $request->status == 2 || $request->status == 1
        ]);

        if ($request->commodities != null) {
            Commodity::where(['sample_id' => $id])->delete();

            foreach ($request->commodities as $c) {
                Commodity::create([
                    'code' => $c['id'],
                    'name' => $c['text'],
                    'sample_id' => $id,
                ]);
            }
        } else {
            Commodity::where(['sample_id' => $id])->delete();
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
