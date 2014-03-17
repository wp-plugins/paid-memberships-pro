<?php
/*
	Addon: PMPro WooCommerce
	Slug: pmpro-woocommerce
*/
pmpro_add_addon('thirdparty', array(
		'title' => 'PMPro WooCommerce',
		'version' => '1.1',
		'widget' => 'pmpro_addon_pmpro_woocommerce_widget',
		'enabled' => function_exists('pmprowoo_add_membership_from_order')
	)
);

function pmpro_addon_pmpro_woocommerce_widget($addon)
{
?>
<img class="addon-thumb" src="<?php echo PMPRO_URL?>/adminpages/addons/images/pmpro-woocommerce.gif" />
<div class="info">							
	<p>Use WooCommerce to purchase membership or set members-only product pricing.</p>
	<div class="actions">							
		<?php if($addon['enabled']) { ?>
			<a href="<?php echo admin_url("plugins.php");?>" class="button">Enabled</a>
		<?php } elseif(file_exists(dirname(__FILE__) . "/../../../pmpro-woocommerce/pmpro-woocommerce.php")) { ?>
			<a href="<?php echo wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=pmpro-woocommerce/pmpro-woocommerce.php'), 'activate-plugin_pmpro-woocommerce/pmpro-woocommerce.php')?>" class="button button-primary">Activate</a>
		<?php } else { ?>
			<a href="<?php echo wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=pmpro-woocommerce'), 'install-plugin_pmpro-woocommerce'); ?>" class="button button-primary">Download</a>
		<?php } ?>				
	</div>						
</div> <!-- end info -->
<?php
}
