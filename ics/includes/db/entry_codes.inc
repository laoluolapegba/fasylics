<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
function update_entry_code($code_id, $SVC_CATEGORY_ID, $CYCLE_ID, $USSD_CODE, 
	$SMS_CODE, $SERVICE_ID, $OPERATION, $billing_code)
{
	$MODIFIED_DATE = date('Y-m-d h:i:s');
	$sql = "UPDATE ".TB_PREF."ics_entry_codes SET SVC_CATEGORY_ID=".db_escape($SVC_CATEGORY_ID).",
		CYCLE_ID=".db_escape($CYCLE_ID).",
		USSD_CODE=".db_escape($USSD_CODE).",
		SMS_CODE=".db_escape($SMS_CODE).",
		SERVICE_ID=".db_escape($SERVICE_ID).",
		OPERATION=".db_escape($OPERATION).",
		billing_code=".db_escape($billing_code).",
		MODIFIED_DATE=".db_escape($MODIFIED_DATE);

	$sql .= " WHERE CODE_ID=".db_escape($code_id);
	db_query($sql, "The entry code could not be updated");
}

function add_entry_code($code_id, $SVC_CATEGORY_ID, $CYCLE_ID, $USSD_CODE, 
	$SMS_CODE, $SERVICE_ID, $OPERATION, $billing_code, $MAKER_ID) 
{
	$CREATED_DATE = date('Y-m-d h:i:s');
	$sql = "INSERT INTO ".TB_PREF."ics_entry_codes (CODE_ID, SVC_CATEGORY_ID, CYCLE_ID, USSD_CODE,
		SMS_CODE, SERVICE_ID, OPERATION, billing_code, MAKER_ID, CREATED_DATE)
		VALUES (".db_escape($code_id).", ".db_escape($SVC_CATEGORY_ID).", ".db_escape($CYCLE_ID).",
		".db_escape($USSD_CODE).", ".db_escape($SMS_CODE).", ".db_escape($SERVICE_ID).", 
		".db_escape($OPERATION).", ".db_escape($billing_code).", ".db_escape($MAKER_ID).", "
		.db_escape($CREATED_DATE).")";
	db_query($sql, "The entry code could not be added");

	
}

function delete_entry_code($entry_code_id)
{
	$sql="DELETE FROM ".TB_PREF."ics_entry_codes WHERE CODE_ID=".db_escape($entry_code_id);
	db_query($sql, "could not delete entry code item");
}

function get_entry_code($entry_code)
{
	$sql = "SELECT *
		FROM ".TB_PREF."ics_entry_codes
		WHERE code_id=".db_escape($entry_code);
	$result = db_query($sql,"an entry code could not be retreived");

	return db_fetch($result);
}
/*
function get_entry_code($entry_code)
{
	$sql = "SELECT ".TB_PREF."ics_entry_codes.*,".TB_PREF."ics_billing_cycle.cycle_name AS cycle_name
		FROM ".TB_PREF."ics_entry_codes,".TB_PREF."ics_billing_cycle
		WHERE ".TB_PREF."ics_entry_codes.cycle_id=".TB_PREF."ics_billing_cycle.cycle_id
		AND code_id=".db_escape($entry_code);
	$result = db_query($sql,"an item could not be retreived");

	return db_fetch($result);
}
*/
function get_entry_codes()
{
	$sql = "SELECT * FROM ".TB_PREF."ics_entry_codes";
	return db_query($sql,"entry_code could not be retreived");
}


?>