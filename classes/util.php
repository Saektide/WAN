<?php

function endsWith($str, $target)
{
    $length = strlen($target);
    if ($length == 0) {
        return true;
    }

    return (substr($str, -$length) === $target);
}

?>