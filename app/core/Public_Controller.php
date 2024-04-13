<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Code here is run before frontend controllers
class Public_Controller extends MY_Controller{

    public $default_country;
    public $application_settings;
	public $group;
	public $selected_language_name;
	public $selected_language_id = '1';
	public $languages;

	public function __construct(){
		parent::__construct();
		$this->group = '';
		$this->config->set_item('csrf_protection', TRUE);
        $this->load->library('ion_auth');
		$this->load->model('pages/pages_m');
		$this->load->model('countries/countries_m');
		$this->load->model('settings/settings_m');
		$this->load->model('languages/languages_m');
		$this->languages = $this->languages_m->get_all();
        $frontend_theme_name = 'chamasoft';
        $this->asset->set_theme($frontend_theme_name);
        $this->application_settings = $this->settings_m->get_settings()?:'';
        
        $this->template->set_theme($frontend_theme_name)->append_metadata( '
		<script type="text/javascript">
			
		</script>' );

        $this->default_country = $this->countries_m->get_default_country();

		// Is there a layout file for this module?
		if ($this->template->layout_exists($this->module . '.html'))
		{
			$this->template->set_layout($this->module . '.html');
		}

		// Nope, just use the default layout
		elseif ($this->template->layout_exists('default.html'))
		{
			$this->template->set_layout('default.html');
		}
		$language_id = '';
		if($this->ion_auth->logged_in()){
			$this->user=$this->ion_auth->get_user();
			$language_id = isset($this->user->language_id)?$this->user->language_id:'';
		}else{
			if(isset($_COOKIE['language_id']) && $_COOKIE['language_id']){
				$language_id = $_COOKIE['language_id'];
			}else{
				$language_id = $this->application_settings->default_language_id;
			}
		}
		foreach($this->languages as $language){
			if($language->id == $language_id){
				$this->selected_language_name = $language->name;
				$this->selected_language_id = $language->country_id;
			}
		}
		if($language = $this->languages_m->get($language_id)){
            $this->lang->load('application',$language->short_code);
        }

	    // Make sure whatever page the user loads it by, its telling search robots the correct formatted URL
	    $this->template->set_metadata('canonical', site_url($this->uri->uri_string()), 'link');
        //$this->template->enable_parser(TRUE)->set_theme($frontend_theme_name)->set_layout('home.html');
	}
}
