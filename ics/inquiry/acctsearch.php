<?php
/**
 * Created by PhpStorm.
 * User: Laolu
 * Date: 06/04/2017
 * Time: 18:27
 */
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/ics/includes/customui.inc");
include_once($path_to_root . "/ics/includes/db/accounts_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$page_security = 'SA_SALESTRANSVIEW';

set_page_security( @$_POST['order_view_mode'],
    array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
        'InvoiceTemplates' => 'SA_SALESINVOICE'),
    array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
        'InvoiceTemplates' => 'SA_SALESINVOICE')
);

if (get_post('type'))
    $trans_type = $_POST['type'];
elseif (isset($_GET['type']) && $_GET['type'] == ST_SALESQUOTE)
    $trans_type = ST_SALESQUOTE;
else
    $trans_type = ST_SALESORDER;

if ($trans_type == ST_SALESORDER)
{

        $_POST['order_view_mode'] = false;
        $_SESSION['page_title'] = _($help_context = "Search All Accounts");

}
else
{
    $_POST['order_view_mode'] = false;
    $_SESSION['page_title'] = _($help_context = "Search All Accounts");
}

if (!@$_GET['popup'])
{
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 600);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page($_SESSION['page_title'], false, false, "", $js);
}

if (isset($_GET['selected_customer']))
{
    $selected_customer = $_GET['selected_customer'];
}
elseif (isset($_POST['selected_customer']))
{
    $selected_customer = $_POST['selected_customer'];
}
else
    $selected_customer = -1;

//---------------------------------------------------------------------------------------------

if (isset($_POST['SelectStockFromList']) && ($_POST['SelectStockFromList'] != "") &&
    ($_POST['SelectStockFromList'] != ALL_TEXT))
{
    $selected_stock_item = $_POST['SelectStockFromList'];
}
else
{
    unset($selected_stock_item);
}
//---------------------------------------------------------------------------------------------
//	Query format functions
//

function view_link($dummy, $order_no)
{
    global $trans_type;
    return  get_customer_trans_view_str($trans_type, $order_no);
}


function edit_link($row)
{
    if (@$_GET['popup'])
        return '';
    global $trans_type;
    //$modify = ($trans_type == ST_SALESORDER ? "ModifyOrderNumber" : "ModifyQuotationNumber");
    return pager_link( _("Edit"),
        "/ics/manage/acctmaint.php?acct_no=" . $row['acct_no'], ICON_EDIT);
}



function delivery_link($row)
{
    return pager_link( _("Delivery"),
        "/sales/sales_order_entry.php?NewDelivery=" .$row['order_no'], ICON_DOC);
}

function order_link($row)
{
    return pager_link( _("Sales Order"),
        "/sales/sales_order_entry.php?NewQuoteToSalesOrder=" .$row['order_no'], ICON_DOC);
}


$id = find_submit('_chgtpl');
if ($id != -1)
    change_tpl_flag($id);

if (isset($_POST['Update']) && isset($_POST['last'])) {
    foreach($_POST['last'] as $id => $value)
        if ($value != check_value('chgtpl'.$id))
            change_tpl_flag($id);
}

$show_dates = !in_array($_POST['order_view_mode'], array('OutstandingOnly', 'InvoiceTemplates', 'DeliveryTemplates'));
//---------------------------------------------------------------------------------------------
//	Order range form
//
/*
if (get_post('_OrderNumber_changed') || get_post('_OrderReference_changed')) // enable/disable selection controls
{
    $disable = get_post('OrderNumber') !== '' || get_post('OrderReference') !== '';

    if ($show_dates) {
        $Ajax->addDisable(true, 'OrdersAfterDate', $disable);
        $Ajax->addDisable(true, 'OrdersToDate', $disable);
    }

    $Ajax->activate('orders_tbl');
}
*/

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
//ref_cells(_("#:"), 'OrderNumber', '',null, '', true);
//ref_cells(_("Ref"), 'OrderReference', '',null, '', true);
if ($show_dates)
{
    date_cells(_("from:"), 'OrdersAfterDate', '', null, -30);
    date_cells(_("to:"), 'OrdersToDate', '', null, 1);
}
//locations_list_cells(_("Location:"), 'StockLocation', null, true, true);

if($show_dates) {
    end_row();
    end_table();

    start_table(TABLESTYLE_NOBORDER);
    start_row();
}
msisdn_items_list_cells(_("Account #:"), 'SelectStockFromList', null, true, true);
//if (!@$_GET['popup'])
//    customer_list_cells(_("Select a customer: "), 'customer_id', null, true, true);
//if ($trans_type == ST_SALESQUOTE)
//    check_cells(_("Show All:"), 'show_all');

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
hidden('order_view_mode', $_POST['order_view_mode']);
hidden('type', $trans_type);

end_row();

end_table(1);
//---------------------------------------------------------------------------------------------
//	Orders inquiry table
//
//$sql = get_sql_for_sales_orders_view
$sql = get_sql_for_acctsearch_view( @$selected_stock_item, @$_POST['OrdersAfterDate'], @$_POST['OrdersToDate']);

if ($trans_type == ST_SALESORDER)
    $cols = array(
        _("Account #") => array('fun'=>'view_link'),
        _("Account name") => array('ord' => '') ,
        _("Balance Available"),
        _("Book Balance"),
        _("Account Open Date") => array('type' =>  'date', 'ord' => ''),
        _("Last updated Date") =>array('type'=>'date', 'ord'=>''),
        _("Last updated by")

    );
else
    $cols = array(
        _("Account #") => array('fun'=>'view_link'),
        _("Account name") => array('ord' => '') ,
        _("Balance Available"),
        _("Book Balance"),
        _("Account Open Date") => array('type' =>  'date', 'ord' => ''),
        _("Last updated Date") =>array('type'=>'date', 'ord'=>''),
        _("Last updated by")
    );

    array_append($cols,array(
        array('insert'=>true, 'fun'=>'edit_link'),
        array('insert'=>true, 'fun'=>'prt_link')));



$table =& new_db_pager('orders_tbl', $sql, $cols);
//$table->set_marker('check_overdue', _("Marked items are overdue."));

$table->width = "80%";

display_db_pager($table);
submit_center('Update', _("Update"), true, '', null);

if (!@$_GET['popup'])
{
    end_form();
    end_page();
}
?>
