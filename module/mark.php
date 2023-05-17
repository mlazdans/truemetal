<?php declare(strict_types = 1);

$now = time();

# TODO: 'viewed_before' vajadzētu būt sessiju root un tapt pārsauktam par marked_before vai ko tādu!!
$_SESSION['res']['viewed_before'] = $now;
$_SESSION['res']['viewed_date'] = array();
$_SESSION['forums']['viewed_date'] = array();

# Remove historic entries
unset($_SESSION['res']['viewed']);
unset($_SESSION['forums']['viewed']);
unset($_SESSION['forums']['viewed_before']);

header("Location: /");
