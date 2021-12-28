<?php
 if(!isset($_GET['url'])) 
die("enter url");
 $ch = curl_init($_GET['url']);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 if(curl_exec($ch))
 {
 $info = curl_getinfo($ch);
 $timems = $info['total_time']*1000; 
echo 'Took ' . $timems . ' Milliseconds to transfer a request to ' . $info['url']; 
} 
curl_close($ch);
 ?> 
