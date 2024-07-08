<?php


function creation_header_thead($header)
{

    $return_header = "";

    foreach ($header as $title) {
        $return_header .= "<th>" . $title . "</th>";
    }
    return $return_header;
}