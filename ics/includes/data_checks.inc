<?php

function db_has_cust_types()
{
    return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."ics_cust_types");
}

function check_db_has_customer_types($msg)
{
    global $path_to_root;
    if (!db_has_cust_types())
    {
        display_error($msg, true);
        end_page();
        exit;
    }
}
function db_has_product_types()
{
    return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."ics_services");
}

function check_db_has_product_types($msg)
{
    global $path_to_root;
    if (!db_has_cust_types())
    {
        display_error($msg, true);
        end_page();
        exit;
    }
}
function db_has_accounts()
{
    return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."ics_account_master");
}
function db_has_customers_local()
{
    return check_empty_result("SELECT COUNT(*) FROM ".TB_PREF."ics_customer_master");
}
?>