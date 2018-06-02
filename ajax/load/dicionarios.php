<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    $name = str_replace('.json', '', $json);
    if($json !== "info" && !empty($name)) {
        $dados = \EntityForm\Metadados::getDicionario($name);
        if($dados && count($dados) > 0) {
            $e = 1;
            foreach ($dados as $i => $dado) {
                if(empty($dado['indice'])) {
                    $dados[$i]['indice'] = $e;
                    $e++;
                }
            }

            $data['data'][$name] = $dados;
        }
    }
}