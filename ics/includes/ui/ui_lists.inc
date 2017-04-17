<?php
/**
 * Created by PhpStorm.
 * User: Laolu
 * Date: 04/04/2017
 * Time: 06:14
 */
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");

//------------------------------------------------------------------------------------------------

function entry_code_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    global $all_items;

    $sql = "SELECT CODE_ID, SMS_CODE FROM ".TB_PREF."ebs_entry_codes";

    return combo_input($name, $selected_id, $sql, 'CODE_ID', 'SMS_CODE',
        array(
            'spec_option' => $all_option === true ? _("All Entry Codes") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );
}

function entry_code_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo entry_code_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function entry_code_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    echo "<tr>";
    entry_code_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

function billing_cycle_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    global $all_items;

    $sql = "SELECT cycle_id, cycle_name FROM ".TB_PREF."ebs_billing_cycle";

    return combo_input($name, $selected_id, $sql, 'cycle_id', 'cycle_name',
        array(
            'spec_option' => $all_option === true ? _("All Billing Cycles") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );
}

function billing_cycle_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo billing_cycle_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function billing_cycle_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    echo "<tr>";
    billing_cycle_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

function service_category_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    global $all_items;

    $sql = "SELECT category_id, category_name FROM ".TB_PREF."ebs_service_category";

    return combo_input($name, $selected_id, $sql, 'category_id', 'category_name',
        array(
            'spec_option' => $all_option === true ? _("All Entry Codes") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );
}

function service_category_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo service_category_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function service_category_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    echo "<tr>";
    service_category_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

function service_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    global $all_items;

    $sql = "SELECT service_id, service_name FROM ".TB_PREF."ics_services";

    return combo_input($name, $selected_id, $sql, 'service_id', 'service_name',
        array(
            'spec_option' => $all_option === true ? _("All Entry Codes") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );
}

function service_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo service_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function service_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    echo "<tr>";
    service_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
function operation_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    global $all_items;

    $sql = "SELECT DISTINCT(OPERATION), OPERATION opt FROM ".TB_PREF."ics_entry_codes";

    return combo_input($name, $selected_id, $sql, 'OPERATION', 'opt',
        array(
            'spec_option' => $all_option === true ? _("All Operations") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );
}

function operation_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo operation_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function operation_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    echo "<tr>";
    operation_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

//-------------------------------------------------------------------------------------

function cust_types_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT flg_cust_typ, txt_cust_typ, flg_mnt_status FROM ".TB_PREF."ics_cust_types";
    return combo_input($name, $selected_id, $sql, 'flg_cust_typ', 'txt_cust_typ',
        array('order'=>array('txt_cust_typ'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_TEXT));
}

function cust_types_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo cust_types_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function cust_types_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    cust_types_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}

//-------------------------------------------------------------------------------------

function countries_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT id, nicename, inactive FROM ".TB_PREF."ics_country";
    return combo_input($name, $selected_id, $sql, 'id', 'nicename',
        array('order'=>array('nicename'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function countries_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo countries_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function countries_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    countries_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}

//-------------------------------------------------------------------------------------

function states_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT state_id, state_name FROM ".TB_PREF."ics_states";
    return combo_input($name, $selected_id, $sql, 'state_id', 'state_name',
        array('order'=>array('state_name'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function states_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo states_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function states_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    states_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}
//------------------------------------------------------------
function lgas_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT local_id, local_name,state_id FROM ".TB_PREF."ics_lgas";
    return combo_input($name, $selected_id, $sql, 'local_id', 'local_name',
        array('order'=>array('local_name'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function lgas_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo lgas_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function lgas_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    lgas_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}

//------------------------------------------------------------
function maritalstat_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT id, maritalstat FROM ".TB_PREF."ics_marstat";
    return combo_input($name, $selected_id, $sql, 'id', 'maritalstat',
        array('order'=>array('maritalstat'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function maritalstat_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo maritalstat_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function maritalstat_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    maritalstat_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}
//------------------------------------------------------------
function occupation_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT id, occupation FROM ".TB_PREF."ics_occupation";
    return combo_input($name, $selected_id, $sql, 'id', 'occupation',
        array('order'=>array('occupation'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function occupation_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo occupation_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function occupation_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    occupation_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}
//------------------------------------------------------------
function gender_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT id, gender FROM ".TB_PREF."ics_gender";
    return combo_input($name, $selected_id, $sql, 'id', 'gender',
        array('order'=>array('gender'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function gender_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo gender_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function gender_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    gender_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}
//----------------------------------------------------------------------------------------------

function account_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                       $show_inactive=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT acct_no, acct_title, cod_ccy, flg_record_status FROM ".TB_PREF."ics_account_master";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)
        set_editor('account', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'acct_no', 'acct_title',
        array(
            'format' => '_format_add_curr',
            'order' => array('acct_title'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'search' => array("acct_no","acct_title"),
            'spec_option' => $spec_option === true ? _("All Accounts") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new account') :
                _('Select account'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('account');
    return $ret;
}

function account_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo account_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";
}

function account_list_row($label, $name, $selected_id=null, $all_option = false,
                           $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    global $path_to_root;

    echo "<tr><td class='label'>$label</td><td nowrap>";
    echo account_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n</tr>\n";
}
//------------------------------------------------------------
function acct_status_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT cod_acct_status, txt_acct_status FROM ".TB_PREF."ics_acct_status";
    return combo_input($name, $selected_id, $sql, 'cod_acct_status', 'txt_acct_status',
        array('order'=>array('cod_acct_status'),
            'spec_option' => $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function acct_status_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo acct_status_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function acct_status_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class='label'>$label</td>";
    acct_status_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}

//---------------------------------------------------------------------------------------------------

function msisdn_items_list($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $opts=array(), $editkey = false)
{
    global $all_items;

    $sql = "SELECT acct_no, acct_title, acct_title, 0 as inactive
			FROM ".TB_PREF."ics_account_master  WHERE flg_record_status='A'";

    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'acct_no', 'acct_title',
        array_merge(
            array(
                'format' => '_format_msisdn_items',
                'spec_option' => $all_option===true ?  _("All Accounts") : $all_option,
                'spec_id' => $all_items,
                'search_box' => true,
                'search' => array("acct_no", "acct_title","acct_title"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'size'=>15,
                'select_submit'=> $submit_on_change,
                //'category' => 0,
                'order' => array('acct_title','acct_no')
            ), $opts) );
    if ($editkey)
        $ret .= add_edit_combo('item');
    return $ret;

}

function _format_msisdn_items($row)
{
    return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function msisdn_items_list_cells($label, $name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo msisdn_items_list($name, $selected_id, $all_option, $submit_on_change,
        array('cells'=>true, 'show_inactive'=>$all), $editkey);
}
?>