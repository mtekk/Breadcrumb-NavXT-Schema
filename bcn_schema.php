<?php
/*
Plugin Name: Breadcrumb NavXT Schema
Plugin URI: https://mtekk.us/extensions/breadcrumb-navxt-schema
Description: Extension to Breadcrumb NavXT, injects Breadcrumb NavXT's JSON-LD schema.org data into Yoast SEO schema block. For details on how to use this plugin visit <a href="http://mtekk.us/extensions/breadcrumb-navxt-schema">Breadcrumb NavXT Schema</a>. 
Version: 1.0.1
Author: John Havlik
Author URI: http://mtekk.us/
License: GPL2
TextDomain: breadcrumb-navxt-schema
DomainPath: /languages/
*/
/*  Copyright 2013-2023  John Havlik  (email : john.havlik@mtekk.us)

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
//Do a PHP version check, require 5.2 or newer
if(version_compare(phpversion(), '5.2.0', '<'))
{
	//Only purpose of this function is to echo out the PHP version error
	function bcn__sc_phpold()
	{
		printf('<div class="error"><p>' . __('Your PHP version is too old, please upgrade to a newer version. Your version is %1$s, Breadcrumb NavXT requires %2$s', 'breadcrumb-navxt-schema') . '</p></div>', phpversion(), '5.2.0');
	}
	//If we are in the admin, let's print a warning then return
	if(is_admin())
	{
		add_action('admin_notices', 'bcn__sc_phpold');
	}
	return;
}
//Include extension base class
if(!class_exists('mtekk_plugkit_extension'))
{
	require_once(dirname(__FILE__) . '/includes/class.mtekk_plugkit_extension.php');
}
class bcn_schema extends mtekk_plugkit_extension
{
	const version = '1.0.1';
	protected $full_name = 'Breadcrumb NavXT Schema';
	protected $short_name = 'Schema';
	protected $identifier = 'breadcrumb-navxt';
	protected $unique_prefix = 'bcn_sc';
	protected $product_prefix = 'schema';
	protected $plugin_basename = '';
	protected $supported_classes = array('bcn_breadcrumb_trail');
	/**
	 * Class default constructor
	 */
	public function __construct()
	{
		//We set the plugin basename here, could manually set it, but this is for demonstration purposes
		$this->plugin_basename = plugin_basename(__FILE__);
		//add_action('plugins_loaded', array($this, 'plugins_loaded_pre_bcn'), 10);
		//We're going to make sure we load the parent's constructor
		parent::__construct();
	}
	/**
	 * Checks wheter nor not the base plugin is active or not
	 *
	 * @return bool Whether or not the base plugin is active
	 */
	public function base_plugin_active()
	{
		return class_exists('breadcrumb_navxt');
	}
	/**
	 * Checks wheter nor not the base plugin version is supported or not
	 *
	 * @return bool Whether or not the base plugin version is supported
	 */
	public function base_plugin_version_supported()
	{
		return defined('breadcrumb_navxt::version') && version_compare(breadcrumb_navxt::version, '6.0.0', '>');
	}
	/**
	 * Adds in default settings needed for Breadcrumb NavXT Menu Magic
	 *
	 * @param array $settings The settings array
	 * @return array The filtered/updated settings array
	 */
	public function settings_setup($settings)
	{
		//Don't run through parent as we are not premium
		return $settings;
	}
	public function base_plugin_inactive_notice()
	{
		sprintf('<div class="error"><p>%s</p></div>', esc_html__('Breadcrumb NavXT is required for Breadcrumb NavXT Schema to work.', 'breadcrumb-navxt-schema'));
	}
	public function base_plugin_version_unsupported_notice()
	{
		$version = __('unknown', 'breadcrumb-navxt-schema');
		//While not usefull today, in the future this will be hit
		if(defined('breadcrumb_navxt::version'))
		{
			$version = breadcrumb_navxt::version;
		}
		//Most will see this one
		else if(class_exists('breadcrumb_navxt'))
		{
			global $breadcrumb_navxt;
			$version = $breadcrumb_navxt->get_version();
		}
		sprintf('<div class="error"><p>%s</p></div>', __('Your Breadcrumb NavXT version is too old, please upgrade to a newer version. Your version is %1$s, Breadcrumb NavXT Schema requires %2$s', 'breadcrumb-navxt-schema') . '</p></div>', $version, '6.0.0');
	}
	public function admin_setup()
	{
		//require_once(dirname(__FILE__) . '/class.bcn_schema_admin.php');
		//$this->admin = new bcn_mm_admin(plugin_basename(__FILE__), $this->product_prefix, $this->full_name);
	}
	public function plugins_loaded_pre_bcn()
	{
	}
	public function plugins_loaded()
	{
		add_filter('wpseo_schema_graph_pieces', array($this, 'add_graph_piece'), 11, 2);
	}
	public function add_graph_piece($pieces, $context)
	{
		require_once(dirname(__FILE__) . '/class-schema-breadcrumb-navxt.php');
		if(class_exists('bcn_schema_breadcrumb'))
		{
			$pieces[] = new bcn_schema_breadcrumb($context);
		}
		return $pieces;
	}
}
$bcn_schema = new bcn_schema();
