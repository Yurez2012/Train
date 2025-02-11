<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainRequest;
use App\Services\BahnService;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class HomeController extends Controller
{
    public $config = [
        'Neumünster'     => 8000271,
        'Kiel Hbf'       => 8000199,
        'Lübeck Hbf'     => 8000237,
        'Güstrow'        => 8010153,
        'ZOB, Güstrow'   => 935600,
        'Hamburg Hbf'    => 8002549,
        'Würzburg Hbf'   => 8000260,
        'Plattling'      => 8000301,
        'Deggendorf Hbf' => 8001397,
    ];

    public function __construct(public BahnService $bahnService)
    {
    }

    public function index(TrainRequest $request)
    {
        $data = $request->validated();

        $result = [];

        foreach (Arr::get($data, 'trains', []) as $key => $train) {
            $evaNumber = $this->config[Arr::get($train, 'station')] ?? $this->bahnService->getStation('Neumünster');

            $result[Arr::get($train, 'station')] = $this->bahnService->getStationInfoByTimeAndTrainId(
                $evaNumber,
                Carbon::parse(Arr::get($train, 'date'))->format('ymd'),
                Arr::get($train, 'time'),
                Arr::get($train, 'trainID'),
                Arr::get($data, 'trains.'.($key - 1).'.trainID', Arr::get($data, 'trains.'.$key.'.trainID'))
            );
        }

//        $result['Neumünster']     = $this->bahnService->getStationInfoByTimeAndTrainId($this->config['Neumünster'], 250211, '08', 11211, 11211);
//        $result['Hamburg Hbf']    = $this->bahnService->getStationInfoByTimeAndTrainId($this->config['Hamburg Hbf'], 250211, 10, 787, 11211);
//        $result['Würzburg Hbf']   = $this->bahnService->getStationInfoByTimeAndTrainId($this->config['Würzburg Hbf'], 250211, 13, 27, 787);
//        $result['Plattling']      = $this->bahnService->getStationInfoByTimeAndTrainId($this->config['Plattling'], 250211, 16, 83935, 27);
//        $result['Deggendorf Hbf'] = $this->bahnService->getStationInfoByTimeAndTrainId($this->config['Deggendorf Hbf'], 250211, 16, 83935, 83935);

        return view('welcome', [
            'result' => $result,
        ]);
    }
}
