<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    if(!preg_match("/_info.json$/i", $json)){
        $dado = json_decode(file_get_contents(PATH_HOME . "entity/cache/" . $json), true);
        $dado = \Helpers\Helper::convertStringToValueArray($dado);
        $data['data'][str_replace('.json', '', $json)] = $dado;
    }
}