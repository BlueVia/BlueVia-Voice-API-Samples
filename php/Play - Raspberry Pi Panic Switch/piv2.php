#!/usr/bin/php

<?php

error_reporting(E_ALL);
exec("setled off");

/*
	Some basic definitions
*/


$anumber = "447591......";
$bnumber = "447540......";

$url = "http://tublue.voxygen.com/comms/v1/calls/";

$body = '{"callerId":"'.$anumber.'","destination":"'.$bnumber.'"}';

$callState = 0;
$session = "";
$answered = false;

/*
	Our Main loop
*/

while (true) {

	// GET BUTTON STATE
	$switchState = exec("checkswitch", $lines);

	echo "State : $switchState\n";
	echo "CallState : $callState\n";

	if ( ($switchState == "ON") && ($callState == 0) ) {

		// Place the call
		$session = placeTheCall($url, $body);

		if ($session) { //($callArray = json_decode($result, true)) {

			// Call has been setup successful
			$callState = 1; // Call is setup

			// Poll on result until one leg is answered....
			while(!$answered) {

				// light the LED !
				exec("setled on");

        		$url      = "http://tublue.voxygen.com/comms/v1/calls/$session";
        		$response = getIt($url, $hcode);
				$currentStatus = json_decode($response, true);

				//print_r($currentStatus);

				$answered = ($currentStatus['callStatus'] == "Established");
				echo "Answered : $answered\n";

				exec("setled on");
				usleep(500000);
				if (!$answered) exec("setled off");
				usleep(500000);

			}

/*
			// Connect to Queue 
			// Now the call is answered, we'll block on the zeroMQ until there are zero participants
			$subscriber = joinSubQueue($session);
			if ($subscriber) {
				echo "Have bound to ZeroMQ for $session\n";

				$loop = true;
				while ($loop) {
					$json = popFromQueue($subscriber);
					echo "\n$json\n";
					$eventArray = json_decode($json, true);
					$loop = ($eventArray['conferenceSize'] > 0);
				}
			}
*/
                     

		}
	}


	if ( ($switchState == "OFF") && ($callState == 1) ) {

		hangUp($session);
		exec("setled off");
		$callState = 0;
		$session = "";
		$answered = false;

	}

	// Wait for a bit before checking again....
	sleep(1);

}







/*

	Some helper functions

*/

function placeTheCall($url, $body) {

	echo "Calling BlueVia $url : $body\n";
	$result = postIt($url, $body);

	if ($callArray = json_decode($result, true)) {
		$callId = $callArray['callId'];
		$url = $url.$callId."/setcommands";
	} else {
        	echo "\nFailed to place call\n";
		exit;
	}

    $dialBlock = array(
        "number" => array(
            "callerId" => $bnumber,
            "destination" => $anumber,
            "sendDigits" => ""
        ),
        "conferenceName" => "Bobs Box of Magic"
    );
    $cmd1      = array(
        "dial" => $dialBlock
    );
    $cmdArray  = array(
        "commands" => array(
            $cmd1
        )
    );
    $commands  = json_encode($cmdArray);
    printf("Call going out: $commands");
    postIt($url, $commands);

	return $callId;

}



function hangUp($session) {

    $cmd1     = array(
        "hangup" => array()
    );
    $cmdArray = array(
        "commands" => array(
            $cmd1
        )
    );
    $commands = json_encode($cmdArray);
    
    $url = "http://tublue.voxygen.com/comms/v1/calls/$session/setcommands";
    
    return postIt($url, $commands);

}



function postIt($url, $args) {

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt($ch, CURLOPT_USERPWD, "a840884312ede2802cdbe9e0b77046:8bd0a8eff35f3be74e4d9139465ccff9");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // If this takes longer than 1 seconds, we probably have an issue....

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $posted = curl_exec($ch);
        curl_close($ch);

        return $posted;

}

function getIt($url, &$httpcode)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    $got = curl_exec($ch);
    if ($got == false) {
        $errmsg = curl_error($ch);
        printf("Error: $errmsg");
    }
    
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode != 200)
        printf("HTTP returned $hcode\n");
    
    curl_close($ch);
    
    return $got;
}




/*

// ZeroMQ functions for receiving conference event indications

// Starts up a subscription queue connection 
function joinSubQueue($theConferenceId) {
	$context 		= new ZMQContext();

	// Socket to talk to server
	$subscriber     = new ZMQSocket($context, ZMQ::SOCKET_SUB);
	$subscriber->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $theConferenceId);
	$subscriber->connect("tcp://tucore.voxygen.com:5559");

	return $subscriber;

}

// Pop messages from Queue 
function popFromQueue($qh) {
	return $qh->recv();
}
*/


?>
