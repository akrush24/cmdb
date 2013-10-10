<?php

# открываем соединение с DB
include 'db_con.php';


# шапка сайта
include 'header.php';

############ Удаление инвентаря 
if ( isset($_POST['env_del']) and isset($_GET['edit']) and $_GET['edit'] != '' ){
	# удаляем все значение по этому объекту
	mysql_query('DELETE from `cmdb_values` WHERE host_id='.$_GET['edit'], $mysql_connect) or die(mysql_error());
	# удаляем сам объект
	mysql_query('DELETE from `cmdb_hosts` WHERE host_id='.$_GET['edit'], $mysql_connect) or die(mysql_error());
	header("Location: index.php"); exit(); # после формы сохранения вохвращяемся на главную страницу
}

####### Удаление выбранной подсказки
if( isset($_POST['cmdb_del_hint']) and !empty($_GET['hintid']) ){
	mysql_query('DELETE FROM `cmdb_hint` WHERE `id`="'.$_GET['hintid'].'"',$mysql_connect) or die(mysql_error());
}
  
####### Обработка бекапа
if ( isset($_GET['backup']) ){
	header("Location: backup.php"); exit();

}
####### Добавление свойства
if ( isset($_GET['add_under_properties']) and isset($_GET['cust_f_id']) ){
	mysql_query('INSERT INTO `cmdb`.`cmdb_values` (`id` ,`field_id` ,`host_id` ,`count` ,`num`)VALUES(NULL,"'.$_GET['cust_f_id'].'","'.$_GET['edit'].'","","'.$_GET['cust_f_numtype'].'")',$mysql_connect) or die(mysql_error());
}


#########################################################################################
##### Список инвентаря, главная страница
if( !isset($_GET['edit']) and !isset($_GET['custom_fields']) and !isset($_GET['hint']) and !isset($_GET['add_label']) and !isset($_GET['help']) )
{
	include 'show_all_inventory.php';
}

