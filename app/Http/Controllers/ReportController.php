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
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::check()) {
            abort(403);
        }

        $lastUpdate = LastUpdate::latest()->first();
        if ($lastUpdate != null) {
            $lastUpdate = $lastUpdate->created_at->addHours(7)->format('j M Y H:i');
        } else {
            $lastUpdate = '';
        }

        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');

        $user = User::find(Auth::user()->id);
        $percentage = 0;
        $dates = [];
        $data = [];

        $startDate = new DateTime('2024-06-01');
        $endDate = new DateTime($today);

        // Include the end date in the loop
        $endDate->modify('+1 day');

        $period = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate
        );

        foreach ($period as $date) {
            $dates[] = $date->format("Y-m-d");
        }

        if ($user->hasRole('adminkab')) {
            $percentage = ReportRegency::where(['regency_long_code' => $user->regency->long_code])->where(['date' => $today])->first()->percentage;
            $data[] = ReportRegency::where(['regency_long_code' => $user->regency->long_code])->whereIn('date', $dates)->get()->pluck('percentage');
        } else {
            $percentage = round(ReportRegency::where(['date' => $today])->get()->pluck('success_sample')->sum() /
                ReportRegency::where(['date' => $today])->get()->pluck('total_sample')->sum() * 100, 2);


            foreach ($dates as $date) {
                $p = ReportRegency::where(['date' => $date])->get()->pluck('percentage');
                if (count($p) == 0) {
                    $data[] = 0;
                } else {
                    $data[] = round($p->sum() / count($p), 2);
                }
            }
        }

        return view('report/index', ['lastUpdate' => $lastUpdate, 'percentage' => $percentage, 'dates' => $dates, 'data' => $data]);
    }

    function reportKab()
    {
        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');

        $regencies = ReportRegency::where(['date' => $today])->orderBy('regency_short_code')->get();
        $prov = round(ReportRegency::where(['date' => $today])->get()->pluck('success_sample')->sum() /
            ReportRegency::where(['date' => $today])->get()->pluck('total_sample')->sum() * 100, 2);

        $lastUpdate = LastUpdate::latest()->first();
        if ($lastUpdate != null) {
            $lastUpdate = $lastUpdate->created_at->addHours(7)->format('j M Y H:i');
        } else {
            $lastUpdate = '';
        }

        return view('report/reportkab', ['regencies' => $regencies, 'lastUpdate' => $lastUpdate, 'prov' => $prov]);
    }

    function reportKec($kodekab)
    {
        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');

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
        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');

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

        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');

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

    function showDownload()
    {
        return view('download/index');
    }

    function download(Request $request)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = User::find(Auth::user()->id);
        if (!$user->hasRole('adminkab|adminprov')) {
            abort(401);
        }
        $area = '';
        if ($user->hasRole('adminkab')) {
            $area = $user->regency->name;
        } else if ($user->hasRole('adminprov')) {
            $area = 'JAWA TIMUR';
        }

        $datetime = new DateTime();
        $datetime->modify('+7 hours');
        $today = $datetime->format('Y-m-d');

        $lastUpdate = LastUpdate::latest()->first();

        $this->validate($request, [
            'level' => 'required',
        ]);

        if ($request->level == 'kab') {

            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            $activeWorksheet->setCellValue('A1', 'Progres Pencacahan SEP');
            $activeWorksheet->mergeCells('A1:C1');
            $activeWorksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $activeWorksheet->setCellValue('A2', 'Data terakhir diupdate pada: ' . $lastUpdate->created_at->addHours(7)->format('j M Y H:i'));
            $activeWorksheet->mergeCells('A2:C2');
            $activeWorksheet->getStyle('A2')->applyFromArray([
                'font' => [
                    'size' => 8,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            $startrow = 4;
            $activeWorksheet->setCellValue('A' . $startrow, 'Kabupaten');
            $activeWorksheet->setCellValue('B' . $startrow, 'Progres Pencacahan (Persen)');
            $activeWorksheet->setCellValue('C' . $startrow, 'Kondisi sd Tanggal');
            $activeWorksheet->getStyle('A' . $startrow . ':C' . $startrow)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $startrow++;

            $rows = ReportRegency::where(['date' => $today])->orderBy('regency_short_code')->get();

            foreach ($rows as $row) {
                $activeWorksheet->setCellValue('A' . $startrow, '[' . $row->regency_short_code . '] ' . $row->regency_name);
                $activeWorksheet->setCellValue('B' . $startrow, $row->percentage);
                $activeWorksheet->setCellValue('C' . $startrow, (new DateTime($row->date))->format('d M Y'));
                $activeWorksheet->getStyle('B' . $startrow . ':C' . $startrow)->applyFromArray(['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],]);

                $startrow++;
            }

            foreach (range('A', 'C') as $columnID) {
                $activeWorksheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Create a Writer to save the spreadsheet as an Excel file
            $writer = new Xlsx($spreadsheet);

            // Prepare the file for download
            $filename = 'Progres Kabupaten.xlsx';

            // Clear any output buffering
            if (ob_get_contents()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } else if ($request->level == 'bs') {
            $rows = null;

            if ($user->hasRole('adminkab')) {
                $rows = ReportBs::where(['regency_short_code' => $user->regency->short_code])->where(['date' => $today])->orderBy('area_code')->get();
            } else if ($user->hasRole('adminprov')) {
                $rows = ReportBs::where(['date' => $today])->orderBy('area_code')->get();
            }

            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            $activeWorksheet->setCellValue('A1', 'Progres Pencacahan SEP Menurut Blok Sensus di ' . $area);
            $activeWorksheet->mergeCells('A1:F1');
            $activeWorksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $activeWorksheet->setCellValue('A2', 'Data terakhir diupdate pada: ' . $lastUpdate->created_at->addHours(7)->format('j M Y H:i'));
            $activeWorksheet->mergeCells('A2:F2');
            $activeWorksheet->getStyle('A2')->applyFromArray([
                'font' => [
                    'size' => 8,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            $startrow = 4;
            $activeWorksheet->setCellValue('A' . $startrow, 'Kabupaten');
            $activeWorksheet->setCellValue('B' . $startrow, 'Kecamatan');
            $activeWorksheet->setCellValue('C' . $startrow, 'Desa');
            $activeWorksheet->setCellValue('D' . $startrow, 'Blok Sensus');
            $activeWorksheet->setCellValue('E' . $startrow, 'Progres Pencacahan (Persen)');
            $activeWorksheet->setCellValue('F' . $startrow, 'Kondisi sd Tanggal');
            $activeWorksheet->getStyle('A' . $startrow . ':F' . $startrow)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $startrow++;

            foreach ($rows as $row) {
                $activeWorksheet->setCellValue('A' . $startrow, '[' . $row->regency_short_code . '] ' . $row->regency_name);
                $activeWorksheet->setCellValue('B' . $startrow, '[' . $row->subdistrict_short_code . '] ' . $row->subdistrict_name);
                $activeWorksheet->setCellValue('C' . $startrow, '[' . $row->village_short_code . '] ' . $row->village_name);
                $activeWorksheet->setCellValue('D' . $startrow, $row->short_code);

                $activeWorksheet->setCellValue('E' . $startrow, $row->percentage);
                $activeWorksheet->setCellValue('F' . $startrow, (new DateTime($row->date))->format('d M Y'));
                $activeWorksheet->getStyle('E' . $startrow . ':F' . $startrow)->applyFromArray(['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,],]);

                $startrow++;
            }

            foreach (range('A', 'F') as $columnID) {
                $activeWorksheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Create a Writer to save the spreadsheet as an Excel file
            $writer = new Xlsx($spreadsheet);

            // Prepare the file for download
            $filename = 'Progres BS ' . $area . '.xlsx';

            // Clear any output buffering
            if (ob_get_contents()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } else if ($request->level == 'sample') {
            $rows = null;

            if ($user->hasRole('adminkab')) {
                $area_code = $user->regency->long_code;
                $rows = Sample::whereHas('bs', function ($query) use ($area_code) {
                    $query->where('long_code', 'LIKE', $area_code . '%');
                })->where(function ($query) {
                    $query->where('is_selected', true)
                        ->orWhere('type', 'Utama');
                })->orderBy('bs_id')->orderBy('no')->get();
            } else if ($user->hasRole('adminprov')) {
                $rows = Sample::where(function ($query) {
                    $query->where('is_selected', true)
                        ->orWhere('type', 'Utama');
                })->where('status_id', '!=', 1)->orderBy('bs_id')->orderBy('no')->get();
            }

            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            $activeWorksheet->setCellValue('A1', 'Progres Pencacahan SEP Menurut Sampel di ' . $area);
            $activeWorksheet->mergeCells('A1:L1');
            $activeWorksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $startrow = 3;
            $activeWorksheet->setCellValue('A' . $startrow, 'Kabupaten');
            $activeWorksheet->setCellValue('B' . $startrow, 'Kecamatan');
            $activeWorksheet->setCellValue('C' . $startrow, 'Desa');
            $activeWorksheet->setCellValue('D' . $startrow, 'Blok Sensus');
            $activeWorksheet->setCellValue('E' . $startrow, 'No Sampel');
            $activeWorksheet->setCellValue('F' . $startrow, 'Nama');
            $activeWorksheet->setCellValue('G' . $startrow, 'Nama Pengelola');
            $activeWorksheet->setCellValue('H' . $startrow, 'Tipe');
            $activeWorksheet->setCellValue('I' . $startrow, 'Status');
            $activeWorksheet->setCellValue('J' . $startrow, 'Pengganti');
            $activeWorksheet->setCellValue('K' . $startrow, 'BS Sampel Pengganti');
            $activeWorksheet->setCellValue('L' . $startrow, 'Komoditas');

            $activeWorksheet->getStyle('A' . $startrow . ':L' . $startrow)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $startrow++;

            foreach ($rows as $row) {

                $activeWorksheet->setCellValue('A' . $startrow, '[' . $row->bs->village->subdistrict->regency->short_code . '] ' . $row->bs->village->subdistrict->regency->name);
                $activeWorksheet->setCellValue('B' . $startrow, '[' . $row->bs->village->subdistrict->short_code . '] ' . $row->bs->village->subdistrict->name);
                $activeWorksheet->setCellValue('C' . $startrow, '[' . $row->bs->village->short_code . '] ' . $row->bs->village->name);
                $activeWorksheet->setCellValue('D' . $startrow, $row->bs->short_code);
                $activeWorksheet->setCellValue('E' . $startrow, $row->no);
                $activeWorksheet->setCellValue('F' . $startrow, $row->name);
                $activeWorksheet->setCellValue('G' . $startrow, $row->name_p);
                $activeWorksheet->setCellValue('H' . $startrow, $row->type);
                $activeWorksheet->setCellValue('I' . $startrow, $row->status->name);
                $activeWorksheet->setCellValue('J' . $startrow, $row->replacement != null ? ('[' . $row->replacement->no . '] ' . $row->replacement->name) : '');
                $activeWorksheet->setCellValue('K' . $startrow, $row->replacement != null ? $row->replacement->bs->long_code : '');
                $activeWorksheet->setCellValue('L' . $startrow, implode(', ', $row->commodities->pluck('name')->toArray()));

                $startrow++;
            }

            foreach (range('A', 'L') as $columnID) {
                $activeWorksheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Create a Writer to save the spreadsheet as an Excel file
            $writer = new Xlsx($spreadsheet);

            // Prepare the file for download
            $filename = 'Progres Ruta ' . $area . '.xlsx';

            // Clear any output buffering
            if (ob_get_contents()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } else if ($request->level == 'petugas') {

            $datetime = new DateTime();
            $datetime->modify('+7 hours');
            $today = $datetime->format('Y-m-d');

            if ($user->hasRole('adminkab')) {
                $rows = ReportPetugas::where(['date' => $today])->where(['regency_id' => $user->regency_id])->orderBy('regency_id')->get();
            } else if ($user->hasRole('adminprov')) {
                $rows = ReportPetugas::where(['date' => $today])->where('regency_id', '!=', null)->orderBy('regency_id')->get();
            }

            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            $statuses = Status::where('id', '!=', 1)->get();
            $columns = [];
            $startChar = 'C';
            foreach ($statuses as $status) {
                $columns[$startChar] = $status->name;
                $startChar++;
            }
            // $startChar = chr(ord($startChar) - 1);

            $activeWorksheet->setCellValue('A1', 'Progres Pencacahan SEP Menurut Petugas di ' . $area);
            $activeWorksheet->mergeCells('A1:' . $startChar . '1');
            $activeWorksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $activeWorksheet->setCellValue('A2', 'Data terakhir diupdate pada: ' . $lastUpdate->created_at->addHours(7)->format('j M Y H:i'));
            $activeWorksheet->mergeCells('A2:' . $startChar . '2');
            $activeWorksheet->getStyle('A2')->applyFromArray([
                'font' => [
                    'size' => 8,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            $startrow = 4;
            $activeWorksheet->setCellValue('A' . $startrow, 'Kabupaten');
            $activeWorksheet->setCellValue('B' . $startrow, 'Nama');
            $activeWorksheet->setCellValue('K' . $startrow, 'Kondisi sd Tanggal');
            foreach ($columns as $col => $name) {
                $activeWorksheet->setCellValue($col . $startrow, $name);
            }

            $activeWorksheet->getStyle('A' . $startrow . ':' . $startChar . $startrow)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $startrow++;

            foreach ($rows as $row) {
                $petugas = User::find($row->user_id);

                if ($petugas->hasRole('pcl')) {
                    $regency = Regency::find($row->regency_id);
                    $activeWorksheet->setCellValue('A' . $startrow, '[' . $regency->short_code . '] ' . $regency->name);
                    $activeWorksheet->setCellValue('B' . $startrow, $row->name);
                    $activeWorksheet->setCellValue('C' . $startrow, $row->status_2_count);
                    $activeWorksheet->setCellValue('D' . $startrow, $row->status_3_count);
                    $activeWorksheet->setCellValue('E' . $startrow, $row->status_4_count);
                    $activeWorksheet->setCellValue('F' . $startrow, $row->status_5_count);
                    $activeWorksheet->setCellValue('G' . $startrow, $row->status_6_count);
                    $activeWorksheet->setCellValue('H' . $startrow, $row->status_7_count);
                    $activeWorksheet->setCellValue('I' . $startrow, $row->status_8_count);
                    $activeWorksheet->setCellValue('J' . $startrow, $row->status_9_count);
                    $activeWorksheet->setCellValue('K' . $startrow, (new DateTime($row->date))->format('d M Y'));

                    $startrow++;
                }
            }

            foreach (range('A', $startChar) as $columnID) {
                $activeWorksheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Create a Writer to save the spreadsheet as an Excel file
            $writer = new Xlsx($spreadsheet);

            // Prepare the file for download
            $filename = 'Progres Petugas ' . $area . '.xlsx';

            // Clear any output buffering
            if (ob_get_contents()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }

        return true;
    }

    function generate()
    {
    }
}
