<?php
/* Copyright (C) 2017  Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * Need to have following variables defined:
 * $object (invoice, order, ...)
 * $action
 * $conf
 * $langs
 *
 * $keyforbreak may be defined to key to switch on second column
 */

// Protection to avoid direct call of template
if (empty($conf) || ! is_object($conf))
{
	print "Error, template page can't be called as URL";
	exit;
}
if (! is_object($form)) $form=new Form($db);

$object->fields = dol_sort_array($object->fields, 'position');


print '<table class="border centpercent">';

// --- Champ catégorie
$currentObj=$db->query("Select * from ".MAIN_DB_PREFIX."c_musical_instrument_category INNER JOIN ".MAIN_DB_PREFIX."musical_instrument_category ON rowid=fk_rowCategory where fk_rowInstrument='".$id."'");
$currentCateg = $db->fetch_object($currentObj);
print '<tr> <td class="titlefieldcreate"> '.$langs->trans('Category').' </td><td>';
if ($currentCateg)
{
	if ($currentCateg->rowid != 0) print $currentCateg->label;
}
print '</td></tr>';
// ---

// *** Lien Produit
if ($object->fk_product > 0){

    $currentObj=$db->query("Select * from ".MAIN_DB_PREFIX."product WHERE rowid='".$object->fk_product."'");
    $currentProd = $db->fetch_object($currentObj);
    print '<tr> <td class="titlefieldcreate"> '.$langs->trans('ProductLinked').' </td><td>';
    print '<a href ="http://localhost/dolibarr/htdocs/product/card.php?id='.$object->fk_product.'">'.$currentProd->label.' ('. $currentProd->ref .') </a>';
    print '</td></tr>';
}
// ***

print '</table>';