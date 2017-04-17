<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Notification Setup"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/ics/includes/ui/ui_lists.inc");
include_once($path_to_root . "/ics/includes/db/notification.inc");

simple_page_mode(true);
//-------------------------------------------------------------------------------------------------

function can_process() 
{

	if (strlen(trim($_POST['EVENTNAME'])) < 1)
	{
		display_error( _("The EVENT NAME entered must be empty."));
		set_focus('EVENTNAME');
		return false;
	}

	if (strlen(trim($_POST['DESCRIPTION'])) < 1)
	{
		display_error( _("The DESCRIPTION entered must be empty."));
		set_focus('DESCRIPTION');
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
			$e = get_notification($selected_id);
			$sql = "SELECT *
			FROM ".TB_PREF."ics_notification_setup
			WHERE EVENTNAME=".db_escape($_POST['EVENTNAME']);
		
			$result = db_query($sql,"a notification for check could not be retreived");
			if (db_num_rows($result) > 0) {
				$check = db_fetch_row($result);	
				if ($check[0] != $e['ID']) {
					display_error( _("A notification with this EVENT NAME already exist."));
					set_focus('EVENTNAME');
					return false;
				}
			}
			
    		update_notification($selected_id, $_POST['EVENTNAME'], $_POST['DESCRIPTION'], $_POST['CHANNEL'],
    			$_POST['INITIAL_MSG'], $_POST['FINAL_MSG']);

    		display_notification_centered(_("The selected notification has been updated."));
    	} 
    	else 
    	{	
			$sql = "SELECT *
			FROM ".TB_PREF."ics_notification_setup
			WHERE EVENTNAME=".db_escape($_POST['EVENTNAME']);
		
			$result = db_query($sql,"a notification for check could not be retrieved");
			if (db_num_rows($result) > 0) {
				display_error( _("A notification with this EVENT NAME already exist."));
				set_focus('EVENTNAME');
				return false;
			}
			$sql = "SELECT MAX(ID) FROM ".TB_PREF."ics_notification_setup";
			$result = db_query($sql,"a notification for insert could not be retrieved");
			$max = db_fetch_row($result);			
			$newid = $max[0] + 1;
    		add_notification($newid, $_POST['EVENTNAME'], $_POST['DESCRIPTION'], $_POST['CHANNEL'],
    			$_POST['INITIAL_MSG'], $_POST['FINAL_MSG'], $_SESSION["wa_current_user"]->username);
			display_notification_centered(_("A notification has been added."));
			
    	}
		$Mode = 'RESET';
	}
}

//-------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_notification($selected_id);
	display_notification_centered(_("Notification cycle has been deleted."));
	$Mode = 'RESET';
}

//-------------------------------------------------------------------------------------------------
if ($Mode == 'RESET')
{
 	$selected_id = -1;
	unset($_POST);	// clean all input fields
}
		
			
$result = get_notifications();
start_form();
start_table(TABLESTYLE_NOBORDER);

$th = array(_("EVENT NAME"), _("DESCRIPTION"), _("CHANNEL"), _("INITIAL MSG"),_("FINAL MSG"), "", "");

inactive_control_column($th);
table_header($th);	

$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);

	label_cell($myrow["EVENTNAME"]);
	label_cell($myrow["DESCRIPTION"]);
	label_cell($myrow["CHANNEL"]);
	label_cell(substr($myrow["INITIAL_MSG"], 0, 30).'...');
	label_cell(substr($myrow["FINAL_MSG"], 0, 30).'...');

	edit_button_cell("Edit".$myrow["ID"], _("Edit"));
    delete_button_cell("Delete".$myrow["ID"], _("Delete"));
	end_row();

} //END WHILE LIST LOOP

end_table(1);
//-------------------------------------------------------------------------------------------------
start_table(TABLESTYLE_NOBORDER);

if ($selected_id != -1) 
{
  	if ($Mode == 'Edit') {
		//editing an existing User
		$myrow = get_notification($selected_id);

		$_POST['EVENTNAME'] = $myrow["EVENTNAME"];
		$_POST['DESCRIPTION'] = $myrow["DESCRIPTION"];
		$_POST['CHANNEL'] = $myrow["CHANNEL"];
		$_POST['INITIAL_MSG'] = $myrow["INITIAL_MSG"];
		$_POST['FINAL_MSG'] = $myrow["FINAL_MSG"];
	}
	hidden('selected_id', $selected_id);
	hidden('code_id');

} 

text_row_ex(_("EVENT NAME").":", 'EVENTNAME',  20);
text_row_ex(_("DESCRIPTION").":", 'DESCRIPTION', 50);
start_row();
label_cell(_("CHANNEL").":");
$select = '<select name="CHANNEL">
				<option value="SMS">SMS</option>
			</select>';
label_cell($select);
end_row();
textarea_row(_("INITIAL MSG").":", 'INITIAL_MSG', null, 35, 3);
textarea_row(_("FINAL MSG").":", 'FINAL_MSG', null, 35, 3);


end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'false');

end_form();
end_page();
?>
