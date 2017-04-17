<?php
/**
 * Created by PhpStorm.
 * User: Laolu
 * Date: 06/04/2017
 * Time: 19:28
 */
$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(900, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

page(_($help_context = "Account maintenance"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/ics/includes/customui.inc");
include_once($path_to_root . "/ics/includes/db/accounts_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");

$user_comp = user_company();
$new_item = get_post('acct_no')=='' || get_post('cancel');
//------------------------------------------------------------------------------------

if (isset($_GET['acct_no']))
{
    $_POST['acct_no'] = $_GET['acct_no'];
}
$acct_no = get_post('acct_no');
if (list_updated('acct_no')) {
    $_POST['NewStockID'] = $acct_no = get_post('acct_no');
    clear_data();
    $Ajax->activate('details');
    $Ajax->activate('controls');
}

if (get_post('cancel')) {
    $_POST['NewStockID'] = $acct_no = $_POST['acct_no'] = '';
    clear_data();
    set_focus('acct_no');
    $Ajax->activate('_page_body');
}
//if (list_updated('category_id') || list_updated('mb_flag')) {
//    $Ajax->activate('details');
//}
//$upload_file = "";

//check_db_has_stock_categories(_("There are no item categories defined in the system. At least one item category is required to add a item."));

//check_db_has_item_tax_types(_("There are no item tax types defined in the system. At least one item tax type is required to add a item."));

function clear_data()
{
    unset($_POST['long_description']);
    unset($_POST['description']);
    unset($_POST['category_id']);
    unset($_POST['tax_type_id']);
    unset($_POST['units']);
    unset($_POST['mb_flag']);
    unset($_POST['NewStockID']);
    unset($_POST['dimension_id']);
    unset($_POST['dimension2_id']);
    unset($_POST['no_sale']);
}

//------------------------------------------------------------------------------------

if (isset($_POST['addupdate']))
{

    $input_error = 0;

    if (strlen($_POST['acct_title']) == 0)
    {
        $input_error = 1;
        display_error( _('The acct title cannot be empty.'));
        set_focus('acct_title');
    }
    elseif (strlen($_POST['NewStockID']) == 0)
    {
        $input_error = 1;
        display_error( _('The account number cannot be empty'));
        set_focus('NewStockID');
    }
    elseif (strstr($_POST['NewStockID'], " ") || strstr($_POST['NewStockID'],"'") ||
        strstr($_POST['NewStockID'], "+") || strstr($_POST['NewStockID'], "\"") ||
        strstr($_POST['NewStockID'], "&") || strstr($_POST['NewStockID'], "\t"))
    {
        $input_error = 1;
        display_error( _('The account number code cannot contain any of the following characters -  & + OR a space OR quotes'));
        set_focus('NewStockID');

    }


    if ($input_error != 1)
    {
        if (!$new_item)
        { /*so its an existing one */
            update_item($_POST['NewStockID'], $_POST['description'],
                $_POST['long_description'], $_POST['category_id'],
                $_POST['tax_type_id'], get_post('units'),
                get_post('mb_flag'), $_POST['sales_account'],
                $_POST['inventory_account'], $_POST['cogs_account'],
                $_POST['adjustment_account'], $_POST['assembly_account'],
                $_POST['dimension_id'], $_POST['dimension2_id'],
                check_value('no_sale'), check_value('editable'));

            set_focus('acct_no');
            $Ajax->activate('acct_no'); // in case of status change
            display_notification(_("Account has been updated."));
        }
        else
        { //it is a NEW acct

        }
        $Ajax->activate('_page_body');
    }
}


//------------------------------------------------------------------------------------
function item_settings(&$acct_no)
{
    global $SysPrefs, $path_to_root, $new_item;

    start_outer_table(TABLESTYLE2);

    table_section(1);

    table_section_title(_("Account"));

    //------------------------------------------------------------------------------------
    if ($new_item)
    {
        //text_row(_("Item Code:"), 'NewStockID', null, 21, 20);

        //$_POST['inactive'] = 0;
    }
    else
    { // Must be modifying an existing item
        if (get_post('NewStockID') != get_post('acct_no') || get_post('addupdate')) { // first item display

            $_POST['NewStockID'] = $_POST['acct_no'];

            //$myrow = get_item($_POST['NewStockID']);
            $myrow = get_account($_POST['NewStockID']);
            $_POST['acct_title'] = $myrow["acct_title"];
            $_POST['custid'] = $myrow["custid"];
            $_POST['cod_prod']  = $myrow["cod_prod"];
            $_POST['acct_status']  = $myrow["acct_status"];
            $_POST['dat_acct_open']  = $myrow["dat_acct_open"];
        }
        label_row(_("Account #:"),$_POST['NewStockID']);
        hidden('NewStockID', $_POST['NewStockID']);
        set_focus('acct_title');
    }
    //label_row(_("Account #:"),$_POST['acct_no']);
    text_row(_("Account name:"), 'acct_title', null, 52, 100);

    service_list_row(_("Product/Service:"), 'cod_prod', null);
    //text_row(_("Account Title:"), 'acct_title', $_POST['acct_title'], 50, 50);
    label_row(_("Customer Number:"), $_POST['custid']);
    acct_status_list_row(_("Acct Status:"), 'acct_status', $_POST['acct_status']);
    //date_row(_("Acct Open date:"), 'dat_acct_open', _('Date of acct opening'),
    //    0, 0, 0, 0, $_POST['dat_acct_open'], true);
    label_row(_("Acct Open date:"),$_POST['dat_acct_open']);

    end_outer_table(1);

    div_start('controls');
    submit_center_first('addupdate', _("Update Account"), '',
        @$_REQUEST['popup'] ? true : 'default');
    submit_return('select', get_post('acct_no'),
        _("Select this items and return to document entry."), 'default');
    submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');

    div_end();
}

//--------------------------------------------------------------------------------------------

start_form(true);

if (db_has_accounts())
{
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    msisdn_items_list_cells(_("Select an account:"), 'acct_no', null,
        _('New item'), true, check_value('show_inactive'));
    $new_item = get_post('acct_no')=='';
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
    hidden('acct_no', get_post('acct_no'));
}

div_start('details');

$acct_no = get_post('acct_no');
if (!$acct_no)
    unset($_POST['_tabs_sel']); // force settings tab for new customer

tabbed_content_start('tabs', array(
    'settings' => array(_('&General settings'), $acct_no)
));

switch (get_post('_tabs_sel')) {
    default:
    case 'settings':
        item_settings($acct_no);
        break;
};
br();
tabbed_content_end();

div_end();


hidden('popup', @$_REQUEST['popup']);
end_form();

//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);