<?php

/**
 * Javascript helper for DMZ.
 *
 * @category   Apps
 * @package    Mail_Archive
 * @subpackage Javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearcenter.com/support/documentation/clearos/dmz/
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
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

clearos_load_language('dmz');
clearos_load_language('base');

header('Content-Type: application/x-javascript');

echo "

function check_protocol_port() {
    if ($('#all:checked').val() == undefined) {
        $('#port').attr('disabled', false);
        $('#protocol').attr('disabled', false);
    } else {
        $('#port').attr('disabled', true);
        $('#protocol').attr('disabled', true);
    }
}

$(document).ready(function() {

    if ($(location).attr('href').match('.*\/incoming|pinhole\/.*$') != null) {
        $('#port').css('width', '50');

        check_protocol_port();
        $('#all').change(function(event) {
            check_protocol_port();
        });
    }

});

";
// vim: syntax=php ts=4
