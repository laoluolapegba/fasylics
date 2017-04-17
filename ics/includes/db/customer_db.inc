<?php
/**
 * Created by PhpStorm.
 * User: Laolu
 * Date: 06/04/2017
 * Time: 11:50
 */

function add_customer_local($cust_type, $surname, $firstname,
                            $midname, $nationality, $state_id, $lga_id, $gender, $dob,
                            $maritalstatus, $employment, $occupation,$address,$busstop,$email,$subs_date,$biz_address,$phone,$nok,
                            $nok_rel,$nok_address,$nok_phone,$nok_email, $makerid)
{
    $record_status = 'A';
    $cust_shortname = $surname . ' ' . $firstname;
    $checkerid = '';
    $mnt_action = 'A';
    $home_brn = '100';

    $sql = "INSERT INTO ".TB_PREF."ics_customer_master (cust_short_name,cust_firstname,cust_midname,
    cust_lastname,flg_cust_typ,dat_cust_open,cust_homebrn,txt_custadr_add1,txt_custadr_add2,
    txt_custadr_add3,nam_custadr_city,nam_custadr_state,nam_custadr_cntry,txt_custadr_zip,cust_phone,cust_email,
    dat_birth_cust,cust_gender,cust_maritalstat,cust_nationality,cod_cust_status,flg_record_status,makerid,
    mnt_action,dat_last_mnt, emp_type, txt_occupation, txt_busstop, lga_id, dat_subscription, biz_address,
    nok_name, nok_relationship, nok_address, nok_phone, nok_email) VALUES ("
        .db_escape($cust_shortname) .", " .db_escape($firstname) .", " .db_escape($midname) .", "
        .db_escape($surname) . ", " . db_escape($cust_type) . ",sysdate(), " .db_escape($home_brn) . ", "
        .db_escape($address) . ", null, null,null," .db_escape($state_id) . ",null,null, " .db_escape($phone) . ", "
        .db_escape($email) . ", " .db_escape($dob) . ",	" .db_escape($gender) . ",	" .db_escape($maritalstatus)
        . ",".db_escape($nationality) . ", 8,".db_escape($record_status) . ",	".db_escape($makerid)
        . ",".db_escape($mnt_action) . ", sysdate(), " .db_escape($employment) . ",	" .db_escape($occupation) . ",	"
        . db_escape($busstop) . "," .db_escape($lga_id) . ",	"  .db_escape($subs_date) . ",	"  .db_escape($biz_address) . ",	"
        .db_escape($nok) . ","  .db_escape($nok_rel) . "," .db_escape($nok_address) . "," .db_escape($nok_phone) . ","
        .db_escape($nok_email)
        . ")";
    //echo 'sql=' . $sql;
    db_query($sql,"The account could not be added");
}