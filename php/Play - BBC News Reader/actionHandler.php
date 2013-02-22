<?php

if (($stream = fopen('php://input', "r")) !== FALSE){
    $bodyContent = stream_get_contents($stream);
}

// get event data
$obj = json_decode($bodyContent);
$eventName = $obj->{'eventName'};

// This should be cached, but for now simply get the RSS feed again
$rss = simplexml_load_file('http://feeds.bbci.co.uk/news/rss.xml');

$i = 0;
switch ($eventName) {
    case "DigitsCollected":
         $digitsCollected = $obj->{'digits'};
		 $i = (int) $digitsCollected;
		 $response = "{
				\"commands\": [
					{
						\"speak\": {
							\"text\": \"" . str_replace("\"", "", $rss->channel->item[$i]->description) . "\",
							\"voice\": \"Female\"
							}
					},
					{
						\"redirect\": {
							\"actionUrl\":\"<add your server here>/BBC-RSS-Reader.php\"
						}
					}

				]
			}";

		echo $response;
    	break;
    case "Answered":
        break;
    default:
        // match all other events - simply exit with nothing
        break;
}

?>

