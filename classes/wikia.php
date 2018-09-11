<?php

header('Access-Control-Allow-Origin: *');

$w = $_GET['w'];
$UA = 'Wikia Activity Notifier (saektide.com/wan) by LemonSaektide';

$url = 'http://'.$w.'.wikia.com/api.php?action=query&list=recentchanges&rclimit=1&rcprop=user|title|ids|loginfo|sizes|timestamp|comment|sizes&rcshow=!bot&format=json';
$data = array(
    'action'  => 'query',
    'list'    => 'recentchanges',
    'rclimit' => '1',
    'rcprop'  => 'user|title|ids|loginfo|sizes|timestamp|comment|sizes',
    'rcshow'  => '!bot',
    'format'  => 'json'
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                     "User-Agent: $UA\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) {
    die('[]');
}

echo $result;

?>