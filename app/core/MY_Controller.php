<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Code here is run before ALL controllers
class MY_Controller extends CI_Controller {

	// Deprecated: No longer used globally
	protected $data;
	public $module;
	public $controller;
	public $method;

	public function __construct()
	{
		parent::__construct();
		
		$this->template->add_theme_location(ADDONPATH.'frontend_themes/');
		$slug ="default";
        $theme=new stdClass();
		$theme->slug = $slug;
		$theme->is_core         = 1;
		$theme->path			= ADDONPATH.'frontend_themes/'.$slug;
		$theme->web_path 		= ADDONPATH.'frontend_themes/'.$slug;
		$theme->screenshot		= $theme->web_path . '/screenshot.png';
		// Load the admin theme so things like partials and assets are available everywhere
		$this->admin_theme = $theme;
		// Load the current theme so we can set the assets right away
		$this->theme = $theme;
		// make a constant as this is used in a lot of places
		define('ADMIN_THEME', $this->admin_theme->slug);
		// Asset library needs to know where the admin theme directory is
		$this->config->set_item('asset_dir', $this->admin_theme->path.'/');
		$this->config->set_item('asset_url', BASE_URL.$this->admin_theme->web_path.'/');
		// Set the front-end theme directory
		$this->config->set_item('theme_asset_dir', dirname($this->theme->path).'/');
		$this->config->set_item('theme_asset_url', BASE_URL.dirname($this->theme->web_path).'/');

		$this->benchmark->mark('my_controller_end');
	}
}

/**
 * Returns the CI object.
 *
 * Example: ci()->db->get('table');
 *
 * @staticvar	object	$ci
 * @return		object
 */
function ci()
{
	return get_instance();
}