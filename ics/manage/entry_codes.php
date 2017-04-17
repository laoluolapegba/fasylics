<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Entry Codes"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/ics/includes/db/entry_codes.inc");
include_once($path_to_root . "/ics/includes/ui/ui_lists.inc");
//include_once($path_to_root . "/ics/includes/db/billing_cycles.inc");
include_once($path_to_root . "/ics/includes/db/svc.inc");

simple_page_mode(true);
//-------------------------------------------------------------------------------------------------

function can_process() 
{

	if (strlen($_POST['USSD_CODE']) < 4)
	{
		display_error( _("The USSD entered must be at least 4 characters long."));
		set_focus('USSD_CODE');
		return false;
	}

	if (strlen($_POST['SMS_CODE']) < 4)
	{
		display_error( _("The SMS CODE entered must be at least 4 characters long."));
		set_focus('SMS_CODE');
		return false;
	}

	return true;
}

//-------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
	if (can_process())
	{
		$s = get_service($_POST["service"]);
    	if ($selected_id != -1) 
    	{
			$e = get_entry_code($selected_id);
			$sql = "SELECT *
			FROM ".TB_PREF."ics_entry_codes
			WHERE USSD_CODE=".db_escape($_POST['USSD_CODE'])." AND SERVICE_ID=".db_escape($_POST['service']);
		
			$result = db_query($sql,"an entry code for check could not be retreived");
			if (db_num_rows($result) > 0) {
				$check = db_fetch_row($result);	
				if ($check[0] != $e['CODE_ID']) {
					display_error( _("An entry code with this USSD already exist."));
					set_focus('USSD_CODE');
					return false;
				}
			}
			
    		update_entry_code($selected_id, $s['CATEGORY_ID'], $s['CYCLE_ID'], $_POST['USSD_CODE'],
    			$_POST['SMS_CODE'], $_POST['service'], $_POST['operation'], $_POST['billing_code']);

    		display_notification_centered(_("The selected entry code has been updated."));
    	} 
    	else 
    	{	
			$sql = "SELECT *
			FROM ".TB_PREF."ics_entry_codes
			WHERE USSD_CODE=".db_escape($_POST['USSD_CODE'])." AND SERVICE_ID=".db_escape($_POST['service']);
		
			$result = db_query($sql,"an entry code for check could not be retreived");
			if (db_num_rows($result) > 0) {
				display_error( _("An entry code with this USSD already exist."));
				set_focus('USSD_CODE');
				return false;
			}
			$sql = "SELECT MAX(CODE_ID) FROM ".TB_PREF."ics_entry_codes";
			$result = db_query($sql,"an entry code for insert could not be retreived");
			$max = db_fetch_row($result);			
			$newid = $max[0] + 1;
    		add_entry_code($newid, $s['CATEGORY_ID'], $s['CYCLE_ID'], $_POST['USSD_CODE'],
    			$_POST['SMS_CODE'], $_POST['service'], $_POST['operation'], $_POST['billing_code'],
				$_SESSION["wa_current_user"]->username);
			display_notification_centered(_("A entry code has been added."));
    	}
		$Mode = 'RESET';
	}
}

//-------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_entry_code($selected_id);
	display_notification_centered(_("Entry code has been deleted."));
	$Mode = 'RESET';
}

//-------------------------------------------------------------------------------------------------
if ($Mode == 'RESET')
{
 	$selected_id = -1;
	unset($_POST);	// clean all input fields
}
		
			
$result = get_entry_codes();
start_form();
start_table(TABLESTYLE_NOBORDER);

$th = array(_("USSD CODE"), _("SMS CODE"), _("SERVICE"),_("OPERATION"), _("BILLING CODE"), "", "");

inactive_control_column($th);
table_header($th);	

$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);
	$service = get_service($myrow["SERVICE_ID"]);

	label_cell($myrow["USSD_CODE"]);
	label_cell($myrow["SMS_CODE"]);
	label_cell($service["SERVICE_NAME"]);
	//label_cell($service["cycle_name"]);
	label_cell($myrow["OPERATION"]);
	label_cell($myrow["billing_code"]);

	edit_button_cell("Edit".$myrow["CODE_ID"], _("Edit"));
    delete_button_cell("Delete".$myrow["CODE_ID"], _("Delete"));
	end_row();

} //END WHILE LIST LOOP

end_table(1);
//-------------------------------------------------------------------------------------------------
start_table(TABLESTYLE_NOBORDER);

if ($selected_id != -1) 
{
  	if ($Mode == 'Edit') {
		//editing an existing User
		$myrow = get_entry_code($selected_id);

		$_POST['code_id'] = $myrow["CODE_ID"];
		$_POST['USSD_CODE'] = $myrow["USSD_CODE"];
		$_POST['SMS_CODE'] = $myrow["SMS_CODE"];
		$_POST['service'] = $myrow["SERVICE_ID"];
		$_POST['operation'] = $myrow["OPERATION"];
		//$_POST['billing_code'] = $myrow["billing_code"];
	}
	hidden('selected_id', $selected_id);
	hidden('code_id');

} else { 
	$_POST['operation'] = "";
	
}

text_row_ex(_("USSD CODE").":", 'USSD_CODE',  20);
text_row_ex(_("SMS CODE").":", 'SMS_CODE',  20);
service_list_row(_("SERVICE:"), 'service', null);
operation_list_row(_("OPERATION:"), 'operation', null);
text_row_ex(_("BILLING CODE").":", 'billing_code',  20);

end_table(1);

	//submit_center('addupdate', _("Add New Service"), true, '');
submit_add_or_update_center($selected_id == -1, '', 'false');

end_form();
end_page();
?>
