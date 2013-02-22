<?php

// Get the POST request body
if (($stream = fopen('php://input', "r")) !== FALSE){
    $bodyContent = stream_get_contents($stream);
}

// get event data from the POST request body
$obj = json_decode($bodyContent);
$eventName = $obj->{'eventName'};

// only provide a response if this is an CalledNumber event
if ($eventName == "CalledNumber") {
	$response = "{
		\"commands\": [
			{
				\"getDigits\": {
					\"timeout\": 5,
					\"numberOfDigits\": 1,
					\"retries\": 2,
					\"actionUrl\": \"http://<your domain>/<your path>/eventHandler.php\",
					\"speak\": {
						\"text\": \"Press 1 to create a new conference. Press 2 to join an existing conference call.\",
						\"voice\": \"Female\"
					}
				}
			},
			{
				\"speak\": {
					\"text\": \"Sorry, you didn't enter a selection in time. Please call and try again.\",
					\"voice\": \"Female\"
				}
			},
			{
				 \"hangup\":{
				 }

	        }
		]
	}";
	echo ($response);
}

?>