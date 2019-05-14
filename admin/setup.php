<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2019 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    musical/admin/setup.php
 * \ingroup musical
 * \brief   Musical setup page.
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT . "/core/actions_setmoduleoptions.inc.php";

require_once DOL_DOCUMENT_ROOT . "/custom/musical/core/modules/modMusical.class.php";

// Translations
$langs->loadLangs(array("admin", "musical@musical"));

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');

$arrayofparameters=array(
    'MUSICAL_DEFAULT_PRICE'=>array('css'=>'minwidth200','enabled'=>0)
);


/*
 * Actions
 */

if ((float) DOL_VERSION >= 6)
{
	include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
}

if (isset($_POST['confirm'])){
    $answer = $_POST['confirm'];
    if ($answer == 'yes'){
        $mod = new modMusical($db);
        $res = $mod->cleanInstall();
    }
    else {
        header('Location:'.$_SERVER["PHP_SELF"].'?action=');
    }
}


/*
 * View
 */

$page_name = "MusicalSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage?$backtopage:DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_musical@musical');

// Configuration header
//$head = mymoduleAdminPrepareHead();
//dol_fiche_head($head, 'settings', '', -1, "musical@musicalr");

// Setup page goes here
echo $langs->trans("MusicalSetupPage").'<br><br>';

if ($action == 'edit')
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

	foreach($arrayofparameters as $key => $val)
	{
		print '<tr class="oddeven"><td>';
		print $form->textwithpicto($langs->trans($key),$langs->trans($key.'Tooltip'));
		print '</td><td><input name="'.$key.'"  class="flat '.(empty($val['css'])?'minwidth200':$val['css']).'" value="' . $conf->global->$key . '"></td></tr>';
	}
	print '</table>';

	print '<br><div class="center">';
	print '<input class="butAction" type="submit" value="'.$langs->trans("Save").'">';
	print '</div>';

	print '</form>';
}

else
{
    if ($action == 'deleteAll'){
        print '<form method="POST" action='.$_SERVER["PHP_SELF"].'>';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<table width="100%" class="valid"><tbody><tr class="validtitre"><td class="validtitre" colspan="3">';
        print '<img src="/dolibarr/htdocs/theme/eldy/img/recent.png" alt="" class="inline-block">';
        print $langs->trans("DeleteAllQ");
        print '</td></tr>';
        print '<tr class="valid"><td class="valid">';
        print $langs->trans('DeleteAllTextQ');
        print '</td><td class="valid">
        <select class="flat width75" id="confirm" name="confirm">
            <option value="yes">Oui</option>
            <option value="no" selected="">Non</option>
        </select>';
        print '</td><td class="valid" align="center"><input class="button valignmiddle" type="submit" value="Valider"></td></tr> </tbody></table>';
        print '</form>';
    }
    else {
        if (!empty($arrayofparameters)) {
            print '<table class="noborder" width="100%">';
            print '<tr class="liste_titre"><td class="titlefield">' . $langs->trans("Parameter") . '</td><td>' . $langs->trans("Value") . '</td></tr>';

            foreach ($arrayofparameters as $key => $val) {
                print '<tr class="oddeven"><td>';
                print $form->textwithpicto($langs->trans($key), $langs->trans($key));
                print '</td><td>' . $conf->global->$key . '</td></tr>';
            }

            print '</table>';

            print '<div class="tabsAction">';
            print '<a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?action=edit">' . $langs->trans("Modify") . '</a>';
            print '</div>';
        } else {
            print '<br>' . $langs->trans("NothingToSetup");
        }
        print '<br/><br/><br/>';
        print '<div class="tabsAction">';
        print '<p>'.$langs->trans("DeleteAllText").'</p> <a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=deleteAll">'.$langs->trans("DeleteAll").'</a>';
        print '</div>';
        print '<br/><br/><br/>';
    }

}




// Page end
dol_fiche_end();

llxFooter();
$db->close();



