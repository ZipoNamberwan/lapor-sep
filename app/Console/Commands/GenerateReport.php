<?php

namespace App\Console\Commands;

use App\Models\Bs;
use App\Models\LastUpdate;
use App\Models\ReportBs;
use App\Models\ReportRegency;
use App\Models\ReportSubdistrict;
use App\Models\ReportVillage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and store the aggregate report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ReportBs::truncate();
        ReportVillage::truncate();
        ReportSubdistrict::truncate();
        ReportRegency::truncate();

        $sql = "
        SELECT 
            a.bs_id, 
            COALESCE(b.sample_count, 0) AS sample_count,
            COALESCE((b.sample_count / 10.0) * 100, 0) AS percentage
        FROM 
            (SELECT DISTINCT bs_id FROM samples) a
        LEFT JOIN 
            (SELECT 
                bs_id, 
                COUNT(*) AS sample_count
            FROM 
                samples
            WHERE 
                status_id = 9
            GROUP BY 
                bs_id) b
        ON 
            a.bs_id = b.bs_id;

                ";

        $bs = DB::select(DB::raw($sql));

        //generate report bs
        $bs_report = [];
        foreach ($bs as $b) {
            $row = [];
            $bs_tmp = Bs::find($b->bs_id);

            $row['area_code'] = $bs_tmp->long_code;
            $row['short_code'] = $bs_tmp->short_code;
            $row['village_short_code'] = $bs_tmp->village->short_code;
            $row['village_long_code'] = $bs_tmp->village->long_code;
            $row['village_name'] = $bs_tmp->village->name;
            $row['subdistrict_short_code'] = $bs_tmp->village->subdistrict->short_code;
            $row['subdistrict_long_code'] = $bs_tmp->village->subdistrict->long_code;
            $row['subdistrict_name'] = $bs_tmp->village->subdistrict->name;
            $row['regency_short_code'] = $bs_tmp->village->subdistrict->regency->short_code;
            $row['regency_long_code'] = $bs_tmp->village->subdistrict->regency->long_code;
            $row['regency_name'] = $bs_tmp->village->subdistrict->regency->name;

            $row['count'] = $b->sample_count;
            $row['percentage'] = $b->percentage;

            $bs_report[] = $row;
        }
        ReportBs::insert($bs_report);
        //generate report bs

        //generate report desa
        $village_transform = [];
        foreach ($bs_report as $b) {
            if (!array_key_exists($b['village_long_code'], $village_transform)) {
                $village_transform[$b['village_long_code']] = [
                    'percentage' => [$b['percentage']],
                    'village_short_code' => $b['village_short_code'],
                    'village_long_code' => $b['village_long_code'],
                    'village_name' => $b['village_name'],
                    'subdistrict_short_code' => $b['subdistrict_short_code'],
                    'subdistrict_long_code' => $b['subdistrict_long_code'],
                    'subdistrict_name' => $b['subdistrict_name'],
                    'regency_short_code' => $b['regency_short_code'],
                    'regency_long_code' => $b['regency_long_code'],
                    'regency_name' => $b['regency_name'],
                ];
            } else {
                $village_transform[$b['village_long_code']]['percentage'][] = $b['percentage'];
            }
        }

        $village_report = [];
        foreach ($village_transform as $v) {
            $row = [];

            $p_total = 0;
            foreach ($v['percentage'] as $p) {
                $p_total = $p_total + $p;
            }

            $row['percentage'] = $p_total / count($v['percentage']);
            $row['village_short_code'] = $v['village_short_code'];
            $row['village_long_code'] = $v['village_long_code'];
            $row['village_name'] = $v['village_name'];
            $row['subdistrict_short_code'] = $v['subdistrict_short_code'];
            $row['subdistrict_long_code'] = $v['subdistrict_long_code'];
            $row['subdistrict_name'] = $v['subdistrict_name'];
            $row['regency_short_code'] = $v['regency_short_code'];
            $row['regency_long_code'] = $v['regency_long_code'];
            $row['regency_name'] = $v['regency_name'];
            $village_report[] = $row;
        }
        ReportVillage::insert($village_report);
        //generate report desa

        //generate report kecamatan
        $subdistrict_transform = [];
        foreach ($village_report as $village) {
            if (!array_key_exists($village['subdistrict_long_code'], $subdistrict_transform)) {
                $subdistrict_transform[$village['subdistrict_long_code']] = [
                    'percentage' => [$village['percentage']],
                    'subdistrict_short_code' => $village['subdistrict_short_code'],
                    'subdistrict_long_code' => $village['subdistrict_long_code'],
                    'subdistrict_name' => $village['subdistrict_name'],
                    'regency_short_code' => $village['regency_short_code'],
                    'regency_long_code' => $village['regency_long_code'],
                    'regency_name' => $village['regency_name'],
                ];
            } else {
                $subdistrict_transform[$village['subdistrict_long_code']]['percentage'][] = $b['percentage'];
            }
        }

        $subdistrict_report = [];
        foreach ($subdistrict_transform as $s) {
            $row = [];

            $p_total = 0;
            foreach ($s['percentage'] as $p) {
                $p_total = $p_total + $p;
            }

            $row['percentage'] = $p_total / count($s['percentage']);
            $row['subdistrict_short_code'] = $s['subdistrict_short_code'];
            $row['subdistrict_long_code'] = $s['subdistrict_long_code'];
            $row['subdistrict_name'] = $s['subdistrict_name'];
            $row['regency_short_code'] = $s['regency_short_code'];
            $row['regency_long_code'] = $s['regency_long_code'];
            $row['regency_name'] = $s['regency_name'];
            $subdistrict_report[] = $row;
        }
        ReportSubdistrict::insert($subdistrict_report);
        //generate report kecamatan

        //generate report kabupaten
        $regency_transform = [];
        foreach ($subdistrict_report as $subdistrict) {
            if (!array_key_exists($subdistrict['regency_long_code'], $regency_transform)) {
                $regency_transform[$subdistrict['regency_long_code']] = [
                    'percentage' => [$subdistrict['percentage']],
                    'regency_short_code' => $subdistrict['regency_short_code'],
                    'regency_long_code' => $subdistrict['regency_long_code'],
                    'regency_name' => $subdistrict['regency_name'],
                ];
            } else {
                $regency_transform[$subdistrict['regency_long_code']]['percentage'][] = $b['percentage'];
            }
        }

        $regency_report = [];
        foreach ($regency_transform as $r) {
            $row = [];

            $p_total = 0;
            foreach ($r['percentage'] as $p) {
                $p_total = $p_total + $p;
            }

            $row['percentage'] = $p_total / count($r['percentage']);
            $row['regency_short_code'] = $r['regency_short_code'];
            $row['regency_long_code'] = $r['regency_long_code'];
            $row['regency_name'] = $r['regency_name'];
            $regency_report[] = $row;
        }
        ReportRegency::insert($regency_report);
        //generate report kabupaten

        LastUpdate::create([]);
        return Command::SUCCESS;
    }
}
