<?php

$entity = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));

$info = \EntityForm\Metadados::getInfo($entity);
$dic = \EntityForm\Metadados::getDicionario($entity);
$sql = new \ConnCrud\SqlCommand();
$del = new \ConnCrud\Delete();
$read = new \ConnCrud\Read();

foreach (["extend_mult", "list_mult"] as $e) {
    if (!empty($info[$e])) {
        foreach ($info[$e] as $id) {
            if ($e === "extend_mult") {
                $read->exeRead(PRE . $entity . "_" . $dic[$id]['relation']);
                if ($read->getResult()) {
                    foreach ($read->getResult() as $item) {
                        $ie = $item[$dic[$id]['relation'] . "_id"];
                        $del->exeDelete(PRE . $dic[$id]['relation'], "WHERE id = :id", "id={$ie}");
                    }
                }
            }
            $sql->exeCommand("DROP TABLE " . PRE . $entity . "_" . $dic[$id]['relation']);
        }
    }
}

if (!empty($info['extend'])) {
    foreach ($info['extend'] as $id) {
        $read->exeRead(PRE . $entity);
        if ($read->getResult()) {
            foreach ($read->getResult() as $item)
                $del->exeDelete(PRE . $dic[$id]['relation'], "WHERE id = :id", "id={$item[$dic[$id]['column']]}");
        }
    }
}

unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json");
unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json");

$sql->exeCommand("DROP TABLE " . PRE . $entity);


$data['data'] = true;