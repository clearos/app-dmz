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
        $this->load->library('network/Network');
        $this->lang->load('dmz');

        // Load the view data 
        //------------------- 
        try {
//            $data['ports'] = $this->egress->get_exception_ports();
 //           $data['ranges'] = $this->egress->get_exception_port_ranges();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('dmz/pinhole/summary', $data, lang('dmz_destination_ports'));
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

        // Set validation rules
        //---------------------

        $is_action = FALSE;

        if ($this->input->post('submit_standard')) {
            $this->form_validation->set_policy('service', 'dmz/Dmz', 'validate_service', TRUE);
            $is_action = TRUE;
        } else if ($this->input->post('submit_port')) {
            $this->form_validation->set_policy('port_nickname', 'dmz/Dmz', 'validate_name', TRUE);
            $this->form_validation->set_policy('port_protocol', 'dmz/Dmz', 'validate_protocol', TRUE);
            $this->form_validation->set_policy('port', 'dmz/Dmz', 'validate_port', TRUE);
            $is_action = TRUE;
        } else if ($this->input->post('submit_range')) {
            $this->form_validation->set_policy('range_nickname', 'dmz/Dmz', 'validate_name', TRUE);
            $this->form_validation->set_policy('range_protocol', 'dmz/Dmz', 'validate_protocol', TRUE);
            $this->form_validation->set_policy('range_from', 'dmz/Dmz', 'validate_port', TRUE);
            $this->form_validation->set_policy('range_to', 'dmz/Dmz', 'validate_port', TRUE);
            $is_action = TRUE;
        }

        // Handle form submit
        //-------------------

        if ($is_action && $this->form_validation->run()) {
            try {
                if ($this->input->post('submit_standard')) {
                    $this->egress->add_exception_standard_service($this->input->post('service'));
                } else if ($this->input->post('submit_port')) {
                    $this->egress->add_exception_port(
                        $this->input->post('port_nickname'),
                        $this->input->post('port_protocol'),
                        $this->input->post('port')
                    );
                } else if ($this->input->post('submit_range')) {
                    $this->egress->add_exception_port_range(
                        $this->input->post('range_nickname'),
                        $this->input->post('range_protocol'),
                        $this->input->post('range_from'),
                        $this->input->post('range_to')
                    );
                }

                $this->page->set_status_added();
                redirect('/dmz');
            } catch (Exception $e) {
                $this->page->set_message(clearos_exception_message($e));
            }
        }

        // FIXME: trim services list for rules that are already enabled
        $data['services'] = $this->egress->get_standard_service_list();
        $data['protocols'] = $this->egress->get_protocols();
        // Only want TCP and UDP
        foreach ($data['protocols'] as $key => $protocol) {
            if ($key != Dmz::PROTOCOL_TCP && $key != Dmz::PROTOCOL_UDP)
                unset($data['protocols'][$key]);
        }
            
        // Load the views
        //---------------

        $this->page->view_form('dmz/port/add', $data, lang('base_add'));
    }

    /**
     * Delete port rule.
     *
     * @param string  $protocol protocol
     * @param integer $port     port
     *
     * @return view
     */

    function delete($protocol, $port)
    {
        $confirm_uri = '/app/dmz/port/destroy/' . $protocol . '/' . $port;
        $cancel_uri = '/app/dmz';
        $items = array($protocol . ' ' . $port);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Delete port range rule.
     *
     * @param string  $protocol protocol
     * @param integer $from     from port
     * @param integer $to       to port
     *
     * @return view
     */

    function delete_range($protocol, $from, $to)
    {
        $confirm_uri = '/app/dmz/port/destroy_range/' . $protocol . '/' . $from . '/' . $to;
        $cancel_uri = '/app/dmz';
        $items = array($protocol . ' ' . $from . ':' . $to);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Destroys port rule.
     *
     * @param string  $protocol protocol
     * @param integer $port     port
     *
     * @return view
     */

    function destroy($protocol, $port)
    {
        // Load libraries
        //---------------

        $this->load->library('dmz/Dmz');

        // Handle form submit
        //-------------------

        try {
            $this->egress->delete_exception_port($protocol, $port);

            $this->page->set_status_deleted();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Destroys port range rule.
     *
     * @param string  $protocol protocol
     * @param integer $from     from port
     * @param integer $to       to port
     *
     * @return view
     */

    function destroy_range($protocol, $from, $to)
    {
        // Load libraries
        //---------------

        $this->load->library('dmz/Dmz');

        // Handle form submit
        //-------------------

        try {
            $this->egress->delete_exception_port_range($protocol, $from, $to);

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
     * @param string  $protocol protocol
     * @param integer $port     port
     *
     * @return view
     */

    function disable($protocol, $port)
    {
        try {
            $this->load->library('dmz/Dmz');

            $this->egress->toggle_enable_exception_port(FALSE, $protocol, $port);

            $this->page->set_status_disabled();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Disables range rule.
     *
     * @param string  $protocol protocol
     * @param integer $from     from port
     * @param integer $to       to port
     *
     * @return view
     */

    function disable_range($protocol, $from, $to)
    {
        try {
            $this->load->library('dmz/Dmz');

            $this->egress->toggle_enable_exception_port_range(FALSE, $protocol, $from, $to);

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
     * @param string  $protocol protocol
     * @param integer $port     port
     *
     * @return view
     */

    function enable($protocol, $port)
    {
        try {
            $this->load->library('dmz/Dmz');

            $this->egress->toggle_enable_exception_port(TRUE, $protocol, $port);

            $this->page->set_status_enabled();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Enables range rule.
     *
     * @param string  $protocol protocol
     * @param integer $from     from port
     * @param integer $to       to port
     *
     * @return view
     */

    function enable_range($protocol, $from, $to)
    {
        try {
            $this->load->library('dmz/Dmz');

            $this->egress->toggle_enable_exception_port_range(TRUE, $protocol, $from, $to);

            $this->page->set_status_enabled();
            redirect('/dmz');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }
}
