<?php

    $row = mysql_query('SELECT id,name FROM `cmdb_fields` WHERE type_id=1',$mysql_connect);
    while ($tablerows = mysql_fetch_row($row))
    {
      $index_num='num'.$tablerows[1];

		$cmdb_values = mysql_real_escape_string($_POST[$tablerows[1]]);

      if(mysql_fetch_row(mysql_query('select * from `cmdb_values` where `field_id`='.$tablerows[0].' and host_id='.$_GET['edit']))){
        if ( !isset($_POST[$index_num]) ){
          mysql_query('UPDATE `cmdb_values` SET `count`="'.$cmdb_values.'" where `field_id`='.$tablerows[0].' and `host_id`='.$_GET['edit']) or die(mysql_error()); 
        }else{
          mysql_query('UPDATE `cmdb_values` SET `count`="'.$cmdb_values.'", num="'.$_POST[$index_num].'" where `field_id`='.$tablerows[0].' and `host_id`='.$_GET['edit']) or die(mysql_error()); 
        }
      }else{
        if ( !isset($_POST[$index_num]) ){
          mysql_query('INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`) VALUES ("'.$tablerows[0].'", "'.$_GET['edit'].'", "'.$cmdb_values.'")') or die(mysql_error());
        }else{
          mysql_query('INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`, `num`) VALUES ("'.$tablerows[0].'", "'.$_GET['edit'].'", "'.$cmdb_values.'", "'.$_POST[$index_num].'")') or die(mysql_error());
        }
      }

    }
	if ( isset($_POST['cmd_label']) and $_POST['cmd_label'] != '' ){
		mysql_query('UPDATE `cmdb_hosts` SET `host`="'.$_POST['cmd_label'].'" where `host_id`='.$_GET['edit']) or die(mysql_error()); 
	}
	 header("Location: index.php"); exit(); # после формы сохранения вохвращяемся на главную страницу
  
?>