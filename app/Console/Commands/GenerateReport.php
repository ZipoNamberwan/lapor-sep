<?php

namespace App\Console\Commands;

use App\Models\Bs;
use App\Models\LastUpdate;
use App\Models\ReportBs;
use App\Models\ReportPetugas;
use App\Models\ReportRegency;
use App\Models\ReportSubdistrict;
use App\Models\ReportVillage;
use DateTime;
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
        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');
        // $today = '2024-06-01';

        ReportBs::where(['date' => $today])->delete();
        ReportVillage::where(['date' => $today])->delete();
        ReportSubdistrict::where(['date' => $today])->delete();
        ReportRegency::where(['date' => $today])->delete();
        ReportPetugas::where(['date' => $today])->delete();

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
            $row['date'] = $today;

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
            $row['date'] = $today;

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
                $subdistrict_transform[$village['subdistrict_long_code']]['percentage'][] = $village['percentage'];
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
            $row['date'] = $today;

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
                $regency_transform[$subdistrict['regency_long_code']]['percentage'][] = $subdistrict['percentage'];
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
            $row['date'] = $today;

            $regency_report[] = $row;
        }
        ReportRegency::insert($regency_report);
        //generate report kabupaten

        LastUpdate::create([]);

        $sql = "
        SELECT 
        u.id,
        u.name,
        u.regency_id,
        COALESCE(s.status_1_count, 0) AS status_1_count,
        COALESCE(s.status_2_count, 0) AS status_2_count,
        COALESCE(s.status_3_count, 0) AS status_3_count,
        COALESCE(s.status_4_count, 0) AS status_4_count,
        COALESCE(s.status_5_count, 0) AS status_5_count,
        COALESCE(s.status_6_count, 0) AS status_6_count,
        COALESCE(s.status_7_count, 0) AS status_7_count,
        COALESCE(s.status_8_count, 0) AS status_8_count,
        COALESCE(s.status_9_count, 0) AS status_9_count
    FROM 
        users u
    LEFT JOIN 
        (SELECT 
            user_id,
            SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) AS status_1_count,
            SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) AS status_2_count,
            SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) AS status_3_count,
            SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) AS status_4_count,
            SUM(CASE WHEN status_id = 5 THEN 1 ELSE 0 END) AS status_5_count,
            SUM(CASE WHEN status_id = 6 THEN 1 ELSE 0 END) AS status_6_count,
            SUM(CASE WHEN status_id = 7 THEN 1 ELSE 0 END) AS status_7_count,
            SUM(CASE WHEN status_id = 8 THEN 1 ELSE 0 END) AS status_8_count,
            SUM(CASE WHEN status_id = 9 THEN 1 ELSE 0 END) AS status_9_count
        FROM 
            samples
        WHERE 
            status_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9)
        GROUP BY 
            user_id) s
    ON 
        u.id = s.user_id;

                ";

        $reportpetugas = DB::select(DB::raw($sql));

        $rows = [];

        foreach ($reportpetugas as $rp) {
            $row = [];
            if ($rp->id != null) {
                $row['user_id'] = $rp->id;
                $row['name'] = $rp->name;
                $row['regency_id'] = $rp->regency_id;
                $row['status_1_count'] = $rp->status_1_count;
                $row['status_2_count'] = $rp->status_2_count;
                $row['status_3_count'] = $rp->status_3_count;
                $row['status_4_count'] = $rp->status_4_count;
                $row['status_5_count'] = $rp->status_5_count;
                $row['status_6_count'] = $rp->status_6_count;
                $row['status_7_count'] = $rp->status_7_count;
                $row['status_8_count'] = $rp->status_8_count;
                $row['status_9_count'] = $rp->status_9_count;

                $row['date'] = $today;
                $rows[] = $row;
            }
        }

        ReportPetugas::insert($rows);

        return Command::SUCCESS;
    }
}
