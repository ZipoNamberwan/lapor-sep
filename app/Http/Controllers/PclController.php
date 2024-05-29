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

        // reset penggantinya jika status berhasil cacah/sedang/belum
        if ($sample->replacement != null) {
            if ($request->status == 9 || $request->status == 2 || $request->status == 1) {
                Sample::find($sample->replacement->id)->update([
                    'status_id' => 1,
                    'is_selected' => false,
                    'sample_id' => null,
                    'user_id' => null
                ]);

                Commodity::where(['sample_id' => $sample->replacement->id])->delete();
            }
        }
        //reset sampai sini

        //kalau sampel cadangan gagal cacah, maka unbind sampel utamanya
        if ($sample->type == 'Cadangan') {
            if (!($request->status == 9 || $request->status == 2 || $request->status == 1)) {
                $main_samples = Sample::where(['sample_id' => $sample->id])->get();
                foreach ($main_samples as $s) {
                    $s->update(['sample_id' => null]);
                }
            }
        }
        //sampai sini rule sampel cadangan

        $result = $sample->update([
            'status_id' => $request->status,
            'user_id' => Auth::user()->id,
            'is_selected' => $request->status == 9 || $request->status == 2 || $request->status == 1,
            'sample_id' => $request->status == 9 || $request->status == 2 || $request->status == 1 ? null : $sample->sample_id
        ]);

        if ($request->commodities != null) {

            //kalau gagal cacah, hapus komoditasnya kalau ada
            Commodity::where(['sample_id' => $id])->delete();

            //kalau berhasil cacah/sedang maka simpan komoditas nya
            if ($request->status == 9 || $request->status == 2) {
                foreach ($request->commodities as $c) {
                    Commodity::create([
                        'code' => $c['id'],
                        'name' => $c['text'],
                        'sample_id' => $id,
                    ]);
                }
            }
        } else {
            Commodity::where(['sample_id' => $id])->delete();
        }

        return $result;
    }

    public function updateSample(Request $request, $id)
    {
        $sample = Sample::find($id);
        //reset pengganti yang sekarang
        if ($sample->replacement != null) {
            $replacement = Sample::find($sample->replacement->id);
            $replacement->update([
                'is_selected' => false,
                'status_id' => 1,
                'sample_id' => null,
                'user_id' => null,
            ]);
            Commodity::where(['sample_id' => $replacement->id])->delete();
        }
        //reset sampai sini

        $result =  $sample->update([
            'sample_id' => $request->replacement,
        ]);

        //reset yang akan jadi pengganti
        $replacement = Sample::find($request->replacement);
        $replacement->update([
            'is_selected' => true,
            'status_id' => 1,
            'sample_id' => null,
            'user_id' => null,
        ]);
        Commodity::where(['sample_id' => $replacement->id])->delete();
        //reset sampai sini

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
