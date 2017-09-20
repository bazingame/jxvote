<?php
include_once "../class/VF.class.php";

$vf = new VF();

$id = $_GET['id'];

$vf->vote($id);
