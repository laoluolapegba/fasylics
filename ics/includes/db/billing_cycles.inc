<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
function update_billing_cycle($cycle_id, $CYCLE_NAME, $VALIDITY, $MAX_SUSPEND_DAYS, 
	$MIN_USE_DAYS, $MAX_OFF_DAYS, $MAKER_ID)
{
	$MODIFIED_DATE = date('Y-m-d h:i:s');
	$sql = "UPDATE ".TB_PREF."ebs_billing_cycle SET CYCLE_NAME=".db_escape($CYCLE_NAME).",
		VALIDITY=".db_escape($VALIDITY).",
		MAX_SUSPEND_DAYS=".db_escape($MAX_SUSPEND_DAYS).",
		MIN_USE_DAYS=".db_escape($MIN_USE_DAYS).",
		MAX_OFF_DAYS=".db_escape($MAX_OFF_DAYS).",
		LAST_UPDATED_BY=".db_escape($MAKER_ID).",
		DAT_LAST_MNT=".db_escape($MODIFIED_DATE);

	$sql .= " WHERE CYCLE_ID=".db_escape($cycle_id);

	db_query($sql, "The entry code could not be updated");
}

function add_billing_cycle($cycle_id, $CYCLE_NAME, $VALIDITY, $MAX_SUSPEND_DAYS, 
	$MIN_USE_DAYS, $MAX_OFF_DAYS, $MAKER_ID) 
{
	$CREATED_DATE = date('Y-m-d h:i:s');
	$sql = "INSERT INTO ".TB_PREF."ebs_billing_cycle (cycle_id, CYCLE_NAME, VALIDITY, MAX_SUSPEND_DAYS,
		MIN_USE_DAYS, MAX_OFF_DAYS, MAKER_ID, DAT_CREATED)
		VALUES (".db_escape($cycle_id).", ".db_escape($CYCLE_NAME).", ".db_escape($VALIDITY).",
		".db_escape($MAX_SUSPEND_DAYS).", ".db_escape($MIN_USE_DAYS).", ".db_escape($MAX_OFF_DAYS).",
		".db_escape($MAKER_ID).", ".db_escape($CREATED_DATE).")";

	db_query($sql, "The entry code could not be added");

	
}

function delete_billing_cycle($cycle_id)
{
	$sql="DELETE FROM ".TB_PREF."ebs_billing_cycle WHERE CYCLE_ID=".db_escape($cycle_id);
	db_query($sql, "could not delete entry code item");
}

function get_billing_cycle($cycle_id)
{
	$sql = "SELECT *
		FROM ".TB_PREF."ebs_billing_cycle
		WHERE CYCLE_ID=".db_escape($cycle_id);
	$result = db_query($sql,"an entry code could not be retreived");

	return db_fetch($result);
}

function get_billing_cycles()
{
	$sql = "SELECT * FROM ".TB_PREF."ebs_billing_cycle";
	return db_query($sql,"entry_code could not be retreived");
}


?>