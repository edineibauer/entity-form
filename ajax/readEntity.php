<?php

$id = filter_input(INPUT_POST, 'entidade', FILTER_DEFAULT);

$edit = new \EntityForm\EntityEdit($id);
echo json_encode($edit->getDados());