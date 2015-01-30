<?php

return [
    'dsn'     => "mysql:host=blu-ray.student.bth.se;dbname=matl14;", //blu-ray.student.bth.se
    'username'        => "matl14",
    'password'        => "1F_n%Bq7", //1F_n%Bq7
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    'table_prefix'    => "snoozearama_",
    //'verbose' => true,
    //'debug_connect' => 'true',
];
