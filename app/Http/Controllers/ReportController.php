<?php

namespace App\Http\Controllers;

use App\Models\Bs;
use App\Models\LastUpdate;
use App\Models\Regency;
use App\Models\ReportBs;
use App\Models\ReportPetugas;
use App\Models\ReportRegency;
use App\Models\ReportSubdistrict;
use App\Models\ReportVillage;
use App\Models\Sample;
use App\Models\Status;
use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('adminkab/report');
    }

    function reportKab()
    {
        $today = date("Y-m-d");
        $regencies = ReportRegency::where(['date' => $today])->orderBy('regency_short_code')->get();
        $lastUpdate = LastUpdate::latest()->first();
        if ($lastUpdate != null) {
            $lastUpdate = $lastUpdate->created_at->addHours(7)->format('j M Y H:i');
        } else {
            $lastUpdate = '';
        }

        return view('report/reportkab', ['regencies' => $regencies, 'lastUpdate' => $lastUpdate]);
    }

    function reportKec($kodekab)
    {
        $today = date("Y-m-d");

        $subdistricts = ReportSubdistrict::where(['date' => $today])->where(['regency_long_code' => $kodekab])->orderBy('subdistrict_short_code')->get();
        $regency = Regency::where(['long_code' => $kodekab])->first();
        $lastUpdate = LastUpdate::latest()->first();
        if ($lastUpdate != null) {
            $lastUpdate = $lastUpdate->created_at->addHours(7)->format('j M Y H:i');
        } else {
            $lastUpdate = '';
        }

        return view('report/reportkec', ['subdistricts' => $subdistricts, 'regency' => $regency, 'lastUpdate' => $lastUpdate]);
    }

    function reportBs($kodekec)
    {
        $today = date("Y-m-d");

        $bs = ReportBs::where(['date' => $today])->where(['subdistrict_long_code' => $kodekec])->orderby('area_code')->get();
        $subdistrict = Subdistrict::where(['long_code' => $kodekec])->first();
        $lastUpdate = LastUpdate::latest()->first();
        if ($lastUpdate != null) {
            $lastUpdate = $lastUpdate->created_at->addHours(7)->format('j M Y H:i');
        } else {
            $lastUpdate = '';
        }

        return view('report/reportbs', ['bs' => $bs, 'subdistrict' => $subdistrict, 'lastUpdate' => $lastUpdate]);
    }

    function reportRuta($kodebs)
    {
        $bs = Bs::where(['long_code' => $kodebs])->first();
        $samples = Sample::where('bs_id', $bs->id)
            ->where(function ($query) {
                $query->where('is_selected', true)
                    ->orWhere('type', 'Utama');
            })
            ->orderBy('no')->get();

        return view('report/reportruta', ['bs' => $bs, 'samples' => $samples]);
    }

    function reportByPetugas()
    {
        $user = User::find(Auth::user()->id);

        if ($user->hasRole('adminprov') || $user->hasRole('pcl')) {
            return abort(401);
        }

        $today = date("Y-m-d");

        $reports = ReportPetugas::where(['date' => $today])->where(['regency_id' => $user->regency_id])->get();
        $lastUpdate = LastUpdate::latest()->first();
        if ($lastUpdate != null) {
            $lastUpdate = $lastUpdate->created_at->addHours(7)->format('j M Y H:i');
        } else {
            $lastUpdate = '';
        }

        $statuses = Status::where('id', '!=', 1)->get();

        return view('report/reportpetugas', ['reports' => $reports, 'lastUpdate' => $lastUpdate, 'user' => $user, 'statuses' => $statuses]);
    }

    function reportDetailPetugas($id)
    {
        $user = User::find($id);
        return view('report/detailpetugas', ['id_petugas' => $id, 'user' => $user]);
    }
}
