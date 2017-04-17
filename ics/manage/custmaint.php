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

page(_($help_context = "Modify Customer"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/ics/includes/customui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
