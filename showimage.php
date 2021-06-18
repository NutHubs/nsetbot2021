<?php
$strID = (string)$_GET['empid'];
$img = 'http://223.27.205.134:12001/'.$strID.'.jpg';
$fp = fopen($img, 'rb');

header('Content-type: image/jpeg;');
foreach ($http_response_header as $h) {
    if (strpos($h, 'Content-Length:') === 0) {
        header($h);
        break;
    }
}

fpassthru($fp);
?>
