<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache/info") as $json) {
    $name = str_replace('.json', '', $json);
    $id = \EntityForm\Metadados::getInfo($name);
    if ($id) {
        if(!$id['identifier'] || $id['identifier'] < 2) {
            $ident = 1;
            foreach (\EntityForm\Metadados::getDicionario($name) as $i => $datum) {
                if($i > $ident)
                    $ident = (int) $i;
            }
            $id['identifier'] = $ident + 1;
        }
        $data['data'][$name] = (int)$id['identifier'];
    }
}