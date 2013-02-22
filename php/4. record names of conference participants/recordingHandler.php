<?php

$msisdn = $_GET['msisdn'];
$myFile = $msisdn . ".wav";
$fh = fopen($myFile, 'w+')  or die("can't open file");
chmod($myFile,0777);

if (($stream = fopen('php://input', "r")) !== FALSE)
    fwrite($fh, (stream_get_contents($stream)));
fclose($fh);

?>