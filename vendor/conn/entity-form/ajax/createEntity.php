<?php

$entity = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);

$updateEntity = new \EntityForm\EntityUpdate($entity);
if (isset($_POST['mod'])) {
    $updateEntity->setMod($_POST['mod']);
}
if (isset($_POST['del'])) {
    $updateEntity->setDel($_POST['del']);
}
if (isset($_POST['add'])) {
    $updateEntity->setAdd($_POST['add']);
}
$updateEntity->setData($_POST['dados']);