<?php

\Helpers\Helper::createFolderIfNoExist(PATH_HOME . "vendor/conn/entity-form/entity");

$dados = $_POST['dados'];
$entityName = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);

foreach ($dados as $i => $dado) {
    $slug = $dado['slug'];
    unset($dado['title'], $dado['slug'], $dado['$$hashKey']);

    $emptyTags = array("size", "allow", "allowRelation", "default", "table", "col", "class", "style", "regular");
    $trueTags = array("null", "update", "edit", "list");
    $falseTags = array("unique", "indice");

    foreach ($dado as $item => $value) {

        $value = (is_array($value) ? $value : ($value === "false" ? false : ($value === "true" ? true : (is_float($value) ? (float)$value : ($value == "0" || (is_numeric($value) && !preg_match('/^0\d+/i', $value)) ? (int)$value : (empty($value) ? NULL : (string)$value))))));

        if(($item === "allow" || $item === "allowRelation") && $value && !is_array($value)) {
            $dataExplode = explode(",", $value);
            $value = array();
            foreach ($dataExplode as $item) {
                $value[] = trim($item);
            }
        }

        $dado[$item] = $value;

        if(in_array($item, $emptyTags) && empty($value)) {
            unset($dado[$item]);

        } elseif(in_array($item, $trueTags)){
           if($value){
               unset($dado[$item]);
           } else {
               $dado[$item] = false;
           }

        } elseif(in_array($item, $falseTags) && !$value) {
            if(!$value){
                unset($dado[$item]);
            } else {
                $dado[$item] = true;
            }
        }
        unset($value, $dataExplode);
    }

    $data[$slug] = $dado;
}

$fp = fopen(PATH_HOME . "vendor/conn/entity-form/entity/" . $entityName . ".json", "w");
fwrite($fp, json_encode($data));
fclose($fp);

$entidade = new \Entity\Entity($entityName, "entity-form");