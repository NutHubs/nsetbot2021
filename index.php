<?php

$strAccessToken = "5BZs8KcNAVApEUfBntOB3k3glbuSJVn0SU7unohAQxcvvQgY3U6/7gQAfGu0F9wi3ZmfNe4OYP/demQPl7RUPuyLyxQS8HTesmy6c8NZuZYeQ2s39w89hA9JY2X0ertUXtLYzPVcoCPSBTlV8HUY8QdB04t89/1O/w1cDnyilFU=";
$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];

if(strtoupper($_msg) == "SCM")
{
    	$arrPostData = array();
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	$arrPostData['messages'][0]['type'] = "template";
	$arrPostData['messages'][0]['altText'] = "Special Command";
   	$arrPostData['messages'][0]['template'] = [
        	"type" => "buttons", 
         	"thumbnailImageUrl" => "https://example.com/bot/images/item1.jpg", 
         	"imageAspectRatio" => "rectangle", 
         	"imageSize" => "cover", 
         	"imageBackgroundColor" => "#000000", 
         	"title" => "Special Command", 
         	"text" => "Please select command", 
         	"defaultAction" => [
            	"type" => "uri", 
            	"label" => "View detail", 
            	"uri" => "http://example.com/page/123" 
        	],
		"actions" => [
			[
                     		"type" => "uri", 
                     		"label" => "ALL", 
                     		"uri" => "http://example.com/page/123" 
               		],
               		[
                  		"type" => "message", 
                  		"label" => "ASSEMBLY", 
                  		"data" => "action=buy&itemid=123" 
               		] 
		]
        ]; 
}

//else if(strtoupper($_msg) == "TT")
{
	//$arrPostData['messages'][1]['type'] = "sticker";
    	//$arrPostData['messages'][1]['packageId'] = "2";
	//$arrPostData['messages'][1]['stickerId'] = "172";
//}

	  
    	//$arrPostData['messages'][1]['type'] = "sticker";
    	//$arrPostData['messages'][1]['packageId'] = "2";
	//$arrPostData['messages'][1]['stickerId'] = "172";


$channel = curl_init();
curl_setopt($channel, CURLOPT_URL,$strUrl);
curl_setopt($channel, CURLOPT_HEADER, false);
curl_setopt($channel, CURLOPT_POST, true);
curl_setopt($channel, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($channel, CURLOPT_RETURNTRANSFER,true);
curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($channel);
curl_close ($channel);

echo "Hello NSET";
 

?>
