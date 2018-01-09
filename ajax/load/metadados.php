<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    if(!preg_match("/_info.json$/i", $json))
        $data['data'][str_replace('.json', '', $json)] = json_decode(file_get_contents(PATH_HOME . "entity/cache/" . $json), true);
}