<?php	
	require_once(dirname(__FILE__) . "/class.pmprogateway.php");
	class PMProGateway_check
	{
		function PMProGateway_check($gateway = NULL)
		{
			$this->gateway = $gateway;
			return $this->gateway;
		}										
		
		function process(&$order)
		{
			//clean up a couple values
			$order->payment_type = "Check";
			$order->CardType = "";
			$order->cardtype = "";
			
			//check for initial payment
			if(floatval($order->InitialPayment) == 0)
			{
				//auth first, then process
				if($this->authorize($order))
				{						
					$this->void($order);										
					if(!pmpro_isLevelTrial($order->membership_level))
					{
						//subscription will start today with a 1 period trial
						$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";
						$order->TrialBillingPeriod = $order->BillingPeriod;
						$order->TrialBillingFrequency = $order->BillingFrequency;													
						$order->TrialBillingCycles = 1;
						$order->TrialAmount = 0;
						
						//add a billing cycle to make up for the trial, if applicable
						if(!empty($order->TotalBillingCycles))
							$order->TotalBillingCycles++;
					}
					elseif($order->InitialPayment == 0 && $order->TrialAmount == 0)
					{
						//it has a trial, but the amount is the same as the initial payment, so we can squeeze it in there
						$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";														
						$order->TrialBillingCycles++;
						
						//add a billing cycle to make up for the trial, if applicable
						if($order->TotalBillingCycles)
							$order->TotalBillingCycles++;
					}
					else
					{
						//add a period to the start date to account for the initial payment
						$order->ProfileStartDate = date("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod)) . "T0:0:0";				
					}
					
					$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
					return $this->subscribe($order);
				}
				else
				{
					if(empty($order->error))
						$order->error = "Unknown error: Authorization failed.";
					return false;
				}
			}
			else
			{
				//charge first payment
				if($this->charge($order))
				{							
					//setup recurring billing					
					if(pmpro_isLevelRecurring($order->membership_level))
					{						
						if(!pmpro_isLevelTrial($order->membership_level))
						{
							//subscription will start today with a 1 period trial
							$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";
							$order->TrialBillingPeriod = $order->BillingPeriod;
							$order->TrialBillingFrequency = $order->BillingFrequency;													
							$order->TrialBillingCycles = 1;
							$order->TrialAmount = 0;
							
							//add a billing cycle to make up for the trial, if applicable
							if(!empty($order->TotalBillingCycles))
								$order->TotalBillingCycles++;
						}
						elseif($order->InitialPayment == 0 && $order->TrialAmount == 0)
						{
							//it has a trial, but the amount is the same as the initial payment, so we can squeeze it in there
							$order->ProfileStartDate = date("Y-m-d") . "T0:0:0";														
							$order->TrialBillingCycles++;
							
							//add a billing cycle to make up for the trial, if applicable
							if(!empty($order->TotalBillingCycles))
								$order->TotalBillingCycles++;
						}
						else
						{
							//add a period to the start date to account for the initial payment
							$order->ProfileStartDate = date("Y-m-d", strtotime("+ " . $this->BillingFrequency . " " . $this->BillingPeriod)) . "T0:0:0";				
						}
						
						$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
						if($this->subscribe($order))
						{
							return true;
						}
						else
						{
							if($this->void($order))
							{
								if(!$order->error)
									$order->error = "Unknown error: Payment failed.";							
							}
							else
							{
								if(!$order->error)
									$order->error = "Unknown error: Payment failed.";
								
								$order->error .= " A partial payment was made that we could not void. Please contact the site owner immediately to correct this.";
							}
							
							return false;								
						}
					}
					else
					{
						//only a one time charge
						$order->status = apply_filters("pmpro_check_status_after_checkout", "success");	//saved on checkout page											
						return true;
					}
				}
				else
				{
					if(empty($order->error))
						$order->error = "Unknown error: Payment failed.";
					
					return false;
				}	
			}	
		}
		
		function authorize(&$order)
		{
			//create a code for the order
			if(empty($order->code))
				$order->code = $order->getRandomCode();
			
			//simulate a successful authorization
			$order->payment_transaction_id = "CHECK" . $order->code;
			$order->updateStatus("authorized");													
			return true;					
		}
		
		function void(&$order)
		{
			//need a transaction id
			if(empty($order->payment_transaction_id))
				return false;
				
			//simulate a successful void
			$order->payment_transaction_id = "CHECK" . $order->code;
			$order->updateStatus("voided");					
			return true;
		}	
		
		function charge(&$order)
		{
			//create a code for the order
			if(empty($order->code))
				$order->code = $order->getRandomCode();
			
			//simulate a successful charge
			$order->payment_transaction_id = "CHECK" . $order->code;
			$order->updateStatus("success");					
			return true;						
		}
		
		function subscribe(&$order)
		{
			//create a code for the order
			if(empty($order->code))
				$order->code = $order->getRandomCode();
						
			//simulate a successful subscription processing
			$order->status = "success";		
			$order->subscription_transaction_id = "CHECK" . $order->code;				
			return true;
		}	
		
		function update(&$order)
		{
			//simulate a successful billing update
			return true;
		}
		
		function cancel(&$order)
		{
			//require a subscription id
			if(empty($order->subscription_transaction_id))
				return false;
			
			//simulate a successful cancel			
			$order->updateStatus("cancelled");					
			return true;
		}	
	}
?>
