<?php
/*  Copyright 2013-2019  John Havlik  (email : john.havlik@mtekk.us)

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
require_once(dirname(__FILE__) . '/includes/block_direct_access.php');

if(!interface_exists('WPSEO_Graph_Piece'))
{
	return;
}

class bcn_schema_breadcrumb implements WPSEO_Graph_Piece
{
	private $context;
	private $index;
	public function __construct(WPSEO_SCHEMA_Context $context)
	{
		$this->context = $context;
	}
	/**
	 * Deals with generating the schema array
	 *
	 * @return array The schema.org JSON-LD array for breadcrumbList
	 */
	public function generate()
	{
		//Grab the breadcrumb trail, reset to arrays
		$base_trail = json_decode(bcn_display_json_ld(true), true);
		//Remoe the context, it's not needed
		unset($base_trail['@context']);
		//Add the ID
		$base_trail['@id'] = $this->context->canonical . WPSEO_Schema_IDs::BREADCRUMB_HASH;
		//Loop through the breadcrumbs and set url to @id and add type=webpage
		array_walk($base_trail['itemListElement'], array($this, 'add_extra_data'));
		return $base_trail;
	}
	public function add_extra_data(&$item, $key)
	{
		$item['item']['@type'] = 'WebPage';
		$item['item']['url'] = $item['item']['@id'];
	}
	/**
	 * Very basic implementation of is needed, just exclude from 404 and front page
	 */
	public function is_needed()
	{
		if(is_front_page() || is_404())
		{
			return false;
		}
		return true;
	}
}
