<?php

$entity = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));

$sql = new \ConnCrud\SqlCommand();
$del = new \ConnCrud\Delete();
$read = new \ConnCrud\Read();

$dic = new \EntityForm\Dicionario($entity);
if (!empty($dic->getAssociationMult())) {
    foreach ($dic->getAssociationMult() as $item) {
        if ($item->getFormat() === "extend_mult") {
            $read->exeRead($entity . "_" . $item->getColumn());
            if ($read->getResult()) {
                foreach ($read->getResult() as $ddd)
                    $del->exeDelete(PRE . $item->getRelation(), "WHERE id = :id", "id={$ddd[$item->getRelation() . "_id"]}");
            }
        }
        $sql->exeCommand("DROP TABLE " . PRE . "{$entity}_{$item->getColumn()}");
    }
}

if (!empty($dic->getExtends())) {
    foreach ($dic->getExtends() as $extend) {
        $read->exeRead($entity);
        if ($read->getResult()) {
            foreach ($read->getResult() as $ddd) {
                if (!empty($ddd[$extend->getColumn()]))
                    $del->exeDelete(PRE . $extend->getRelation(), "WHERE id = :id", "id={$ddd[$extend->getColumn()]}");
            }
        }
    }
}

//change name entity in others relations
foreach (\Helpers\Helper::listFolder(PATH_HOME . "entity/cache") as $f) {
    if ($f !== "info" && preg_match('/\.json$/i', $f)) {
        $cc = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$f}"), true);
        $fEntity = str_replace(".json", "", $f);
        foreach ($cc as $i => $c) {
            if ($c['relation'] === $entity) {
                if (in_array($c['format'], ['extend_mult', 'list_mult', 'selecao_mult', 'checkbox_mult']))
                    $sql->exeCommand("DROP TABLE " . PRE . "{$fEntity}_{$c['column']}");

                unset($cc[$i]);
            }
        }
        $file = fopen(PATH_HOME . "entity/cache/{$f}", "w");
        fwrite($file, json_encode($cc));
        fclose($file);
    }
}

if (file_exists(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json"))
    unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json");

if (file_exists(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json"))
    unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json");

$sql->exeCommand("DROP TABLE " . PRE . $entity);

$data['data'] = true;