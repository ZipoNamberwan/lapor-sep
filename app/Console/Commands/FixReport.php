<?php

namespace App\Console\Commands;

use App\Models\Bs;
use App\Models\ReportBs;
use App\Models\ReportRegency;
use App\Models\ReportSubdistrict;
use App\Models\ReportVillage;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;

class FixReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startDate = new DateTime('2024-06-01');
        $endDate = new DateTime('2024-06-02');

        // Include the end date in the loop
        $endDate->modify('+1 day');

        $period = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate
        );

        foreach ($period as $date) {
            ReportVillage::where(['date' => $date->format("Y-m-d")])->delete();
            ReportSubdistrict::where(['date' => $date->format("Y-m-d")])->delete();
            ReportRegency::where(['date' => $date->format("Y-m-d")])->delete();

            $reportbs = ReportBs::where(['date' => $date->format("Y-m-d")])->get();
            foreach ($reportbs as $bs) {
                $bs->update([
                    'total_sample' => 10,
                    'success_sample' => $bs->percentage / 10
                ]);
            }

            $bs = ReportBs::where(['date' => $date->format("Y-m-d")])->get();

            $bs_report = [];
            foreach ($bs as $b) {
                $row = [];
                $bs_tmp = Bs::where(['long_code' => $b->area_code])->first();

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

                $row['total_sample'] = $b->total_sample;
                $row['success_sample'] = $b->success_sample;
                $row['count'] = $b->success_sample;
                $row['percentage'] = $b->percentage;
                $row['date'] = $date->format("Y-m-d");

                $bs_report[] = $row;
            }

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
                $row['date'] = $date->format("Y-m-d");

                $village_report[] = $row;
            }

            ReportVillage::insert($village_report);
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
                $row['date'] = $date->format("Y-m-d");

                $subdistrict_report[] = $row;
            }

            ReportSubdistrict::insert($subdistrict_report);
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
                $row['date'] = $date->format("Y-m-d");

                $regency_report[] = $row;
            }

            ReportRegency::insert($regency_report);
        }

        return 1;
    }
}
