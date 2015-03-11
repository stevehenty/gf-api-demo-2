<?php
/*
Plugin Name: Gravity Forms Web API Demo 2 Add-On
Plugin URI: http://www.gravityforms.com
Description: Demonstrates signature authentication for external clients of the Gravity Forms API.
Version: 1.0
Author: stevehenty
Author URI: http://www.stevenhenty.com

------------------------------------------------------------------------
Copyright 2015 Steven Henty

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

//------------------------------------------


define( 'GF_WEB_API_DEMO_2_VERSION', '1.0' );

add_action( 'gform_loaded', array( 'GF_Web_Api_Demo_2_Bootstrap', 'load' ), 5 );

class GF_Web_Api_Demo_2_Bootstrap {

	public static function load(){

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-web-api-demo-2.php' );

		GFAddOn::register( 'GF_Web_Api_Demo_2' );
	}

}

function gf_web_api_demo_2(){
	return GF_Web_Api_Demo_2::get_instance();
}