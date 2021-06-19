<?php

$strAccessToken = "5BZs8KcNAVApEUfBntOB3k3glbuSJVn0SU7unohAQxcvvQgY3U6/7gQAfGu0F9wi3ZmfNe4OYP/demQPl7RUPuyLyxQS8HTesmy6c8NZuZYeQ2s39w89hA9JY2X0ertUXtLYzPVcoCPSBTlV8HUY8QdB04t89/1O/w1cDnyilFU=";
$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];


  
  if(strtoupper($_msg) == "MANPOWER")
  {
	  include("lib/nusoap.php");
	  $client = new nusoap_client("http://223.27.205.134:12000/Administration/nset_getdata.asmx?wsdl",true); 
	  $data = $client->call('chkManpower');
	  $mydata = json_decode($data["chkManpowerResult"],true); 
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "MANPOWER\n-----------------------\n Total : ".number_format($mydata[0]['TTL_MANPOWER'])."\n Indirect : ".number_format($mydata[0]['TTL_INDIRECT'])."\n Direct : ".number_format($mydata[0]['TTL_DIRECT']);
	  
  }
  else if(strtoupper($_msg) == "HOLIDAY")
  {
	  include("lib/nusoap.php");
	  $client = new nusoap_client("http://223.27.205.134:12000/Administration/nset_getdata.asmx?wsdl",true); 
	  $data = $client->call('chkHoliday');
	  $mydata = json_decode($data["chkHolidayResult"],true); 
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "TODAY Holiday : ".$mydata[0]['Total']." person.";
	  
  }  
  //Holiday personal
  else if(ereg("^(HOLIDAY[[:space:]])([0-9][0-9][0-9][0-9][0-9][0-9])$", strtoupper($_msg)) == true)
  {
	  include("lib/nusoap.php");
	  $client = new nusoap_client("http://223.27.205.134:12000/Administration/nset_getdata.asmx?wsdl",true); 
	  $arrMsg = explode(" ", $_msg);
	  $params = array('empID' => (string)$arrMsg[1]);
	  $data = $client->call('chkHolidayPersonal', $params);
	  $mydata = json_decode($data["chkHolidayPersonalResult"],true); 
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $mydata[0]['Total'];	  
	  
  }
  //who emoployee id
  else if(ereg("^(WHO[[:space:]])([0-9][0-9][0-9][0-9][0-9][0-9])$", strtoupper($_msg)) == true)
  {
	  include("lib/nusoap.php");
	  $client = new nusoap_client("http://223.27.205.134:12000/Administration/nset_getdata.asmx?wsdl",true); 
	  $arrMsg = explode(" ", $_msg);
	  $params = array('empID' => (string)$arrMsg[1]);
	  $data = $client->call('chkEmployee', $params);
	  $mydata = json_decode($data["chkEmployeeResult"],true); 
	  //$arrdata = explode("|", $mydata);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "Name : ".$mydata[0]['description']."\n Position : ".$mydata[0]['position']."\n Section : ".$mydata[0]['section_name'];
	  
	  $arrPostData['messages'][1]['type'] = "image";
    $arrPostData['messages'][1]['originalContentUrl'] = "https://nsetbot.herokuapp.com/showimage.php?empid=".(string)$arrMsg[1];
	  $arrPostData['messages'][1]['previewImageUrl'] = "https://nsetbot.herokuapp.com/showimage.php?empid=".(string)$arrMsg[1];
	  
  }
  else if(strtoupper($_msg) == "OT")
  {
	  include("lib/nusoap.php");
	  $client = new nusoap_client("http://223.27.205.134:12000/Administration/nset_getdata.asmx?wsdl",true); 
	  $data = $client->call('chkOT');
	  $mydata = json_decode($data["chkOTResult"],true); 
    	
	  $strData = "OT TODAY \n ----------------- \n";
	  $strCount = 0;
	  
	  foreach ($mydata as $result)
	  {
		  $strData = $strData.$result["Shop_name"]." : ".$result["Total"]."\n";
		  $strCount += (int)$result['Total'];
	  }
	  
	  $strData = $strData."\n :: Total ::  ".$strCount." person.";
	
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $strData;
	  
  }
  //Special Command
  else if(ereg("^(SPC[[:space:]])([[:space:]][A-Z])([[:space:]][A-Z])$", strtoupper($_msg)) == true)
  {
	  include("lib/nusoap.php");
	  $client = new nusoap_client("http://223.27.205.134:12000/Administration/nset_getdata.asmx?wsdl",true); 
	  $arrMsg = explode(" ", $_msg);
	  $params = array('processGrp' => (string)$arrMsg[1], 'status' => (string)$arrMsg[2]);
	  $data = $client->call('setSpecialCommand', $params);
	  $mydata = json_decode($data["setSpecialCommandResult"],true); 
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $mydata[0]['resultMsg'];	  
	  
  }
  else if(strtoupper($_msg) == "TT")
  {	
	$arrPostData = array();
	$arrPostData['replyToken'] = $arrJson['event'][0]['replyToken'];
	  
	$arrPostData['message'][0]['type'] = "template";
	$arrPostData['message'][0]['altText'] = "Hello My Template";
	$arrPostData['message'][0]['template'] = array(
                    'type' => 'buttons', //類型 (按鈕)
                    'thumbnailImageUrl' => 'https://api.reh.tw/line/bot/example/assets/images/example.jpg', //圖片網址 <不一定需要>
                    'title' => 'Example Menu', //標題 <不一定需要>
                    'text' => 'Please select', //文字
                    'actions' => array(
                        array(
                            'type' => 'postback', //類型 (回傳)
                            'label' => 'Postback example', //標籤 1
                            'data' => 'action=buy&itemid=123' //資料
                        ),
                        array(
                            'type' => 'message', //類型 (訊息)
                            'label' => 'Message example', //標籤 2
                            'text' => 'Message example' //用戶發送文字
                        ),
                        array(
                            'type' => 'uri', //類型 (連結)
                            'label' => 'Uri example', //標籤 3
                            'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                        )
                    );
  }
  else if(strpos(strtoupper($_msg), "หร๊อยหร่อย") !== false)
  {	  
    	$arrPostData = array();
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	    $arrPostData['messages'][0]['type'] = "text";
   	  $arrPostData['messages'][0]['text'] = 'อร่อยที่สุดในโลกเลยหล่ะ';
	  
    	$arrPostData['messages'][1]['type'] = "sticker";
    	$arrPostData['messages'][1]['packageId'] = "2";
	    $arrPostData['messages'][1]['stickerId'] = "172";
	  
  }
  else if(strtoupper($_msg) == 'LINE ID')
  {	
	$strMID = $arrJson['events'][0]['source']['userId'];
	
    	$arrPostData = array();
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	$arrPostData['messages'][0]['type'] = "text";
   	$arrPostData['messages'][0]['text'] = $strMID;	  
  }
  else if(strtoupper($_msg) == "PUSH TEST")
  {	
	    $strUrlPush = "https://api.line.me/v2/bot/message/push";
	    $strAccessTokenPush = "6qu1XX+9fv8jsUMRV39GsMvl9qiO/RHYpkSH6H2DDEs4xPJ+TL5jSuB6vCpvxEEFXSZOQUs5DmFz8i938BpzeYuWnsIUkRooWQJmVr4Def9WAgyIvrbk+fSfdtlcxt9pc2qNTUF0CsaHVLHYOCIDJAdB04t89/1O/w1cDnyilFU=";	  
	    $arrHeader = array();
	    $arrHeader[] = "Content-Type: application/json";
	    $arrHeader[] = "Authorization: Bearer {$strAccessTokenPush}";
 	
	    $strMID1 = $arrJson['events'][0]['message']['id'];
	    $strMID2 = $arrJson['events'][0]['message']['type'];
	  
	    $arrPostData = array();
	    $arrPostData['to'] = "Uaf136cf40f4f7a2c1bedacc48fa7622b"; //USER ID
	    $arrPostData['messages'][0]['type'] = "text";
    	    $arrPostData['messages'][0]['text'] = $strMID1." | ".$strMID2;  
 
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$strUrlPush);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($ch);
	    curl_close ($ch);	  
  }
  else
  {
    	$arrPostData = array(); 
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    	$arrPostData['messages'][0]['type'] = "text";
    	$arrPostData['messages'][0]['text'] = 'คุณสามารถสอนบอทให้ฉลาดขึ้นได้ เพียงพิมพ์: สอนบอท[คำถาม|คำตอบ]';
   }


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

?>
