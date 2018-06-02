<?php

$name = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));
$newName = str_replace("-", "_", \Helpers\Check::name(trim(strip_tags(filter_input(INPUT_POST, 'newName', FILTER_DEFAULT)))));
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$dados = filter_input(INPUT_POST, 'dados', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$save = new \EntityForm\SaveEntity($name, $dados, $id);

if($name !== $newName) {
    $sql = new \ConnCrud\SqlCommand();
    $sql->exeCommand("RENAME TABLE  `" . PRE . "{$name}` TO  `" . PRE . "{$newName}`");
    rename(PATH_HOME . "entity/cache/{$name}.json",PATH_HOME . "entity/cache/{$newName}.json");
    rename(PATH_HOME . "entity/cache/info/{$name}.json",PATH_HOME . "entity/cache/info/{$newName}.json");

    //change name entity in others relations
    foreach (\Helpers\Helper::listFolder(PATH_HOME . "entity/cache") as $f) {
        if($f !== "info" && preg_match('/\.json$/i', $f)) {
            $c = file_get_contents(PATH_HOME . "entity/cache/{$f}");
            $c = str_replace('"' . $name . '",', '"' . $newName . '",', $c);
            $file = fopen(PATH_HOME . "entity/cache/{$f}", "w");
            fwrite($file, $c);
            fclose($file);
        }
    }
}

$data['data'] = true;