<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Services"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/ics/includes/ui/ui_lists.inc");
include_once($path_to_root . "/ics/includes/db/svc.inc");
include_once($path_to_root . "/ics/includes/db/billing_cycles.inc");

simple_page_mode(true);
//-------------------------------------------------------------------------------------------------

function can_process() 
{

	if (strlen(trim($_POST['SERVICE_NAME'])) < 1)
	{
		display_error( _("The SERVICE NAME entered must be empty."));
		set_focus('SERVICE_NAME');
		return false;
	}

	if (strlen(trim($_POST['SERVICE_DESC'])) < 1)
	{
		display_error( _("The SERVICE DESCRIPTION entered must be empty."));
		set_focus('SERVICE_DESC');
		return false;
	}


	return true;
}

//-------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
	if (can_process())
	{
    	if ($selected_id != -1) 
    	{
			$e = get_service($selected_id);
			$sql = "SELECT *
			FROM ".TB_PREF."ics_services
			WHERE SERVICE_NAME=".db_escape($_POST['SERVICE_NAME']);
		
			$result = db_query($sql,"a service for check could not be retrieved");
			if (db_num_rows($result) > 0) {
				$check = db_fetch_row($result);	
				if ($check[0] != $e['SERVICE_ID']) {
					display_error( _("A service with this NAME already exist."));
					set_focus('SERVICE_NAME');
					return false;
				}
			}
			
    		update_service($selected_id, $_POST['SERVICE_NAME'], $_POST['SERVICE_DESC'], $_POST['TXT_ADVERT_MSG'],
    			0,0);

    		display_notification_centered(_("The selected service has been updated."));
    	} 
    	else 
    	{	
			$sql = "SELECT *
			FROM ".TB_PREF."ics_services
			WHERE SERVICE_NAME=".db_escape($_POST['SERVICE_NAME']);
		
			$result = db_query($sql,"a service could not be retrieved");
			if (db_num_rows($result) > 0) {
				display_error( _("A service with this NAME already exist."));
				set_focus('SERVICE_NAME');
				return false;
			}
			$sql = "SELECT MAX(SERVICE_ID) FROM ".TB_PREF."ics_services";
			$result = db_query($sql,"a service for insert could not be retrieved");
			$max = db_fetch_row($result);			
			$newid = $max[0] + 1;
    		add_service($newid, $_POST['SERVICE_NAME'], $_POST['SERVICE_DESC'], $_POST['TXT_ADVERT_MSG'],
    			null, null, $_SESSION["wa_current_user"]->username);
			display_notification_centered(_("A service has been added."));
			
    	}
		$Mode = 'RESET';
	}
}

//-------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_service($selected_id);
	display_notification_centered(_("Service has been deleted."));
	$Mode = 'RESET';
}

//-------------------------------------------------------------------------------------------------
if ($Mode == 'RESET')
{
 	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);	// clean all input fields
	$_POST['show_inactive'] = $sav;
}
		
			
//$result = get_services();
$result = get_services(check_value('show_inactive'));
start_form();
start_table(TABLESTYLE_NOBORDER);

$th = array(_("SERVICE ID"), _("SERVICE NAME"), _("DESCRIPTION"), _("ADVERT MSG"),_("CATEGORY"), _("BILLING CYCLE"), "", "");

inactive_control_column($th);
table_header($th);	

$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);
	//$bc = get_billing_cycle($myrow["CYCLE_ID"]);
	
	//$sql = "SELECT category_name FROM ".TB_PREF."ebs_service_category WHERE category_id=".$myrow["CATEGORY_ID"];
	//$res = db_query($sql,"a service category could not be retrieved");
	//$sc = db_fetch_row($res);
	label_cell($myrow["SERVICE_ID"]);
	label_cell($myrow["SERVICE_NAME"]);
	label_cell($myrow["SERVICE_DESC"]);
	label_cell(substr($myrow["TXT_ADVERT_MSG"], 0, 30).'...');
	//label_cell($sc[0]);
	//label_cell($bc["CYCLE_NAME"]);
	
	inactive_control_cell($myrow["SERVICE_ID"], $myrow["FLG_STATUS"], 'ics_services', 'SERVICE_ID');
		
	edit_button_cell("Edit".$myrow["SERVICE_ID"], _("Edit"));
    delete_button_cell("Delete".$myrow["SERVICE_ID"], _("Delete"));
	end_row();

} //END WHILE LIST LOOP
inactive_control_row($th);

end_table(1);
//-------------------------------------------------------------------------------------------------
start_table(TABLESTYLE_NOBORDER);

if ($selected_id != -1) 
{
  	if ($Mode == 'Edit') {
		//editing an existing User
		$myrow = get_service($selected_id);

		$_POST['SERVICE_NAME'] = $myrow["SERVICE_NAME"];
		$_POST['SERVICE_DESC'] = $myrow["SERVICE_DESC"];
		$_POST['TXT_ADVERT_MSG'] = $myrow["TXT_ADVERT_MSG"];
		$_POST['CATEGORY_ID'] = $myrow["CATEGORY_ID"];
		$_POST['CYCLE_ID'] = $myrow["CYCLE_ID"];
	}
	hidden('selected_id', $selected_id);
} 

text_row_ex(_("SERVICE NAME").":", 'SERVICE_NAME',  20);
text_row_ex(_("DESCRIPTION").":", 'SERVICE_DESC', 50);
//textarea_row(_("ADVERT MSG").":", 'TXT_ADVERT_MSG', $_POST['TXT_ADVERT_MSG'], 35, 3);
textarea_row(_('ADVERT MSG:'), 'TXT_ADVERT_MSG', null, 35, 3);
//service_category_list_row(_("SERVICE CATEGORY:"), 'CATEGORY_ID', null);
//billing_cycle_list_row(_("BILLING CYCLE:"), 'CYCLE_ID', null);


end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'false');

end_form();
end_page();
?>