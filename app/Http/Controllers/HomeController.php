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

        return view('welcome', [
            'result' => $result,
        ]);
    }
}
