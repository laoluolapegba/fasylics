<?php
/**
 * Created by PhpStorm.
 * User: Laolu
 * Date: 06/04/2017
 * Time: 10:02
 */
function add_account($custid,$acct_no, $cod_prod, $surname, $firstname, $midname, $makerid)
{
    if(isset($custid) && $custid != 0)
    {
        $acct_title = $surname . ' ' . $firstname;
        $record_status = 'U';
        $checkerid = '';
        $mnt_action = 'A';
        $acct_brn = '100';
        $sql = "INSERT INTO ".TB_PREF."ics_account_master (custid,acct_no,acct_title,product_code,dat_acct_open,
    acct_brn,cod_ccy,acct_status,flg_record_status,makerid,checkerid,mnt_action,dat_last_mnt) VALUES ("
            .db_escape($custid) .", " .db_escape($acct_no) .", "
            .db_escape($acct_title) . ", " . db_escape($cod_prod) . ",". " sysdate(), " . db_escape($acct_brn). ",1,8,"
            .db_escape($record_status) . ", "
            .db_escape($makerid) . ", ".db_escape($checkerid) . ",
		" . db_escape($mnt_action) .  ", sysdate())";

        db_query($sql,"The account could not be added");
    }

}

function update_account($acct_no, $acct_title, $acct_status, $makerid, $checkerid)
{
    $sql = "UPDATE ".TB_PREF."ics_account_master SET acct_title=" . db_escape($acct_title) . ",
		acct_status=".db_escape($acct_status) . ",
		makerid=".db_escape($makerid) . ",
		checkerid=".db_escape($checkerid) . "
		WHERE acct_no = ".db_escape($acct_no);

    db_query($sql,"The customer could not be updated");
}

function delete_account($acct_no)
{
    begin_transaction();
    //delete_entity_contacts('customer', $acct_no);

    $sql = "DELETE FROM ".TB_PREF."ics_account_master WHERE acct_no=".db_escape($acct_no)  ;
    db_query($sql,"cannot delete customer");
    commit_transaction();
}


function get_account($acct_no)
{
    $sql = "SELECT * FROM ".TB_PREF."ics_account_master WHERE acct_no=".db_escape($acct_no) . " and flg_record_status='A'";

    $result = db_query($sql, "could not get account");

    return db_fetch($result);
}

function get_account_name($acct_no)
{
    $sql = "SELECT acct_title FROM ".TB_PREF."ics_account_master WHERE acct_no=".db_escape($acct_no);

    $result = db_query($sql, "could not get account");

    $row = db_fetch_row($result);

    return $row[0];
}


function is_new_acct($id)
{
    $tables = array('cust_branch', 'debtor_trans', 'recurrent_invoices', 'sales_orders');

    return !key_in_foreign_table($id, $tables, 'debtor_no');
}

function get_acct_by_name($acct_title)
{
    $sql = "SELECT * FROM ".TB_PREF."ics_account_master WHERE acct_title=".db_escape($acct_title);

    $result = db_query($sql, "could not get account");

    return db_fetch($result);
}
function get_sql_for_acctsearch_view($msisdn=null, $from='', $to='')
{

    $sql = "SELECT
			acct_no,
			acct_title,
			available_bal,
			book_bal,
			dat_acct_open,
			dat_last_mnt,
			makerid
		FROM ".TB_PREF."ics_account_master
			WHERE 1=1";

    $date_after = date2sql($from);
    $date_before = date2sql($to);

    $sql .=  " AND dat_acct_open >= '$date_after'"
        ." AND dat_acct_open <= '$date_before'";
    if (isset($msisdn))
        $sql .= " AND acct_no=".db_escape($msisdn);


    return $sql;
}
function get_sql_for_unauthmaint($msisdn=null, $from='', $to='')
{

    $sql = "select a.custid,b.acct_no, b.acct_title,  cust_firstname, cust_lastname,
            case a.cust_gender when 1 then 'Male' else 'Female' end gender,
            b.dat_acct_open, b.available_bal,b.book_bal, b.makerid,b.dat_last_mnt,
            case b.flg_record_status when 'A' then 'Authorized' else 'Unauthorized' end status,b.flg_record_status
            from ics_customer_master a, ics_account_master b
            where a.custid = b.custid
            and a.flg_record_status = 'U' or b.flg_record_status = 'U'
                         and 1=1";

    $date_after = date2sql($from);
    $date_before = date2sql($to);

    $sql .=  " AND dat_acct_open >= '$date_after'"
        ." AND dat_acct_open <= '$date_before'";
    if (isset($msisdn))
        $sql .= " AND acct_no=".db_escape($msisdn);


    return $sql;
}

function get_sql_for_txnhist($msisdn=null, $from='', $to='')
{

    $sql = "select a.dat_txn, a.dat_value, a.trans_id,  a.amt_txn, a.txn_desc,
case a.cod_drcr when 'C' then 'Credit' else 'Debit' end drcr,
b.acct_no, b.acct_title,

            b.flg_record_status
            from ics_transaction_hist a, ics_account_master b
            where a.acct_no = b.acct_no
            and b.flg_record_status = 'A'
                         and 1=1";

    $date_after = date2sql($from);
    $date_before = date2sql($to);

    $sql .=  " AND a.dat_txn >= '$date_after'"
        ." AND a.dat_txn <= '$date_before'";
    if (isset($msisdn))
        $sql .= " AND a.acct_no=".db_escape($msisdn);


    return $sql;
}