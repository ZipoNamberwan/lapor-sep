<?php

namespace App\Http\Controllers;

use App\Models\Bs;
use App\Models\Sample;
use App\Models\Village;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function getVillage($id)
    {
        return json_encode(Village::where('subdistrict_id', $id)->get());
    }
    public function getBs($id)
    {
        return json_encode(Bs::where('village_id', $id)->get());
    }
    public function getSample($id)
    {
        $samples = Sample::where('bs_id', $id)->get();
        foreach ($samples as $sample) {
            $sample->area = ucwords(strtolower($sample->bs->village->subdistrict->name)) . ', ' .
                ucwords(strtolower($sample->bs->village->name)) . ', ' .
                $sample->bs->name;
        }
        return json_encode($samples);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
        //
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
