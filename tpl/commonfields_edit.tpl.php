<?php

$object->fields = dol_sort_array($object->fields, 'position');

foreach($object->fields as $key => $val)
{
    // Discard if extrafield is a hidden field on form
    if (abs($val['visible']) != 1) continue;

    if (array_key_exists('enabled', $val) && isset($val['enabled']) && ! verifCond($val['enabled'])) continue;	// We don't want this field

    if ($key == 'serial') {
        print '<tr><td class="fieldrequired">';
        if (! empty($val['help'])) print $form->textwithpicto($langs->trans($val['label']), $val['help']);
        else print $langs->trans($val['label']);
        print '</td><td>';
        print '<input type="text" value="'.$object->serial.'" readonly />';
        print '</td></tr>';
        continue;
    }
    print '<tr><td';
    print ' class="titlefieldcreate';
    if ($val['notnull'] > 0) print ' fieldrequired';
    if ($val['type'] == 'text' || $val['type'] == 'html') print ' tdtop';
    print '">';
    if (! empty($val['help'])) print $form->textwithpicto($langs->trans($val['label']), $val['help']);
    else print $langs->trans($val['label']);
    print '</td>';
    print '<td>';
    if (in_array($val['type'], array('int', 'integer'))) $value = GETPOSTISSET($key)?GETPOST($key, 'int'):$object->$key;
    elseif ($val['type'] == 'text' || $val['type'] == 'html') $value = GETPOSTISSET($key)?GETPOST($key,'none'):$object->$key;
    else $value = GETPOSTISSET($key)?GETPOST($key, 'alpha'):$object->$key;
    //var_dump($val.' '.$key.' '.$value);
    print $object->showInputField($val, $key, $value, '', '', '', 0);
    print '</td>';
    print '</tr>';
}

// --- Champ catégorie
print '<tr id="field_category"> <td class="titlefieldcreate fieldrequired">'.$langs->trans('Category').'</td> ';
$currentObj=$db->query("Select * from ".MAIN_DB_PREFIX."musical_instrument_category where fk_rowInstrument='".$id."'");
$currentCateg = $db->fetch_object($currentObj);
$resql=$db->query("Select * from ".MAIN_DB_PREFIX."c_musical_instrument_category WHERE active = '1'");
if ($resql->num_rows > 1)
{
    $num = $db->num_rows($resql);
    $i = 0;
    print '<td><select class="flat" name="category">';
    print '<option selected value=""> </option>';
    if ($currentCateg == null){
        if ($num)
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    print '<option value="' . $obj->rowid . '">' . $obj->label . '</option>';
                }
                $i++;
            }

            print '</select></td></tr>';
        }

    }
    else {
        if ($num)
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    if ($currentCateg->fk_rowCategory == $obj->rowid){
                        print '<option selected value="' . $obj->rowid . '">' . $obj->label . '</option>';
                    }
                    else {
                        print '<option value="' . $obj->rowid . '">' . $obj->label . '</option>';
                    }
                }
                $i++;
            }
        }
    }
    print '</select></td></tr>';
}
else {
    print '<td><a href="'.dol_buildpath('../admin/dict.php?mainmenu=home',1).'">'.$langs->trans('NoCategory').'</a></td></tr>';
}

?>