elseif ( (isset($_GET['edit']) and !isset($_GET['hint'])) or isset($_GET['add_label']) )
{
	if(isset($_GET['edit']))$INVID=$_GET['edit'];
	
	### Создаем новый инвентарь
	if( isset($_GET['add_label']) and !isset($_GET['edit'])){
		mysql_query('INSERT INTO `cmdb`.`cmdb_hosts` (`host_id`,`host`) VALUES ("","new")',$mysql_connect) or die(mysql_error());
		$sql = mysql_query('SELECT `host_id` FROM  `cmdb_hosts` ORDER BY host_id DESC LIMIT 1',$mysql_connect) or die(mysql_error());
		$INVID = mysql_fetch_row($sql)[0];
	}
	
	######### Сохранения свойств по объекту
	if(isset($_GET['save']) and isset($_GET['edit']) and $_GET['edit']!='' ){
		
		$row = mysql_query('SELECT * FROM `cmdb_values` WHERE host_id="'.$INVID.'"',$mysql_connect); // выбираем все поля по нужному инвентарю
		while ($tablerows = mysql_fetch_row($row))
		{
			$value_id = $tablerows[0];
			$cust_f_id = $tablerows[1];
			$index_num='num'.$value_id;
			
			if(isset($_POST[$value_id])){	
				$cmdb_values = mysql_real_escape_string($_POST[$value_id]);
			}else{
				$cmdb_values = '';
			};
			
		$is_value = mysql_fetch_row(mysql_query('select count from `cmdb_values` where `id`='.$value_id));
		#print '<b>'.$is_value[0].'</b><br>';
		  if( mysql_fetch_row(mysql_query('select count from `cmdb_values` where `id`='.$value_id)) ){
		  #print 'select id from `cmdb_values` where `id`='.$value_id.'<br>';
		  // если значение существует, то делает update
			if ( !isset($_POST[$index_num]) ){
				#print 'UPDATE `cmdb_values` SET `count`="'.$cmdb_values.'" where `id`='.$value_id.'<br>';
				mysql_query('UPDATE `cmdb_values` SET `count`="'.$cmdb_values.'" where `id`='.$value_id) or die(mysql_error()); 
			}else{
				#print 'UPDATE `cmdb_values` SET `count`="'.$cmdb_values.'", num="'.$_POST[$index_num].'" where `id`='.$value_id.'<br>';
				mysql_query('UPDATE `cmdb_values` SET `count`="'.$cmdb_values.'", num="'.$_POST[$index_num].'" where `id`='.$value_id) or die(mysql_error());
			}
		  
		  }else{
		  // если значения не существует делаем Insert
			if ( $cmdb_values != '' ){
			if ( !isset($_POST[$index_num]) ){
				#print 'INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`) VALUES ("'.$cust_f_id.'","'.$_GET['edit'].'","'.$cmdb_values.'")<br>';
				mysql_query('INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`) VALUES ("'.$cust_f_id.'","'.$INVID.'","'.$cmdb_values.'")') or die(mysql_error());
			}else{
				#print 'INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`, `num`) VALUES ("'.$cust_f_id.'", "'.$_GET['edit'].'", "'.$cmdb_values.'", "'.$_POST[$index_num].'")<br>';
				mysql_query('INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`, `num`) VALUES ("'.$cust_f_id.'", "'.$INVID.'", "'.$cmdb_values.'", "'.$_POST[$index_num].'")') or die(mysql_error());
			}
			}
		  }

		}
		if ( isset($_POST['cmd_label']) and $_POST['cmd_label'] != '' ){
			mysql_query('UPDATE `cmdb_hosts` SET `host`="'.$_POST['cmd_label'].'" where `host_id`='.$INVID) or die(mysql_error()); 
		}
		header("Location: index.php"); exit(); # после формы сохранения возвращяемся на главную страницу
	};

#################################################################
######## Окно редактирования и добавления свойств объекта #######
#################################################################
  print '<div style="padding-left:15px;">';
  print '<form action="index.php?edit='.$INVID.'&save" name="form_cmdb_fields" method="post" style="padding:0px; margin:0px;";>
  <table>';
  
  $sql = mysql_query('SELECT host_id, host FROM `cmdb_hosts` WHERE host_id='.$INVID ,$mysql_connect);
  while ($tablerows = mysql_fetch_row($sql))
    {
		$env_name = $tablerows[1];
		print '<tr><td id="clear">NAME: <b></td><td></td><td><input size=80 style="font-weight: bold;" value="'.$env_name.'" name="cmd_label"></b></td></tr>';
    }

  $row = mysql_query('SELECT id,name,num FROM `cmdb_fields` WHERE type_id=1 ORDER BY  `cmdb_fields`.`sort` ASC',$mysql_connect); // получаем все свойства объекта
  while ($tablerows = mysql_fetch_row($row))
  {
	$cust_f_id = $tablerows[0];
	$cust_f_name = $tablerows[1];
	$cust_f_numtype = $tablerows[2];
    print '<tr style="border-color:#FAFAFA;border-width:1px 0 0 0;"><td>'.$cust_f_name.'</td><td id="clear" style="padding-left:10px;"></td><td>';

  
  //если поля не существуем то создаем его с пустыми значениями
  $sql_cmdb_values_test = mysql_query('SELECT id,count,num FROM `cmdb_values` WHERE `cmdb_values`.`host_id`='.$INVID.' and field_id='.$cust_f_id,$mysql_connect);
  if( !mysql_fetch_row($sql_cmdb_values_test) ){
	mysql_query('INSERT INTO `cmdb_values` (`field_id`, `host_id`, `count`, `num`) VALUES ("'.$cust_f_id.'", "'.$INVID.'", "", "'.$cust_f_numtype.'")') or die(mysql_error());
  };
  
  $sql_cmdb_values = mysql_query('SELECT id,count,num FROM `cmdb_values` WHERE `cmdb_values`.`host_id`='.$INVID.' and field_id='.$cust_f_id,$mysql_connect);
  #print 'SELECT id,count,num FROM `cmdb_values` WHERE `cmdb_values`.`host_id`='.$_GET['edit'].' and field_id='.$cust_f_id;
  
  // перебираем все поля по выбранному "свойству"
  while ( ($cmdb_values = mysql_fetch_row($sql_cmdb_values)) )
   {
		$ID=1;
		$result = mysql_query('select value from `cmdb_hint` where `field_id`='.$cust_f_id.' ORDER BY  `cmdb_hint`.`id` ASC ');
	  
	  if(  mysql_fetch_assoc($result) )
	  { # Если есть подсказки в cmdb_hint у данного поля (`field_id`='.$cust_f_id.') то создаем выпадающий список

		$result = mysql_query('SELECT value FROM `cmdb_hint` WHERE `field_id`='.$cust_f_id.' ORDER BY sort');
		print '<input name='.$cmdb_values[0].' value='.$cmdb_values[0].' type="hidden">'; // невидимое поле содержащее ValueID
		print '<select style="font-weight: bold;" name="'.$cmdb_values[0].'"><option style="font-weight: bold;" value=""></option>';
		$hint_true=0;
		while( $cmdb_hint = mysql_fetch_row($result ) ){
		  if($cmdb_values[1] == $cmdb_hint[0]){
			$selected='selected'; $hint_true=1;
		  }else{
			$selected='';
		  }
		  print '<option '.$selected.' style="font-weight: bold;"  value="'.$cmdb_hint[0].'">'.$cmdb_hint[0].'</option>';  
		};
		if( $hint_true==0 ){print '<option selected style="font-weight: bold;"  value="'.$cmdb_values[1].'">'.$cmdb_values[1].'</option>';  };
		print '</select>';

	  }else{ # Если подсказок для поля нет, выводим пустое поле ввода
	  
		print '<input name='.$cmdb_values[0].' value='.$cmdb_values[0].' type="hidden">'; // невидимое поле содержащее ValueID
		print '<input size=80 style="font-weight: bold;" value="'.$cmdb_values[1].'" name="'.$cmdb_values[0].'">';
		
	  }

	  # количественное значение к полю (поле num в cmdb_fields != 0) 
	  # добавляем рядом с полем выпадающий список от 1 до 99
	  if($cust_f_numtype != 0){
		print ' x <select name="num'.$cmdb_values[0].'">';
		for($i=1; $i <= 99; $i++){
		  if( $cmdb_values[2] == $i ){
			$selected = 'selected';
		  }
		  print '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		  $selected = '';
		}
		print '</select>';
	  }
	  
	  // кнопка добавления аналогичного поля
	  print '<a href="index.php?edit='.$INVID.'&cust_f_id='.$cust_f_id.'&cust_f_numtype='.$cust_f_numtype.'&add_under_properties">[+]</a></br>';
   }
  
  }

  print '</td></tr>';
  print '</table>';
  print '</div>';
  print '
  <a href="javascript:document.form_cmdb_fields.submit()" id="menu" style="margin-left:3px;">Save</a>
  <a href="" id="menu" style="margin-left:3px;">Clone</a>
  <a href="javascript:document.form_cmdb_fields_del.submit()" id="menu" style="margin-left:3px;" onClick="return window.confirm(\'Удалить элемент: '.$env_name.'. Бутут удалены все связанные с ним данные !\')">Delete</a>
  </form>
  <form action="index.php?edit='.$INVID.'&inv_del" name="form_cmdb_fields_del" method="post" style="padding:0px; margin:0px;";><input name="env_del" type="hidden" value="1"></form>';

} elseif ( !isset($_GET['edit']) and isset($_GET['custom_fields'])  and !isset($_GET['hint']) and !isset($_GET['add_label']) ){
####################################################################################
#########################    Работа с кастомными полями ############################
####################################################################################

    // редактирование полей 
    if( isset($_GET['id']) and isset($_GET['edited']) and isset($_POST['sort']) and isset($_POST['name']) and isset($_GET['custom_fields']) and !isset($_POST['delete']) ){
       
      if ( !isset($_POST['num']) ){
        $num = '0';
      }else{
        $num = '1';
      }

      if ( !isset($_POST['front']) ){
        $front = '0';
      }else{
        $front = '1';
      }

      mysql_query('UPDATE `cmdb_fields` SET name="'.str_replace(' ','_',$_POST['name']).'", front="'.$front.'", sort="'.$_POST['sort'].'", num="'.$num.'" WHERE id='.$_GET['id'].' LIMIT 1',$mysql_connect) or die(mysql_error());
    }

    // Добавляем новое поле
    if( isset($_GET['add']) and isset($_POST['sort']) and isset($_POST['name']) and $_POST['name'] != '' and isset($_GET['custom_fields']) and !isset($_POST['delete']) ){ 
      if ( !isset($_POST['num']) ){
        $num = '0';
      }else{
        $num = '1';
      }

      if ( !isset($_POST['front']) ){
        $front = '0';
      }else{
        $front = '1';
      }       
      mysql_query('INSERT `cmdb_fields` (`name`, `front`, `sort`, `type_id`, `num`) VALUES("'.str_replace(' ','_',$_POST['name']).'", "'.$front.'", "'.$_POST['sort'].'", "1", "'.$num.'")', $mysql_connect) or die(mysql_error());
    }

	// Удаляем поле "Свойства объекта"
	if(isset($_POST['delete']))
	{
		# чистим все значения закрепленные за этим полем
		mysql_query('DELETE from `cmdb_values` WHERE field_id='.$_GET['id'], $mysql_connect) or die(mysql_error());
		# чистим справочник по этому полю
		mysql_query('DELETE from `cmdb_hint` WHERE field_id='.$_GET['id'], $mysql_connect) or die(mysql_error());
		# удаляем поле из таблички cmdb_fields
		mysql_query('DELETE from cmdb_fields WHERE id='.$_GET['id'], $mysql_connect) or die(mysql_error());
	}

###################################################	
###### Окно редактирования Свойст объекта
	
    $res_cmdb_fields = mysql_query('SELECT id,name,sort,front,num FROM `cmdb_fields` ORDER BY `sort` ASC',$mysql_connect);
    print "<table><tr><td id='head'>Название</td><td id='head'>Sort</td id='head'><td id='head'>На главный экран</td><td id='head'>Нумирация</td><td></td></tr>";
    while ($row = mysql_fetch_row($res_cmdb_fields)){
	
		print '<form id="clear" name="edit_cmdb_fields'.$row[0].'" action="i
		ndex.php?custom_fields&id='.$row[0].'&edited" method="post">';
		$field_name = $row[1];
		print '<tr><td style="text-align:center";><input name="name" style="font-weight: bold;" value="'.$row[1].'"></td>
		<td><input name="sort"  style="width:100%" value="'.$row[2].'"></td>';

		$checked = '';
		if($row[3] == '1'){
			$checked='checked';
		}else{
			$checked='';
		}
		print '<td style="text-align:center"><input '.$checked.' name="front" type="checkbox" value="1"></td>';
		  
		$checked = '';
		if($row[4] == '1'){
			$checked='checked';
		}else{
			$checked='';
		}
		print '<td style="text-align:center"><input '.$checked.' name="num" type="checkbox" value="1"></td>';

		print '<td>
		<a href="javascript:document.edit_cmdb_fields'.$row[0].'.submit()" id="menu" style="margin-left:3px;">Edit</a>
		<input type="submit" value="Delete" name="delete" id="menu" onClick="return window.confirm(\'Удалить элемент: '.$field_name.'? Так же будут удалены все связанные данные!\')">
		</td></tr>
		</form>';
    }
    
    print '
<form id="clear" name="add_cmdb_fields" action="index.php?custom_fields&add" method="post">
<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
<tr>
<td><input name="name" style="width:100%"></td>
<td><input name="sort" style="width:100%"></td>
<td style="text-align:center"><input name="num" type="checkbox" value="1"></td>
<td style="text-align:center"><input name="front" type="checkbox" value="1"></td>
<td style="text-align:center">
<a href="javascript:document.add_cmdb_fields.submit()" id="menu" style="margin-left:3px;">ADD</a>
</td>
</tr>
</form>';
    print '</table>';

  #}


#################################################################################
##### Справочник 
}elseif ( isset($_GET['hint']) and !isset($_GET['add_label']) ){
// вставляем новое кастомное поле
  if( isset($_GET['add']) and isset($_GET['fieldid']) ){
    mysql_query('INSERT `cmdb_hint` (`field_id`, `value`) VALUES("'.$_GET['fieldid'].'", "'.$_POST['value'].'")',$mysql_connect) or die(mysql_error());
  }
// изменяем отдельное поля справочника
  if( isset($_GET['edit']) and isset($_GET['hintid']) and isset($_POST['value']) ){
	// в таблице cmdb_values
	mysql_query('UPDATE `cmdb_values` SET `cmdb_values`.`count` =  "'.$_POST['value'].'" WHERE  `cmdb_values`.`count` = (select value from `cmdb_hint` WHERE `cmdb_hint`.`id`="'.$_GET['hintid'].'");',$mysql_connect) or die(mysql_error());
	// в cmdb_hint
    mysql_query('UPDATE `cmdb_hint` SET `value`="'.$_POST['value'].'" WHERE `id`="'.$_GET['hintid'].'"',$mysql_connect) or die(mysql_error());
  }
// изменение сортировки
  if( isset($_GET['edit']) and isset($_GET['hintid']) and isset($_GET['sortup']) ){
    mysql_query('UPDATE `cmdb_hint` SET `sort`=`sort`-1 WHERE `id`="'.$_GET['hintid'].'"',$mysql_connect) or die(mysql_error());
  }
  if( isset($_GET['edit']) and isset($_GET['hintid']) and isset($_GET['sortdown']) ){
    mysql_query('UPDATE `cmdb_hint` SET `sort`=`sort`+1 WHERE `id`="'.$_GET['hintid'].'"',$mysql_connect) or die(mysql_error());
  }
  
// Выводим список подсказок по каждому кастомному полю
  print '<table style="margin-left:3px;width:99%;">';
  $res_cmdb_fields = mysql_query('SELECT id,name FROM `cmdb_fields` ORDER BY `sort` ASC',$mysql_connect);
  $tr='';$tr2='';$t=0;
  while ($row = mysql_fetch_row($res_cmdb_fields)){
    if($t==6){
      $t=0;$tr='<tr>';$tr2='</tr>';
    }
    print $tr2.$tr.'<td>';
    print '<b>'.$row[1].'</b>';
    print '<table>';
    $res_cmdb_hint = mysql_query('SELECT id,value FROM `cmdb_hint` WHERE field_id='.$row[0].' ORDER BY  `cmdb_hint`.`sort` ASC ',$mysql_connect);
    while ( $row2 = mysql_fetch_row($res_cmdb_hint) ){
      print '
<form name="add_cmdb_hint" action="index.php?hint&edit&hintid='.$row2[0].'" method="post">
<tr>
<td><input name="value" value="'.$row2[1].'"></td>
<td><input id="button" type="submit" value="edit"></td>
<td><a href="javascript:document.del_cmdb_hint_'.$row2[0].'.submit()" id="menu" onClick="return window.confirm(\'Удалить элемент: '.$row2[1].'? Так же будут удалены все связанные данные!\')">del</a></td>
<td><a id="button" href="index.php?hint&edit&hintid='.$row2[0].'&sortup">+</a></td>
<td><a id="button" href="index.php?hint&edit&hintid='.$row2[0].'&sortdown">-</a></td>
</tr>
</form>';
print '<form name="del_cmdb_hint_'.$row2[0].'" action="index.php?hint&edit&hintid='.$row2[0].'" method="post"><input type="hidden" name="cmdb_del_hint" value="1"></form>';
    }
    print '<form name="add_cmdb_hint" action="index.php?hint&add&fieldid='.$row[0].'" method="post"><tr><td><input name="value" value=""></td><td><input type="submit" value="add"></td><td></td></tr></form>';
    print '</table></td>';
    $t++;$tr='';$tr2='';
  }
  print '</table></div>'; 


#################################################################################
#### Функция добавления нового элемента
/*
}elseif( isset($_GET['add_label']) ) {
	print '<form name="add_label" action="index.php?add_label" method="post">
	Name: <input name="add_cmdb_label" value="">
	<input type="submit" value="add">
	</form>';
	if( isset($_POST['add_cmdb_label']) and $_POST['add_cmdb_label'] != "" ){
		//print 'INSERT INTO  `cmdb`.`cmdb_hosts` (`host`) VALUES ('.$_POST['add_cmdb_label'].');';
		$res_cmdb_label = mysql_query('INSERT INTO `cmdb_hosts` (`host`) VALUES ("'.$_POST['add_cmdb_label'].'");', $mysql_connect) or die(mysql_error());
		header("Location: index.php"); exit(); # после формы сохранения вохвращяемся на главную страницу
	}
*/

#################################################################################
#### Help
}elseif( isset($_GET['help']) ) {
	print '<pre>';
	include 'README.txt';
	print '</pre>';
}


include 'tail.php';

include 'db_end.php'; # Закрываем соединения с DB

?>

