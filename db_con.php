<?php

$dbname="cmdb"; //��� ���� ������
$mysql_username="cmdb"; //��� ������������ ���� ������
$mysql_password="unix11"; //������ ������������ ���� ������
$mysql_host="localhost"; //������ ���� ������

//����������� � ����� ������
$mysql_connect = mysql_connect($mysql_host, $mysql_username, $mysql_password);

//�������� ���� ������ ��� ������
mysql_select_db($dbname);
mysql_set_charset('utf8');

?>