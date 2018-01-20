<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    $name = str_replace('.json', '', $json);
    if($json !== "info")
        $data['data'][$name] = \EntityForm\Metadados::getDicionario($name);
}