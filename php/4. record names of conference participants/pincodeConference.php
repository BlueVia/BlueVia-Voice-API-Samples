<?php


// Get the notification event data from the HTPT POST request
if (($stream = fopen('php://input', "r")) !== FALSE){
    $bodyContent = stream_get_contents($stream);
}

// get event data
$obj = json_decode($bodyContent);
$eventName = $obj->{'eventName'};


// ONLY PROVIDE A RESPONSE IF THIS IS FOR AN INBOUND NUMBER
if ($eventName == "CalledNumber") {
	echo ("{
		\"commands\": [
			{
				\"getDigits\": {
					\"timeout\": 5,
					\"numberOfDigits\": 1,
					\"retries\": 2,
					\"actionUrl\": \"<your-hostname-and-path>/eventHandler.php\",
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
				\"hangup\": \"\"
			}
		]
	}");

}

?>