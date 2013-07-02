<?php

const API_KEY = 'xx';
const SECRET_KEY = 'XXXX';
const HASH_ALGORITHM = 'sha256';

$ch = curl_init();

$requestBody = '';
$signature = hash_hmac(HASH_ALGORITHM, $requestBody, SECRET_KEY);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    sprintf('Authorization: Token %s:%s', API_KEY, $signature),
    'X-Publisher-Host: yunait.com',
    'Range: deal=0-9'
));
curl_setopt($ch, CURLOPT_URL, 'https://api.yunait.com/deals');

$response = curl_exec($ch);

if(!$response) {
    echo(curl_error($ch)."\n");
    exit();
}

$jsonData = json_decode($response);
var_dump($jsonData);
