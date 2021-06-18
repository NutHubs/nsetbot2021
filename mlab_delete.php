<?php
  $mid = "59f96b1cbd966f490aa3918d";
  $api_key="_ZLyBX6InXGzrE-ki01xKzo-QyXHOwPN";
  $url = 'https://api.mlab.com/api/1/databases/nsetbot_db/collections/linebot/'.$mid.'?apiKey='.$api_key;
  
  $opts = array('http' =>
    array(
      'method' => 'DELETE',
      'header' => 'Content-type: application/json'
    ) 
  );
  
  $context = stream_context_create($opts);
  $returnVal = file_get_contents($url, false, $context);
  echo "Deleted : ".$returnVal;
?>
