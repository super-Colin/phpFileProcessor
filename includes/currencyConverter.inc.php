<?php
// Thanks to 
// https://medium.com/@maillysandhu/how-to-integrate-free-currency-converter-api-in-php-6f4edeec34a7

class CurrencyConverter { 
function convert($currency_from,$currency_to,$currency_input){ $yql_base_url = "http://query.yahooapis.com/v1/public/yql"; $yql_query = 'select * from yahoo.finance.xchange where pair in ("'.$currency_from.$currency_to.'")'; 
$yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query); $yql_query_url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys"; 
$yql_session = curl_init($yql_query_url); 
var_dump($yql_session);
curl_setopt($yql_session, CURLOPT_RETURNTRANSFER,true); 
$yqlexec = curl_exec($yql_session); 
var_dump($yqlexec);
$yql_json = json_decode($yqlexec,true); 
var_dump($yql_json);
$currency_output = (float) $currency_input*$yql_json['query']['results']['rate']['Rate']; 
return $currency_output; } 
}