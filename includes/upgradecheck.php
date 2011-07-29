<?php
/*
	These functions below handle DB upgrades, etc
*/
function pmpro_checkForUpgrades()
{
	$pmpro_db_version = pmpro_getOption("db_version");
	
	//if we can't find the DB tables, reset db_version to 0
	global $wpdb, $table_prefix;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_levels = $table_prefix . 'pmpro_membership_levels';
	$table_exists = $wpdb->query("SHOW TABLES LIKE '" . $wpdb->pmpro_membership_levels . "'");	
	if(!$table_exists)		
		$pmpro_db_version = 0;
	
	if(!$pmpro_db_version)
		$pmpro_db_version = pmpro_upgrade_1();		
}

function pmpro_upgrade_1()
{		
	/*
		default options
	*/
	$nonmembertext = "This content is for !!levels!! members only. <a href=\"" . wp_login_url() . "?action=register\">Register here</a>.";
	pmpro_setOption("nonmembertext", $nonmembertext);
	
	$notloggedintext = "Please <a href=\"" . wp_login_url( get_permalink() ) . "\">login</a> to view this content. (<a href=\"" . wp_login_url() . "?action=register\">Register here</a>.)";
	pmpro_setOption("notloggedintext", $notloggedintext);
	
	$rsstext = "This content is for members only. Visit the site and log in/register to read.";
	pmpro_setOption("rsstext", $rsstext);
	
	$gateway_environment = "sandbox";
	pmpro_setOption("gateway_environment", $gateway_environment);
	
	$pmpro_accepted_credit_cards = "Visa,Mastercard,American Express,Discover";
	pmpro_setOption("accepted_credit_cards", $pmpro_accepted_credit_cards);		
	
	$parsed = parse_url(home_url()); 
	$hostname = $parsed[host];
	$hostparts = split("\.", $hostname);				
	$email_domain = $hostparts[count($hostparts) - 2] . "." . $hostparts[count($hostparts) - 1];		
	$from_email = "wordpress@" . $email_domain;
	pmpro_setOption("from_email", $from_email);
	
	$from_name = "WordPress";
	pmpro_setOption("from_name", $from_name);
		
	
	/*
		DB table setup	
	*/
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_levels = $wpdb->prefix . 'pmpro_membership_levels';
	$wpdb->pmpro_memberships_users = $wpdb->prefix . 'pmpro_memberships_users';
	$wpdb->pmpro_memberships_categories = $wpdb->prefix . 'pmpro_memberships_categories';
	$wpdb->pmpro_memberships_pages = $wpdb->prefix . 'pmpro_memberships_pages';
	$wpdb->pmpro_membership_orders = $wpdb->prefix . 'pmpro_membership_orders';
	
	//wp_pmpro_membership_levels
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_membership_levels . "` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL,
		  `description` longtext NOT NULL,
		  `initial_payment` decimal(10,2) NOT NULL default '0.00',
		  `billing_amount` decimal(10,2) NOT NULL default '0.00',
		  `cycle_number` int(11) NOT NULL default '0',
		  `cycle_period` enum('Day','Week','Month','Year') default 'Month',
		  `billing_limit` int(11) NOT NULL COMMENT 'After how many cycles should billing stop?',
		  `trial_amount` decimal(10,2) NOT NULL default '0.00',
		  `trial_limit` int(11) NOT NULL default '0',		  
		  `allow_signups` tinyint(4) NOT NULL default '1',
		  PRIMARY KEY  (`id`),
		  KEY `allow_signups` (`allow_signups`),
		  KEY `initial_payment` (`initial_payment`),
		  KEY `name` (`name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
	";
	$wpdb->query($sqlQuery);
	
	//wp_pmpro_membership_orders
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_membership_orders . "` (
		  `id` int(11) NOT NULL auto_increment,
		  `code` varchar(10) NOT NULL,
		  `session_id` varchar(64) NOT NULL default '',
		  `user_id` int(11) NOT NULL default '0',
		  `membership_id` int(11) NOT NULL default '0',
		  `paypal_token` varchar(64) NOT NULL default '',
		  `billing_name` varchar(128) NOT NULL default '',
		  `billing_street` varchar(128) NOT NULL default '',
		  `billing_city` varchar(128) NOT NULL default '',
		  `billing_state` varchar(32) NOT NULL default '',
		  `billing_zip` varchar(16) NOT NULL default '',
		  `billing_phone` varchar(32) NOT NULL,
		  `subtotal` varchar(16) NOT NULL default '',
		  `tax` varchar(16) NOT NULL default '',
		  `couponamount` varchar(16) NOT NULL default '',
		  `certificate_id` int(11) NOT NULL default '0',
		  `certificateamount` varchar(16) NOT NULL default '',
		  `total` varchar(16) NOT NULL default '',
		  `payment_type` varchar(64) NOT NULL default '',
		  `cardtype` varchar(32) NOT NULL default '',
		  `accountnumber` varchar(32) NOT NULL default '',
		  `expirationmonth` char(2) NOT NULL default '',
		  `expirationyear` varchar(4) NOT NULL default '',
		  `status` varchar(32) NOT NULL default '',
		  `gateway` varchar(64) NOT NULL,
		  `gateway_environment` varchar(64) NOT NULL,
		  `payment_transaction_id` varchar(64) NOT NULL,
		  `subscription_transaction_id` varchar(32) NOT NULL,
		  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
		  `affiliate_id` varchar(32) NOT NULL,
		  `affiliate_subid` varchar(32) NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `code` (`code`),
		  KEY `session_id` (`session_id`),
		  KEY `user_id` (`user_id`),
		  KEY `membership_id` (`membership_id`),
		  KEY `timestamp` (`timestamp`),
		  KEY `gateway` (`gateway`),
		  KEY `gateway_environment` (`gateway_environment`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	$wpdb->query($sqlQuery);
	
	//wp_pmpro_memberships_categories
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_memberships_categories . "` (
		  `membership_id` int(11) NOT NULL,
		  `category_id` int(11) NOT NULL,
		  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  UNIQUE KEY `membership_category` (`membership_id`,`category_id`),
		  UNIQUE KEY `category_membership` (`category_id`,`membership_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	$wpdb->query($sqlQuery);
	
	//wp_pmpro_memberships_pages
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_memberships_pages . "` (
		  `membership_id` int(11) NOT NULL,
		  `page_id` int(11) NOT NULL,
		  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  UNIQUE KEY `category_membership` (`page_id`,`membership_id`),
		  UNIQUE KEY `membership_page` (`membership_id`,`page_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	$wpdb->query($sqlQuery);
	
	//wp_pmpro_memberships_users
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_memberships_users . "` (
		  `user_id` int(11) NOT NULL,
		  `membership_id` int(11) NOT NULL,
		  `initial_payment` decimal(10,2) NOT NULL,
		  `billing_amount` decimal(10,2) NOT NULL,
		  `cycle_number` int(11) NOT NULL,
		  `cycle_period` enum('Day','Week','Month','Year') NOT NULL default 'Month',
		  `billing_limit` int(11) NOT NULL,
		  `trial_amount` decimal(10,2) NOT NULL,
		  `trial_limit` int(11) NOT NULL,		  
		  `startdate` datetime NOT NULL,
		  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`user_id`),
		  KEY `membership_id` (`membership_id`),
		  KEY `modified` (`modified`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	$wpdb->query($sqlQuery);		
	
	pmpro_setOption("db_version", "1");
	return 1;
}
?>