<?php

require_once('lib//Res.php');

$res_id = (int)array_shift($sys_parameters);

$location = Res::Route($res_id, (int)get('c_id'));

header("Location: $location");

