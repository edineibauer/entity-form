<?php

$name = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));
$icon = trim(strip_tags(filter_input(INPUT_POST, 'icon', FILTER_DEFAULT)));
$newName = str_replace("-", "_", \Helpers\Check::name(trim(strip_tags(filter_input(INPUT_POST, 'newName', FILTER_DEFAULT)))));
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$dados = filter_input(INPUT_POST, 'dados', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$save = new \EntityForm\SaveEntity($name, $icon, $dados, $id);

if($name !== $newName) {
    $sql = new \ConnCrud\SqlCommand();
    $sql->exeCommand("RENAME TABLE  `" . PRE . "{$name}` TO  `" . PRE . "{$newName}`");
    rename(PATH_HOME . "entity/cache/{$name}.json",PATH_HOME . "entity/cache/{$newName}.json");
    rename(PATH_HOME . "entity/cache/info/{$name}.json",PATH_HOME . "entity/cache/info/{$newName}.json");

    //change name entity in others relations
    $dic = new \Entity\Dicionario($newName);
    foreach ($dic->getAssociationMult() as $item)
        $sql->exeCommand("RENAME TABLE  `" . PRE . "{$name}_{$item->getColumn()}` TO  `" . PRE . "{$newName}_{$item->getColumn()}`");

    //change name entity in others relations
    foreach (\Helpers\Helper::listFolder(PATH_HOME . "entity/cache") as $f) {
        if($f !== "info" && preg_match('/\.json$/i', $f)) {
            $cc = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$f}"), true);
            foreach ($cc as $i => $c) {
                if($c['relation'] === $name)
                    $cc[$i]['relation'] = $newName;
            }
            $file = fopen(PATH_HOME . "entity/cache/{$f}", "w");
            fwrite($file, json_encode($cc));
            fclose($file);
        }
    }
}

$data['data'] = true;