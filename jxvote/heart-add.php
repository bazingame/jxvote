<?php
include_once '../class/VF.class.php';

$id = $_GET['id'];

$vf = new VF();
$vf->addHeart($id);