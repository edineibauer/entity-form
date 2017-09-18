<?php

$entity = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);

$updateEntity = new \EntityForm\EntityUpdate($entity);
$updateEntity->setMod($_POST['mod'] ?? null);
$updateEntity->setDel($_POST['del'] ?? null);
$updateEntity->setAdd($_POST['add'] ?? null);
$updateEntity->setData($_POST['dados']);