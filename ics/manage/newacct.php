<?php
/**
 * Created by PhpStorm.
 * User: Laolu
 * Date: 01/04/2017
 * Time: 15:06
 */

$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(900, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

page(_($help_context = "Customers"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/ics/includes/customui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/ics/includes/db/accounts_db.inc");
include_once($path_to_root . "/ics/includes/db/customer_db.inc");

$selected_id = '';

if (isset($_GET['acct_no']))
{
    $selected_id = $_POST['acct_no'] = $_GET['acct_no'];
}
else{
    $selected_id = get_post('acct_no','');
}
//get_post('acct_no','');

//--------------------------------------------------------------------------------------------

function can_process()
{
    if (strlen($_POST['acct_no']) == 0)
    {
        display_error(_("The account number cannot be empty."));
        set_focus('acct_no');
        return false;
    }
    /*
    elseif ($new_item && db_num_rows(get_item_kit($_POST['NewStockID'])))
    {
        $input_error = 1;
        display_error( _("This item code is already assigned to stock item or sale kit."));
        set_focus('NewStockID');
    }
    */

    if (strlen($_POST['surname']) == 0)
    {
        display_error(_("The customer surname cannot be empty."));
        set_focus('surname');
        return false;
    }
    if (strlen($_POST['firstname']) == 0)
    {
        display_error(_("The customer firstname cannot be empty."));
        set_focus('firstname');
        return false;
    }
    if (strlen($_POST['occupation']) == 0)
    {
        display_error(_("The customer occupation cannot be empty."));
        set_focus('occupation');
        return false;
    }
    if (strlen($_POST['phone']) == 0)
    {
        display_error(_("The customer phone cannot be empty."));
        set_focus('phone');
        return false;
    }
    if (strlen($_POST['email']) == 0)
    {
        display_error(_("The customer email cannot be empty."));
        set_focus('email');
        return false;
    }
    return true;
}

//--------------------------------------------------------------------------------------------

function handle_submit(&$selected_id)
{
    global $path_to_root, $Ajax, $auto_create_customer;

    if (!can_process())
        return;

    if ($selected_id)
    {
        update_account($_POST['acct_no'], $_POST['acct_title'], $_POST['acct_status'],
            $_SESSION["wa_current_user"]->username, '');

        //update_record_status($_POST['customer_id'], $_POST['inactive'],
        //    'debtors_master', 'debtor_no');

        $Ajax->activate('acct_no'); // in case of status change
        display_notification(_("Account has been updated."));
    }
    else
    { 	//it is a new customer

        begin_transaction();
        $cust_id = 0;

        if (isset($auto_create_customer) && $auto_create_customer == 1)
        {
            //echo 'calling add cust';
            add_customer_local($_POST['cust_type'], $_POST['surname'],
                $_POST['firstname'], $_POST['midname'], $_POST['nationality'], $_POST['state_id'], $_POST['lga_id'],
                $_POST['gender'], $_POST['dob'], $_POST['maritalstatus'], $_POST['employment'], $_POST['occupation'], $_POST['address'],
                $_POST['busstop'], $_POST['email'], $_POST['subs_date'], $_POST['biz_address'], $_POST['phone'],
                $_POST['nok'], $_POST['nok_rel'], $_POST['nok_address'], $_POST['nok_email'], $_POST['nok_phone'],
                $_SESSION["wa_current_user"]->username );
            //echo 'after calling add cust custid is:' . $cust_id;
            $cust_id = db_insert_id();
        }

        add_account($cust_id, $_POST['acct_no'],$_POST['cod_prod'],$_POST['surname'], $_POST['firstname'],
            $_POST['midname'],    $_SESSION["wa_current_user"]->username   );

        //$selected_id = $_POST['acct_no'] ; //= db_insert_id();


        commit_transaction();

        display_notification(_("A new account has been added."));

        if (isset($auto_create_customer) && $auto_create_customer == 1)
            display_notification(_("A new Customer has been automatically created."));

        $Ajax->activate('_page_body');
    }
}
//--------------------------------------------------------------------------------------------

if (isset($_POST['submit']))
{
    handle_submit($selected_id);
}

function customer_settings($selected_id)
{
    global $SysPrefs, $path_to_root, $auto_create_customer;

    if (!$selected_id)
    {
        if (list_updated('acct_no') || !isset($_POST['acct_title'])) {
            $_POST['acct_title'] = $_POST['acct_title'] = $_POST['address'] = $_POST['surname'] = $_POST['firstname']  = '';
            $_POST['lga_id'] = -1;
            $_POST['state_id'] = -1;
            //$_POST['cod_ccy']  = get_company_currency();
        }
    }
    else
    {
        $myrow = get_account($selected_id);

        $_POST['acct_title'] = $myrow["acct_title"];
        $_POST['custid'] = $myrow["custid"];
        $_POST['cod_prod']  = $myrow["cod_prod"];
        $_POST['acct_status']  = $myrow["acct_status"];
        $_POST['dat_acct_open']  = $myrow["dat_acct_open"];

    }

    start_outer_table(TABLESTYLE2);
    table_section(1);
    table_section_title(_("Account Details"));
    if (!$selected_id) {
        service_list_row(_("Product/Service:"), 'cod_prod', null);
        cust_types_list_row(_("Customer Type:"), 'cust_type', 'I');
        text_row(_("Account Number:"), 'acct_no', null, 15, 15);
        text_row(_("Surname:"), 'surname', null, 30, 30);
        text_row(_("Firstname:"), 'firstname', null, 40, 80);
        text_row(_("Middlename:"), 'midname', null, 40, 80);
        countries_list_row(_("Nationality:"), 'nationality', 156);
        states_list_row(_("State of Origin:"), 'state_id', null);
        lgas_list_row(_("LGA:"), 'lga_id', null);
        gender_list_row(_("Gender:"), 'gender', null);
        date_row(_("Date of Birth:"), 'dob', _('Date of birth'),
            0, 0, 0, 0, null, true);
        maritalstat_list_row(_("MaritalStatus:"), 'maritalstatus', null);
        occupation_list_row(_("Employment type:"), 'employment', null);
        text_row(_("Occupation:"), 'occupation', null, 50, 55);


        //if($selected_id)
        //    record_status_list_row(_("Account status:"), 'inactive');
        //elseif (isset($auto_create_branch) && $auto_create_branch == 1)
        //{

        //}
        table_section_title(_("Address Information"));
        textarea_row(_("Residential Address:"), 'address', null, 35, 5);
        text_row(_("Nearest Bus Stop:"), 'busstop', null, 32, 30);
        email_row(_("E-mail Address:"), 'email', null, 35, 55);
        date_row(_("Subscription date:"), 'subs_date', _('Date of order receive'),
            0, 0, 0, 0, null, true);
        textarea_row(_("Business Address:"), 'biz_address', null, 35, 5);
        text_row(_("Phone:"), 'phone', null, 32, 30);

        table_section(2);

        table_section_title(_("Next of Kin"));
        text_row(_("Next of Kin:"), 'nok', null, 32, 30);
        text_row(_("Relationship:"), 'nok_rel', null, 32, 30);
        textarea_row(_("Address Address:"), 'nok_address', null, 35, 5);
        text_row(_("Phone number:"), 'nok_phone', null, 32, 30);
        email_row(_("E-mail Address:"), 'nok_email', null, 35, 55);

    }
    else
    {
        service_list_row(_("Product/Service:"), 'cod_prod', $_POST['cod_prod']);
        text_row(_("Account Title:"), 'acct_title', $_POST['acct_title'], 50, 50);
        text_row(_("Customer Number:"), 'custid', $_POST['custid'], 30, 30);
        acct_status_list_row(_("Acct Status:"), 'acct_status', $_POST['acct_status']);
        date_row(_("Acct Open date:"), 'dat_acct_open', _('Date of acct opening'),
            0, 0, 0, 0, $_POST['dat_acct_open'], true);
    }
    end_outer_table(1);

    div_start('controls');
    if (!$selected_id)
    {
        submit_center('submit', _("Open New Account"), true, '', 'default');
    }
    else
    {
        submit_center_first('submit', _("Update Account"),
            _('Update account data'), @$_REQUEST['popup'] ? true : 'default');
        submit_return('select', $selected_id, _("Select this account and return to document entry."));
        submit_center_last('delete', _("Delete Account"),
            _('Delete customer data if have been never used'), true);
    }
    div_end();
}

//--------------------------------------------------------------------------------------------

check_db_has_customer_types(_("There are no customer types defined. Please define at least one customer type before adding a customer."));

check_db_has_product_types(_("There are no services/ products defined. Please define at least one product before opening an account."));

start_form();

if (db_has_accounts())
{
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    account_list_cells(_("Select an account: "), 'acct_no', null,
        _('New Account'), true, check_value('show_inactive'));
    //check_cells(_("Show inactive:"), 'show_inactive', null, true);
    end_row();
    end_table();

    if (get_post('_show_inactive_update')) {
        $Ajax->activate('acct_no');
        set_focus('acct_no');
    }
}
else
{
   // hidden('acct_no');
}

if (!$selected_id || list_updated('acct_no'))
    unset($_POST['_tabs_sel']); // force settings tab for new customer

tabbed_content_start('tabs', array(
    'settings' => array(_('&General settings'), $selected_id),
    //'contacts' => array(_('&Contacts'), $selected_id),
    'transactions' => array(_('&Transactions'),
        ($_SESSION["wa_current_user"]->can_access_page('SA_SALESTRANSVIEW') ? $selected_id : null)),
    'orders' => array(_('Sales &Orders'),
        ($_SESSION["wa_current_user"]->can_access_page('SA_SALESTRANSVIEW') ? $selected_id : null)),
));

switch (get_post('_tabs_sel')) {
    default:
    case 'settings':
        customer_settings($selected_id);
        break;
    case 'transactions':
        $_GET['customer_id'] = $selected_id;
        $_GET['popup'] = 1;
        include_once($path_to_root."/sales/inquiry/customer_inquiry.php");
        break;
    case 'orders':
        $_GET['customer_id'] = $selected_id;
        $_GET['popup'] = 1;
        include_once($path_to_root."/sales/inquiry/sales_orders_view.php");
        break;
};
br();
tabbed_content_end();

hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>
