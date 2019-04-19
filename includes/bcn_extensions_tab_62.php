<?php
/*  
	Copyright 2013-2019  John Havlik  (email : john.havlik@mtekk.us)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once(dirname(__FILE__) . '/block_direct_access.php');
function bcn_extensions_tab($opt)
{
	?>
	<fieldset id="extensions" class="bcn_options">
		<h3 class="tab-title" title="<?php _e('The settings for Breadcrumb NavXT extension plugins.', 'breadcrumb-navxt');?>"><?php _e('Extensions', 'breadcrumb-navxt'); ?></h3>
		<?php do_action('bcn_after_settings_tab_extensions', $opt); ?>
	</fieldset>
	<?php
}