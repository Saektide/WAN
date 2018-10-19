<?php
header('Content-type: application/json;charset=utf-8'); // Set for JSON content.
error_reporting(0); // For warnings and notices.
include_once('./session.php'); // For auth session
header('Access-Control-Allow-Origin: *'); // For CORS policy.

/**
 * Makes a request to API.php to get RC
 * 
 * @param string $d The interwiki domain
 * @param string $userAgent The User-Agent to be used for this request.
 */
function r__($d, $userAgent) {

    $url = 'http://'.$d.'/api.php';
    
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
                         "User-Agent: $userAgent\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

/**
 * Makes a request to API.php to compare revs
 * 
 * @param string $d The interwiki domain
 * @param string $userAgent The User-Agent to be used for this request.
 * @param number $oldrevid Old revision id to compare
 * @param number $revid Current revision id
 */
function diff__($d, $userAgent, $oldrevid, $revid) {
    $url = 'http://'.$d.'/api.php';
    $data = array(
        'action'  => 'compare',
        'fromrev' => $oldrevid,
        'torev'   => $revid,
        'format'  => 'json'
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                         "User-Agent: $userAgent\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

/**
 * Get metainfo about site.
 *
 * @param string $d The domain
 * @return void
 */
function meta__($d) {
    $url = 'http://'.$d.'/api.php?action=query&meta=siteinfo&format=json';

    $result = file_get_contents($url);

    return $result;
}

$response = array(); // Here starts for JSON response.

$w = $_GET['w']; // Get "w" param
$UA = 'Wiki Activity Notifier (saektide.com/wan) by LemonSaektide'; // Default User-Agent
$wikis = explode('|', $w); // Split $w for get wikis, example: ?w=ut|terraria|c
$response['wikisList'] = $wikis; // Append wikis in a single list

if ($_SESSION['auth']) { // API will be responds for authed users
    $response['successAuth'] = true;
    // First time, doesn't require the r__ function.
    foreach ($wikis as $wiki) {

        $url = 'http://'.$wiki.'/api.php';
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

        /**
         * Note:
         * 
         * $http_response_header[0] is the Status HTTP (404, 200, 410, 301, etc)
         * $http_response_header[6] is the Location (if Status HTTP is 301)
         */

        preg_match('/HTTP\/1.1 (.*?) /', $http_response_header[0], $responseMatch, PREG_OFFSET_CAPTURE);
        $response['wikisRC'][$wiki]['status'] = $responseMatch[1][0];
        if ($responseMatch[1][0] == '301') {
            // 301 means for permanent redirect
            $response['wikisRC'][$wiki]['from'] = $wiki;
            preg_match('/Location: http(s)?:\/\/(.*).wikia.com\/api.php/', $http_response_header[6], $redirectMatch, PREG_OFFSET_CAPTURE);
            $response['wikisRC'][$wiki]['to'] = $redirectMatch[2][0];

            $result = r__($redirectMatch[2][0], $UA);
        }
        if ($result === FALSE) {
            // RC key will be null if request fails
            $response['wikisRC'][$wiki]['rc'] = null;
        } else {
            $response['wikisRC'][$wiki]['rc'] = json_decode($result)->query->recentchanges[0];
        }
        // Get DIFF
        if (json_decode($result)->query->recentchanges[0]->type == 'edit') {
            $oldRevID = json_decode($result)->query->recentchanges[0]->old_revid;
            $revID = json_decode($result)->query->recentchanges[0]->revid;
            if ($redirectMatch) $diffResult = diff__($redirectMatch[2][0], $UA, $oldRevID, $revID);
            else $diffResult = diff__($wiki, $UA, $oldRevID, $revID);
        }
        // Get metainfo
        if ($redirectMatch) $metaResult = meta__($redirectMatch[2][0]);
        else $metaResult = meta__($wiki);

        $response['wikisRC'][$wiki]['siteName'] = json_decode($metaResult)->query->general->sitename;
        if (json_decode($result)->query->recentchanges[0]->type == 'edit') $response['wikisRC'][$wiki]['diff'] = json_decode($diffResult)->compare;
        else $response['wikisRC'][$wiki]['diff'] = null;
    }
} else {
    $response['successAuth'] = false;
    $response['info'] = 'Invalid session';
}

echo json_encode($response, 448); // Print the JSON encoded and formatted

?>