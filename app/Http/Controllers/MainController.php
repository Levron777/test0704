<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Workers;
use Carbon\Carbon;

class MainController extends Controller
{
    // public function index()
    // {
    //     // $news = News::all()->paginate(2);
    //     $news = DB::table('news')->orderBy('created_at', 'desc')->paginate(2);

    //     return view('main', [
    //         'news' => $news,
    //     ]);
    // }

    public function index() 
    {
        return view('main');
    }

    public function addInput(Request $request)
    {
        $worker = new Workers;

        // $worker->id = $request->input('InputId');
        $worker->TimeStart = $request->input('timeStart');
        $worker->DateStart = $request->input('InputStartDate');
        $worker->DateEnd = $request->input('InputEndDate');
        $worker->Vacation = $request->input('InputVacation');
        $worker->save();

        return redirect('ready');
    }

    public function indexInput(Request $request)
    {
        return view('InputData');
    }

    public function ready(Request $request) 
    {

        // GET http://localhost/schedule?startDate=2018-01-01&?endDate=2018-01-14&userId=1;

        $result=parse_url('http://localhost/schedule?startDate=2018-01-01&?endDate=2018-01-14&userId=1');
        parse_str($result['query'], $query);

        var_dump($query);

        $id = DB::table('workers')->max('id');
        $worker = Workers::find($id);

        $dateStart = Carbon::createFromFormat('Y-m-d', $worker->DateStart);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $worker->DateEnd);

        $holiday = $worker->Vacation;

        for ($i = $dateStart, $j = 0; $dateStart->lessThanOrEqualTo($dateEnd); $i->addDay(), $j++) {

            $json = json_decode(file_get_contents('https://isdayoff.ru/' . $i->format("Ymd")), true);
            if ($json == 0 && !in_array($i->format('Y-m-d'), explode(",", $holiday))) {
                $startWorkTime = $worker->TimeStart . '00';
                $shedule['shedule'][$j]['day'] = $i->format('Y-m-d');
                $shedule['shedule'][$j]['timeRanges'][0]['start'] = $startWorkTime;
                $shedule['shedule'][$j]['timeRanges'][0]['end'] = $shedule['shedule'][$j]['timeRanges'][0]['start'] + 300;
                $shedule['shedule'][$j]['timeRanges'][1]['start'] = $shedule['shedule'][$j]['timeRanges'][0]['end'] + 100;
                $shedule['shedule'][$j]['timeRanges'][1]['end'] = $shedule['shedule'][$j]['timeRanges'][1]['start'] + 500;
            }
        }

        if (isset($shedule)) { 
            $resultShedule = json_encode($shedule);
        } else {
            $resultShedule = 'Все дни выходные!';
        }

        return view('ready', [
            'resultShedule' => $resultShedule,
        ]);
    }
}
