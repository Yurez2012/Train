<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class BahnService
{
    protected string $url;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->url          = config('services.bahn.url');
        $this->clientId     = config('services.bahn.client_id');
        $this->clientSecret = config('services.bahn.client_secret');
    }

    /**
     * @param $station
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStation($station)
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/station/{$station}");

        return $this->getResponse($response);
    }

    /**
     * @param $eko
     * @param $date
     * @param $time
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStationInfoByTime($eko, $date, $time)
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/plan/{$eko}/{$date}/{$time}");

        return $this->getResponse($response);
    }

    /**
     * @param $eko
     * @param $date
     * @param $time
     * @param $trainId
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStationInfoByTimeAndTrainId($eko, $date, $time, $trainId, $arrivesId)
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/plan/{$eko}/{$date}/{$time}");

        return $this->getResponseByTrain($this->getResponse($response), $trainId, $arrivesId, $eko, $date, $time);
    }

    /**
     * @param $eko
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStationInfo($eko)
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/fchg/{$eko}");

        return $this->getResponse($response);
    }

    /**
     * @param $eko
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStationInfoAndId($eko, $id)
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/fchg/{$eko}");

        return $this->getResponseById($this->getResponse($response), $id);
    }

    /**
     * @param $eko
     * @param $date
     * @param $time
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStationInfoRchg($eko)
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/rchg/{$eko}");

        return $this->getResponse($response);
    }

    /**
     * @return array
     */
    protected function getHeader(): array
    {
        return [
            'DB-Api-Key'   => $this->clientSecret,
            'DB-Client-Id' => $this->clientId,
            'accept'       => 'application/xml',
        ];
    }

    /**
     * @param $response
     *
     * @return array
     */
    protected function getResponse($response): array
    {
        if ($response->failed()) {
            return [
                'error'         => 'Error query to API',
                'response_body' => $response->body(),
                'status_code'   => $response->status(),
            ];
        }

        $data = $response->body();
        $xml  = simplexml_load_string($data);
        $json = json_encode($xml);

        return json_decode($json, true);
    }

    /**
     * @param $response
     * @param $trainId
     * @param $eko
     * @param $arrivesId
     *
     * @return array|mixed
     */
    public function getResponseByTrain($response, $trainId, $arrivesId, $eko, $date, $time)
    {
        $data = [];

        foreach (Arr::get($response, 's', []) as $item) {
            if (Arr::get($item, 'tl.@attributes.n') == $arrivesId) {
                $data['arrives'] = $this->castTrainData(Arr::get($item, 'ar.@attributes', []));
            }

            if (Arr::get($item, 'tl.@attributes.n') == $trainId) {
                $data['departure'] = $this->castTrainData(Arr::get($item, 'dp.@attributes', []));
            }

            if (Arr::get($item, 'dp.@attributes.l') == $trainId) {
                $data['departure'] = $this->castTrainData(Arr::get($item, 'dp.@attributes', []));
            }

            if (Arr::get($item, 'ar.@attributes.l') == $trainId) {
                $data['departure'] = $this->castTrainData(Arr::get($item, 'ar.@attributes', []));
            }
        }

        if(!isset($data['arrives']) && !is_string($arrivesId)) {
            $result = $this->getStationInfoByTimeAndTrainId($eko, $date, $time - 1, $trainId, $arrivesId);

            $data['arrives'] = Arr::get($result, 'arrives');
        }

        return $data;
    }

    /**
     * @param $response
     * @param $id
     *
     * @return mixed|null
     */
    public function getResponseById($response, $id)
    {
        foreach (Arr::get($response, 's', []) as $item) {
            if (Arr::get($item, '@attributes.id') == $id) {
                return $this->castTrainDelayData(Arr::get($item, 'dp.@attributes', []));
            }
        }

        return null;
    }

    public function castTrainData(array $data): array
    {
        $date = null;

        if (!empty(Arr::get($data, 'ct'))) {
            $date = Carbon::createFromFormat('ymdHi', Arr::get($data, 'ct'));
        }

        if (!empty(Arr::get($data, 'pt'))) {
            $date = Carbon::createFromFormat('ymdHi', Arr::get($data, 'pt'));
        }

        return [
            'date'            => !empty(Arr::get($data, 'ct')) || !empty(Arr::get($data, 'pt'))  ? $date->format('Y-m-d H:i') : null,
            'platform'        => Arr::get($data, 'pp'),
            'the_path_taken'  => Arr::get($data, 'ppth'),
        ];
    }

    public function castTrainDelayData(array $data): ?string
    {
        if (!empty(Arr::get($data, 'ct'))) {
            $date = Carbon::createFromFormat('ymdHi', Arr::get($data, 'ct'));

            return $date->format('Y-m-d H:i');
        }

        return null;
    }
}
