<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$qs = empty($_SERVER["QUERY_STRING"]) ? "" : "?".$_SERVER["QUERY_STRING"];
$redir = "/article/".join("/", $sys_parameters).$qs;

header("Location: $redir", true, 301);

