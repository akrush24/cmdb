<?php
# открываем соединение с DB
include 'db_con.php';

$sql = mysql_query('SELECT host_id,host FROM `cmdb_hosts` ORDER BY `host` ASC' ,$mysql_connect);

print '<table>
<thead>
<tr>
<td>#</td><td>[Name]</td>';
// другие столбцы вытягиваются из `cmdb_fields` WHERE type_id=1
$row = mysql_query('SELECT name FROM `cmdb_fields` WHERE type_id=1 ORDER BY `cmdb_fields`.`sort` ASC',$mysql_connect);
while ($tablerows = mysql_fetch_row($row))
  {
     print '<td>['.$tablerows[0].']</td>';
  }
print '</tr>
<thead>
<tbody>';

$hostcount = 0;
while (@$tablerows = mysql_fetch_row($sql))
{
  $hostcount++; // нумирация
//теперь в цикле для каждой полученной строки сделаем вывод 
  echo('<tr><td>'.$hostcount.'</td>');
  echo("<td>
  $tablerows[1]</td>
");
  
  $sql_id_cmdb_values = mysql_query('SELECT id,name,num FROM `cmdb_fields` WHERE type_id=1 ORDER BY  `cmdb_fields`.`sort` ASC');
  while ($mysql_fetch_rowtablerows = mysql_fetch_row($sql_id_cmdb_values))
  {
    $sql_cmdb_values = mysql_query('SELECT cmdb_values.count,num FROM `cmdb_values` WHERE cmdb_values.field_id='.$mysql_fetch_rowtablerows[0].' and cmdb_values.host_id='.$tablerows[0],$mysql_connect);
    $cmdb_values = mysql_fetch_array($sql_cmdb_values);
    print '<td>';
    if( $mysql_fetch_rowtablerows[2] != 0 and $cmdb_values[0] ){
      print $cmdb_values[1].''; // Кол-во
    }
    print $cmdb_values[0].'</td>';
  }

  echo("</tr>");
}

echo "<tbody></table>";

include 'db_end.php'; # Закрываем соединения с DB

?>