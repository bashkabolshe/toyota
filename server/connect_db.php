<?php

header('charset=utf-8');
$link = mysqli_connect("localhost", "user75455_toyo", "kdmY6PSV", "user75455_toyota");
mysqli_set_charset($link, "utf8");
if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$rows = 0;

function insert($link, $table, $cols, $values, $exclude) {
    global $rows;
    $sql = 'INSERT INTO ' . $table . ' (' . $cols . ')  SELECT ' . $values . ' FROM DUAL WHERE NOT EXISTS (SELECT * FROM ' . $table . ' WHERE ' . $exclude . ')';
//    ECHO $sql;
    mysqli_query($link, $sql) or die(mysqli_error($link) . " | " . $sql);
    $rows = $rows + mysqli_affected_rows($link);
}

function truncate($link) {
    $sql = 'TRUNCATE cars';
    mysqli_query($link, $sql) or die(mysqli_error($link) . " | " . $sql);
    $sql = 'TRUNCATE colors';
    mysqli_query($link, $sql) or die(mysqli_error($link) . " | " . $sql);
    $sql = 'TRUNCATE grades';
    mysqli_query($link, $sql) or die(mysqli_error($link) . " | " . $sql);
    $sql = 'TRUNCATE technicalSpecification';
    mysqli_query($link, $sql) or die(mysqli_error($link) . " | " . $sql);
}
