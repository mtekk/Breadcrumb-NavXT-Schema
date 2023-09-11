<?php
/*
	Copyright 2015-2021  John Havlik  (email : john.havlik@mtekk.us)

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
require_once(__DIR__ . '/block_direct_access.php');
use mtekk\adminKit\setting;
class mtekk_plugkit_extension // extends mtekk_plugkit //Eventually, someday
{
	protected $version;
	protected $full_name;
	protected $short_name;
	protected $identifier;
	protected $unique_prefix;
	protected $plugin_basename;
	protected $product_prefix;
	protected $admin;
	/**
	 * Basic constructor
	 */
	public function __construct()
	{
		add_filter('bcn_settings_init', array($this, 'settings_setup'));
		add_action('plugins_loaded', array($this, 'plugins_loaded'), 16);
	}
	/**
	 * Deals with the default licensing settings setup
	 * 
	 * @param array $settings The settings array
	 * @return array The filtered/updated settings array
	 */
	public function settings_setup($settings)
	{
		//BCN 7.0 compat
		if(class_exists('mtekk\adminKit\setting\setting_bool'))
		{
			if(!class_exists('mtekk\adminKit\setting\setting_string_nosave'))
			{
				require_once(__DIR__ . '/adminKit/setting/class-mtekk_adminkit_setting_string_nosave.php');
			}
			if(!isset($settings['S' . $this->product_prefix . '_key']))
			{
				//Add our 'default' _key option
				$settings['S' . $this->product_prefix . '_key'] = new setting\setting_string(
						$this->product_prefix . '_key',
						'',
						__('License Key', $this->identifier));
			}
			if(!isset($settings['J' . $this->product_prefix . '_key_status']))
			{
				//Add our 'default' _key_status option
				$settings['J' . $this->product_prefix . '_key_status'] = new setting\setting_string_nosave(
						$this->product_prefix . '_key_status',
						'inactive',
						'License Key Status'
						);
			}
			if(!isset($settings['J' . $this->product_prefix . '_key_site_active']))
			{
				//Add our 'default' _key_site_active option
				$settings['J' . $this->product_prefix . '_key_site_active'] = new setting\setting_string_nosave(
						$this->product_prefix . '_key_site_active',
						'',
						'License Key Activated Site'
						);
			}
		}
		//Legacy compat
		else
		{
			if(!isset($settings['S' . $this->product_prefix . '_key']))
			{
				//Add our 'default' paths_key option
				$settings['S' . $this->product_prefix . '_key'] = '';
			}
			if(!isset($settings['J' . $this->product_prefix . '_key_status']))
			{
				//Add our 'default' paths_key_status option
				$settings['J' . $this->product_prefix . '_key_status'] = 'inactive';
			}
			if(!isset($settings['J' . $this->product_prefix . '_key_site_active']))
			{
				//Add our 'default' Jmenu_magic_key_site_active option
				$settings['J' . $this->product_prefix . '_key_site_active'] = '';
			}
		}
		return $settings;
	}
	/**
	 * Template function meant to be overridden by the extending class with appropriate
	 * check for if the base plugin is active or not
	 * 
	 * @return bool Whether or not the base plugin is active
	 */
	public function base_plugin_active()
	{
		return true;
	}
	/**
	 * Template function meant to be overridden by the extending class with appropriate
	 * check for if the base plugin version is supported or not
	 * 
	 * @return bool Whether or not the base plugin version is supported
	 */
	public function base_plugin_version_supported()
	{
		return true;
	}
	/**
	 * Template function meant to be overridden by the extending class with appropriate
	 * message for the user when the required plugin is not active
	 */
	public function base_plugin_inactive_notice()
	{
		sprintf('<div class="error"><p>%s</p></div>', esc_html__('Base plugin required for this plugin to work.'));
	}
	/**
	 * Template function meant to be overridden by the extending class with appropriate
	 * message for the user when the required plugin version is not available
	 */
	public function base_plugin_version_unsupported_notice()
	{
		sprintf('<div class="error"><p>%s</p></div>', esc_html__('Base plugin is too old for this plugin to work.'));
	}
	/**
	 * Template function meant to be overridden by the extending class with appropriate
	 * class instantiation for the admin/settings screen
	 */
	public function admin_setup()
	{
		
	}
	/**
	 * Handles bugging the user if the base plugin is inactive and prepares the settings
	 * page for extension settings
	 */
	public function plugins_loaded()
	{
		//If base plugin isn't active yet, warn the user
		if(!$this->base_plugin_active())
		{
			//If we are in the admin, let's print a warning then return
			if(is_admin())
			{
				add_action('admin_notices', array($this, 'base_plugin_inactive_notice'));
			}
			return;
		}
		if(!$this->base_plugin_version_supported())
		{
			//If we are in the admin, let's print a warning then return
			if(is_admin())
			{
				add_action('admin_notices', array($this, 'base_plugin_version_unsupported_notice'));
			}
			return;
		}
		if(is_admin() && $this->base_plugin_active() && (class_exists('mtekk_adminKit') || class_exists('mtekk\adminKit\adminKit')))
		{
			//Check to see if someone else has setup the extensions settings tab
			if(has_action('bcn_after_settings_tabs', 'bcn_extensions_tab') === false)
			{
				//All versions prior to 6.3.0 used a different extensions tab format
				if(!defined('breadcrumb_navxt::version') || version_compare(breadcrumb_navxt::version, '6.2.60', '<'))
				{
					require_once(dirname(__FILE__) . '/bcn_extensions_tab_62.php');
				}
				else
				{
					require_once(dirname(__FILE__) . '/bcn_extensions_tab.php');
				}
				add_action('bcn_after_settings_tabs', 'bcn_extensions_tab');
			}
			$this->admin_setup();
		}
	}
}