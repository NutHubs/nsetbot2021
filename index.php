<?php

$strAccessToken = "5BZs8KcNAVApEUfBntOB3k3glbuSJVn0SU7unohAQxcvvQgY3U6/7gQAfGu0F9wi3ZmfNe4OYP/demQPl7RUPuyLyxQS8HTesmy6c8NZuZYeQ2s39w89hA9JY2X0ertUXtLYzPVcoCPSBTlV8HUY8QdB04t89/1O/w1cDnyilFU=";
$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];

/*mongodb connect*/
$api_key="_ZLyBX6InXGzrE-ki01xKzo-QyXHOwPN";
$url = 'https://api.mlab.com/api/1/databases/nsetbot_db/collections/linebot?apiKey='.$api_key.'';
$json = file_get_contents('https://api.mlab.com/api/1/databases/nsetbot_db/collections/linebot?apiKey='.$api_key.'&q={"question":"'.$_msg.'"}');
$data = json_decode($json);
$isData = sizeof($data);

if (strpos($_msg, 'สอนบอท') !== false) 
{

  	if (strpos($_msg, 'สอนบอท') !== false) 
    {	
      $x_tra = str_replace("สอนบอท","", $_msg);
      $pieces = explode("|", $x_tra);
      $_question=str_replace("[","",$pieces[0]);
      $_answer=str_replace("]","",$pieces[1]);
	
      /*Post New Data*/
      $newData = json_encode(
        array(
          'question' => $_question,
          'answer'=> $_answer
        )
      );	 
      
      $opts = array(
        'http' => array(
            'method' => "POST",
            'header' => "Content-type: application/json",
            'content' => $newData
         )
      );
      
      $context = stream_context_create($opts);
      $returnValue = file_get_contents($url,false,$context);
      $arrPostData = array();
      $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
      $arrPostData['messages'][0]['type'] = "text";
      $arrPostData['messages'][0]['text'] = 'ขอบคุณที่สอนบอท';
  
  }
  else
  {
    //find loop Json in db
    if($isData > 0){    
   	  foreach($data as $rec)
      {
    	    $arrPostData = array();
    	    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	        $arrPostData['messages'][0]['type'] = "text";
    	    $arrPostData['messages'][0]['text'] = $rec->answer;
      }
    }
    
	}
	  
}
else
{
  
    if(strtoupper($_msg) == "SERVER TEMP")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartServerMonitor/ServerRoom1?retain&auth=OLfJOENYvYLmbqG:J0o3U9oywRvgnLtl5lLhscdJ5";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strTemp = $obj[0]['payload'];
    $arrTemp = explode("|", $strTemp);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "Server room temp : \n".$arrTemp[0]." °C";
    
  }
  else if(strtoupper($_msg) == "OFFICE TEMP" || strtoupper($_msg) == "OFFICE TEMPERATURE")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/feed/aircond011withfeed007?apikey=oSHt1BDhi5VLw9nMaRGWcNp02uAXjJQu&granularity=15second&since=1day";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strTemp = $obj['lastest_data'];
    //$arrTemp = explode("|", $strTemp);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "Office temp : \n".$strTemp[1]['values'][1]." °C";
    
  }
  else if(strtoupper($_msg) == "QC TEMP" || strtoupper($_msg) == "QC TEMPERATURE")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/NSETEnergySaving/AirCond018/Temperature?retain&auth=ejfAKHEIYXQAJzK:Ni7EbcUpW7KWgsFPQzFEOBWdY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strTemp = $obj[0]['payload'];
    //$arrTemp = explode("|", $strTemp);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "QC Room temp : \n".$strTemp." °C";
    
  }
  else if(strtoupper($_msg) == "MANPOWER")
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
  else if(strtoupper($_msg) == "MDB1")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartpowerMeter/electricroom/metermdb1?retain&auth=5tsipuvi6tRjgOX:Y9p0oR3bjlSCiVyNK5PlSNNFY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strData = $obj[0]['payload'];
    $arrData = explode("|", $strData);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "MDB1\n----------\n Frequency : ".((int)$arrData[16] / 100)." Hz. \n PowerFactor : ".$arrData[15]."\n K watt : ".number_format($arrData[14])." Kw \n EC Phase(R) : ".number_format($arrData[1])." Amp";
	  
  }
  else if(strtoupper($_msg) == "MDB2")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartpowerMeter/electricroom/metermdb2?retain&auth=5tsipuvi6tRjgOX:Y9p0oR3bjlSCiVyNK5PlSNNFY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strData = $obj[0]['payload'];
    $arrData = explode("|", $strData);
    
    $varData = "MDB2\n----------\n Frequency : ".((int)$arrData[16] / 100)." Hz. \n PowerFactor : ".$arrData[15]."\n K watt : ".number_format($arrData[14])." Kw \n";
    $varData = $varData."EC Phase(R) : ".number_format($arrData[1])." Amp \nEC Phase(S) : ".number_format($arrData[4])." Amp \nEC Phase(T) : ".number_format($arrData[7])." Amp \n";
    $varData = $varData."Voltage phase R - S : ".number_format(((int)$arrData[3]/10))." V \n"."Voltage phase S - T : ".number_format(((int)$arrData[6]/10))." V \n"."Voltage phase T - R : ".number_format(((int)$arrData[9]/10))." V";
	  
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $varData;
	  
  }
  else if(strtoupper($_msg) == "MDB3")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartpowerMeter/electricroom/metermdb3?retain&auth=5tsipuvi6tRjgOX:Y9p0oR3bjlSCiVyNK5PlSNNFY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strData = $obj[0]['payload'];
    $arrData = explode("|", $strData);
    
    $varData = "MDB3\n----------\n Frequency : ".((int)$arrData[16] / 100)." Hz. \n PowerFactor : ".$arrData[15]."\n K watt : ".number_format($arrData[14])." Kw \n";
    $varData = $varData."EC Phase(R) : ".number_format($arrData[1])." Amp \nEC Phase(S) : ".number_format($arrData[4])." Amp \nEC Phase(T) : ".number_format($arrData[7])." Amp \n";
    $varData = $varData."Voltage phase R - S : ".number_format(((int)$arrData[3]/10))." V \n"."Voltage phase S - T : ".number_format(((int)$arrData[6]/10))." V \n"."Voltage phase T - R : ".number_format(((int)$arrData[9]/10))." V";
	  
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $varData;
	  
  }
  else if(strtoupper($_msg) == "MDB4")
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartpowerMeter/electricroom/metermdb4?retain&auth=5tsipuvi6tRjgOX:Y9p0oR3bjlSCiVyNK5PlSNNFY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strData = $obj[0]['payload'];
    $arrData = explode("|", $strData);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "MDB4\n----------\n Frequency : ".((int)$arrData[16] / 100)." Hz. \n PowerFactor : ".$arrData[15]."\n K watt : ".number_format($arrData[14])." Kw \n EC Phase(R) : ".number_format($arrData[1])." Amp \n EC Phase(S) : ".number_format($arrData[4])." Amp";
	  
  }
  else if(strtoupper($_msg) == "WM100" || strpos(strtoupper($_msg), "PROD") !== false && strpos(strtoupper($_msg), "WM100") !== false)
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartMachine/WM100AS/Monitor0?retain&auth=gRYd0nLxFMQiZuP:tKosWuhZZTHNjYdW1Jw3QPTBY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strWM100 = $obj[0]['payload'];
    $arrWM100 = explode("|", $strWM100);
    
    date_default_timezone_set("Asia/Bangkok");
    $strH = date('H');
    $strUPH = 0;
	  
    if((int)$strH >= 8 && (int)$strH <= 18)
    {
    	$strUPH =  9 - (18 - (int)$strH);
    }
    else if((int)$strH >= 20 && (int)$strH <= 23)
    {
	    $strUPH =  21 - (24 - (int)$strH);
    }
    else if((int)$strH >= 0 && (int)$strH <= 6)
    {
	    $strUPH = (int)$strH + 4;
    }
	  
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "LINE WM100 \n -------------------------- \n OK : ".number_format($arrWM100[1])." Pcs.\n NG : ".number_format($arrWM100[2])." Pcs.\n TOTAL : ".number_format($arrWM100[3])." Pcs.\n"."---------------------------\n UPH : ".number_format((int)$arrWM100[3] / $strUPH);
  
  }
  else if(strtoupper($_msg) == "WM100 OEE" || strpos(strtoupper($_msg), "WM100") !== false && strpos(strtoupper($_msg), "OEE") !== false)
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartMachine/WM100AS/Monitor0?retain&auth=gRYd0nLxFMQiZuP:tKosWuhZZTHNjYdW1Jw3QPTBY";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strWM100 = $obj[0]['payload'];
    $arrWM100 = explode("|", $strWM100);
    
    date_default_timezone_set("Asia/Bangkok");
    $strH = date('H');
    $strM = date('i');	  
    $HoureX = 0;
	  
    if((int)$strH >= 8 && (int)$strH <= 18)
    {
    	$HoureX = (int)$strH - 8;
    }
    else if((int)$strH >= 20 && (int)$strH <= 23)
    {
	    $HoureX = (int)$strH - 20;
    }
    else if((int)$strH >= 0 && (int)$strH <= 6)
    {
	    $HoureX = (int)$strH + 4;
    }
	  
    $strActual = ((int)$arrWM100[1] * 23) / 60;
    $strPlan = ($HoureX *60) + (int)$strM;
	  
    $strPLproduct = ($HoureX * 60) + ((int)$strM / 23);
    $strACproduct = (int)$arrWM100[1];
    
    $varAvability = ($strActual/$strPlan) * 100;
    $varQuality = ((int)$arrWM100[1]/(int)$arrWM100[3])*100;
    $varPerformance = $strACproduct/$strPLproduct* 100;
    $varOEE = ($varAvability * $varQuality * $varPerformance) / 10000;
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = " OEE- WM100 \n ------------------ \n Actual : ".(int)$strActual."\n Plan : ".$strPlan."\n PL.product : ".(int)$strPLproduct."\n AC.product : ".$strACproduct."\n Avability : ".round($varAvability,2)." %\n Quality : ".round($varQuality,2)." %\n Performance : ".round($varPerformance,2)." %\n OEE : ".round($varOEE, 2)." %";
	  
  }
  else if(strtoupper($_msg) == "PRODUCTION" || strtoupper($_msg) == "ACTUAL" || strpos(strtoupper($_msg), "PRODUCT") !== false)
  {
    header('Access-Control-Allow-Origin: *');
    $url = "https://api.netpie.io/topic/SmartCounter/Actual?auth=sq9HZRpoNGgxWIE:pssfGTjYIzmfjnLePlOYkN3oP";
    $response = file_get_contents($url);
    $obj = json_decode($response, true);
    $strActual = $obj[0]['payload'];
	  
    $url1 = "https://api.netpie.io/topic/SmartCounter/Target?auth=sq9HZRpoNGgxWIE:pssfGTjYIzmfjnLePlOYkN3oP";
    $response1 = file_get_contents($url1);
    $obj1 = json_decode($response1, true);
    $strTarget = $obj1[0]['payload'];
    //$arrTemp = explode("|", $strTemp);
    
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = "Target : ".number_format($strTarget)." unit.";
    $arrPostData['messages'][1]['type'] = "text";
    $arrPostData['messages'][1]['text'] = "Actual : ".number_format($strActual)." unit.";
  }
  else if(strtoupper($_msg) == "AIR1")
  {   
    $ch = curl_init("https://api.netpie.io/topic/SmartOfficeNSET/gearname/Air_PAC101_8_CTRL?retain&auth=GWzr8IhAEiqU0bQ:YgXAiVXQakianq4wMZraDMhux");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"PWR_ON");
    $response = curl_exec($ch);
	  
    $arrJsonX = json_decode($response, true);
	  
    if(strtoupper($arrJsonX['message']) == "SUCCESS")
    {
    	$arrPostData = array();
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    	$arrPostData['messages'][0]['type'] = "text";
    	$arrPostData['messages'][0]['text'] = "OK";
    }
    else
    {
	    $arrPostData = array();
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    	$arrPostData['messages'][0]['type'] = "text";
    	$arrPostData['messages'][0]['text'] = "Try again !";
    }
	  
    
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
  else if(strtoupper($_msg) == "DB")
  {
      /*Post New Data*/
      $newData = json_encode(
        array(
          'mid' => '602457364',
          'textX'=> 'lineC',
	  'countX'=> '1'
        )
      );
      
      $opts = array(
        'http' => array(
            'method' => "POST",
            'header' => "Content-type: application/json",
            'content' => $newData
         )
      );
      
      $context = stream_context_create($opts);
      $returnValue = file_get_contents($url,false,$context);

     $json2 = file_get_contents('https://api.mlab.com/api/1/databases/nsetbot_db/collections/linebot?apiKey='.$api_key.'&q={"textX":"lineC"}');
     $data2 = json_decode($json2);
     $isData2 = sizeof($data2);
	  
     if($isData2 > 0){
     //alarm to GM
   	  //foreach($data2 as $rec2)
       	  //{
    	    $arrPostData = array();
    	    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
	    $arrPostData['messages'][0]['type'] = "text";
	    $arrPostData['messages'][0]['text'] = (string)$isData2;
    	    //$arrPostData['messages'][0]['text'] = $rec2->countX;
          //}
      }
      
  }
  else
  {
    	$arrPostData = array(); 
    	$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    	$arrPostData['messages'][0]['type'] = "text";
    	$arrPostData['messages'][0]['text'] = 'คุณสามารถสอนบอทให้ฉลาดขึ้นได้ เพียงพิมพ์: สอนบอท[คำถาม|คำตอบ]';
	  
	    //$strUrlPush = "https://api.line.me/v2/bot/message/6919286473241/content";
	    //$strAccessTokenPush = "6qu1XX+9fv8jsUMRV39GsMvl9qiO/RHYpkSH6H2DDEs4xPJ+TL5jSuB6vCpvxEEFXSZOQUs5DmFz8i938BpzeYuWnsIUkRooWQJmVr4Def9WAgyIvrbk+fSfdtlcxt9pc2qNTUF0CsaHVLHYOCIDJAdB04t89/1O/w1cDnyilFU=";	  
	    //$arrHeader = array();
	    //$arrHeader[] = "Content-Type: application/json";
	    //$arrHeader[] = "Authorization: Bearer {$strAccessTokenPush}";

	  
    	//$response1 = file_get_contents($url1);
    	//$obj1 = json_decode($response1, true);
    	//$strTarget = $obj1[0]['payload'];
    
	    //$ch = curl_init();
	    //curl_setopt($ch, CURLOPT_URL,$strUrlPush);
	    //curl_setopt($ch, CURLOPT_HEADER, false);
	    //curl_setopt($ch, CURLOPT_GET, true);
	    //curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
	    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
	    //curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    //$result = curl_exec($ch);
	    //curl_close ($ch);
	  
   }
  
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
