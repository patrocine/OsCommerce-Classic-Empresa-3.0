<?php
/*
osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright � 2003 osCommerce
  
Database Manager Contribution 2.0
Custom Developed by James Harvey aka DemonAngel
altered@alteredpixels.net
Copyright � 2003 Altered Pixels
* 
* Modified by Rigadin (rigadin@osc-help.net) to see only the store database.
 
http://www.alteredpixels.net/
Released under the GPL
*/

  require('includes/application_top.php');

  $current_boxes = DIR_FS_ADMIN . DIR_WS_BOXES;
 
?>
<?php   require(DIR_WS_INCLUDES . 'template_top.php');?>

<?php
set_time_limit(0);
error_reporting (E_ALL ^ E_NOTICE);
session_start();

$DBH=DB_SERVER;
$DBU=DB_SERVER_USERNAME;
$DBP=DB_SERVER_PASSWORD;

$VER=array(
  "NAME"=>TEXT_NAME,
  "WEB"=>"OsCommerce",
  "MAJOR"=>"2.0",
  "MINOR"=>".50",
  "BUILD"=>"150"
);
$WIZ=$_SERVER[SCRIPT_NAME];
if(!$_SESSION[RPP]) $_SESSION[RPP]=20;

$dbl=@mysql_connect($DBH,$DBU,$DBP) or die(TEXT_DENIED);

switch($_REQUEST[hop]) {
//SET DATABASE
  case "1":
    $_SESSION[DBN]=DB_DATABASE; // Rigadin: limit to store database
    $_SESSION[msg]=FOOTER_TEXT_DB_SERVER;
    // $_SESSION[DBN];
  break;

//UNSET DATABASE AND TABLE
  case "2":
    unset($_SESSION[DBN]);
    unset($_SESSION[TBN]);
    unset($_SESSION[select]);
    unset($_SESSION[order]);
    $_SESSION[msg]=FOOTER_TEXT_DB_SERVER;
  break;

//CREATE NEW DATABASE
  case "3":
    // Rigadin: code removed, do not allow to create new database
  break;

//DROP DATABASE
  case "4":
    if(mysql_query(TEXT_DROP_DB." $_GET[dbn2drop]",$dbl)) {
      $_SESSION[msg]=TEXT_DB."<b>$_GET[dbn2drop]</b>".TEXT_TABLE_REMOVED;
      unset($_SESSION[DBN]);
    } else {
      $_REQUEST[op]=1;
      $_SESSION[msg]=TEXT_ERROR_REMOVING_DB." <b>$_GET[dbn2drop]</b>";
    }
  break;

//SET TABLE
  case "5":
    $_SESSION[TBN]=$_REQUEST[tbn];
    $_SESSION[PG]=0;
    $_SESSION[WHERE]="";
    $_SESSION[msg]=TEXT_TABLE."<b>$_SESSION[DBN].$_SESSION[TBN]</b>".TEXT_TABLE_SELECTED;
  break;

//UNSET TABLE
  case "6":
    unset($_SESSION[TBN]);
    $_SESSION[msg]=TEXT_TABLE_OF."<b>$_SESSION[DBN]</b>";
  break;

//SET RPP VALUE
  case "7":
    $_SESSION[RPP]=$_POST[rpp];
    $_SESSION[PG]=0;
  break;

//SET PG VALUE
  case "8":
    $_SESSION[PG]=($_REQUEST[pg]) ? $_REQUEST[pg] : 0;
  break;

//SET WHERE FOR QUERY
  case "9":
    if($_REQUEST[tbn]) $_SESSION[TBN]=$_REQUEST[tbn];
    $_SESSION[PG]=0;
    $where=stripslashes(trim($_POST[where]));
    $_POST[qr]="SELECT * FROM $_SESSION[TBN] WHERE ".(($where)?$where:"1");
    $_SESSION[msg]=TEXT_TABLE."<b>$_SESSION[DBN].$_SESSION[TBN]</b>".TEXT_TABLE_SELECTED;
  break;

  case "9b":
    if(!$_SESSION[PG]) $_SESSION[PG]=0;
  break;

//10:ESPORTA CSV
  case "10":
    $campi=mysql_list_fields($_SESSION[DBN],$_SESSION[TBN],$dbl);
    $cols=mysql_num_fields($campi);
    for($i=0;$i<$cols;$i++) $dump.="\"".mysql_field_name($campi,$i)."\",";
    $dump=substr($dump,0,-1)."\n";
    $rs=mysql_query("SELECT * FROM $_SESSION[TBN]",$dbl);
    while($rc=mysql_fetch_row($rs)) {
      for($i=0;$i<$cols;$i++) $dump.="\"".addslashes($rc[$i])."\",";
      $dump=substr($dump,0,-1)."\n";
    }
    header("Content-type: text/plain");
    header("Content-Disposition: filename=$_SESSION[DBN].$_SESSION[TBN].sql");
    die($dump);
  break;

//11:ALTER TABLE
  case "11":
    $_SESSION[msg]=TEXT_ALTER_TABLE."<b>$_SESSION[DBN].$_SESSION[TBN]</b>";
  break;
}

if($_SESSION[DBN]) {
  mysql_select_db($_SESSION[DBN],$dbl);
  $rs=mysql_query("SHOW TABLES from $_SESSION[DBN]",$dbl);
  echo mysql_error();
  for($i=0;$i<mysql_num_rows($rs);$i++) {
    $tbn=mysql_tablename($rs,$i);
    $TABLES.="<option value='$tbn' ".(($tbn==$_SESSION[TBN])?TEXT_SELECT:"").">$tbn</option>";
  }
} elseif ($_REQUEST[op]!="999") unset($_REQUEST[op]);
?>
<table cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr valign="middle" height="20" class="dataTableHeadingRow">
    <td width="75%" class="headerBarContent">&nbsp; <b><?php echo HEADER_TITLE_MENU;?></b><?php echo HEADER_TITLE_INTERVAL;?>
      <a href="<?php echo($WIZ); ?>?hop=2" class="headerLink"><b><?php echo HEADER_TITLE_SHOW_DB;?></b></a>
