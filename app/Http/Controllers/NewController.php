<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Workers;
use Carbon\Carbon;

class NewController extends Controller
{
    public function index(Request $request) 
    {
        // GET http://localhost/schedule?startDate=2018-01-01&?endDate=2018-01-14&userId=1;

        // get the data from incoming GET Request
        $start = $request->get('startDate');
        $end = $request->get('?endDate');
        $userId = $request->get('userId');

        // find our user from the DB by incoming UserID
        $worker = Workers::find($userId);
        $dateStart = Carbon::createFromFormat('Y-m-d', $start);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $end);

        // get holidays from DB
        $holiday = $worker->Vacation;

        for ($i = $dateStart, $j = 0; $dateStart->lessThanOrEqualTo($dateEnd); $i->addDay(), $j++) {

            // check our date using API 
            $json = json_decode(file_get_contents('https://isdayoff.ru/' . $i->format("Ymd")), true);

            // if our date is not a holiday run the code
            if ($json == 0 and !in_array($i->format('Y-m-d'), explode(",", $holiday))) {

                // fill the array with business days
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

        return view('main', [
            'resultShedule' => $resultShedule,
        ]);
    }

}
