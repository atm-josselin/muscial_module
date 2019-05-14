<?php
/* Copyright (C) 2019 SuperAdmin
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
 * \file    core/triggers/interface_10_all_MusicalTriggers.class.php
 * \ingroup musical
 * \brief   Example trigger.
 *
 * Put detailed description here.
 *
 * \remarks You can create other triggers by copying this one.
 * - File name should be either:
 *      - interface_99_modMusical_MyTrigger.class.php
 *      - interface_99_all_MyTrigger.class.php
 * - The file must stay in core/triggers
 * - The class name must be InterfaceMytrigger
 * - The constructor method must be named InterfaceMytrigger
 * - The name property name must be MyTrigger
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';

/**
 *  Class of triggers for Musical module
 */
class InterfaceMusicalTriggers extends DolibarrTriggers
{
    /**
     * @var DoliDB Database handler
     */
    protected $db;

    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;

        $this->name = preg_replace('/^Interface/i', '', get_class($this));
        $this->family = "demo";
        $this->description = "Musical triggers.";
        // 'development', 'experimental', 'dolibarr' or version
        $this->version = 'development';
        $this->picto = 'musical@musical';
    }

    /**
     * Trigger name
     *
     * @return string Name of trigger file
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Trigger description
     *
     * @return string Description of trigger file
     */
    public function getDesc()
    {
        return $this->description;
    }


    /**
     * Function called when a Dolibarrr business event is done.
     * All functions "runTrigger" are triggered if file
     * is inside directory core/triggers
     *
     * @param string $action Event action code
     * @param CommonObject $object Object
     * @param User $user Object user
     * @param Translate $langs Object langs
     * @param Conf $conf Object conf
     * @return int                    <0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
    {
        if (empty($conf->musical->enabled)) return 0;     // Module not active, we do nothing

        // Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action

        switch ($action) {

            case 'PRODUCT_DELETE': // If a product is deleted, we need to delete the links with instruments too
                $error = 0;
                $sql = "SELECT * FROM ".MAIN_DB_PREFIX."musical_instrument WHERE fk_product=" . $object->id;
                $resql = $this->db->query($sql);
                if ($resql) {
                    $num = $this->db->num_rows($resql);
                    $i = 0;
                    if ($num) {
                        while ($i < $num) {
                            $obj = $this->db->fetch_object($resql);
                            if ($obj) {
                                $sql = "UPDATE ".MAIN_DB_PREFIX."musical_instrument SET fk_product='NULL' WHERE rowid='".$obj->rowid."';";
                                $this->db->query($sql);
                            }
                            $i++;
                        }
                    }
                }

                break;

            default:
                dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
                break;
        }
        return 0;
    }
}