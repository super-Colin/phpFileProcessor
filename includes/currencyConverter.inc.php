<?php

class CurrencyConverter { 

  // Dirty way to grab a current conversion rate without having to sign up for an API key
  static function getConverstionRate($from, $to){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://www.google.com/finance/quote/$from-$to");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // We WILL be redirected checking this page..
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    // echo 'HTTP Status Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
    // echo 'Response Body: ' . $response . PHP_EOL;
    curl_close($ch);
    $h1EndsAt = strpos($response, "</h1>");
    $afterH1 = substr($response, $h1EndsAt, 200);
    // var_dump($h1EndsAt);
    // var_dump($afterH1);

    $rate = self::getFloatFromDomString($afterH1);
    if($rate !== false){
      return $rate;
    }else{
      return false;
    }
    curl_close($ch);
  }

  static function getFloatFromDomString($domStringWithNum){
    // stringpos();
    // $numString = preg_match( '/[0-9]{3}/', $domStringWithNum );
    $numString = preg_match( '/[0-9]+\.[0-9]{2}/', $domStringWithNum, $matches );
    // echo "NUM STRING IS $numString <br />";
    // var_dump($matches);
    if($matches !== false){
    $float = (float) $matches[0];
    return $float;
    }else{
      return false;
    }

  }


  static function getFirstInnerMostDiv($stringDom){
    $workingDom = $stringDom;
    $onInnerMostDiv = false;
    while($onInnerMostDiv){

    }
  }


  function googleScrapeTest(){
      // Initialize a connection with cURL (ch = cURL handle, or "channel")
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // We WILL be redirected checking this page..
      // Set the URL
      // curl_setopt($ch, CURLOPT_URL, 'http://www.example.com');
      // curl_setopt($ch, CURLOPT_URL, 'http://www.google.com');
      curl_setopt($ch, CURLOPT_URL, 'http://www.google.com/finance/quote/USD-CAD');

      // Set the HTTP method
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

      // Return the response instead of printing it out
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      // Send the request and store the result in $response
      $response = curl_exec($ch);

      echo 'HTTP Status Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
      echo 'Response Body: ' . $response . PHP_EOL;

      // Close cURL resource to free up system resources
      curl_close($ch);

  }
}






// function convert($currency_from,$currency_to,$currency_input){ $yql_base_url = "http://query.yahooapis.com/v1/public/yql"; $yql_query = 'select * from yahoo.finance.xchange where pair in ("'.$currency_from.$currency_to.'")'; 
// $yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query); $yql_query_url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys"; 
// $yql_session = curl_init($yql_query_url); 
// var_dump($yql_session);
// curl_setopt($yql_session, CURLOPT_RETURNTRANSFER,true); 
// $yqlexec = curl_exec($yql_session); 
// var_dump($yqlexec);
// $yql_json = json_decode($yqlexec,true); 
// var_dump($yql_json);
// $currency_output = (float) $currency_input*$yql_json['query']['results']['rate']['Rate']; 
// return $currency_output; } 
// }




// function currency_converter($from,$to,$amount)
// {
//  $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to"; 
 
//  $request = curl_init(); 
//  $timeOut = 0; 
//  curl_setopt ($request, CURLOPT_URL, $url); 
//  curl_setopt ($request, CURLOPT_RETURNTRANSFER, 1); 
 
//  curl_setopt ($request, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)"); 
//  curl_setopt ($request, CURLOPT_CONNECTTIMEOUT, $timeOut); 
//  $response = curl_exec($request); 
//  curl_close($request); 
 
//  return $response;
// } 

// if(isset($_POST['convert_currency']))
// {
//  $amount=$_POST['amount'];
//  $from=$_POST['convert_from'];
//  $to=$_POST['convert_to'];
	
//  $rawData = currency_converter($from,$to,$amount);
//  $regex = '#\<span class=bld\>(.+?)\<\/span\>#s';
//  preg_match($regex, $rawData, $converted);
//  $result = $converted[0];
//  echo $result;
// }
