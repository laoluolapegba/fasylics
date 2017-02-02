<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_SALESMANREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Salesman Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_salesman_list();

//----------------------------------------------------------------------------------------------------

function GetSalesmanTrans($from, $to)
{
	$fromdate = date2sql($from);
	$todate = date2sql($to);

	$sql = "SELECT DISTINCT ".TB_PREF."debtor_trans.*,
		ov_amount+ov_discount AS InvoiceTotal,
		".TB_PREF."debtors_master.name AS DebtorName, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."cust_branch.br_name,
		".TB_PREF."cust_branch.contact_name, ".TB_PREF."salesman.*
		FROM ".TB_PREF."debtor_trans, ".TB_PREF."debtors_master, ".TB_PREF."sales_orders, ".TB_PREF."cust_branch,
			".TB_PREF."salesman
		WHERE ".TB_PREF."sales_orders.order_no=".TB_PREF."debtor_trans.order_
		    AND ".TB_PREF."sales_orders.branch_code=".TB_PREF."cust_branch.branch_code
		    AND ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code
		    AND ".TB_PREF."debtor_trans.debtor_no=".TB_PREF."debtors_master.debtor_no
		    AND (".TB_PREF."debtor_trans.type=".ST_SALESINVOICE." OR ".TB_PREF."debtor_trans.type=".ST_CUSTCREDIT.")
		    AND ".TB_PREF."debtor_trans.tran_date>='$fromdate'
		    AND ".TB_PREF."debtor_trans.tran_date<='$todate'
		ORDER BY ".TB_PREF."salesman.salesman_code, ".TB_PREF."debtor_trans.tran_date";

	return db_query($sql, "Error getting order details");
}

//----------------------------------------------------------------------------------------------------

function print_salesman_list()
{
	global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$summary = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$destination = $_POST['PARAM_5'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$orientation = ($orientation ? 'L' : 'P');

	if ($summary == 0)
		$sum = _("No");
	else
		$sum = _("Yes");

	$dec = user_price_dec();

	$cols = array(0, 60, 150, 220, 325,	385, 450, 515);

	$headers = array(_('Invoice'), _('Customer'), _('Branch'), _('Customer Ref'),
		_('Inv Date'),	_('Total'),	_('Provision'));

	$aligns = array('left',	'left',	'left', 'left', 'left', 'right',	'right');

	$headers2 = array(_('Salesman'), " ",	_('Phone'), _('Email'),	_('Provision'),
		_('Break Pt.'), _('Provision')." 2");

    $params =   array( 	0 => $comments,
	    				1 => array(  'text' => _('Period'), 'from' => $from, 'to' => $to),
	    				2 => array(  'text' => _('Summary Only'),'from' => $sum,'to' => ''));

	$aligns2 = $aligns;

	$rep = new FrontReport(_('Salesman Listing'), "SalesmanListing", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
	$cols2 = $cols;
	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns, $cols2, $headers2, $aligns2);

	$rep->NewPage();
	$salesman = 0;
	$subtotal = $total = $subprov = $provtotal = 0;

	$result = GetSalesmanTrans($from, $to);

	while ($myrow=db_fetch($result))
	{
		$rep->NewLine(0, 2, false, $salesman);
		if ($salesman != $myrow['salesman_code'])
		{
			if ($salesman != 0)
			{
				$rep->Line($rep->row - 8);
				$rep->NewLine(2);
				$rep->TextCol(0, 3, _('Total'));
				$rep->AmountCol(5, 6, $subtotal, $dec);
				$rep->AmountCol(6, 7, $subprov, $dec);
    			$rep->Line($rep->row  - 4);
    			$rep->NewLine(2);
			}
			$rep->TextCol(0, 2,	$myrow['salesman_code']." ".$myrow['salesman_name']);
			$rep->TextCol(2, 3,	$myrow['salesman_phone']);
			$rep->TextCol(3, 4,	$myrow['salesman_email']);
			$rep->TextCol(4, 5,	number_format2($myrow['provision'], user_percent_dec()) ." %");
			$rep->AmountCol(5, 6, $myrow['break_pt'], $dec);
			$rep->TextCol(6, 7,	number_format2($myrow['provision2'], user_percent_dec()) ." %");
			$rep->NewLine(2);
			$salesman = $myrow['salesman_code'];
			$total += $subtotal;
			$provtotal += $subprov;
			$subtotal = 0;
			$subprov = 0;
		}
		$rate = $myrow['rate'];
		$amt = $myrow['InvoiceTotal'] * $rate;
		if ($subprov > $myrow['break_pt'] && $myrow['provision2'] != 0)
			$prov = $myrow['provision2'] * $amt / 100;
		else
			$prov = $myrow['provision'] * $amt / 100;
		if (!$summary)
		{
			$rep->TextCol(0, 1,	$myrow['trans_no']);
			$rep->TextCol(1, 2,	$myrow['DebtorName']);
			$rep->TextCol(2, 3,	$myrow['br_name']);
			$rep->TextCol(3, 4,	$myrow['contact_name']);
			$rep->DateCol(4, 5,	$myrow['tran_date'], true);
			$rep->AmountCol(5, 6, $amt, $dec);
			$rep->AmountCol(6, 7, $prov, $dec);
			$rep->NewLine();
		}
		$subtotal += $amt;
		$subprov += $prov;
	}
	if ($salesman != 0)
	{
		$rep->Line($rep->row - 4);
		$rep->NewLine(2);
		$rep->TextCol(0, 3, _('Total'));
		$rep->AmountCol(5, 6, $subtotal, $dec);
		$rep->AmountCol(6, 7, $subprov, $dec);
		$rep->Line($rep->row  - 4);
		$rep->NewLine(2);
		$total += $subtotal;
		$provtotal += $subprov;
	}
	$rep->fontSize += 2;
	$rep->TextCol(0, 3, _('Grand Total'));
	$rep->fontSize -= 2;
	$rep->AmountCol(5, 6, $total, $dec);
	$rep->AmountCol(6, 7, $provtotal, $dec);
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
	$rep->End();
}

?>