<?php declare(strict_types = 1);

$qs = empty($_SERVER["QUERY_STRING"]) ? "" : "?".$_SERVER["QUERY_STRING"];
$redir = "/article/".join("/", $sys_parameters).$qs;

redirectp($redir);

