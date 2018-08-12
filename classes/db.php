<?php

class DB {
    public static function GETAuth($key, $done, $failed) {
        $conn = new mysqli('localhost', 'saekt32_public', '**********', 'saekt32_wan');

        if ($conn->connect_error) die('Conn failed! ('.$conn->connect_error.')');


        $sql = "SELECT auth, username FROM authlist";
        $result = $conn->query($sql);

        $len = $result->num_rows;
        $i = 1;

        if ($len > 0) {
            while($row = $result->fetch_assoc()) {
                if ($row['auth'] == base64_encode($key)) {
                    call_user_func_array($done, array($row['username']));
                    return;
                } else $i++;
                if ($i > $len) {
                    call_user_func($failed);
                };
            }
        } else {
            call_user_func($failed);
        }
        $conn->close();
    }
}

// Note: This isn't an API, front-end only.

?>