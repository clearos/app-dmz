<?php

/**
 * DMZ pinhole controller.
 *
 * @category   Apps
 * @package    Dmz_Firewall
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

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\dmz\Dmz as Dmz;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * DMZ pinhole controller.
 *
 * @category   Apps
 * @package    Dmz_Firewall
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dmz/
 */

class Pinhole extends ClearOS_Controller
{
    /**
     * Dmz pinhole overview.
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->load->library('dmz/Dmz');
        $this->lang->load('dmz');

        // Load view data
        //---------------

        try {
            $data['pinholes'] = $this->dmz->get_pinhole_ports();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 

        // Load views
        //-----------

        $this->page->view_form('dmz/pinhole/summary', $data, lang('dmz_app_name'));
    }

    /**
     * Add pinhole rule.
     *
     * @return view
     */

    function add()
    {
        // Load libraries
        //---------------

        $this->load->library('dmz/Dmz');
        $this->lang->load('dmz');
        $this->lang->load('base');

        $this->form_validation->set_policy('nickname', 'dmz/Dmz', 'validate_name', TRUE);
        $this->form_validation->set_policy('ip_address', 'dmz/Dmz', 'validate_ip', TRUE);
        if ($this->input->post('all') != 'on') {
            $this->form_validation->set_policy('protocol', 'dmz/Dmz', 'validate_protocol', TRUE);
            $this->form_validation->set_policy('port', 'dmz/Dmz', 'validate_port', TRUE);
        }
        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if (($this->input->post('submit') && $form_ok)) {
            try {
                $my_protocol = $this->input->post('protocol');
                $my_port = $this->input->post('port');

                if ($this->input->post('all') == 'on') {
                    $my_protocol = Dmz::PROTOCOL_ALL;
                    $my_port = Dmz::CONSTANT_ALL_PORTS;
                }

                $this->dmz->add_pinhole_port(
                    $this->input->post('nickname'),
                    $this->input->post('ip_address'),
                    $my_protocol,
                    $my_port
                );

                $this->page->set_status_added();
                redirect('/dmz');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load view data
        //---------------

        try {
            $data['protocols'] = $this->dmz->get_protocols();
            // Only want TCP and UDP
            foreach ($data['protocols'] as $key => $protocol) {
                if ($key != Dmz::PROTOCOL_TCP && $key != Dmz::PROTOCOL_UDP)
                    unset($data['protocols'][$key]);
            }
            
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load the views
        //---------------

        $this->page->view_form('dmz/pinhole/add', $data, lang('base_add'));
    }

    /**
     * Delete pinhole rule.
     *
     * @param string $name     nickname
     * @param string $ip       IP address
     * @param string $protocol protocol
     * @param string $port     port
     *
     * @return view
     */

    function delete($name, $ip, $protocol, $port)
    {
        $confirm_uri = '/app/dmz/pinhole/destroy/' . $ip . '/' . $protocol . '/' . $port;
        $cancel_uri = '/app/dmz';
        $items = array($name. ' (' . $ip . ')');

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Destroys pinhole rule.
     *
     * @param string $ip       IP address
     * @param string $protocol protocol
     * @param string $port     port
     *
     * @return view
     */

    function destroy($ip, $protocol, $port)
    {
        // Load libraries
        //---------------

        $this->load->library('dmz/Dmz');

        // Handle form submit
        //-------------------

        try {
            $this->dmz->delete_pinhole_port($ip, $protocol, ($port) ? $port : 0);

            $this->page->set_status_deleted();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Disables port rule.
     *
     * @param string $name     nickname
     * @param string $ip       IP address
     * @param string $protocol protocol
     * @param string $port     port
     *
     * @return view
     */

    function disable($name, $ip, $protocol, $port)
    {
        try {
            $this->load->library('dmz/Dmz');

            $this->dmz->toggle_enable_pinhole_port(FALSE, $ip, $protocol, ($port) ? $port : 0);

            $this->page->set_status_disabled();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Enables port rule.
     *
     * @param string $name     nickname
     * @param string $ip       IP address
     * @param string $protocol protocol
     * @param string $port     port
     *
     * @return view
     */

    function enable($name, $ip, $protocol, $port)
    {
        try {
            $this->load->library('dmz/Dmz');

            $this->dmz->toggle_enable_pinhole_port(TRUE, $ip, $protocol, ($port) ? $port : 0);

            $this->page->set_status_enabled();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

}
