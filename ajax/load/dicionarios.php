<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    $name = str_replace('.json', '', $json);
    if($json !== "info" && !empty($name)) {
        $dados = \EntityForm\Metadados::getDicionario($name);
        if($dados && count($dados) > 0)
            $data['data'][$name] = $dados;
    }
}