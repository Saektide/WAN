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
        include './classes/render.php';
    }
}

?>