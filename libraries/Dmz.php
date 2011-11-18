<?php

/**
 * DMZ firewall class.
 *
 * @category   Apps
 * @package    DMZ
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dmz/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\dmz;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('base');
clearos_load_language('dmz');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\firewall\Rule as Rule;
use \clearos\apps\firewall\Firewall as Firewall;
use \clearos\apps\firewall\Metadata as Metadata;

clearos_load_library('firewall/Rule');
clearos_load_library('firewall/Firewall');
clearos_load_library('firewall/Metadata');

// Exceptions
//-----------

use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\Validation_Exception as Validation_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/Validation_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * DMZ firewall class.
 *
 * @category   Apps
 * @package    DMZ
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dmz/
 */

class Dmz extends Firewall
{
    ///////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Dmz constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct();
    }

    /**
     * Add a port/to the forward allow list.
     *
     * @param string $nickname optional rule nickname
     * @param string $ip       IP address
     * @param string $protocol the protocol - UDP/TCP
     * @param int    $port     port number
     *
     * @return void
     * @throws Engine_Exception
     */

    public function add_forward_port($nickname, $ip, $protocol, $port)
    {
        clearos_profile(__METHOD__, __LINE__);

        $rule = new Rule();

        try {

            // Validation
            //-----------

            Validation_Exception::is_valid($this->validate_name($nickname));
            Validation_Exception::is_valid($this->validate_ip($ip));
            Validation_Exception::is_valid($this->validate_protocol($protocol));
            Validation_Exception::is_valid($this->validate_port($port));

            $rule->set_protocol($rule->convert_protocol_name($protocol));
            $rule->set_name($nickname);
            $rule->set_address($ip);
            $rule->set_port($port);
            $rule->set_flags(Rule::DMZ_INCOMING | Rule::ENABLED);

            $this->add_rule($rule);

        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }
    }

    /**
     * Add a port/to the pinhole allow list.
     *
     * @param string $nickname optional rule nickname
     * @param string $ip       IP address
     * @param string $protocol the protocol - UDP/TCP
     * @param int    $port     port number
     *
     * @return void
     * @throws Engine_Exception
     */

    public function add_pinhole_port($nickname, $ip, $protocol, $port)
    {
        clearos_profile(__METHOD__, __LINE__);

        $rule = new Rule();

        try {

            // Validation
            //-----------

            Validation_Exception::is_valid($this->validate_name($nickname));
            Validation_Exception::is_valid($this->validate_ip($ip));
            Validation_Exception::is_valid($this->validate_protocol($protocol));
            Validation_Exception::is_valid($this->validate_port($port));

            $rule->set_protocol($rule->convert_protocol_name($protocol));
            $rule->set_name($nickname);
            $rule->set_address($ip);
            $rule->set_port($port);
            $rule->set_flags(Rule::DMZ_PINHOLE | Rule::ENABLED);

            $this->add_rule($rule);

        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }
    }

    /**
     * Delete a port from the pinhole allow list.
     *
     * @param string $ip       target IP address
     * @param string $protocol the protocol - UDP/TCP
     * @param int    $port     to port number
     *
     * @return void
     * @throws Engine_Exception
     */

    public function delete_pinhole_port($ip, $protocol, $port)
    {
        clearos_profile(__METHOD__, __LINE__);

        $rule = new Rule();

        try {
            // Validation
            //-----------

            Validation_Exception::is_valid($this->validate_ip($ip));
            Validation_Exception::is_valid($this->validate_protocol($protocol));
            Validation_Exception::is_valid($this->validate_port($port));

            switch ($protocol) {
                case "TCP":
                    $rule->set_protocol(Firewall::PROTOCOL_TCP);
                    break;

                case "UDP":
                    $rule->set_protocol(Firewall::PROTOCOL_UDP);
                    break;
            }

            $rule->set_address($ip);
            $rule->set_port(($port) ? $port : 0);
            $rule->set_flags(Rule::DMZ_PINHOLE);
            $this->delete_rule($rule);
        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }
    }

    /**
     * Delete a port from the forward allow list.
     *
     * @param string $ip       target IP address
     * @param string $protocol the protocol - UDP/TCP
     * @param int    $port     to port number
     *
     * @return void
     * @throws Engine_Exception
     */

    public function delete_forward_port($ip, $protocol, $port)
    {
        clearos_profile(__METHOD__, __LINE__);

        $rule = new Rule();

        try {

            // Validation
            //-----------

            Validation_Exception::is_valid($this->validate_ip($ip));
            Validation_Exception::is_valid($this->validate_protocol($protocol));
            Validation_Exception::is_valid($this->validate_port($port));

            switch ($protocol) {
                case "TCP":
                    $rule->set_protocol(Firewall::PROTOCOL_TCP);
                    break;

                case "UDP":
                    $rule->set_protocol(Firewall::PROTOCOL_UDP);
                    break;
            }

            $rule->set_address($ip);
            $rule->set_port(($port) ? $port : 0);
            $rule->set_flags(Rule::DMZ_INCOMING);
            $this->delete_rule($rule);
        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }
    }

    /**
     * Enable/disable a port from the pinhole allow list.
     *
     * @param boolean $enabled  enable or disable rule?
     * @param string  $ip       target IP address
     * @param string  $protocol the protocol - UDP/TCP
     * @param int     $port     to port number
     *
     * @return void
     * @throws Engine_Exception
     */

    public function toggle_enable_pinhole_port($enabled, $ip, $protocol, $port)
    {
        clearos_profile(__METHOD__, __LINE__);

        $rule = new Rule();

        try {
            // Validation
            //-----------

            Validation_Exception::is_valid($this->validate_ip($ip));
            Validation_Exception::is_valid($this->validate_protocol($protocol));
            Validation_Exception::is_valid($this->validate_port($port));

            switch ($protocol) {
                case "TCP":
                    $rule->set_protocol(Firewall::PROTOCOL_TCP);
                    break;

                case "UDP":
                    $rule->set_protocol(Firewall::PROTOCOL_UDP);
                    break;
            }

            $rule->set_address($ip);
            $rule->set_port(($port) ? $port : 0);
            $rule->set_flags(Rule::DMZ_PINHOLE);

            if(!($rule = $this->find_rule($rule))) return;

            $this->delete_rule($rule);
            ($enabled) ? $rule->Enable() : $rule->Disable();
            $this->add_rule($rule);
        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }
    }

    /**
     * Enable/disable a port from the forward allow list.
     *
     * @param boolean $enabled  enable or disable rule?
     * @param string  $ip       target IP address
     * @param string  $protocol the protocol - UDP/TCP
     * @param int     $port     to port number
     *
     * @return void
     * @throws Engine_Exception
     */

    public function toggle_enable_forward_port($enabled, $ip, $protocol, $port)
    {
        clearos_profile(__METHOD__, __LINE__);

        $rule = new Rule();

        try {
            // Validation
            //-----------

            Validation_Exception::is_valid($this->validate_ip($ip));
            Validation_Exception::is_valid($this->validate_protocol($protocol));
            Validation_Exception::is_valid($this->validate_port($port));
            switch ($protocol) {
                case "TCP":
                    $rule->set_protocol(Firewall::PROTOCOL_TCP);
                    break;

                case "UDP":
                    $rule->set_protocol(Firewall::PROTOCOL_UDP);
                    break;
            }

            $rule->set_address($ip);
            $rule->set_port(($port) ? $port : 0);
            $rule->set_flags(Rule::DMZ_INCOMING);

            if(!($rule = $this->find_rule($rule))) return;

            $this->delete_rule($rule);
            ($enabled) ? $rule->Enable() : $rule->Disable();
            $this->add_rule($rule);
        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }
    }

    /**
     * Gets forwarded DMZ ports.  The information is an array
     * with the following hash array entries:
     *
     *  info[name]
     *  info[protocol]
     *  info[ip]
     *  info[port]
     *  info[enabled]
     *
     * @return array array list of allowed forward ports
     * @throws Engine_Exception
     */

    public function get_forward_ports()
    {
        clearos_profile(__METHOD__, __LINE__);

        $portlist = array();

        try {
            $rules = $this->get_rules();
        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }

        foreach ($rules as $rule) {
            if (!($rule->get_flags() & Rule::DMZ_INCOMING)) continue;

            $portinfo = array();

            switch ($rule->get_protocol()) {
                case Firewall::PROTOCOL_TCP:
                    $portinfo['protocol'] = "TCP";
                    break;

                case Firewall::PROTOCOL_UDP:
                    $portinfo['protocol'] = "UDP";
                    break;

                default:
                    $portinfo['protocol'] = Firewall::PROTOCOL_ALL;
                    break;
            }

            $portinfo['name'] = $rule->get_name();
            $portinfo['ip'] = $rule->get_address();
            $portinfo['port'] = $rule->get_port();
            $portinfo['enabled'] = $rule->is_enabled();

            $portlist[] = $portinfo;
        }

        return $portlist;
    }

    /**
     * Gets forwarded DMZ ports.  The information is an array
     * with the following hash array entries:
     *
     *  info[name]
     *  info[protocol]
     *  info[ip]
     *  info[port]
     *  info[enabled]
     *
     * @return array array list of allowed forward ports
     */

    public function get_pinhole_ports()
    {
        clearos_profile(__METHOD__, __LINE__);

        $portlist = array();

        try {
            $rules = $this->get_rules();
        } catch (Exception $e) {
            throw new Engine_Exception(clearos_exception_message($e), CLEAROS_ERROR);
        }

        foreach ($rules as $rule) {
            if (!($rule->get_flags() & Rule::DMZ_PINHOLE))
                continue;

            $portinfo = array();

            switch ($rule->get_protocol()) {
                case Firewall::PROTOCOL_TCP:
                    $portinfo['protocol'] = "TCP";
                    break;

                case Firewall::PROTOCOL_UDP:
                    $portinfo['protocol'] = "UDP";
                    break;

                default:
                    $portinfo['protocol'] = Firewall::PROTOCOL_ALL;
                    break;
            }

            $portinfo['name'] = $rule->get_name();
            $portinfo['ip'] = $rule->get_address();
            $portinfo['port'] = $rule->get_port();
            $portinfo['enabled'] = $rule->is_enabled();

            $portlist[] = $portinfo;
        }

        return $portlist;
    }

    ///////////////////////////////////////////////////////////////////////////////
    // V A L I D A T I O N   M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

}

// vim: syntax=php ts=4
