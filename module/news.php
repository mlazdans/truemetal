<?php

$qs = empty($_SERVER["QUERY_STRING"]) ? "" : "?".$_SERVER["QUERY_STRING"];
$redir = "$sys_http_root/article/".join("/", $sys_parameters).$qs;

header("Location: $redir", true, 301);

