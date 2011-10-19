<?php

/**
 * DMZ firewall pinhole add view.
 *
 * @category   ClearOS
 * @package    Dmz
 * @subpackage Views
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
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('base');
$this->lang->load('firewall');

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('dmz/pinhole/add');
echo form_header(lang('dmz_pinhole_connection'));

echo field_input('nickname', $nickname, lang('firewall_nickname'));
echo field_input('ip_address', $ip_address, lang('firewall_ip_address'));
echo field_checkbox('all', $all, lang('dmz_all_protocols_and_ports'));
echo field_simple_dropdown('protocol', $protocols, $protocol, lang('firewall_protocol'));
echo field_input('port', $port, lang('firewall_port'));

echo field_button_set(
    array(
        form_submit_add('submit', 'high'),
        anchor_cancel('/app/dmz')
    )
);

echo form_footer();
echo form_close();
