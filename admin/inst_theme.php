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
$page_security = 'SA_CREATEMODULES';
$path_to_root="..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root."/includes/packages.inc");

if ($use_popup_windows) {
	$js = get_js_open_window(900, 500);
}
page(_($help_context = "Install Themes"), false, false, '', $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
include_once($path_to_root . "/admin/db/maintenance_db.inc");
include_once($path_to_root . "/includes/ui.inc");

//---------------------------------------------------------------------------------------------
// If theme is used in customer record set to default
//
function clean_user_themes($id)
{
	global $db_connections, $db, $installed_extensions;

	$theme = $installed_extensions[$id]['package'];
	$comp = user_company();

	foreach ($db_connections as $n => $conn) {
		$db = $_SESSION["wa_current_user"]->set_db_connection($n);
		$sql = "UPDATE {$conn['tbpref']}users SET theme='default' WHERE theme='$theme'";
		if (!db_query($sql, 'Cannot update user theme settings'))
			return false;
	}
	$db = $_SESSION["wa_current_user"]->set_db_connection($comp);

	$_SESSION['wa_current_user']->prefs->theme = 'default';
	return true;
}

if (($id = find_submit('Delete', false)) && clean_user_themes($id))
{
	$extensions = get_company_extensions();
	$theme = $extensions[$id]['package'];
	if (uninstall_package($theme)) {
		$dirname = $path_to_root.'/themes/'.$theme;
		flush_dir($dirname, true);
		rmdir($dirname);
		unset($extensions[$id]);
		if (update_extensions($extensions)) {
			display_notification(_("Selected theme has been successfully deleted"));
			meta_forward($_SERVER['PHP_SELF']);
		}
	}
}

if ($id = find_submit('Update', false))
	install_extension($id);

//---------------------------------------------------------------------------------------------
start_form(true);

	div_start('ext_tbl');
	start_table(TABLESTYLE);

	$th = array(_("Theme"),  _("Installed"), _("Available"), "", "");
	table_header($th);

	$k = 0;
	$mods = get_themes_list();

	foreach($mods as $pkg_name => $ext)
	{
		$available = @$ext['available'];
		$installed = @$ext['version'];
		$id = @$ext['local_id'];

		alt_table_row_color($k);

//		label_cell(is_array($ext['Descr']) ? $ext['Descr'][0] : $ext['Descr']);
		label_cell($available ? get_package_view_str($pkg_name, $ext['name']) : $ext['name']);

		label_cell($id === null ? _("None") :
			($available && $installed ? $installed : _("Unknown")));
		label_cell($available ? $available : _("None"));

		if ($available && check_pkg_upgrade($installed, $available)) // outdated or not installed theme in repo
			button_cell('Update'.$pkg_name, $installed ? _("Update") : _("Install"),
				_('Upload and install latest extension package'), ICON_DOWN);
		else
			label_cell('');

		if ($id !== null) {
			delete_button_cell('Delete'.$id, _('Delete'));
			submit_js_confirm('Delete'.$id, 
				sprintf(_("You are about to remove package \'%s\'.\nDo you want to continue ?"), 
					$ext['name']));
		} else
			label_cell('');

		end_row();
	}

	end_table(1);

	div_end();

//---------------------------------------------------------------------------------------------
end_form();

end_page();
?>