<?php
  if($_REQUEST[op]=="1") {
    //SHOW TABLES
    echo("".HEADER_TITLE_INTERVAL." <a href='javascript:if(confirm(\"Sure to drop database $_SESSION[DBN]?\")) window.open(\"$WIZ?hop=4&op=1&dbn2drop=$_SESSION[DBN]\",\"_self\")' class='headerLink'><b>".HEADER_TITLE_DROP_CURRENT_DB." </b></a>");
  } else {
    //TABLE FUNCTIONS
    echo("".HEADER_TITLE_INTERVAL." <a href='$WIZ?op=1&hop=6' class='headerLink'><b>".TEXT_BACK_TABLE."</b></a>");
    if($_SESSION[TBN]) 
      echo("".HEADER_TITLE_INTERVAL."<a href='$WIZ?op=2' class='headerLink'><b>".TEXT_PROPERTIES."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='$WIZ?op=4&hop=9' class='headerLink'><b>".TEXT_BROWSE."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='$WIZ?op=8' class='headerLink'><b>".TEXT_INSERT."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='javascript:if(confirm(\"Sure to EMPTY table $_SESSION[TBN]?\")) window.location=\"$WIZ?op=6\"' class='headerLink'><b>".TEXT_EMPTY."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='javascript:if(confirm(\"Sure to DROP table $_SESSION[TBN]?\")) window.location=\"$WIZ?op=7&table2drop=$_SESSION[TBN]\"' class='headerLink'><b>".TEXT_DROP."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='$WIZ?op=14' class='headerLink'><b>".TEXT_OPTIMIZE."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='$WIZ?op=10' class='headerLink'><b>".TEXT_IMPORT."</b></a>
            ".HEADER_TITLE_INTERVAL."<a href='$WIZ?hop=10' target='_blank' class='headerLink'><b>".TEXT_EXPORT."</b></a>
            ");
     }

    if($_SESSION[DBN]) {
      echo($_SESSION[DBN]);
      if($_SESSION[TBN]) echo(".$_SESSION[TBN]");
    }
?>
      </b></font> &nbsp;
	</td>
  </tr>
</table>
<table cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr valign="top">
<?php
  if($_REQUEST[op]<100) {
?>
    <td width="160" align="center" class="smallText">
<?php
    if($_SESSION[DBN]) {
?>
      <form name='qry' action="<?php echo($WIZ); ?>?op=4" method="post">
      <b><br><img src="images/database.gif" alt="db_admin" width="20" height="20" align="absmiddle"> <?php echo TEXT_QUERY;?><br><br></b><br>
      <textarea name="qr" style="width:150pt;height:75pt;"><?php echo(($_POST[qr])?stripslashes($_POST[qr]):$_SESSION[select]); ?></textarea><br>
      <input type="submit" style="width:150pt;" value="<?php echo TEXT_EXECUTE; ?>"><br>
      <input onClick=javascript:this.form.qr.focus();this.form.qr.select(); type="button" value="<?php echo TEXT_SELECT; ?>" style="width:75pt;"><input onClick="javascript:this.form.qr.focus();this.form.qr.value='';" type="button" value="<?php echo TEXT_CLEAN; ?>" style="width:75pt;">
      </form>
	  <hr>
      <form name="qry" action="<?php echo($WIZ); ?>?op=4" method="post" enctype="multipart/form-data">
      <b><br><img src="images/database.gif" alt="db_admin" width="20" height="20" align="absmiddle"> <?php echo TEXT_QUERY_FROM_FILE;?><br><br></b><br>
      <input type="file" name="qrf" style="width:150pt;"><br>
      <input type="submit" style="width:150pt;" value="<?php echo TEXT_EXECUTE; ?>">
      </form>
      <br>
      <?php echo(($TABLES)?"<hr><form name='sel' action='$WIZ?op=4&hop=9' method='post'>
      <b><br><img src='images/database.gif' alt='db_admin' width='20' height='20' align='absmiddle'> <b>".TEXT_SELECT."</b><br><br>".TEXT_SELECT_FROM."<br><br>
      <select name='tbn'>$TABLES</select><br><br>".TEXT_WHERE."<br><br>
      <textarea name='where' style='width:150pt;height:75pt;'>$_SESSION[WHERE]</textarea><br>
      <input type='submit' style='width:150pt;' value=".TEXT_EXECUTE."><br>
      </form>":""); ?>
      <hr><form action="<?php echo($WIZ); ?>?op=12" method="post">
      <b><br><img src="images/database.gif" alt="db_admin" width="20" height="20" align="absmiddle"> <?php echo TEXT_CREATE_TABLE;?><br><br></b>
      <?php echo TEXT_TABLE_NAME;?><br><br>
      <input type="text" name="tablenew" style="width:150pt;"><br><br><?php echo TEXT_NUMBER_FIELD;?><br><br><input type="text" name="tablefields" style="width:50pt;"><br>
      <input type="submit" style="width:150pt;" value="<?php echo TEXT_EXECUTE; ?>">
      <br>
      </form>
<?php
    } // Rigadin: Create Database option removed
?>
    </td>
<?php
  }
?>
    <td align="center" bgcolor="#FFFFFF" class="smallText" >
<?php
  switch($_REQUEST[op]) {
//1: SHOW TABLES
  case "1":
    echo("<table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>
            <tr style='background-color:#f4f7fd;'>
              <th width='70%' class='smalltext' align='left'>".TEXT_TABLE_NAME."</th>
              <th width='30%' class='smalltext' align='right'>".TEXT_TABLE_RECORDS."</th>
            </tr>
		  </table>\n");
    echo("<div align='center' style='overflow: auto; height: 720px; width: 100%; scrollbar-width: 7px; text-align:left;'>");
    echo("<table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>");
    $rs=mysql_query("SHOW TABLES from $_SESSION[DBN]",$dbl);
    echo mysql_error();
    for($i=0;$i<mysql_num_rows($rs);$i++) {
      $bgcolor=($bgcolor=="#E2CFCF")?"#FFFFFF":"#E2CFCF";
      $tbn=mysql_tablename($rs,$i);
      echo("<tr align='center' style='background-color:$bgcolor' onMouseOver='javascript:this.style.backgroundColor=\"#f4f7fd\"' onMouseOut='javascript:this.style.backgroundColor=\"$bgcolor\"'>
              <td align='left'><a href='$WIZ?op=2&hop=5&tbn=$tbn'><b>$tbn</b></a></td>
              <td class='smalltext'>".mysql_num_rows(mysql_query("SELECT * FROM $tbn",$dbl))."</td>");
    }
    echo("</tr></table>");
  break;

//2:TABLE PROPERTIES
  case "2":
    if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
      $BORDER="style='border:1pt dotted #666666;border-collapse:collapse;' width='100%'";
      echo("<b><br>".TEXT_TABLE_STRUCTURE."</b><br><br><table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>
              <tr style='background-color:#f4f7fd;'>
                <th width='90' $BORDER class='smalltext'>".TEXT_FIELD."</th>
                <th width='180' $BORDER class='smalltext'>".TEXT_TYPE."</th>
                <th width='90' $BORDER class='smalltext'>".TEXT_NULL."</th>
                <th width='90' $BORDER class='smalltext'>".TEXT_DEFAULT."</th>
                <th width='90' $BORDER class='smalltext'>".TEXT_EXTRA."</th>
                <th $BORDER class='smalltext' colspan='3'>".TEXT_ACTIONS."</th>
              </tr>\n");
      $campi=mysql_list_fields("$_SESSION[DBN]","$_SESSION[TBN]",$dbl);
      $cols=mysql_num_fields($campi);
      for($i=0;$i<$cols;$i++) {
        $bgcolor=($bgcolor=="#E2CFCF")?"#FFFFFF":"#E2CFCF";
        $col_info=mysql_fetch_array(mysql_query("SHOW COLUMNS FROM $_SESSION[TBN] LIKE '".mysql_field_name($campi,$i)."'"));
        echo("<tr style='background-color:$bgcolor' onMouseOver='javascript:this.style.backgroundColor=\"#f4f7fd\"' onMouseOut='javascript:this.style.backgroundColor=\"$bgcolor\"'>
                <td $BORDER class='smalltext'><b>".(($col_info[3]=="PRI")?"<u>":"")."$col_info[0]</u></b></td>
                <td $BORDER class='smalltext'>$col_info[1]</td>
                <td $BORDER class='smalltext'>$col_info[2]</td>
                <td $BORDER class='smalltext'>$col_info[4]</td>
                <td $BORDER class='smalltext'>$col_info[5]</td>
                <td $BORDER class='smalltext'><a href='$WIZ?hop=11&op=16&field=$col_info[0]&act=1'>".TEXT_CHANGE."</a></td>
                <td $BORDER class='smalltext'><a href='javascript:if(confirm(".TEXT_SURE_DROP_FIELD." &#39;$col_info[0]&#39;?\")) window.open(\"$WIZ?hop=11&op=15&field=$col_info[0]\",\"_self\")'>".TEXT_DROP."</a></td>
                <td $BORDER class='smalltext'><a href='$WIZ?hop=11&op=16&field=$col_info[0]&act=2'>".TEXT_ADD_FIELD."</a></td>
              </tr>");
      }
      echo("</table>
            <p><b>".TEXT_TABLE_INDEXES."</b><br><br><table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>
              <tr style='background-color:#f4f7fd;'>
                <th width='90' $BORDER class='smalltext'>".TEXT_KEY_NAME."</th>
                <th width='90' $BORDER class='smalltext'>".TEXT_FIELD."</th>
                <th width='90' $BORDER class='smalltext'>".TEXT_TYPE."</th>
                <th width='90' $BORDER class='smalltext'>".TEXT_CARDINALITY."</th>
                <th $BORDER class='smalltext'>".TEXT_ACTIONS."</th>
               </tr>\n");
      $rs_idx=mysql_query("SHOW INDEX FROM $_SESSION[TBN]",$dbl);
      while($rc_idx=mysql_fetch_object($rs_idx)) {
        if($rc_idx->Index_type=="BTREE") {
          if($rc_idx->Non_unique==1) $idx_type="INDEX";
          elseif($rc_idx->Key_name=="PRIMARY") $idx_type="PRIMARY";
          else $idx_type="UNIQUE";
        } elseif($rc_idx->Index_type=="FULLTEXT") {
          $idx_type="FULLTEXT";
        }
        $idx[$rc_idx->Key_name][type]=$idx_type;
        $idx[$rc_idx->Key_name][column][]=$rc_idx->Column_name;
        $idx[$rc_idx->Key_name][cardinality]=(isset($rc_idx->Cardinality))?$rc_idx->Cardinality:"None";
      }
      if(is_array($idx)) foreach($idx as $idx_name=>$idx_prop) {
        $bgcolor=($bgcolor=="#E2CFCF")?"#FFFFFF":"#E2CFCF";
        echo("<tr style='background-color:$bgcolor' onMouseOver='javascript:this.style.backgroundColor=\"#f4f7fd\"' onMouseOut='javascript:this.style.backgroundColor=\"$bgcolor\"'>
                <td $BORDER class='smalltext'><b>$idx_name</b></td>
                <td $BORDER class='smalltext'>");
        foreach($idx_prop[column] as $col) echo("$col<br>");
        echo("  </td>
                <td $BORDER class='smalltext'>$idx_prop[type]</td>
                <td $BORDER class='smalltext'>$idx_prop[cardinality]</td>
                <td $BORDER class='smalltext' align='center'><a href='javascript:if(confirm(".TEXT_SURE_DROP_INDEX." &#39;$idx_name&#39;?\")) window.open(\"$WIZ?hop=11&op=18&index=$idx_name\",\"_self\")'>".TEXT_DROP."</a></td>
              </tr>");
      }
      echo("</table>\n<p>".TEXT_ADD_INDEX."<select onChange='javascript:if(this.value>0 && confirm(".TEXT_CREATE_INDEX."\"+this.value+\" columns?\")) window.location=\"$WIZ?hop=11&op=19&cols=\"+this.value'>");
      for($i=0;$i<$cols;$i++) echo("<option value='$i'>".(($i>0)?$i:"")."</option>\n");
      echo("</select> ".TEXT_COLUMNS."</p>
            <p><b>".TEXT_TABLE_OPERATIONS."</b><br><br><table cellspacing='0' cellpadding='2' border='0'>
              <tr><td class='smalltext'><form method='post' action='$WIZ?hop=11&op=21'>".TEXT_TABLE_RENAME."<input type='text' name='new_table_name' value='$_SESSION[TBN]'> <input type='submit' value=".TEXT_RENAME.">
              </form></td></tr>
            </table></p>");
  break;

//4:EXECUTE QUERY
  case "4":
 $_POST[qr]=trim($_POST[qr]);
 if($_GET[order]) $_SESSION[ORDER]=$_GET[order];
 if(strtolower(substr($_POST[qr],0,6))=="select" || (!$_POST[qr] && $_SESSION[select])) {
  if($_POST[qr]) $_SESSION[select]=stripslashes($_POST[qr]);
  if(strrpos($_SESSION[select],";") == (strlen($_SESSION[select])-1)) $_SESSION[select]=substr($_SESSION[select],0,-1);
  if($rs=@mysql_query($_SESSION[select],$dbl)) {
   $rc_columns=mysql_list_fields($_SESSION[DBN],$_SESSION[TBN],$dbl);
   for($i=0;$i<mysql_num_fields($rc_columns);$i++) $columns[]=mysql_field_name($rc_columns, $i);
   for($i=0;($i*$_SESSION[RPP])<mysql_num_rows($rs);$i++) $pages.="<option value='$i' ".(($_SESSION[PG]==$i)?"selected":"").">".($i+1)."</option>\n";
   $disablenext=(mysql_num_rows($rs)<=($_SESSION[RPP]*($_SESSION[PG]+1)))?"disabled":"";
   $rs=mysql_query("$_SESSION[select] " . (($_SESSION[ORDER] && in_array($_SESSION[ORDER], $columns)) ? "ORDER BY $_SESSION[ORDER]" : "") . " LIMIT ".($_SESSION[PG]*$_SESSION[RPP]).",$_SESSION[RPP]",$dbl);
   echo("<p><b>".TEXT_QUERY."</b>: ".$_SESSION[select]."</p>
    <table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>
     <tr style='background-color:#f4f7fd;'><td colspan='2'>&nbsp;</td>");
   $i=0;
   while($fn[$i]=@mysql_field_name($rs,$i)) {
    echo("<th style='border:1pt dotted #999999;border-collapse:collapse;' width='100%'><a href='$WIZ?op=4&order=$fn[$i]'>$fn[$i]</a></th>");
    $i++;
   }
   echo("</tr>");
   while($rc=mysql_fetch_row($rs)) {
    $bgcolor=($bgcolor=="#E2CFCF")?"#FFFFFF":"#E2CFCF";
    echo("<tr style='background-color:$bgcolor;' onMouseOver='javascript:this.style.backgroundColor=\"#f4f7fd\"' onMouseOut='javascript:this.style.backgroundColor=\"$bgcolor\"'>");
    $where=$fields="";
    for($j=0;$j<$i;$j++) {
     $fields.="<td style='border:1pt dotted #999999;'>".nl2br($rc[$j])."</td>\n";
     if($rc[$j]) $where.=$fn[$j]."='".addslashes($rc[$j])."' AND ";
    }
    echo("
     <td align='left'><form method='post' action='$WIZ?op=8'>
      <input type='hidden' name='edit' value='".base64_encode($where)."'>
      <input type='submit' value=".TEXT_EDIT.">
     </form></td>
     <td><form method='post' action='$WIZ?op=5'>
      <input type='hidden' name='del' value='".base64_encode($where)."'>
      <input type='button' value=".TEXT_DELETE." onClick='javascript:if(confirm(". TEXT_DELETE_RECORD .")) submit();'>
     </form></td>
     $fields
    </tr>");
   }
   echo("</table>
    <p align='left'>
      <input type='button' value='&lt;&lt; ".TEXT_PAGE_PREVIOUS."' style='width:150pt;' ".((($_SESSION[PG]-1)<0)?"disabled":"")." onClick='javascript:window.location=\"$WIZ?op=4&hop=8&pg=".($_SESSION[PG]-1)."\"'>
      &nbsp;
      <input type='button' value='".TEXT_PAGE_NEXT." &gt;&gt;' style='width:150pt;' $disablenext onClick='javascript:window.location=\"$WIZ?op=4&hop=8&pg=".($_SESSION[PG]+1)."\"'>
    </p>
    <form method='post' action='$WIZ?op=4&hop=7'>
      <p align='left'>
        <input type='submit' value=".TEXT_SHOW."> <input type='text' name='rpp' value='$_SESSION[RPP]' size='4'>".TEXT_RECORDS_FOR_PAGE."
      </p>
	</form>
    <form method='post' action='$WIZ?op=4&hop=8'>
      <p align='left'>
        <input type='submit' value=".TEXT_GO.">".TEXT_PAGE."<select name='pg' onChange='javascript:submit()'>$pages</select>
      </p> 
    </form>
   ");
  } else {
   echo("<p align='left'><b>".TEXT_SELECT_FAILED."</b> - ".TEXT_CHECK_QUERY."<br>Error: ".mysql_error()."</p>\n");
  }
 } else {
   echo ("<br>".TEXT_QUERY_RESULTS."");
  if(file_exists($_FILES[qrf][tmp_name])) {
   $queries=file($_FILES[qrf][tmp_name]);
   for($i=0;$i<count($queries);$i++) {
    $queries[$i]=trim($queries[$i]);
    if($queries[$i][0]!="#") $new_queries[]=$queries[$i];
   }
   $qr=split(";\n",implode("\n",$new_queries));
  } else {
   $qr=split(";\n",stripslashes($_POST[qr]));
  }
  foreach($qr as $qry) {
   if(trim($qry)) echo("<p align='left'>".((mysql_query(trim($qry),$dbl))?"<b>".TEXT_OK."</b> - $qry<br>":"<b>".TEXT_FAILED."</b> - $qry")."</p>\n");
  }
 }
 if(!$_SESSION[TBN]) echo("<p align='left'><a href='$WIZ?op=1'><b>&gt;&gt;".TEXT_SHOW_TABLE."</b></a></p>");
 else echo("<p align='left'><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//5:DELETE RECORD
case "5":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo("<p>".((mysql_query("DELETE FROM $_SESSION[TBN] WHERE ".base64_decode($_POST[del])." 1 LIMIT 1",$dbl))?TEXT_RECORDS_DELETED:TEXT_UNABLE_DELETED_RECORDS)."</p>");
 echo("<p><a href='$WIZ?op=4&hop=9b'><b>&gt;&gt;".TEXT_BROWSE_TABLE."</b></a></p>");
 break;

//6:EMPTY TABLE
case "6":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 mysql_query("DELETE FROM $_SESSION[TBN]");
 echo("<b>".TEXT_TABLE."$_SESSION[TBN]".TEXT_EMPTY_NOW."</b></p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt;".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//7:DROP TABLE
case "7":
 if(!$_GET[table2drop]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 mysql_query("DROP TABLE $_GET[table2drop]");
 echo("<b>".TEXT_TABLE."$_GET[table2drop]".TEXT_DROPPED."</b></p>");
 echo("<p><a href='$WIZ?op=1'><b>&gt;&gt;".TEXT_TABLE_LIST."</b></a></p>");
 unset($_SESSION[TBN]);
 break;

//8:INSERT/EDIT RECORD
case "8":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo("<form method='post' action='$WIZ?op=9'><input type='hidden' name='edit' value='$_POST[edit]'>");
 if($_POST[edit]) $rc=mysql_fetch_row(mysql_query("SELECT * FROM $_SESSION[TBN] WHERE ".base64_decode($_POST[edit])." 1 LIMIT 1",$dbl));
 $campi=mysql_list_fields($_SESSION[DBN],$_SESSION[TBN],$dbl);
 $cols=mysql_num_fields($campi);
 echo("<p><table>");
 for($i=0;$i<$cols;$i++) echo("<tr><td align='right'><b>".mysql_field_name($campi,$i)."</b>: </td><td>".((mysql_field_type($campi,$i)=="blob")?"<textarea cols='40' rows='4' name='".mysql_field_name($campi,$i)."'>$rc[$i]</textarea>":"<input type='text' name='".mysql_field_name($campi,$i)."' value='$rc[$i]' size='50'>")."</td></tr>");
 echo("</table></p>");
 echo("<input type='submit' value=".TEXT_SAVE."> <input type='reset' value=".TEXT_RESET."> <input type='button' value=".TEXT_BACK_TABLE_CONTENT." onClick='javascript:window.location=\"$WIZ?op=4&hop=9b\"'></form>");
 break;

//9:SAVE RECORD
case "9":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 $campi=mysql_list_fields($_SESSION[DBN],$_SESSION[TBN],$dbl);
 $cols=mysql_num_fields($campi);
 for($i=0;$i<$cols;$i++) {
  $field="$"."_POST[".mysql_field_name($campi,$i)."]";
  eval("\$field=\"$field\";");
  $fields.=mysql_field_name($campi,$i)."='" . str_replace("\$","\\\$",$field) . "', ";
 }
 eval("\$fields=\"$fields\";");
 $fields=substr($fields,0,-2);
 $qry=($_POST[edit])?"UPDATE $_SESSION[TBN] SET $fields WHERE ".base64_decode($_POST[edit])." 1 LIMIT 1":"INSERT INTO $_SESSION[TBN] SET $fields";
 echo((mysql_query($qry,$dbl))?TEXT_QUERY_EXECUTE:TEXT_ERROR_EXECUTING_QUERY);
 echo("<p><a href='$WIZ?op=4&hop=9b'><b>&gt;&gt;".TEXT_BROWSE_TABLE."</b></a></p>");
 break;

//10:SET CSV IMPORT
case "10":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo("<p><br>".TEXT_SELECT_CSV."$_SESSION[DBN].$_SESSION[TBN]</b></p>
  <form action='$WIZ?op=11' method='post' enctype='multipart/form-data'>
   <p>".TEXT_CSV_FILE."<input name='csv' type='file'></p>
   <p><input type='submit' value=".TEXT_IMPORT_CSV."></p>
  </form>");
 break;

//11:IMPORT CSV
case "11":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 if(!mysql_query("LOAD DATA LOCAL INFILE '".$_FILES['csv']['tmp_name']."' REPLACE INTO TABLE $_SESSION[TBN] FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\' LINES TERMINATED BY '\n'",$dbl)) $no="NOT";
 echo("<p><b>CSV $no imported into $_SESSION[DBN].$_SESSION[TBN]</b></p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt;".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//12:CREATE TABLE
case "12":
 echo("<p>Define <b>$_POST[tablefields]</b> fields for new table <b>$_POST[tablenew]</b>:</p>");
 echo("<form method='post' action='$WIZ?op=13&tablefields=$_POST[tablefields]&tablenew=$_POST[tablenew]'>");
 CreateTableStructure($_POST[tablefields],NULL);
 echo("<p><input type='submit' value=".TEXT_CREATE_TABLE."></p></form>");
 break;

//13:CREATE TABLE EXECUTE
case "13":
 $queryindex=array();
 echo("<p>Creating table <b>$_GET[tablenew]</b>:</p>");
 $query="CREATE TABLE $_GET[tablenew] (";
 for($i=0;$i<$_GET[tablefields];$i++) {
  $query.=$_POST[field][$i]." ";
  $query.=$_POST[type][$i]." ";
  if($_POST[len][$i]) $query.="(".stripslashes($_POST[len][$i]).") ";
  $query.=$_POST[attr][$i]." ";
  $query.=$_POST[null][$i]." ";
  if($_POST[def][$i]) $query.=" DEFAULT '".$_POST[def][$i]."' ";
  $query.=$_POST[extra][$i]." ";
  if($_POST[index][$i]) {
   if($_POST[index][$i]=="INDEX") $queryindex[]="ALTER TABLE $_GET[tablenew] ADD INDEX (".$_POST[field][$i]."); ";
   else $query.=$_POST[index][$i];
  }
  $query.=", ";
 }
 $query=substr($query,0,-2).");";
 echo("<p>");
 if(@mysql_query($query,$dbl)) {
  foreach($queryindex as $qi) @mysql_query($qi,$dbl);
  echo(TEXT_TABLE."<b>$_GET[tablenew]</b>".TEXT_CREATE);
  $_SESSION[TBN]=$_GET[tablenew];
  $_SESSION[PG]=0;
  $_SESSION[WHERE]="";
  $_SESSION[msg]=TEXT_TABLE."<b>$_SESSION[DBN].$_GET[tablenew]</b>".TEXT_CREATE;
 } else {
  echo(TEXT_ERROR_CREATING_TABLE."<b>$_GET[tablenew]</b>");
 }
 echo("</p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//14:OPTIMIZE TABLE
case "14":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 mysql_query("OPTIMIZE TABLE $_SESSION[TBN]");
 echo("<br><b>".TEXT_TABLE."$_SESSION[TBN]&nbsp;".TEXT_TABLE_OPTIMIZED."</b></p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//15:DROP FIELD FROM TABLE
case "15":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo("<b>".((mysql_query("ALTER TABLE $_SESSION[TBN] DROP COLUMN $_GET[field]"))?TEXT_FIELD." &quot;$_GET[field]&quot;".TEXT_FROM_TABLE."&quot;$_SESSION[TBN]&quot; ".TEXT_DROPPED:TEXT_UNABLE_DROP_FIELD."&quot;$_GET[field]&quot;".TEXT_FROM_TABLE."&quot;$_SESSION[TBN]&quot;")."</b></p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//16:ALTER TABLE
case "16":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo((($_GET[act]==1)?TEXT_EDIT_FIELD."<b>$_GET[field]</b>":TEXT_ADD_FIELD) . TEXT_INTO_TABLE."<b>$_SESSION[DBN].$_SESSION[TBN]</b>");
 echo("<form method='post' action='$WIZ?hop=11&op=17&act=$_GET[act]&field=$_GET[field]'>");
 if($_GET[act]==1) $col_info=mysql_fetch_object(mysql_query("SHOW COLUMNS FROM $_SESSION[TBN] LIKE '$_GET[field]'"));
 CreateTableStructure(1,$col_info);
 if($_GET[act]==2) echo("<p>".TEXT_ADD_FIELD." <select name='where'><option value=".TEXT_AFTER.">".TEXT_AFTER_COLUMN."$_GET[field]</option><option value=".TEXT_FIRST.">".TEXT_AT_BEGINNING_TABLE."</option><option value=".TEXT_LAST.">".TEXT_AT_END_TABLE."</option></select></p>\n");
 echo("<p><input type='submit' value='".(($_GET[act]==1)?TEXT_EDIT_FIELD:TEXT_ADD_FIELD)."'></p></form>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//17:ALTER TABLE EXECUTE
case "17":
 echo("<p>".TEXT_ALTER_TABLE."<b>$_SESSION[TBN]</b>:</p>");
 $query="ALTER TABLE $_SESSION[TBN] ";
 $query.=(($_GET[act]=="1")?"CHANGE $_GET[field] ".$_POST[field][0]:"ADD ".$_POST[field][0])." ";
 $query.=$_POST[type][0]." ";
 if($_POST[len][0]) $query.="(".stripslashes($_POST[len][0]).") ";
 $query.=$_POST[attr][0]." ";
 $query.=$_POST[null][0]." ";
 if($_POST[def][0]) $query.=" DEFAULT '".$_POST[def][0]."' ";
 $query.=$_POST[extra][0]." ";
 if($_POST[index][0]) {
  if($_POST[index][0]=="INDEX") $queryindex="ALTER TABLE $_SESSION[TBN] ADD INDEX (".$_POST[field][0]."); ";
  else $query.=$_POST[index][0];
 }
 if($_POST[where]) {
  switch($_POST[where]) {
   case "FIRST":
    $query.=" FIRST";
    break;
   case "AFTER":
    $query.=" AFTER $_GET[field]";
    break;
  }
 }
 echo("<p>");
 if(@mysql_query($query,$dbl)) {
  @mysql_query($queryindex,$dbl);
  echo(TEXT_TABLE."<b>$_SESSION[TBN]</b>".TEXT_ALTERED);
  $_SESSION[msg]=TEXT_TABLE."<b>$_SESSION[TBN]</b>".TEXT_ALTERED;
 } else {
  echo(TEXT_UNABLE_ALTERED_TABLE."<b>$_SESSION[TBN]</b>");
 }
 echo("</p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//18:DROP INDEX FROM TABLE
case "18":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo("<b>".((mysql_query("ALTER TABLE $_SESSION[TBN] DROP INDEX $_GET[index]"))?TEXT_INDEX."&quot;$_GET[index]&quot;".TEXT_FROM_TABLE."&quot;$_SESSION[TBN]&quot;".TEXT_DROPPED:TEXT_UNABLE_DROP_INDEX."&quot;$_GET[index]&quot;".TEXT_FROM_TABLE."&quot;$_SESSION[TBN]&quot;")."</b></p>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//19:CREATE INDEX ON MULTIPLE COLUMNS
case "19":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 $campi=mysql_list_fields("$_SESSION[DBN]","$_SESSION[TBN]",$dbl);
 $cols=mysql_num_fields($campi);
 for($i=0;$i<$cols;$i++) $field_list.="<option value='".mysql_field_name($campi,$i)."'>".mysql_field_name($campi,$i)."</option>\n";
 echo("<p><b>".TEXT_CREATE_INDEX."$_GET[cols]".TEXT_COLUMNS."</b></p>");
 echo("<form method='post' action='$WIZ?hop=11&op=20'><table>
  <tr><td align='right'>".TEXT_INDEX_NAME."</td><td><input type='text' name='idx_name' size='20'></td></tr>
  <tr><td align='right'>".TEXT_INDEX_TYPE."</td><td><select name='idx_type'><option value='PRIMARY KEY'>PRIMARY</option><option value='INDEX'>INDEX</option><option value='UNIQUE'>UNIQUE</option><option value='FULLTEXT'>FULLTEXT</option></select> (*".TEXT_INFO.")</td></tr>");
 for($i=0;$i<$_GET[cols];$i++) echo("<tr><td align='right'>".TEXT_COLUMNS_NUMBERS."$i: </td><td><select name='idx_col[]'><option value=''>-- none</option>$field_list</select></td></tr>");
 echo("<tr><td>&nbsp;</td><td><input type='submit' value=".TEXT_CREATE_INDEX_2."></td></tr></table></form>");
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//20:CREATE INDEX ON MULTIPLE COLUMNS EXECUTE
case "20":
 echo("<p>".TEXT_CREATE_INDEX_TABLE."<b>$_SESSION[TBN]</b>:</p>");
 $query="ALTER TABLE $_SESSION[TBN] ADD $_POST[idx_type] $_POST[idx_name] (";
 foreach($_POST[idx_col] as $col) {
  if($col) $query.="$col, ";
 }
 $query=substr($query,0,-2).")";
 if(@mysql_query($query,$dbl)) {
  echo("<p>".TEXT_INDEX."<b>$_POST[idx_name]</b>".TEXT_FROM_TABLE."<b>$_SESSION[TBN]</b>".TEXT_CREATED."</p>");
  $_SESSION[msg]=TEXT_TABLE."<b>$_SESSION[TBN]</b>".TEXT_ALTERED;
 } else {
  echo("<p>".TEXT_UNABLE_CREATE_INDEX."<b>$_POST[idx_name]</b>".TEXT_FOR_TABLE."<b>$_SESSION[TBN]</b></p>");
 }
 echo("<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//21:RENAME TABLE
case "21":
 if(!$_SESSION[TBN]) die("<script language='javascript'>window.location='$WIZ?op=1'</script>");
 echo("<p><b>");
 if(mysql_query("ALTER TABLE $_SESSION[TBN] RENAME $_POST[new_table_name]")) {
  echo(TEXT_TABLE."&quot;$_SESSION[TBN]&quot;".TEXT_RENAME_AS."&quot;$_POST[new_table_name]&quot;");
  $_SESSION[TBN]=$_POST[new_table_name];
 } else {
  echo(TEXT_UNABLE_RENAME_TABLE."&quot;$_SESSION[TBN]&quot;");
 }
 echo("</b></p>\n<p><a href='$WIZ?op=2'><b>&gt;&gt; ".TEXT_TABLE_PROPERTIES."</b></a></p>");
 break;

//999:CREDITS
case "999":
 echo("
  <p><b>Maintainer</b>:<br>
   &nbsp; <font color='#3366AA'><b>Marco Avidano</b></font>
  </p>
  <p><b>Thanks to</b>:<br>
   &nbsp; <font color='#3366AA'><b>Chris St. Pierre</b></font> (cooperation for creation table wizard)<br>
   &nbsp; <font color='#3366AA'><b>Kees Serier</b></font> (some bug reports [ver. 0.10.5, 0.10.6, 0.10.8])<br>
   &nbsp; <font color='#3366AA'><b>Bob</b></font> (bug reports and new feature requests [ver. 0.11.1])<br>
   &nbsp; <font color='#3366AA'><b>Bruce Painter</b></font> (bug report [ver. 0.11.1])<br>
   &nbsp; <font color='#3366AA'><b>Tomek Klimczak</b></font> (bug report [ver. 0.11.1])<br>
   &nbsp; <font color='#3366AA'><b>... and a lot of other anonymous</b></font><br>
  </p>
  <p><b>Some information about this program</b>:<br>
   &nbsp; Project name: <font color='#3366AA'><b>$VER[NAME]</b></font><br>
   &nbsp; Major version: <font color='#3366AA'><b>$VER[MAJOR]</b></font><br>
   &nbsp; Minor version: <font color='#3366AA'><b>$VER[MINOR]</b></font><br>
   &nbsp; Build: <font color='#3366AA'><b>$VER[BUILD]</b></font><br>
   &nbsp; Shortly: <font color='#3366AA'><b>$VER[MAJOR].$VER[MINOR]</b></font><br>
   &nbsp; Web site: <font color='#3366AA'><b>$VER[WEB]</b></font>
  </p>
  <p><b>Support my work</b>:<br>
   WizMySQLAdmin is totally free. I to not make any profit by its development and maintenance.<br>
   If you think it's great and useful, you can make me a little donation through Paypal.<br>
   Not a lot: only 2 or 3 Euros (or dollars, it's the same for me), and I'll drink a pineapple juice to yours health!<br>
   If you want go to:<br>
    <a href='https://www.paypal.com/xclick/business=paypal%40wiz.homelinux.net&item_name=Wiz%27s+Shelf+-+WizMySQLAdmin&no_note=1&tax=0&currency_code=EUR' target='_blank'>https://www.paypal.com/xclick/business=paypal%40wiz.homelinux.net&item_name=Wiz%27s+Shelf+-+WizMySQLAdmin&no_note=1&tax=0&currency_code=EUR</a><br>
   and fill the Paypal form to make me a donation.<br>
   Thank you in advance!
  <p><b>License</b>:<br>
   Copyright (C) 2004  Marco Avidano<br>
   This program is free software; you can redistribute it and/or modify<br>
   it under the terms of the GNU General Public License as published by<br>
   the Free Software Foundation; either version 2 of the License, or<br>
   (at your option) any later version.<br>
   This program is distributed in the hope that it will be useful,<br>
   but WITHOUT ANY WARRANTY; without even the implied warranty of<br>
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the<br>
   GNU General Public License for more details.<br>
   You should have received a copy of the GNU General Public License<br>
   along with this program; if not, write to the Free Software<br>
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA<br>
   See <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>http://www.gnu.org/licenses/gpl.html</a> for the complete text of the license.
  </p>
  <p><b>Something else</b>:<br>
   &nbsp; &quot;I don't know half of you half as well as I should like;<br>
   &nbsp; and I like less than half of you half as well as you deserve.&quot;<br>
   &nbsp; - Bilbo Baggins
  </p>
  <p>&nbsp;</p>
 ");
 $_SESSION[msg]="Take it easy!";
 break;

//DEFAULT: SHOW DATABASES
default:
 echo("<table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>
  <tr style='background-color:#f4f7fd;'>
   <th width='100'>".HEADER_TITLE_DB_LIST."</th>
  </tr>");
// Rigadin: limit to store database
// $rs=mysql_list_dbs($dbl);
// while($rc=mysql_fetch_object($rs)) {
  $bgcolor=($bgcolor=="#E2CFCF")?"#FFFFFF":"#E2CFCF";
  echo("<tr style='background-color:$bgcolor' onMouseOver='javascript:this.style.backgroundColor=\"#f4f7fd\"' onMouseOut='javascript:this.style.backgroundColor=\"$bgcolor\"'><td><a href='$WIZ?op=1&hop=1&dbn=".DB_DATABASE."'><b>".DB_DATABASE."</b></a></td></tr>\n");
// }
 echo("</table>");
 break;
}
?>  </td>
 </tr>
</table>

<table cellspacing="0" cellpadding="0" width="100%" border="0">
 <tr valign="middle" height="20" class="dataTableHeadingContent">
  <td width="100%" class="infoBoxContent">&nbsp; <b><?php echo FOOTER_TEXT_LOG;?></b><?php echo($_SESSION[msg]); ?></td>
 </tr>
</table>



<?php
mysql_close($dbl);

function CreateTableStructure($FieldNum,$FieldStructure) {
 echo("<table cellspacing='0' cellpadding='2' border='0' style='border:1pt solid #666666;border-collapse:collapse;' width='100%'>
   <tr style='background-color:#f4f7fd;'><th>FIELD</th><th>TYPE</th><th>LENGTH/VALUES*</th><th>ATTRIBUTES</th><th>NULL</th><th>DEFAULT</th><th>EXTRA</th><th>INDEX</th></tr>\n");
 for($i=0;$i<$FieldNum;$i++) {
  $bgcolor=($bgcolor=="#E2CFCF")?"#FFFFFF":"#E2CFCF";
  $field_type=strtoupper(substr($FieldStructure->Type,0,strpos($FieldStructure->Type,"(")));
  $field_values=substr($FieldStructure->Type,strpos($FieldStructure->Type,"(")+1,strpos($FieldStructure->Type,")")-strpos($FieldStructure->Type,"(")-1);
  $field_attr=split(" ",$FieldStructure->Type,2);
  echo("<tr align='center' style='background-color:$bgcolor' onMouseOver='javascript:this.style.backgroundColor=\"#f4f7fd\"' onMouseOut='javascript:this.style.backgroundColor=\"$bgcolor\"'>
   <td><input type='text' name='field[$i]' style='width:70pt;' value='$FieldStructure->Field'></td>
   <td>
    <select name='type[$i]'>");
     $types=array("","TINYINT","SMALLINT","MEDIUMINT","INT","BIGINT","DOUBLE","DECIMAL","FLOAT","DATE","TIME","TIMESTAMP","DATETIME","YEAR","VARCHAR","TINYTEXT","TEXT","MEDIUMTEXT","LONGTEXT","TINYBLOB","BLOB","MEDIUMBLOB","LONGBLOB","ENUM","SET");
     foreach($types as $type) echo("<option value='$type' ".(($type==$field_type)?"selected":"").">$type</option>\n");
     echo("</select>
   </td>
   <td><input type='text' name='len[$i]' style='width:100pt;' value=\"$field_values\"></td>
   <td>
    <select name='attr[$i]'>");
     $attrs=array("","BINARY","UNSIGNED","UNSIGNED ZEROFILL");
     foreach($attrs as $attr) echo("<option value='$attr' ".(($attr==strtoupper($field_attr[1]))?"selected":"").">$attr</option>\n");
     echo("</select>
   </td>
   <td>
    <select name='null[$i]'>");
     $nulls=array("NOT NULL","NULL");
     foreach($nulls as $null) echo("<option value='$null' ".(($FieldStructure->Null=="YES" && $null=="NULL")?"selected":"").">$null</option>\n");
     echo("</select>
   </td>
   <td><input type='text' name='def[$i]' style='width:50pt;'  value=\"$FieldStructure->Default\"></td>
   <td>
    <select name='extra[$i]'>");
     $extras=array("","AUTO_INCREMENT");
     foreach($extras as $extra) echo("<option value='$extra' ".(($FieldStructure->Extra=="auto_increment" && $extra=="AUTO_INCREMENT")?"selected":"").">$extra</option>\n");
     echo("</select>
   </td>
   <td>
    <select name='index[$i]'>");
     $indexs=array("","PRIMARY KEY","INDEX","UNIQUE KEY");
     foreach($indexs as $index) echo("<option value='$index' ".(($FieldStructure->Key=="PRI" && $index=="PRIMARY KEY")?"selected":"") . (($FieldStructure->Key=="MUL" && $index=="INDEX")?"selected":"") . (($FieldStructure->Key=="UNI" && $index=="UNIQUE KEY")?"selected":"").">$index</option>\n");
     echo("</select>
   </td>
  </tr>");
 }
 echo("</table>
  <p>*
    For &quot;enum&quot; and &quot;set&quot; fields enter the values using this format: 'first','second','third'.<br>
    Escape backslash or single quote with a backslash (example: 'first','se\'cond','thi\\\\rd').
  </p>");
 return;
}
?>
</td>
      </tr>
      
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
