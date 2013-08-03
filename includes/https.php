<?php
/*
	Code related to HTTPS/SSL
*/

//this function checks if we have set the $isapage variable, and if so prevents WP from sending a 404
function pmpro_status_filter($s)
{
	global $isapage;
	if($isapage && strpos($s, "404"))
		return false;	//don't send the 404
	else
		return $s;
}

//filters links/etc to add HTTPS to URL if needed
function pmpro_https_filter($s)
{
	global $besecure;
	$besecure = apply_filters('pmpro_besecure', $besecure);
		
	if($besecure || is_ssl())
		return str_replace("http:", "https:", $s);
	else
		return str_replace("https:", "http:", $s);
}
add_filter('status_header', 'pmpro_status_filter');
add_filter('bloginfo_url', 'pmpro_https_filter');
add_filter('wp_list_pages', 'pmpro_https_filter');
add_filter('option_home', 'pmpro_https_filter');
add_filter('option_siteurl', 'pmpro_https_filter');
add_filter('logout_url', 'pmpro_https_filter');
add_filter('login_url', 'pmpro_https_filter');
add_filter('home_url', 'pmpro_https_filter');

//this function sets the besecure global which may be used in early code
/*
function pmpro_besecure_set()
{	
	global $besecure;		
	if(force_ssl_admin() || force_ssl_login() || is_ssl())
		$besecure = true;
	
	$besecure = apply_filters("pmpro_besecure", $besecure);
}
add_action('init', 'pmpro_besecure_set', 2);
*/

//this function updates the besecure global with post data and redirects if needed
function pmpro_besecure()
{
	global $besecure, $post;

	//check the post option
	if(!empty($post->ID) && !$besecure)
		$besecure = get_post_meta($post->ID, "besecure", true);
		
	//if forcing ssl on admin, be secure in admin and login page
	if(!$besecure && force_ssl_admin() && (is_admin() || pmpro_is_login_page()))
		$besecure = true;		
		
	//if forcing ssl on login, be secure on the login page
	if(!$besecure && force_ssl_login() && pmpro_is_login_page())
		$besecure = true;			
		
	$besecure = apply_filters("pmpro_besecure", $besecure);
						
	if($besecure && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off" || $_SERVER['HTTPS'] == "false"))
	{
		//need to be secure		
		wp_redirect("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}
	elseif(!$besecure && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" && $_SERVER['HTTPS'] != "false")
	{
		//don't need to be secure		
		wp_redirect("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}	
}
add_action('wp', 'pmpro_besecure', 2);
add_action('login_init', 'pmpro_besecure', 2);

//If the site URL starts with https:, then force SSL/besecure to true. (Added 1.5.2)
function pmpro_check_site_url_for_https($besecure)
{	
	global $wpdb, $pmpro_siteurl;

	//need to get this from the database because we filter get_option
	if(empty($pmpro_siteurl))
		$pmpro_siteurl = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl' LIMIT 1");		
	
	//entire site is over https?
	if(strpos($pmpro_siteurl, "https:") !== false)
		$besecure = true;
	
	return $besecure;
}
add_filter("pmpro_besecure", "pmpro_check_site_url_for_https");

//capturing case where a user links to https admin without admin over https
function pmpro_admin_https_handler()
{
	if(!empty($_SERVER['HTTPS']))
	{
		if($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off" && $_SERVER['HTTPS'] != "false" && is_admin())
		{
			if(substr(get_option("siteurl"), 0, 5) == "http:" && !force_ssl_admin())
			{
				//need to redirect to non https
				wp_redirect("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				exit;
			}
		}
	}
}
add_action('init', 'pmpro_admin_https_handler');

/*
	This code is for the "nuke" option to make URLs secure on secure pages.
*/
function pmpro_NuclearHTTPS()
{
	//did they choose the option?
	$nuking = pmpro_getOption("nuclear_HTTPS");
	if(!empty($nuking))
	{
		ob_start("pmpro_replaceURLsInBuffer");
	}
}
add_action("init", "pmpro_NuclearHTTPS");

function pmpro_replaceURLsInBuffer($buffer)
{
	global $besecure;
	
	//only swap URLs if this page is secure
	if($besecure)
	{
		/*
			okay swap out all links like these:
			* http://domain.com
			* http://anysubdomain.domain.com
			* http://any.number.of.sub.domains.domain.com
		*/
		$buffer = preg_replace("/http\:\/\/([a-zA-Z0-9\.\-]*" . str_replace(".", "\.", PMPRO_DOMAIN) . ")/i", "https://$1", $buffer);		
	}
	
	return $buffer;
}