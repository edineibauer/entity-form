<?php

$id = filter_input(INPUT_POST, 'entidade', FILTER_DEFAULT);

$edit = new \EntityForm\ReadEntityForm($id);
echo json_encode($edit->getDados());