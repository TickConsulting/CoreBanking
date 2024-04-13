<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Themes{
	public function __construct(){
		$this->ci= & get_instance();
	}

	public function generate_theme_style_sheet($theme = array()){
		if($theme){
			echo "
<style>
html,.page-container{
    background: ".$theme->primary_background_color.";
}
a {
    text-shadow: none;
    color: ".$theme->secondary_text_color.";
}
a:hover {
    cursor: pointer;
    color: ".$theme->secondary_text_color.";
}

.btn.blue:not(.btn-outline) {
    color: #FFF;
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_text_color.";
}

.btn.blue:not(.btn-outline).active, .btn.blue:not(.btn-outline):active, .btn.blue:not(.btn-outline):hover, .open>.btn.blue:not(.btn-outline).dropdown-toggle {
    color:".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->primary_background_color.";
}

.btn.blue:not(.btn-outline).focus, .btn.blue:not(.btn-outline):focus {
    color:  ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->primary_border_color.";
}

.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.active>a, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.active.open>a, .page-sidebar .page-sidebar-menu>li.active>a, .page-sidebar .page-sidebar-menu>li.active.open>a {
    background: ".$theme->tertiary_background_color.";
    color: ".$theme->secondary_text_color.";
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.active>a>i, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.active.open>a>i, .page-sidebar .page-sidebar-menu>li.active>a>i, .page-sidebar .page-sidebar-menu>li.active.open>a>i {
    color: ".$theme->secondary_text_color.";
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.active>a:hover, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.active.open>a:hover, .page-sidebar .page-sidebar-menu>li.active>a:hover, .page-sidebar .page-sidebar-menu>li.active.open>a:hover {
    background: ".$theme->tertiary_background_color.";
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li:hover>a, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.open>a, .page-sidebar .page-sidebar-menu>li:hover>a, .page-sidebar .page-sidebar-menu>li.open>a {
    background: ".$theme->tertiary_background_color.";
    color: ".$theme->secondary_text_color.";
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li>a>i, .page-sidebar .page-sidebar-menu .sub-menu>li>a>i {
    color: ".$theme->secondary_text_color.";
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li:hover>a>i, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li.open>a>i, .page-sidebar .page-sidebar-menu>li:hover>a>i, .page-sidebar .page-sidebar-menu>li.open>a>i {
    color: ".$theme->secondary_text_color.";
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li>a>i, .page-sidebar .page-sidebar-menu>li>a>i {
    color:".$theme->secondary_text_color.";
}

.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li:hover>a, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li.open>a, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li.active>a, .page-sidebar .page-sidebar-menu .sub-menu>li:hover>a, .page-sidebar .page-sidebar-menu .sub-menu>li.open>a, .page-sidebar .page-sidebar-menu .sub-menu>li.active>a {
    color: ".$theme->secondary_text_color.";
    background: ".$theme->tertiary_background_color." !important;
}
.page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li:hover>a>i, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li.open>a>i, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu .sub-menu>li.active>a>i, .page-sidebar .page-sidebar-menu .sub-menu>li:hover>a>i, .page-sidebar .page-sidebar-menu .sub-menu>li.open>a>i, .page-sidebar .page-sidebar-menu .sub-menu>li.active>a>i {
    color: ".$theme->secondary_text_color.";
}
.dashboard-stat.green {
    background-color: ".$theme->quaternary_background_color.";
}


.dashboard-stat.blue {
    background-color: ".$theme->secondary_background_color.";
}

.tabbable-line>.nav-tabs>li.active {
    background: 0;
    border-bottom: 4px solid ".$theme->secondary_border_color.";
}

.font-green,.font-green-haze  {
    color: ".$theme->secondary_text_color." !important;
}
.label-info {
    background-color: ".$theme->secondary_background_color.";
}
.label-success {
    background-color: ".$theme->secondary_background_color.";
}

.btn-primary {
    color: #fff;
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_text_color.";
}
.btn.green:not(.btn-outline) ,.btn.green:not(.btn-outline):hover{
    color: ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}
.btn.green:not(.btn-outline):active:hover, .btn.green:not(.btn-outline):active:focus, .btn.green:not(.btn-outline):active.focus, .btn.green:not(.btn-outline).active:hover, .btn.green:not(.btn-outline).active:focus, .btn.green:not(.btn-outline).active.focus, .open>.btn.green:not(.btn-outline).dropdown-toggle:hover, .open>.btn.green:not(.btn-outline).dropdown-toggle:focus, .open>.btn.green:not(.btn-outline).dropdown-toggle.focus {
    color: ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn.blue:not(.btn-outline).disabled.focus, .btn.blue:not(.btn-outline).disabled:focus, .btn.blue:not(.btn-outline).disabled:hover, .btn.blue:not(.btn-outline)[disabled].focus, .btn.blue:not(.btn-outline)[disabled]:focus, .btn.blue:not(.btn-outline)[disabled]:hover, fieldset[disabled] .btn.blue:not(.btn-outline).focus, fieldset[disabled] .btn.blue:not(.btn-outline):focus, fieldset[disabled] .btn.blue:not(.btn-outline):hover {
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn.blue:not(.btn-outline):active:hover, .btn.blue:not(.btn-outline):active:focus, .btn.blue:not(.btn-outline):active.focus, .btn.blue:not(.btn-outline).active:hover, .btn.blue:not(.btn-outline).active:focus, .btn.blue:not(.btn-outline).active.focus, .open>.btn.blue:not(.btn-outline).dropdown-toggle:hover, .open>.btn.blue:not(.btn-outline).dropdown-toggle:focus, .open>.btn.blue:not(.btn-outline).dropdown-toggle.focus {
    color: ".$theme->primary_text_color.";
    background-color:  ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn.blue:not(.btn-outline):active:hover, .btn.blue:not(.btn-outline):active:focus, .btn.blue:not(.btn-outline):active.focus, .btn.blue:not(.btn-outline).active:hover, .btn.blue:not(.btn-outline).active:focus, .btn.blue:not(.btn-outline).active.focus, .open>.btn.blue:not(.btn-outline).dropdown-toggle:hover, .open>.btn.blue:not(.btn-outline).dropdown-toggle:focus, .open>.btn.blue:not(.btn-outline).dropdown-toggle.focus {
    color: ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn-primary:hover {
    color: ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn-primary:active, .btn-primary.active, .open>.btn-primary.dropdown-toggle{
    color:  ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn-primary:active:hover, .btn-primary:active:focus, .btn-primary:active.focus, .btn-primary.active:hover, .btn-primary.active:focus, .btn-primary.active.focus, .open>.btn-primary.dropdown-toggle:hover, .open>.btn-primary.dropdown-toggle:focus, .open>.btn-primary.dropdown-toggle.focus{
    color: ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}

.btn-primary:focus, .btn-primary.focus {
    color: ".$theme->primary_text_color.";
    background-color: ".$theme->secondary_background_color.";
    border-color: ".$theme->secondary_border_color.";
}
 .form-wizard .steps>li.active>a.step .number,.progress-bar-success{
    background-color: ".$theme->secondary_text_color.";
}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
    color: #555;
    background-color: #ddd;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
    cursor: default;
}
</style>
			";
		}
	}
}