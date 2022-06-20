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
    
	print_item_header($l->g(68700));

	$form_name = "button_slide";
	echo open_form($form_name);
	if(isset($protectedGet['subid'])){
		echo '<a class="btn btn-info" href="index.php?function=ms_azure">'.$l->g(68718).'</a>&nbsp;&nbsp;';
	}
	if(isset($protectedGet['subid']) && isset($protectedGet['resgrp'])){
		echo '<a class="btn btn-info" href="index.php?function=ms_azure&subid='.$protectedGet['subid'].'">'.$l->g(68719).'</a>';
	}
	echo close_form();
	
	if (!isset($protectedPost['SHOW']))
		$protectedPost['SHOW'] = 'NOSHOW';
    
    if(!isset($protectedGet['subid']) && !isset($protectedGet['resgrp']) && !isset($protectedGet['vmid'])){
        $form_name="azuresub";
        $table_name=$form_name;
        $tab_options=$protectedPost;
        $tab_options['form_name']=$form_name;
        $tab_options['table_name']=$table_name;
        echo open_form($form_name);

        $list_fields=array(
				$l->g(1268) => "SUBID",
				$l->g(49) => "SUBNAME",
                $l->g(81) => "SUBSTATE"
        );
        
        $tab_options['LIEN_LBL'][$l->g(49)] = 'index.php?' . PAG_INDEX . '=ms_azure&subid=';
        $tab_options['LIEN_CHAMP'][$l->g(49)] = 'SUBID';

        $list_col_cant_del=$list_fields;
        $default_fields= $list_fields;
        $sql=prepare_sql_tab($list_fields);
        $sql['SQL']  .= "FROM azuresub";
        array_push($sql['ARG'],$systemid ?? '');
        $tab_options['ARG_SQL']=$sql['ARG'];
        $tab_options['ARG_SQL_COUNT']=$systemid ?? '';
        ajaxtab_entete_fixe($list_fields,$default_fields,$tab_options,$list_col_cant_del);
        echo close_form();
    }else if(isset($protectedGet['subid']) && !isset($protectedGet['resgrp']) && !isset($protectedGet['vmid'])){
		$form_name="azureresgrp";
        $table_name=$form_name;
        $tab_options=$protectedPost;
        $tab_options['form_name']=$form_name;
        $tab_options['table_name']=$table_name;
        echo open_form($form_name);

        $list_fields=array(
				$l->g(1268) => "RESSUBID",
				$l->g(49) => "RESNAME",
				$l->g(81) => "RESTYPE",
				$l->g(68715) => "RESLOCATION"
        );
        
        $tab_options['LIEN_LBL'][$l->g(49)] = 'index.php?' . PAG_INDEX . '=ms_azure&subid='.$protectedGet['subid'].'&resgrp=';
        $tab_options['LIEN_CHAMP'][$l->g(49)] = 'RESNAME';

        $list_col_cant_del=$list_fields;
        $default_fields= $list_fields;
        $sql=prepare_sql_tab($list_fields);
        $sql['SQL']  .= "FROM azureresgroups WHERE (RESSUBID = '%s')";
        array_push($sql['ARG'], $protectedGet['subid']);
		$tab_options['ARG_SQL'] = $sql['ARG'];
        ajaxtab_entete_fixe($list_fields,$default_fields,$tab_options,$list_col_cant_del);
        echo close_form();
	}else if(isset($protectedGet['subid']) && isset($protectedGet['resgrp']) && !isset($protectedGet['vmid'])){

		//form name
        $form_name = 'azure_details';
        //form open
		echo open_form($form_name, '', '', 'form-horizontal');

		$table_name=$form_name;
        $tab_options=$protectedPost;
        $tab_options['form_name']=$form_name;
        $tab_options['table_name']=$table_name;
		
		//definition of onglet
        $def_onglets['VM'] = $l->g(68716);
        $def_onglets['RESOURCES'] = $l->g(68717); 

        //default => first onglet
        if (empty($protectedPost['onglet'])) {
            $protectedPost['onglet'] = "VM";
        }

        //show first ligne of onglet
		show_tabs($def_onglets,$form_name,"onglet",true);

		// Col
		echo '<div class="col col-md-10">';

		/******************************* VM *******************************/
        if(isset($protectedPost['onglet']) && $protectedPost['onglet'] == "VM"){

            $sql['SQL'] = 'SELECT * FROM azurevms WHERE VMRESGRP = "%s" AND VMSUBID = "%s"';
            $sql['ARG'] = [$protectedGet['resgrp'], $protectedGet['subid']];

            $list_fields = array(
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
			
			$tab_options['LIEN_LBL'][$l->g(68702)] = 'index.php?' . PAG_INDEX . '=ms_azure&subid='.$protectedGet['subid'].'&resgrp='.$protectedGet['resgrp']."&vmid=";
        	$tab_options['LIEN_CHAMP'][$l->g(68702)] = 'VMID';

        }

        /******************************* RESOURCES *******************************/
        if(isset($protectedPost['onglet']) && $protectedPost['onglet'] == "RESOURCES"){

			$sql['SQL'] = 'SELECT * FROM azureres WHERE RESGRP = "%s" AND RESSUBID = "%s"';
            $sql['ARG'] = [$protectedGet['resgrp'], $protectedGet['subid']];

			$list_fields = array(
                $l->g(49) => 'RESNAME',
				$l->g(66) => 'RESTYPE',
				$l->g(68715) => 'RESLOCATION'
            );
		}
		
		$default_fields = $list_fields;
        $list_col_cant_del = $default_fields;
        $tab_options['ARG_SQL'] = $sql['ARG'];

        $result_exist = ajaxtab_entete_fixe($list_fields, $default_fields, $tab_options, $list_col_cant_del);
		
		echo "</div>";
        echo close_form();
	} else if(isset($protectedGet['subid']) && isset($protectedGet['resgrp']) && isset($protectedGet['vmid'])){
		//form name
		$form_name = 'azure_vmdetails';
		//form open
		echo open_form($form_name, '', '', 'form-horizontal');

		$table_name=$form_name;
        $tab_options=$protectedPost;
        $tab_options['form_name']=$form_name;
        $tab_options['table_name']=$table_name;

		//definition of onglet
        $def_onglets['DISKS'] = $l->g(92); 

        //default => first onglet
        if (empty($protectedPost['onglet'])) {
            $protectedPost['onglet'] = "DISKS";
        }

        //show first ligne of onglet
		show_tabs($def_onglets,$form_name,"onglet",true);

		// Col
		echo '<div class="col col-md-10">';

        /******************************* DISKS *******************************/
        if(isset($protectedPost['onglet']) && $protectedPost['onglet'] == "DISKS"){

			$sql['SQL'] = 'SELECT * FROM azuredisk WHERE VMID = "%s"';
            $sql['ARG'] = [$protectedGet['vmid']];

			$list_fields = array(
                $l->g(49) => 'DISKNAME',
				$l->g(66) => 'DISKTYPE',
				$l->g(67) => 'DISKSIZE',
				$l->g(68710) => 'DISKCREATEOPTION',
				$l->g(68711) => 'DISKCACHING'
			);
			
			$default_fields = $list_fields;
			$list_col_cant_del = $default_fields;
			$tab_options['ARG_SQL'] = $sql['ARG'];

			$result_exist = ajaxtab_entete_fixe($list_fields, $default_fields, $tab_options, $list_col_cant_del);
		}

		echo "</div>";
        echo close_form();
	}
    
    if ($ajax){
		ob_end_clean();
		tab_req($list_fields,$default_fields,$list_col_cant_del,$sql['SQL'],$tab_options);
		ob_start();
	}
?>