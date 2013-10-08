<html>

<head>
<title>CMDB - Configuration management database</title>
<style type="text/css">
	body {background-color: #FFFFFF;padding: 0px; margin: 0px; width:100%;}
	#bk{border: 1px solid black;border-collapse:collapse;}
	#bk td{	padding-left: 2px;}
</style>

<meta content="text/html; charset=utf-8" http-equiv="content-type"/>

</head>

<?php
# открываем соединение с DB
include 'db_con.php';

$sql = mysql_query('SELECT host_id,host FROM `cmdb_hosts` ORDER BY `host` ASC' ,$mysql_connect);

print '<table id="bk">
<tr id="bk">
<td id="bk">#</td><td id="bk">Name</td>';
// другие столбцы вытягиваются из `cmdb_fields` WHERE type_id=1
$row = mysql_query('SELECT name FROM `cmdb_fields` WHERE type_id=1 ORDER BY `cmdb_fields`.`sort` ASC',$mysql_connect);
while ($tablerows = mysql_fetch_row($row))
  {
     print '<td id="bk">'.$tablerows[0].'</td>';
  }
print '</tr>';

$hostcount = 0;
while (@$tablerows = mysql_fetch_row($sql))
{
  $hostcount++; // нумирация
//теперь в цикле для каждой полученной строки сделаем вывод 
  echo('<tr><td id="bk">'.$hostcount.'</td>');
  echo('<td id="bk">'.$tablerows[1].'</td>');
  
  $sql_id_cmdb_values = mysql_query('SELECT id,name,num FROM `cmdb_fields` WHERE type_id=1 ORDER BY  `cmdb_fields`.`sort` ASC');
  while ($mysql_fetch_rowtablerows = mysql_fetch_row($sql_id_cmdb_values))
  {
    $sql_cmdb_values = mysql_query('SELECT cmdb_values.count,num FROM `cmdb_values` WHERE cmdb_values.field_id='.$mysql_fetch_rowtablerows[0].' and cmdb_values.host_id='.$tablerows[0],$mysql_connect);
    $cmdb_values = mysql_fetch_array($sql_cmdb_values);
    print '<td id="bk">';
    if( $mysql_fetch_rowtablerows[2] != 0 and $cmdb_values[0] ){
      print $cmdb_values[1].'<b color="blue">x</b>'; // Кол-во
    }
    print $cmdb_values[0].'</td>';
  }

  echo("</tr>");
}

echo "</table>";

include 'db_end.php'; # Закрываем соединения с DB

?>

</body>
</html>