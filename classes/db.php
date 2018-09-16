<?php

// DB means for DataBase
class DB {
    public static function GETAuth($key, $done, $failed) {
        $conn = new mysqli('localhost', 'saekt32_public', '**********', 'saekt32_wan');

        if ($conn->connect_error) die('Conn failed! ('.$conn->connect_error.')');

        /**
         * SQL Code, it will get the AUTH key encoded then this will be compared
         * with the written auth/password.
         * 
         * @var string
         */
        $sql = "SELECT auth, username FROM authlist";
        $result = $conn->query($sql);

        $len = $result->num_rows;
        $i = 1;

        // Search for auth key, by usernames.
        if ($len > 0) {
            while($row = $result->fetch_assoc()) {
                if ($row['auth'] == base64_encode($key)) {
                    // $done will be called if auth is correct
                    call_user_func_array($done, array($row['username']));
                    return;
                } else $i++;
                if ($i > $len) {
                    // $failed will be called if auth doesn't match with any username
                    call_user_func($failed);
                };
            }
        } else {
            // In this case, $failed will be called when table is empty
            call_user_func($failed);
        }
        $conn->close();
    }
}

?>