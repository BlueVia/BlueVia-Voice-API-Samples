<?php

if (($stream = fopen('php://input', "r")) !== FALSE){
    $bodyContent = stream_get_contents($stream);
}

$state = $_GET['state'];
fwrite($fh2, date(DATE_RFC822) . " startConference.php State " . $state . "\n");

// get event data
$obj = json_decode($bodyContent);
$eventName = $obj->{'eventName'};
$callerId = $obj->{'callerId'};

// remove the leading 'tel:+'
$callerId = str_replace("tel:+", "", $callerId);

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
							\"speak\": {
								\"text\": \"Please record your name, then press hash\",
								\"voice\": \"Female\"
							}
						},
						{
							\"record\": {
								\"finishOnKey\": \"#\",
								\"playBeep\": \"true\",
								\"export\": {
									\"url\": \"<your-hostname-and-path>/recordingHandler.php?msisdn=" . $callerId . "\"
								}
							}
				        },
				        {
					  		\"dial\": {
								\"conferenceName\": \"" . $digitsLessHash . "\",
								\"conferenceProperties\": {
									\"playOnEnter\": {
										\"url\": \"<your-hostname-and-path>/" . $callerId . ".wav\"
									}
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
							\"speak\": {
								\"text\": \"Please record your name, then press hash\",
								\"voice\": \"Female\"
							}
						},
						{
							\"record\": {
								\"finishOnKey\": \"#\",
								\"playBeep\": \"true\",
								\"export\": {
									\"url\": \"<your-hostname-and-path>/recordingHandler.php?msisdn=" . $callerId . "\"
								}
							}
				        },
				        {
					  		\"dial\": {
									\"conferenceName\": \"" . $digitsLessHash . "\",
									\"conferenceProperties\": {
									    \"playOnEnter\": {
									    	\"url\": \"<your-hostname-and-path>/" . $callerId . ".wav\"
									    }
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


