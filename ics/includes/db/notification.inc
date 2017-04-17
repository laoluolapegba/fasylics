<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
function update_notification($id, $EVENTNAME, $DESCRIPTION, $CHANNEL, 
	$INITIAL_MSG, $FINAL_MSG)
{
	$MODIFIED_DATE = date('Y-m-d h:i:s');
	$sql = "UPDATE ".TB_PREF."ics_notification_setup SET EVENTNAME=".db_escape($EVENTNAME).",
		DESCRIPTION=".db_escape($DESCRIPTION).",
		CHANNEL=".db_escape($CHANNEL).",
		INITIAL_MSG=".db_escape($INITIAL_MSG).",
		FINAL_MSG=".db_escape($FINAL_MSG);

	$sql .= " WHERE ID=".db_escape($id);

	db_query($sql, "The entry code could not be updated");
}

function add_notification($id, $EVENTNAME, $DESCRIPTION, $CHANNEL, 
	$INITIAL_MSG, $FINAL_MSG, $MAKER_ID) 
{
	$CREATED_DATE = date('Y-m-d h:i:s');
	$sql = "INSERT INTO ".TB_PREF."ics_notification_setup (ID, EVENTNAME, DESCRIPTION, CHANNEL,
		INITIAL_MSG, FINAL_MSG, MAKER_ID, CREATED_DATE)
		VALUES (".db_escape($id).", ".db_escape($EVENTNAME).", ".db_escape($DESCRIPTION).",
		".db_escape($CHANNEL).", ".db_escape($INITIAL_MSG).", ".db_escape($FINAL_MSG).",
		".db_escape($MAKER_ID).", ".db_escape($CREATED_DATE).")";

	db_query($sql, "The entry code could not be added");
	
}

function delete_notification($id)
{
	$sql="DELETE FROM ".TB_PREF."ics_notification_setup WHERE ID=".db_escape($id);
	db_query($sql, "could not delete entry code item");
}

function get_notification($id)
{
	$sql = "SELECT *
		FROM ".TB_PREF."ics_notification_setup
		WHERE ID=".db_escape($id);
	$result = db_query($sql,"an entry code could not be retrieved");

	return db_fetch($result);
}

function get_notifications()
{
	$sql = "SELECT * FROM ".TB_PREF."ics_notification_setup";
	return db_query($sql,"entry_code could not be retrieved");
}


?>