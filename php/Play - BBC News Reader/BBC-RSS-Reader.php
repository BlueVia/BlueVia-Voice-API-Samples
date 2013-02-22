<?php

// Get the news
$rss = simplexml_load_file('http://feeds.bbci.co.uk/news/rss.xml');

$speakString = "";
for ($i = 0; $i <= 4; $i++) {
	$stringtoAppend = " Press " . $i . " for more detail on " . $rss->channel->item[$i]->title . ".";
	$speakString = "{$speakString}{$stringtoAppend}";
}

$response = "{
		\"commands\": [
			{
				\"speak\": {
					\"text\": \"Welcome to the BBC News\",
					\"voice\": \"Female\"
					}
			},
			{
				\"getDigits\": {
					\"numberOfDigits\": 1,
					\"actionUrl\": \"<add your server here>/actionHandler.php\",
					\"speak\": {
						\"text\": \"". $speakString . "\",
						\"voice\": \"Female\"
					}
				}
			}

		]
	}";

echo $response;

?>