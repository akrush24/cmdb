<?php 
$dbname="cmdbtest"; //Имя базы данных
$mysql_username="root"; //Имя пользователя базы данных
$mysql_password="unix11"; //Пароль пользователя базы данных
$mysql_host="localhost"; //Сервер базы данных

//Соединяемся с базой данных
$mysql_connect = mysql_connect($mysql_host, $mysql_username, $mysql_password);

//Выбираем базу данных для работы
mysql_select_db($dbname);
mysql_set_charset('utf8'); 
?>
