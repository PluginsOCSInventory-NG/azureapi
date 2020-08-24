<?php
//====================================================================================
// OCS INVENTORY REPORTS
// Copyleft GILLES DUBOIS 2020 (erwan(at)ocsinventory-ng(pt)org)
// Web: http://www.ocsinventory-ng.org
//
// This code is open source and may be copied and modified as long as the source
// code is always made freely available.
// Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
//====================================================================================
 
if(AJAX){
  parse_str($protectedPost['ocs']['0'], $params);
  $protectedPost+=$params;
  ob_start();
  $ajax = true;
}
else{
  $ajax=false;
}

require "require/function_machine.php";

print_item_header($l->g(68800));

//form name
$form_name = 'azure_fulllist';
//form open
echo open_form($form_name, '', '', 'form-horizontal');

$table_name=$form_name;
$tab_options=$protectedPost;
$tab_options['form_name']=$form_name;
$tab_options['table_name']=$table_name;

$sql['SQL'] = 'SELECT * FROM azurevms AS azv INNER JOIN azuresub AS azs ON azs.SUBID = azv.VMSUBID INNER JOIN azureresgroups AS azrg ON azrg.RESNAME = azv.VMRESGRP GROUP BY azv.VMID';

$list_fields = array(
  $l->g(1268) => "SUBID",
  $l->g(68721) => "SUBNAME",
  $l->g(81) => "SUBSTATE",
  $l->g(1268) => "RESSUBID",
  $l->g(68720) => "RESNAME",
  $l->g(68702) => 'VMID',
  $l->g(49) => 'VMNAME',
  $l->g(66) => 'VMTYPE',
  $l->g(68715) => 'VMLOCATION',
  $l->g(68705) => 'VMIMAGE',
  $l->g(68706) => 'VMSKU',
  $l->g(277) => 'VMVERSION',
  $l->g(68707) => 'VMEXACTVERSION',
  $l->g(68708) => 'VMPROVSTATE'
);

$default_fields = $list_fields;
$list_col_cant_del = $default_fields;

$result_exist = ajaxtab_entete_fixe($list_fields, $default_fields, $tab_options, $list_col_cant_del);

if ($ajax){
  ob_end_clean();
  tab_req($list_fields,$default_fields,$list_col_cant_del,$sql['SQL'],$tab_options);
  ob_start();
}
