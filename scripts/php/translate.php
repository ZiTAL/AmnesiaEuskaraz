<?php
$file = '/home/projects/AmnesiaEuskaraz/config/lang_main/basque.lang';
$file = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".file_get_contents($file);
$dom = new DOMDocument('1.0', 'utf-8');
$dom->loadXML($file);
$xpath = new DOMXPath($dom);

$entries = $xpath->query('//Entry');
foreach($entries as $entry)
{
    $key        = $entry->getAttribute('Name');
    $message    = $entry->nodeValue;

    $m = getNeuronalMessage($key, $message);
    $entry->nodeValue = $m;
}

echo $dom->saveXML();

function getNeuronalMessage($key, $message)
{
    $url = 'https://api.euskadi.eus/itzuli/es2eu/translate';
    $headers = 
    [
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:95.0) Gecko/20100101 Firefox/95.0',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate, br',
        'Content-Type: application/json',
        'Origin: https://www.euskadi.eus',
        'Connection: keep-alive',
        'Referer: https://www.euskadi.eus/',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site',
        'Pragma: no-cache',
        'Cache-Control: no-cache'
    ];
    
    $data_raw =
    [
        'mkey'  => '8d9016025eb0a44215c7f69c2e10861d',
        'text'  => $message,
        'model' => 'generic_es2eu'
    ];
    $data_raw = json_encode($data_raw);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    
    $result = json_decode($result, true);
    if(isset($result['success']) && $result['success']==1)
        return $result['message'];
    else
        error_log("Error: {$key} {$message}");
}