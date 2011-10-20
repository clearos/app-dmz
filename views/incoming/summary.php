<?php

/**
 * DMZ firewall summary view.
 *
 * @category   ClearOS
 * @package    DMZ
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

use \clearos\apps\firewall\Firewall as Firewall;

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

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

$anchors = array(anchor_add('/app/dmz/incoming/add'));

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($ports as $rule) {
    $state = ($rule['enabled']) ? 'disable' : 'enable';
    $state_anchor = 'anchor_' . $state;
    $key = $rule['name'] . '/' . $rule['ip'] . '/' . $rule['protocol'] . '/' . $rule['port'];

    $item['title'] = $rule['name'];
    $item['action'] = '/app/dmz/incoming/delete/' . $key;
    $item['anchors'] = button_set(
        array(
            $state_anchor('/app/dmz/incoming/' . $state . '/' . $key, 'high'),
            anchor_delete('/app/dmz/incoming/delete/' . $key, 'low')
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

echo summary_table(
    lang('dmz_incoming_connections'),
    $anchors,
    $headers,
    $items
);
