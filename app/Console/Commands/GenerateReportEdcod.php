<?php

namespace App\Console\Commands;

use App\Models\BsEdcod;
use App\Models\LastUpdate;
use App\Models\ReportBsEdcod;
use App\Models\ReportRegencyEdcod;
use App\Models\ReportSubdistrictEdcod;
use App\Models\ReportVillageEdcod;
use DateTime;
use Illuminate\Console\Command;

class GenerateReportEdcod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report-edcod:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Report Edcod';

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

        ReportBsEdcod::where(['date' => $today])->delete();
        ReportVillageEdcod::where(['date' => $today])->delete();
        ReportSubdistrictEdcod::where(['date' => $today])->delete();
        ReportRegencyEdcod::where(['date' => $today])->delete();

        $bs = BsEdcod::all();

        //generate report bs
        $bs_report = [];
        foreach ($bs as $b) {
            $row = [];

            $row['area_code'] = $b->bs->long_code;
            $row['short_code'] = $b->bs->short_code;
            $row['village_short_code'] = $b->bs->village->short_code;
            $row['village_long_code'] = $b->bs->village->long_code;
            $row['village_name'] = $b->bs->village->name;
            $row['subdistrict_short_code'] = $b->bs->village->subdistrict->short_code;
            $row['subdistrict_long_code'] = $b->bs->village->subdistrict->long_code;
            $row['subdistrict_name'] = $b->bs->village->subdistrict->name;
            $row['regency_short_code'] = $b->bs->village->subdistrict->regency->short_code;
            $row['regency_long_code'] = $b->bs->village->subdistrict->regency->long_code;
            $row['regency_name'] = $b->bs->village->subdistrict->regency->name;

            $row['total_sample'] = 10;
            $row['success_sample'] = $b->edcoded;
            $row['count'] = $b->edcoded;
            $row['percentage'] = $b->edcoded / 10 * 100;
            $row['date'] = $today;

            $bs_report[] = $row;
        }

        ReportBsEdcod::insert($bs_report);
        //generate report bs

        //generate report desa
        $village_transform = [];
        foreach ($bs_report as $b) {
            if (!array_key_exists($b['village_long_code'], $village_transform)) {
                $village_transform[$b['village_long_code']] = [
                    'success_sample' => [$b['success_sample']],
                    'total_sample' => [$b['total_sample']],
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
                $village_transform[$b['village_long_code']]['total_sample'][] = $b['total_sample'];
                $village_transform[$b['village_long_code']]['success_sample'][] = $b['success_sample'];
            }
        }


        $village_report = [];
        foreach ($village_transform as $v) {
            $row = [];

            $p_success = 0;
            foreach ($v['success_sample'] as $p) {
                $p_success = $p_success + $p;
            }

            $p_total = 0;
            foreach ($v['total_sample'] as $p) {
                $p_total = $p_total + $p;
            }

            $row['percentage'] = $p_success / $p_total * 100;
            $row['success_sample'] = $p_success;
            $row['total_sample'] = $p_total;
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

        ReportVillageEdcod::insert($village_report);
        //generate report desa

        //generate report kecamatan
        $subdistrict_transform = [];
        foreach ($village_report as $village) {
            if (!array_key_exists($village['subdistrict_long_code'], $subdistrict_transform)) {
                $subdistrict_transform[$village['subdistrict_long_code']] = [
                    'success_sample' => [$village['success_sample']],
                    'total_sample' => [$village['total_sample']],
                    'subdistrict_short_code' => $village['subdistrict_short_code'],
                    'subdistrict_long_code' => $village['subdistrict_long_code'],
                    'subdistrict_name' => $village['subdistrict_name'],
                    'regency_short_code' => $village['regency_short_code'],
                    'regency_long_code' => $village['regency_long_code'],
                    'regency_name' => $village['regency_name'],
                ];
            } else {
                $subdistrict_transform[$village['subdistrict_long_code']]['total_sample'][] = $village['total_sample'];
                $subdistrict_transform[$village['subdistrict_long_code']]['success_sample'][] = $village['success_sample'];
            }
        }

        $subdistrict_report = [];
        foreach ($subdistrict_transform as $s) {

            $row = [];

            $p_success = 0;
            foreach ($s['success_sample'] as $p) {
                $p_success = $p_success + $p;
            }

            $p_total = 0;
            foreach ($s['total_sample'] as $p) {
                $p_total = $p_total + $p;
            }

            $row['percentage'] = $p_success / $p_total * 100;
            $row['success_sample'] = $p_success;
            $row['total_sample'] = $p_total;
            $row['subdistrict_short_code'] = $s['subdistrict_short_code'];
            $row['subdistrict_long_code'] = $s['subdistrict_long_code'];
            $row['subdistrict_name'] = $s['subdistrict_name'];
            $row['regency_short_code'] = $s['regency_short_code'];
            $row['regency_long_code'] = $s['regency_long_code'];
            $row['regency_name'] = $s['regency_name'];
            $row['date'] = $today;

            $subdistrict_report[] = $row;
        }

        ReportSubdistrictEdcod::insert($subdistrict_report);
        //generate report kecamatan

        //generate report kabupaten
        $regency_transform = [];
        foreach ($subdistrict_report as $subdistrict) {
            if (!array_key_exists($subdistrict['regency_long_code'], $regency_transform)) {
                $regency_transform[$subdistrict['regency_long_code']] = [
                    'success_sample' => [$subdistrict['success_sample']],
                    'total_sample' => [$subdistrict['total_sample']],
                    'regency_short_code' => $subdistrict['regency_short_code'],
                    'regency_long_code' => $subdistrict['regency_long_code'],
                    'regency_name' => $subdistrict['regency_name'],
                ];
            } else {
                $regency_transform[$subdistrict['regency_long_code']]['total_sample'][] = $subdistrict['total_sample'];
                $regency_transform[$subdistrict['regency_long_code']]['success_sample'][] = $subdistrict['success_sample'];
            }
        }

        $regency_report = [];
        foreach ($regency_transform as $r) {
            $row = [];

            $p_success = 0;
            foreach ($r['success_sample'] as $p) {
                $p_success = $p_success + $p;
            }

            $p_total = 0;
            foreach ($r['total_sample'] as $p) {
                $p_total = $p_total + $p;
            }

            $row['percentage'] = $p_success / $p_total * 100;
            $row['success_sample'] = $p_success;
            $row['total_sample'] = $p_total;
            $row['regency_short_code'] = $r['regency_short_code'];
            $row['regency_long_code'] = $r['regency_long_code'];
            $row['regency_name'] = $r['regency_name'];
            $row['date'] = $today;

            $regency_report[] = $row;
        }

        ReportRegencyEdcod::insert($regency_report);
        //generate report kabupaten

        LastUpdate::create([]);

        return 1;
    }
}
