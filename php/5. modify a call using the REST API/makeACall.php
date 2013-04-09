<?php

$url = "https://live-api.bluevia.com:443/comms/v1";
$accessCredentials = "<your API key>" . ":" . "<your secret>";
$headers = array(
    'Content-Type: application/json',
    'Accept: application/json'
    );

// Create the First Call Leg i.e. add Phone A to the call
// this is the body content passed in with the API request
$bodyContent = "{\"callerId\":\"tel:+<Country Code><your BlueVia Phone Number\",\"destination\":\"tel:+<Country Code><Phone A Number>\"}";


// PHP stuff for making the RESTful API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, $accessCredentials);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
// return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// turn off SSL cert validation
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// get header detail
curl_setopt($ch, CURLOPT_HEADER, 1);

// $output contains the HTTP Response to the POST request
$output = curl_exec($ch);

echo("RESPONSE: " . $output . "\n");

// get the HTTP response body body
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($output, 0, $header_size);
$body = substr($output, $header_size);

// Retrieve the call ID created for this call resource.
// Not required for the demo, but shows how you can access it.
$obj = json_decode($body);
$callId = $obj->{'callId'};

// close curl resource to free up system resources
curl_close($ch)


//
// Use setCommands to modify the call created Above
// Speak To The Participants On The Call
//

// PRESS KEY TO START NEXT TEST
readline ("Press Enter To Continue");

$bodyContent = "{
    \"commands\": [
        {
	  \"speak\": {
                \"text\": \"Hello. Welcome to the call. Why not have a chat\",
                \"voice\": \"female\"

            }
        }
    ]
}";

// in this case the url represents the call URL
$url = $url . "/" . $callId . "/" . $setcommands;


// CREATE CURL RESOURCE AND SET OPTIONS
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, $accessCredentials);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
// return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// turn off SSL cert validation
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// get header detail
curl_setopt($ch, CURLOPT_HEADER, 1);

//// $output contains the output string
$output = curl_exec($ch);
?>