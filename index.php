<?php

# открываем соединение с DB
include 'db_con.php';


# шапка сайта
include 'header.php';


#########################################################################################
// Список инвентаря 
if( !isset($_GET['edit']) and !isset($_GET['custom_fields']) and !isset($_GET['hint']) and !isset($_GET['add_label']) and !isset($_GET['help']) )
{

	include 'show_all_inventory.php';

} elseif (isset($_GET['edit']) and !isset($_GET['hint']) and !isset($_GET['add_label']) )
{
// Сохраняем кастомные поля
	if(isset($_GET['save'])){
		include 'custom_fields_save.php';
	};

// Вывод всех полей по выбранному инвентарю
  print '<div style="padding-left:15px;">';
  print '<form action="index.php?edit='.$_GET['edit'].'&save" name="form_cmdb_fields" method="post" style="padding:0px; margin:0px;";>
  <table>';
  
  $sql = mysql_query('SELECT host_id, host FROM `cmdb_hosts` WHERE host_id='.$_GET['edit'] ,$mysql_connect);
  while ($tablerows = mysql_fetch_row($sql))
    {
       print '<tr><td id="clear">NAME: <b></td><td></td><td><input size=80 style="font-weight: bold;" value="'.$tablerows[1].'" name="cmd_label"></b></td></tr>';
	   $env_name = $tablerows[1];
    }

  $row = mysql_query('SELECT id,name,num FROM `cmdb_fields` WHERE type_id=1 ORDER BY  `cmdb_fields`.`sort` ASC',$mysql_connect);
  while ($tablerows = mysql_fetch_row($row))
  {
    print '<tr style="border-color:#FAFAFA;border-width:1px 0 0 0;"><td>'.$tablerows[1].'</td><td id="clear" style="padding-left:10px;"></td>';

  $sql_cmdb_values = mysql_query('SELECT id,count,num FROM `cmdb_values` WHERE cmdb_values.host_id='.$_GET['edit'].' and field_id='.$tablerows[0],$mysql_connect);
  $cmdb_values = mysql_fetch_array($sql_cmdb_values);
  print '<td>';
  $result = mysql_query('select value from `cmdb_hint` where `field_id`='.$tablerows[0].' ORDER BY  `cmdb_hint`.`id` ASC ');
  
  if(  mysql_fetch_assoc($result) ){ 
  # Если есть подсказки в cmdb_hint у данного поля (`field_id`='.$tablerows[0].') то создаем выпадающий список
    $result = mysql_query('select value from `cmdb_hint` where `field_id`='.$tablerows[0].' ORDER BY sort');
    print '<select style="font-weight: bold;" name="'.$tablerows[1].'">';
    print '<option style="font-weight: bold;" value="'.$cmdb_hint[0].'"></option>';
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

  }else{
  # Если подсказок нет выводим пустое поле ввода
    print '<input size=80 style="font-weight: bold;" value="'.$cmdb_values[1].'" name="'.$tablerows[1].'">';
  }

  # количественное значение к полю (поле num в cmdb_fields != 0) 
  # то добавляем рядом с полем выпадающий список от 1 до 99
  if($tablerows[2] != 0){
    print ' x <select name="num'.$tablerows[1].'">';
    for($i=1; $i <= 99; $i++){
      if( $cmdb_values[2] == $i ){
        $selected = 'selected';
      }
      print '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
      $selected = '';
    }
    print '</select>';
  }

  }

  print '</td></tr>';
  print '</table>';
  print '</div>';
  print '
  <a href="javascript:document.form_cmdb_fields.submit()" id="menu" style="margin-left:3px;">Save</a>
  <a href="" id="menu" style="margin-left:3px;" onClick="return window.confirm(\'Удалить элемент: '.$env_name.' \')">Delete</a>
  </form>';

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

	// Удаляем поле
	if(isset($_POST['delete']))
	{
		# чистим все значения закрепленные за этим полем
		mysql_query('DELETE from `cmdb_values` WHERE field_id='.$_GET['id'], $mysql_connect) or die(mysql_error());
		# удаляем поле из таблички cmdb_fields
		mysql_query('DELETE from cmdb_fields WHERE id='.$_GET['id'], $mysql_connect) or die(mysql_error());
	}
	
// выводим список всех кастомных полей
    $res_cmdb_fields = mysql_query('SELECT id,name,sort,front,num FROM `cmdb_fields` ORDER BY `sort` ASC',$mysql_connect);
    print "<table><tr><td id='head'>Название</td><td id='head'>Sort</td id='head'><td id='head'>На главный экран</td><td id='head'>Нумирация</td><td></td></tr>";
    while ($row = mysql_fetch_row($res_cmdb_fields)){
      print '<form id="clear" name="edit_cmdb_fields'.$row[0].'" action="index.php?custom_fields&id='.$row[0].'&edited" method="post">';
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
	  <input type="submit" value="Delete" name="delete" id="menu" onClick="return window.confirm(\'Удалить элемент: '.$field_name.'? Так же будут удалены все связанные данные с данным полем!\')">
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
// изменяем существующее кастомное поле
  if( isset($_GET['edit']) and isset($_GET['hintid']) and isset($_POST['value']) ){
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
<td><input id="button" type="submit" value="dell"></td>
<td><a id="button" href="index.php?hint&edit&hintid='.$row2[0].'&sortup">+</a></td>
<td><a id="button" href="index.php?hint&edit&hintid='.$row2[0].'&sortdown">-</a></td>
</tr>
</form>';
    }
    print '<form name="add_cmdb_hint" action="index.php?hint&add&fieldid='.$row[0].'" method="post"><tr><td><input name="value" value=""></td><td><input type="submit" value="add"></td><td></td></tr></form>';
    print '</table></td>';
    $t++;$tr='';$tr2='';
  }
  print '</table></div>'; 


#################################################################################
#### Функция добавления нового элемента
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

