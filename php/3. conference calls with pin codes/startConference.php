<?php

if (($stream = fopen('php://input', "r")) !== FALSE){
    $bodyContent = stream_get_contents($stream);
}

$state = $_GET['state'];

// get event data
$obj = json_decode($bodyContent);
$eventName = $obj->{'eventName'};

switch ($eventName) {
    case "DigitsCollected":
    	$digitsCollected = $obj->{'digits'};

    	// if there is a hash in the $digitsCollected then strip it
		$digitsLessHash = str_replace("#", "", $digitsCollected);

		// switch based on state = moderator/participant
    	switch ($state) {
    		case "moderator":
				$response = "{
					\"commands\": [
						{
					  \"dial\": {
									\"conferenceName\": \"" . $digitsLessHash . "\",
									\"conferenceProperties\": {
										\"startOnEnter\":true
									}
							}
						}

					]
				}";
				echo ($response);
    		break;
    		case "participant";
				$response = "{
					\"commands\": [
						{
					  \"dial\": {
									\"conferenceName\": \"" . $digitsLessHash . "\",
									\"conferenceProperties\": {
										\"startOnEnter\":false
									}
							}
						}

					]
				}";

				echo ($response);
    		break;
    		default:
    			echo ($response);
    		break;
    	}
        break;
    default:
        // match all other events - simply exit with nothing
        break;
}

?>


