<?php

// VARIABLES FOR ALL TESTS
$url = "https://live-api.bluevia.com:443/comms/v1/me/numbers/tel:%2B<country code><number to update>";
$accessCredentials = "<your API key>" . ":" . "<your API secret>";
$headers = array(
    'Content-Type: application/json',
    'Accept: application/json'
    );

// BODY CONTENT
$bodyContent = "{
    \"notificationFormat\": \"JSON\",
    \"voiceCallbackUrl\":\"<your callback URL>\",
    \"voiceFallbackUrl\":\"<your fallback URL>\"
}";

// CREATE CURL RESOURCE AND SET OPTIONS
// CREATE FIRST LEG OF THE CALL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, $accessCredentials);
curl_setopt($ch, CURLOPT_POST, 1);
// set the body content
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
// return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// turn off SSL cert validation
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// get header detail
curl_setopt($ch, CURLOPT_HEADER, 1);

// $output contains the output string
$output = curl_exec($ch);

echo($testCase . "RESPONSE: " . $output . "\n");

// close curl resource to free up system resources
curl_close($ch);

?>