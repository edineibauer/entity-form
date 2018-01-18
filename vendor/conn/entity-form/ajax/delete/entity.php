<?php

$name = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));

\Helpers\Helper::createFolderIfNoExist(PATH_HOME . "entity/trash");
copy(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $name . ".json", PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "trash" . DIRECTORY_SEPARATOR . $name . ".json");
unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $name . ".json");

$data['data'] = true;