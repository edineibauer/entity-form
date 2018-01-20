<?php

$name = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));
$dados = filter_input(INPUT_POST, 'dados', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$save = new \EntityForm\SaveEntity($name, $dados);

$data['data'] = true;