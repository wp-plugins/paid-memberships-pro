<?php
	global $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_requirebilling, $pmpro_level, $tospage;
	global $username, $password, $password2, $bfirstname, $blastname, $baddress1, $bcity, $bstate, $bzipcode, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth,$ExpirationYear;
?>
<?php if($pmpro_level) { ?>
<p>You have selected the <strong><?=$pmpro_level->name?></strong> membership level. <small><a href="<?php echo pmpro_url("levels"); ?>">change</a></small></p>
<p><?=pmpro_getLevelCost($pmpro_level)?></p>
<?php } ?>

<form class="pmpro_form" action="<?=pmpro_url("checkout", "", "https")?>" method="post">

	<input type="hidden" name="level" value="<?=$pmpro_level->id?>" />		
	<?php if($pmpro_msg) 
		{
	?>
		<div class="pmpro_message <?=$pmpro_msgt?>"><?=$pmpro_msg?></div>
	<?php
		}
	?>
	
	<?php if(!$current_user->ID) { ?>
	<table class="pmpro_checkout" width="100%" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th>
				<span class="pmpro_thead-msg">If you already have an account, <a href="<?=get_bloginfo("url")?>/wp-login.php?redirect_to=<?=urlencode(pmpro_url("checkout", "?level=" . $pmpro_level->id))?>">log in here</a>.</span>Account Information
			</th>						
		</tr>
	</thead>
	<tbody>                
		<tr>
			<td>
				<div>
					<label for="username">Username</label>
					<input id="username" name="username" type="text" class="input" size="30" value="<?=$username?>" /> 
				</div>
				
				<?php
					do_action('pmpro_checkout_after_username');
				?>
				
				<div>
					<label for="password">Password</label>
					<input id="password" name="password" type="password" class="input" size="30" value="<?=$password?>" /> 
				</div>
				<div>
					<label for="password2">Confirm Password</label>
					<input id="password2" name="password2" type="password" class="input" size="30" value="<?=$password2?>" /> 
				</div>
				
				<?php
					do_action('pmpro_checkout_after_password');
				?>
				
				<div>
					<label for="bemail">E-mail Address</label>
					<input id="bemail" name="bemail" type="text" class="input" size="30" value="<?=$bemail?>" /> 
				</div>
				<div>
					<label for="bconfirmemail">Confirm E-mail</label>
					<input id="bconfirmemail" name="bconfirmemail" type="text" class="input" size="30" value="<?=$bconfirmemail?>" /> 
				</div>
				
				<?php
					do_action('pmpro_checkout_after_email');
				?>
				
				<div class="pmpro_hidden">
					<label for="fullname">Full Name</label>
					<input id="fullname" name="fullname" type="text" class="input" size="30" value="<?=$fullname?>" /> <strong>LEAVE THIS BLANK</strong>
				</div>				

				<div class="pmpro_captcha">
				<?php 																								
					global $recaptcha, $recaptcha_publickey;
					if($recaptcha == 2 || ($recaptcha == 1 && !(float)$pmpro_level->billing_amount && !(float)$pmpro_level->trial_amount)) 
					{											
						echo recaptcha_get_html($recaptcha_publickey, NULL, true);
					}								
				?>								
				</div>
				
				<?php
					do_action('pmpro_checkout_after_captcha');
				?>
				
			</td>
	</table>   
	<?php } else { ?>                        	                       										
		<p>You are logged in as <strong><?=$current_user->user_login?></strong>. If you would like to use a different account for this membership, <a href="<?=wp_logout_url(pmpro_url("checkout", "?level=" . $pmpro_level->id));?>">log out now</a>.</p>
	<?php } ?>
	
	<?php					
		if($tospage)
		{						
		?>
		<table class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
		<tr>
			<th><?=$tospage->post_title?></th>
		</tr>
	</thead>
		<tbody>
			<tr class="odd">
				<td>								
					<div id="pmpro_license">
<?=wpautop($tospage->post_content)?>
					</div>								
					<input type="checkbox" name="tos" value="1" /> I agree to the <?=$tospage->post_title?>
				</td>
			</tr>
		</tbody>
		</table>
		<?php
		}
	?>
	
	<?php if($pmpro_requirebilling) { ?>				
	<table class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th>Billing Address</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<div>
					<label for="bfirstname">First Name</label>
					<input id="bfirstname" name="bfirstname" type="text" class="input" size="30" value="<?=$bfirstname?>" /> 
				</div>	
				<div>
					<label for="blastname">Last Name</label>
					<input id="blastname" name="blastname" type="text" class="input" size="30" value="<?=$blastname?>" /> 
				</div>					
				<div>
					<label for="baddress1">Address 1</label>
					<input id="baddress1" name="baddress1" type="text" class="input" size="30" value="<?=$baddress1?>" /> 
				</div>
				<div>
					<label for="baddress2">Address 2</label>
					<input id="baddress2" name="baddress2" type="text" class="input" size="30" value="<?=$baddress2?>" /> <small class="lite">(optional)</small>
				</div>
				<div>
					<label for="bcity_state_zip">City, State Zip</label>
					<input id="bcity" name="bcity" type="text" class="input" size="14" value="<?=$bcity?>" />, <input id="bstate" name="bstate" type="text" class="input" size="2" value="<?=$bstate?>" /> <input id="bzipcode" name="bzipcode" type="text" class="input" size="5" value="<?=$bzipcode?>" /> 
				</div>
				<div>
					<label for="bphone">Phone</label>
					<input id="bphone" name="bphone" type="text" class="input" size="30" value="<?=$bphone?>" /> 
				</div>		
				<?php if($current_user->ID) { ?>
				<?php
					if(!$bemail && $current_user->user_email)									
						$bemail = $current_user->user_email;
					if(!$bconfirmemail && $current_user->user_email)									
						$bconfirmemail = $current_user->user_email;									
				?>
				<div>
					<label for="bemail">E-mail Address</label>
					<input id="bemail" name="bemail" type="text" class="input" size="30" value="<?=$bemail?>" /> 
				</div>
				<div>
					<label for="bconfirmemail">Confirm E-mail</label>
					<input id="bconfirmemail" name="bconfirmemail" type="text" class="input" size="30" value="<?=$bconfirmemail?>" /> 

				</div>	                        
				<?php } ?>    
			</td>						
		</tr>											
	</tbody>
	</table>                   
		
	<?php
		$pmpro_accepted_credit_cards = pmpro_getOption("accepted_credit_cards");
		$pmpro_accepted_credit_cards = split(",", $pmpro_accepted_credit_cards);
		if(count($pmpro_accepted_credit_cards) == 1)
		{
			$pmpro_accepted_credit_cards_string = $pmpro_accepted_credit_cards[0];
		}
		elseif(count($pmpro_accepted_credit_cards) == 2)
		{
			$pmpro_accepted_credit_cards_string = $pmpro_accepted_credit_cards[0] . " and " . $pmpro_accepted_credit_cards[1];
		}
		elseif(count($pmpro_accepted_credit_cards) > 2)
		{
			$allbutlast = $pmpro_accepted_credit_cards;
			unset($allbutlast[count($allbutlast) - 1]);
			$pmpro_accepted_credit_cards_string = implode(", ", $allbutlast) . ", and " . $pmpro_accepted_credit_cards[count($pmpro_accepted_credit_cards) - 1];
		}
	?>
	
	<table class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th colspan="2"><span class="pmpro_thead-msg">We Accept <?=$pmpro_accepted_credit_cards_string?></span>Credit Card Information</th>
		</tr>
	</thead>
	<tbody>                    
		<tr valign="top">		
			<td>	
				<?php
					$sslseal = pmpro_getOption("sslseal");
					if($sslseal)
					{
					?>
						<div class="pmpro_sslseal"><?=stripslashes($sslseal)?></div>
					<?php
					}
				?>
				<div>
					<label for="CardType">Card Type</label>
					<select name="CardType">
						<?php foreach($pmpro_accepted_credit_cards as $cc) { ?>
							<option value="<?=$cc?>" <?php if($CardType == $cc) { ?>selected="selected"<?php } ?>><?=$cc?></option>
						<?php } ?>												
					</select> 
				</div>
			
				<div>
					<label for="AccountNumber">Card Number</label>
					<input id="AccountNumber" name="AccountNumber"  class="input" type="text" size="25" value="<?=$AccountNumber?>" /> 
				</div>
			
				<div>
					<label for="ExpirationMonth">Expiration Date</label>
					<select name="ExpirationMonth">
						<option value="01" <?php if($ExpirationMonth == "01") { ?>selected="selected"<?php } ?>>01</option>
						<option value="02" <?php if($ExpirationMonth == "02") { ?>selected="selected"<?php } ?>>02</option>
						<option value="03" <?php if($ExpirationMonth == "03") { ?>selected="selected"<?php } ?>>03</option>
						<option value="04" <?php if($ExpirationMonth == "04") { ?>selected="selected"<?php } ?>>04</option>
						<option value="05" <?php if($ExpirationMonth == "05") { ?>selected="selected"<?php } ?>>05</option>
						<option value="06" <?php if($ExpirationMonth == "06") { ?>selected="selected"<?php } ?>>06</option>
						<option value="07" <?php if($ExpirationMonth == "07") { ?>selected="selected"<?php } ?>>07</option>
						<option value="08" <?php if($ExpirationMonth == "08") { ?>selected="selected"<?php } ?>>08</option>
						<option value="09" <?php if($ExpirationMonth == "09") { ?>selected="selected"<?php } ?>>09</option>
						<option value="10" <?php if($ExpirationMonth == "10") { ?>selected="selected"<?php } ?>>10</option>
						<option value="11" <?php if($ExpirationMonth == "11") { ?>selected="selected"<?php } ?>>11</option>
						<option value="12" <?php if($ExpirationMonth == "12") { ?>selected="selected"<?php } ?>>12</option>
					</select>/<select name="ExpirationYear">
						<?php
							for($i = date("Y"); $i < date("Y") + 10; $i++)
							{
						?>
							<option value="<?=$i?>" <?php if($ExpirationYear == $i) { ?>selected="selected"<?php } ?>><?=$i?></option>
						<?php
							}
						?>
					</select> 
				</div>
			
				<div>
					<label for="CVV">CVV</label>
					<input class="input" id="CVV" name="CVV" type="text" size="4" value="" />  <small>(<a href="#" onclick="javascript:window.open('<?=plugins_url( "/pages/popup-cvv.html", dirname(__FILE__))?>','cvv','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=475');">what's this?</a>)</small>
				</div>
			</td>			
		</tr>
	</tbody>
	</table>	
	
	<div align="center">
		<input type="hidden" name="submit-checkout" value="1" />
		<input type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="Submit and Checkout &raquo;" />
	</div>	
	
	<?php } else { ?>
	<div align="center">
		<input type="hidden" name="submit-checkout" value="1" />
		<input type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="Submit and Confirm &raquo;" />
	</div>
	<?php } ?>	
	
</form>
<script>
	// Find ALL <form> tags on your page
	jQuery('form').submit(function(){
		// On submit disable its submit button
		jQuery('input[type=submit]', this).attr('disabled', 'disabled');
		jQuery('input[type=image]', this).attr('disabled', 'disabled');
	});
</script>