<?php
error_reporting(0);

class WAN {
    /**
     * Class constructor,
     * render the page. This function will be called
     * by main entry script (index.php).
     *
     */
    public function __construct() {
        preg_match('/localhost(:8080)?/', $_SERVER['HTTP_HOST'], $matchHost);
        $allowedLocalhosts = array('localhost', 'localhost:8080');
        if (in_array($matchHost[0], $allowedLocalhosts)) $onDevRelease = true;
        else $onDevRelease = false;
        
        include './classes/render.php';
    }
}

?>