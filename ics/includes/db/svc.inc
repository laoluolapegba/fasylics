<?php
/**********************************************************************
    Copyright (C) mCMS, Technrunch Solutions Ltd
***********************************************************************/
function update_service($id, $SERVICE_NAME, $SERVICE_DESC, $TXT_ADVERT_MSG, 
	$CATEGORY_ID, $CYCLE_ID)
{				
	$sql = "UPDATE ".TB_PREF."ics_services SET SERVICE_NAME=".db_escape($SERVICE_NAME).",
		SERVICE_DESC=".db_escape($SERVICE_DESC).",
		TXT_ADVERT_MSG=".db_escape($TXT_ADVERT_MSG);
		//CATEGORY_ID=".db_escape($CATEGORY_ID).",
		//CYCLE_ID=".db_escape($CYCLE_ID);

	$sql .= " WHERE SERVICE_ID=".db_escape($id);
	db_query($sql, "The entry code could not be updated");
}

function add_service($id, $SERVICE_NAME, $SERVICE_DESC, $TXT_ADVERT_MSG, 
	$CATEGORY_ID, $FINAL_MSG, $MAKER_ID) 
{
	$CREATED_DATE = date('Y-m-d h:i:s');
	$sql = "INSERT INTO ".TB_PREF."ics_services (SERVICE_ID, SERVICE_NAME, SERVICE_DESC, TXT_ADVERT_MSG,
		CATEGORY_ID, CYCLE_ID, MAKER_ID, DAT_CREATED)
		VALUES (".db_escape($id).", ".db_escape($SERVICE_NAME).", ".db_escape($SERVICE_DESC).",
		".db_escape($TXT_ADVERT_MSG).",1, 1, ".db_escape($MAKER_ID).", ".db_escape($CREATED_DATE).")";

	db_query($sql, "The service could not be added");
	
}


function delete_service($service_id)
{
	$sql="DELETE FROM ".TB_PREF."ics_services WHERE service_id=".db_escape($service_id);
	db_query($sql, "could not delete service item");

	
	//delete_item_kit($stock_id);
}

function get_service($stock_id)
{
	$sql = "SELECT *
		FROM ics_services
		WHERE 1=1
		AND service_id=".db_escape($stock_id);
	$result = db_query($sql,"an item could not be retreived");
//die($sql);
	return db_fetch($result);
}

function get_services($all=false)
{
	$sql = "SELECT * FROM ics_services";
	if (!$all) $sql .= " WHERE !FLG_STATUS";
	//die($sql);
	return db_query($sql, "could not get services");
}

?>