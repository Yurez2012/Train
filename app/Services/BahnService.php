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

        return Arr::get($this->getResponse($response), 'station.@attributes.eva');
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
     * @param $arrivesId
     *
     * @return array|mixed
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
     * @param $id
     *
     * @return mixed|string|null
     * @throws ConnectionException
     */
    public function getStationInfoAndId($eko, $id, $type = 'ar'): mixed
    {
        $response = Http::withHeaders($this->getHeader())->get($this->url . "/fchg/{$eko}");

        return $this->getResponseById($this->getResponse($response), $id, $type);
    }

    /**
     * @param $eko
     *
     * @return array
     * @throws ConnectionException
     */
    public function getStationInfoRchg($eko): array
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
     * @param $arrivesId
     * @param $eko
     * @param $date
     * @param $time
     *
     * @return array
     * @throws ConnectionException
     */
    protected function getResponseByTrain($response, $trainId, $arrivesId, $eko, $date, $time): array
    {
        $data = [];

        foreach (Arr::get($response, 's', []) as $item) {
            if (Arr::get($item, 'tl.@attributes.n') == $arrivesId) {
                $data['arrives'] = $this->castTrainData(Arr::get($item, 'ar.@attributes', []), $eko, Arr::get($item, '@attributes.id'));
            }

            if (Arr::get($item, 'tl.@attributes.n') == $trainId) {
                $data['departure'] = $this->castTrainData(Arr::get($item, 'dp.@attributes', []), $eko, Arr::get($item, '@attributes.id'));
            }

            if (Arr::get($item, 'dp.@attributes.l') == $trainId) {
                $data['departure'] = $this->castTrainData(Arr::get($item, 'dp.@attributes', []), $eko, Arr::get($item, '@attributes.id'));
            }

            if (Arr::get($item, 'ar.@attributes.l') == $trainId) {
                $data['departure'] = $this->castTrainData(Arr::get($item, 'ar.@attributes', []), $eko, Arr::get($item, '@attributes.id'));
            }
        }

        return $data;
    }

    /**
     * @param $response
     * @param $id
     * @param $type
     *
     * @return mixed
     */
    protected function getResponseById($response, $id, $type): mixed
    {
        foreach (Arr::get($response, 's', []) as $item) {
            if (Arr::get($item, '@attributes.id') == $id) {
                return $this->castTrainDelayData(Arr::get($item, $type.'.@attributes', []));
            }
        }

        return null;
    }

    protected function castTrainData(array $data, $eko, $id): array
    {
        $date = null;

        if (!empty(Arr::get($data, 'ct'))) {
            $date = Carbon::createFromFormat('ymdHi', Arr::get($data, 'ct'));
        }

        if (!empty(Arr::get($data, 'pt'))) {
            $date = Carbon::createFromFormat('ymdHi', Arr::get($data, 'pt'));
        }

        return [
            'date'           => !empty(Arr::get($data, 'ct')) || !empty(Arr::get($data, 'pt')) ? $date->format('Y-m-d H:i') : null,
            'platform'       => Arr::get($data, 'pp'),
            'the_path_taken' => Arr::get($data, 'ppth'),
            'eko'            => $eko,
            'id'             => $id,
            'ar_ct'           => $this->getStationInfoAndId($eko, $id),
            'dp_ct'           => $this->getStationInfoAndId($eko, $id, 'dp'),
        ];
    }

    protected function castTrainDelayData(array $data): ?string
    {
        if (!empty(Arr::get($data, 'ct'))) {
            $date = Carbon::createFromFormat('ymdHi', Arr::get($data, 'ct'));

            return $date->format('Y-m-d H:i');
        }

        return null;
    }
}
