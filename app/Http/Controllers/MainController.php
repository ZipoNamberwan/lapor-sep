<?php

namespace App\Http\Controllers;

use App\Models\Bs;
use App\Models\Sample;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        // $samples = Sample::where('bs_id', $id)->where(function ($query) {
        //     $query->where('type', 'Utama')
        //         ->orWhere('is_selected', true);
        // })->get();

        $samples = Sample::where('bs_id', $id)->orderBy('no')->get();

        foreach ($samples as $sample) {
            $sample->status_name = $sample->status->name;
            $sample->color = $sample->status->color;
            $sample->commodities = $sample->commodities;
            $sample->sample_name = $sample->replacement != null ? $sample->replacement->name : null;
            $sample->sample_no = $sample->replacement != null ? $sample->replacement->no : null;

            $sample->area = ucwords(strtolower($sample->bs->village->subdistrict->name)) . ', ' .
                ucwords(strtolower($sample->bs->village->name)) . ', ' .
                $sample->bs->name;
        }
        return json_encode($samples);
    }

    function getPetugasData(Request $request, $id = null)
    {
        if (Auth::user() == null) {
            return abort(401);
        }

        $user = User::find(Auth::user()->id);
        $records = null;
        if ($user->hasRole('adminkab')) {
            if ($id == null) {
                $records = Sample::where(function ($query) {
                    $query->where('is_selected', true)
                        ->orWhere('type', 'Utama');
                })->whereHas('bs', function ($query) use ($user) {
                    $query->where('long_code', 'LIKE', $user->regency->long_code . '%');
                });
            } else {
                $records = Sample::where(['user_id' => $id])->where(function ($query) {
                    $query->where('is_selected', true)
                        ->orWhere('type', 'Utama');
                })->whereHas('bs', function ($query) use ($user) {
                    $query->where('long_code', 'LIKE', $user->regency->long_code . '%');
                });
            }
        } else if ($user->hasRole('pcl')) {
            $records = Sample::where('user_id', $user->id);
        }

        $recordsTotal = $records->count();

        $orderColumn = 'bs_id';
        $orderDir = 'desc';
        if ($request->order != null) {
            if ($request->order[0]['dir'] == 'asc') {
                $orderDir = 'asc';
            } else {
                $orderDir = 'desc';
            }
            if ($request->order[0]['column'] == '0') {
                $orderColumn = 'bs_id';
            } else if ($request->order[0]['column'] == '1') {
                $orderColumn = 'name';
            } else if ($request->order[0]['column'] == '2') {
                $orderColumn = 'status_id';
            }
        }

        $searchkeyword = $request->search['value'];
        $samples = $records->get();
        if ($searchkeyword != null) {
            $samples = $samples->filter(function ($q) use (
                $searchkeyword
            ) {
                return Str::contains(strtolower($q->bs->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->bs->village->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->bs->village->subdistrict->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->name), strtolower($searchkeyword));
            });
        }
        $recordsFiltered = $samples->count();

        if ($orderDir == 'asc') {
            $samples = $samples->sortBy($orderColumn);
        } else {
            $samples = $samples->sortByDesc($orderColumn);
        }

        if ($request->length != -1) {
            $samples = $samples->skip($request->start)
                ->take($request->length);
        }

        $samplesArray = array();
        $i = $request->start + 1;

        foreach ($samples as $sample) {
            $sampleData = array();
            $sampleData["index"] = $i;
            $sampleData["id"] = $sample->id;
            $sampleData["user_id"] = $sample->user != null ? $sample->user->id : '';
            $sampleData["user_name"] = $sample->user != null ? $sample->user->name : '';
            $sampleData["name"] = $sample->name;
            $sampleData["type"] = $sample->type;
            $sampleData["no"] = $sample->no;
            $sampleData["status_id"] = $sample->status->id;
            $sampleData["status_name"] = $sample->status->name;
            $sampleData["status_color"] = $sample->status->color;

            $sampleData["area_code"] = $sample->bs->long_code;
            $sampleData["bs_code"] = $sample->bs->short_code;
            $sampleData["nks"] = $sample->bs->nks;
            $sampleData["village_code"] = $sample->bs->village->short_code;
            $sampleData["village_name"] = ucwords(strtolower($sample->bs->village->name));
            $sampleData["subdistrict_code"] = $sample->bs->village->subdistrict->short_code;
            $sampleData["subdistrict_name"] = ucwords(strtolower($sample->bs->village->subdistrict->name));

            $samplesArray[] = $sampleData;
            $i++;
        }

        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $samplesArray
        ]);
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
