<?php

namespace App\Http\Controllers;

use App\Services\BahnService;

class HomeController extends Controller
{
    public function __construct(public BahnService $bahnService)
    {
    }

    public function index()
    {
        $result = [];

        $result['Neumünster']   = $this->bahnService->getStationInfoByTimeAndTrainId(8000271, 250210, 10, 11210, 11210);
        $result['Kiel Hbf']     = $this->bahnService->getStationInfoByTimeAndTrainId(8000199, 250210, 10, 21017, 11210);
        $result['Lübeck Hbf']   = $this->bahnService->getStationInfoByTimeAndTrainId(8000237, 250210, 12, 52889, 21017);
        $result['Güstrow']      = $this->bahnService->getStationInfoByTimeAndTrainId(8010153, 250210, 13, 'RE83', 52889);
        $result['ZOB, Güstrow'] = $this->bahnService->getStationInfoByTimeAndTrainId(935600, 250210, 14, 44455, 'RE83');

        return view('welcome', [
            'result' => $result,
        ]);
    }
}
