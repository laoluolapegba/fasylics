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
class global_params_app extends application
{
	function global_params_app()
	{
		$this->application("stock", _($this->help_context = "&Global Parameters"));

		$this->add_module(_("Transactions"));
		//$this->add_lapp_function(0, _("Inventory Location &Transfers"),
		//	"inventory/transfers.php?NewTransfer=1", 'SA_LOCATIONTRANSFER', MENU_TRANSACTION);
		////$this->add_lapp_function(0, _("Inventory &Adjustments"),
		//	"inventory/adjustments.php?NewAdjustment=1", 'SA_INVENTORYADJUSTMENT', MENU_TRANSACTION);

		$this->add_module(_("Inquiries and Reports"));
		/*$this->add_lapp_function(1, _("Inventory Item &Movements"),
			"inventory/inquiry/stock_movements.php?", 'SA_ITEMSTRANSVIEW', MENU_INQUIRY);
		$this->add_lapp_function(1, _("Inventory Item &Status"),
			"inventory/inquiry/stock_status.php?", 'SA_ITEMSSTATVIEW', MENU_INQUIRY);
		$this->add_rapp_function(1, _("Inventory &Reports"),
			"reporting/reports_main.php?Class=2", 'SA_ITEMSTRANSVIEW', MENU_REPORT);
		*/
		$this->add_module(_("Maintenance"));
		$this->add_lapp_function(2, _("&Products / Services"),
			"ics/manage/services.php?", 'SA_ITEM', MENU_ENTRY);

		$this->add_lapp_function(2, _("Add and Manage &Entry Codes"),
				"ics/manage/entry_codes.php?", 'SA_ITEM');


		$this->add_rapp_function(2, _("Manage &Notifications"),
				"ics/manage/notification.php?", 'SA_ITEM');


		$this->add_extensions();
	}
}


?>