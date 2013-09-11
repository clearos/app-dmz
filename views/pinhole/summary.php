<?php

/**
 * DMZ firewall summary view.
 *
 * @category   apps
 * @package    dmz
 * @subpackage views
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

use \clearos\apps\firewall\Firewall as Firewall;
use \clearos\apps\network\Network as Network;

$this->lang->load('dmz');
$this->lang->load('firewall');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('firewall_nickname'),
    lang('firewall_ip_address'),
    lang('firewall_protocol'),
    lang('firewall_port')
);

///////////////////////////////////////////////////////////////////////////////
// Anchors 
///////////////////////////////////////////////////////////////////////////////

$anchors = array(anchor_add('/app/dmz/pinhole/add'));

///////////////////////////////////////////////////////////////////////////////
// Ports
///////////////////////////////////////////////////////////////////////////////

foreach ($pinholes as $rule) {
    $key = $rule['name'] . '/' . $rule['ip'] . '/' . $rule['protocol'] . '/' . $rule['port'];
    $state = ($rule['enabled']) ? 'disable' : 'enable';
    $state_anchor = 'anchor_' . $state;

    $item['title'] = $rule['name'];
    $item['current_state'] = (bool)$rule['enabled'];
    $item['action'] = '/app/dmz/pinhole/delete/' . $key;
    $item['anchors'] = button_set(
        array(
            $state_anchor('/app/dmz/pinhole/' . $state . '/' . $key, 'high'),
            anchor_delete('/app/dmz/pinhole/delete/' . $key, 'low')
        )
    );
    $item['details'] = array(
        $rule['name'],
        $rule['ip'],
        ($rule['protocol'] == Firewall::PROTOCOL_ALL ? lang('base_all') : $rule['protocol']),
        ($rule['port'] == Firewall::CONSTANT_ALL_PORTS ? lang('base_all') : $rule['port'])
    );

    $items[] = $item;
}

sort($items);

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options = array (
    'default_rows' => 25,
    'row-enable-disable' => TRUE
);

echo summary_table(
    lang('dmz_pinhole_connections'),
    $anchors,
    $headers,
    $items,
    $options
);
