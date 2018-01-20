<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache/info") as $json) {
    $name = str_replace('.json', '', $json);
    $id = \EntityForm\Metadados::getInfo($name);
    if ($id)
        $data['data'][$name] = (int)$id['identifier'];
}