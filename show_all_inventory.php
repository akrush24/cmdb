<?php

$sql = mysql_query('SELECT host_id,host FROM `cmdb_hosts` ORDER BY `host` ASC' ,$mysql_connect);
print '<div style="width:100%; overflow:scroll; overflow-y:hidden; overflow-x:auto; padding-bottom:4px;">';
print '<table style="background-color:#FFF;margin:4px;" class="sort">
<thead>
<tr>
<td id="head">#</td><td id="head">[Name]</td>';
// другие столбцы вытягиваются из `cmdb_fields` WHERE type_id=1 and front=1
$row = mysql_query('SELECT name FROM `cmdb_fields` WHERE type_id=1 and front=1 ORDER BY `cmdb_fields`.`sort` ASC',$mysql_connect);
while ($tablerows = mysql_fetch_row($row))
  {
     print '<td id=head>['.$tablerows[0].']</td>';
  }
print '</tr>
<thead>
<tbody>';

$hostcount = 0;
while (@$tablerows = mysql_fetch_row($sql))
{
  $hostcount++; // нумирация
//теперь в цикле для каждой полученной строки сделаем вывод 
  echo('<tr id="blink"><td id="head" style="color:#848484">'.$hostcount.'</td>');
  echo("<td id=grid>
  <a href=index.php?edit=$tablerows[0]><pre><b>$tablerows[1]</b></pre></a></td>
");
  
  $sql_id_cmdb_values = mysql_query('SELECT id,name,num FROM `cmdb_fields` WHERE type_id=1 and front=1 ORDER BY  `cmdb_fields`.`sort` ASC');
  while ($mysql_fetch_rowtablerows = mysql_fetch_row($sql_id_cmdb_values))
  {
    $sql_cmdb_values = mysql_query('SELECT cmdb_values.count,num FROM `cmdb_values` WHERE cmdb_values.field_id='.$mysql_fetch_rowtablerows[0].' and cmdb_values.host_id='.$tablerows[0],$mysql_connect);
    $cmdb_values = mysql_fetch_array($sql_cmdb_values);
    print '<td id=grid><a href="index.php?edit='.$tablerows[0].'" width="100%"><pre>';
    if( $mysql_fetch_rowtablerows[2] != 0 and $cmdb_values[0] ){
      print $cmdb_values[1].' <b color="blue">x</b> '; // Кол-во
    }
    print $cmdb_values[0].'</pre></a></td>';
  }

  echo("</tr>");
}

echo "<tbody></table></div>";

?>