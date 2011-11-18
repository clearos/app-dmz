<?php

/**
 * DMZ firewall controller.
 *
 * @category   Apps
 * @package    DMZ
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dmz/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\network\Role as Role;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * DMZ firewall controller.
 *
 * @category   Apps
 * @package    DMZ
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dmz/
 */

class Dmz extends ClearOS_Controller
{
    /**
     * Firewall (egress) overview.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('dmz');
        $this->load->library('network/Iface_Manager');

        // Sanity check - make sure there is a DMZ interface configured
        //-------------

        $sanity_ok = FALSE;
        $network_interface = $this->iface_manager->get_interface_details();
        foreach ($network_interface as $interface => $detail) {
            if ($detail['role'] == Role::ROLE_DMZ)
                $sanity_ok = TRUE;
        }

        if (!$sanity_ok)
            $this->page->set_message(lang('dmz_network_not_configured'), 'warning');

        // Load views
        //-----------

        $views = array('dmz/incoming', 'dmz/pinhole');


        $this->page->view_forms($views, lang('dmz_app_name'));
    }
}
