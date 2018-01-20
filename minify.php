<?php
require('./_config/config.php');

if(DEV) {
    foreach (\Helpers\Helper::listFolder(PATH_HOME . "assets") as $a) {
        if (preg_match('/.css$/i', $a)) {
            $minifier = new MatthiasMullie\Minify\CSS(PATH_HOME . "assets/" . $a);
            $minifier->minify(PATH_HOME . "assets/" . str_replace(".css", ".min.css", $a));
        } elseif (preg_match('/.js$/i', $a)) {
            $minifier = new MatthiasMullie\Minify\JS(PATH_HOME . "assets/" . $a);
            $minifier->minify(PATH_HOME . "assets/" . str_replace(".js", ".min.js", $a));
        }
    }
}