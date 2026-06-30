<?php

function getBpsWonogiri(): array
{
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/3312/var/574/th/126/key/d3e573a5bfa1b72f6faa5adc3dc920cb";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);

        return [
            'nilai' => 0,
            'tampil' => '0',
            'unit' => 'Ribu Perjalanan',
            'update' => '-'
        ];
    }

    curl_close($ch);

    $json = json_decode($response, true);

    $nilai = 0;

    if (!empty($json['datacontent'])) {
        $nilai = (float) reset($json['datacontent']);
    }

    return [
        'nilai' => $nilai,
        'tampil' => number_format($nilai, 2, ',', '.'),
        'unit' => $json['var'][0]['unit'] ?? 'Ribu Perjalanan',
        'update' => $json['last_update'] ?? '-'
    ];
}