<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Billing Cycles"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/mcms/includes/ui/ui_lists.inc");
include_once($path_to_root . "/mcms/includes/db/billing_cycles.inc");

simple_page_mode(true);
//-------------------------------------------------------------------------------------------------

function can_process() 
{

	if (strlen(trim($_POST['CYCLE_NAME'])) < 1)
	{
		display_error( _("The CYCLE NAME entered must be empty."));
		set_focus('CYCLE_NAME');
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
			$e = get_billing_cycle($selected_id);
			$sql = "SELECT *
			FROM ".TB_PREF."ebs_billing_cycle
			WHERE CYCLE_NAME=".db_escape($_POST['CYCLE_NAME']);
		
			$result = db_query($sql,"an billing cycle for check could not be retreived");
			if (db_num_rows($result) > 0) {
				$check = db_fetch_row($result);	
				if ($check[0] != $e['CYCLE_ID']) {
					display_error( _("An billing cycle with this CYCLE NAME already exist."));
					set_focus('USSD_CODE');
					return false;
				}
			}
			
    		update_billing_cycle($selected_id, $_POST['CYCLE_NAME'], $_POST['VALIDITY'], $_POST['MAX_SUSPEND_DAYS'],
    			$_POST['MIN_USE_DAYS'], $_POST['MAX_OFF_DAYS'], $_SESSION["wa_current_user"]->username);

    		display_notification_centered(_("The selected billing cycle has been updated."));
    	} 
    	else 
    	{	
			$sql = "SELECT *
			FROM ".TB_PREF."ebs_billing_cycle
			WHERE CYCLE_NAME=".db_escape($_POST['CYCLE_NAME']);
		
			$result = db_query($sql,"a billing cycle for check could not be retreived");
			if (db_num_rows($result) > 0) {
				display_error( _("A billing cycle with this CYCLE NAME already exist."));
				set_focus('USSD_CODE');
				return false;
			}
			$sql = "SELECT MAX(CYCLE_ID) FROM ".TB_PREF."ebs_billing_cycle";
			$result = db_query($sql,"a billing cycle for insert could not be retreived");
			$max = db_fetch_row($result);			
			$newid = $max[0] + 1;
    		add_billing_cycle($newid, $_POST['CYCLE_NAME'], $_POST['VALIDITY'], $_POST['MAX_SUSPEND_DAYS'],
    			$_POST['MIN_USE_DAYS'], $_POST['MAX_OFF_DAYS'], $_SESSION["wa_current_user"]->username);
			display_notification_centered(_("A billing cycle has been added."));
			
    	}
		$Mode = 'RESET';
	}
}

//-------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_entry_code($selected_id);
	display_notification_centered(_("Billing cycle has been deleted."));
	$Mode = 'RESET';
}

//-------------------------------------------------------------------------------------------------
if ($Mode == 'RESET')
{
 	$selected_id = -1;
	unset($_POST);	// clean all input fields
}
		
			
$result = get_billing_cycles();
start_form();
start_table($table_style);

$th = array(_("CYCLE NAME"), _("VALIDITY"), _("MAX SUSPEND DAYS"), _("MIN USE DAYS"),_("MAX OFF DAYS"), "", "");

inactive_control_column($th);
table_header($th);	

$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);

	label_cell($myrow["CYCLE_NAME"]);
	label_cell($myrow["VALIDITY"]);
	label_cell($myrow["MAX_SUSPEND_DAYS"]);
	label_cell($myrow["MIN_USE_DAYS"]);
	label_cell($myrow["MAX_OFF_DAYS"]);

	edit_button_cell("Edit".$myrow["CYCLE_ID"], _("Edit"));
    delete_button_cell("Delete".$myrow["CYCLE_ID"], _("Delete"));
	end_row();

} //END WHILE LIST LOOP

end_table(1);
//-------------------------------------------------------------------------------------------------
start_table($table_style2);

if ($selected_id != -1) 
{
  	if ($Mode == 'Edit') {
		//editing an existing User
		$myrow = get_billing_cycle($selected_id);

		$_POST['CYCLE_ID'] = $myrow["CYCLE_ID"];
		$_POST['CYCLE_NAME'] = $myrow["CYCLE_NAME"];
		$_POST['VALIDITY'] = $myrow["VALIDITY"];
		$_POST['MAX_SUSPEND_DAYS'] = $myrow["MAX_SUSPEND_DAYS"];
		$_POST['MIN_USE_DAYS'] = $myrow["MIN_USE_DAYS"];
		$_POST['MAX_OFF_DAYS'] = $myrow["MAX_OFF_DAYS"];
	}
	hidden('selected_id', $selected_id);
	hidden('code_id');

} 

text_row_ex(_("CYCLE NAME").":", 'CYCLE_NAME',  20);
text_row_ex(_("VALIDITY").":", 'VALIDITY',  5, '', '', $_POST['VALIDITY'], '', 'day(s)');
text_row_ex(_("MAX SUSPEND DAYS").":", 'MAX_SUSPEND_DAYS',  5, '', '', $_POST['MAX_SUSPEND_DAYS'], '', 'day(s)');
text_row_ex(_("MIN USE DAYS").":", 'MIN_USE_DAYS',  5, '', '', $_POST['MIN_USE_DAYS'], '', 'day(s)');
text_row_ex(_("MAX OFF DAYS").":", 'MAX_OFF_DAYS',  5, '', '', $_POST['MAX_OFF_DAYS'], '', 'day(s)');


end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'false');

end_form();
end_page();
?>
