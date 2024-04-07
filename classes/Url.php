<?php

class Url {

    public static function redirectUrl($path) {
        if (!headers_sent()) { 
            if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] != "off") {
                $url_protocol = "https";
            } else {
                $url_protocol = "http";
            }
            
            header("Location: $url_protocol://" . $_SERVER["HTTP_HOST"] . $path);
            exit; 
        } else {
            echo "Error: Headers already sent";
        }
    }
    
}