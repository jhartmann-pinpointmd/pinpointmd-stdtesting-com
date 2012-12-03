<?php
require_once './wp-load.php';
require_once TEMPLATEPATH . '/functions-customer-checkout.php';

global $wpdb;

ppSetupPartnerPromo();

$whiteList = array('labName','labAddr','labCity','labState','labZipcode',
'labID','labPhone','labHours','codeString','nameString','priceString',
'totalCost','packageType','packageName','athome','http_referer','zipinput',
'labType','testCode','testRec','sessionID','landingPage','action','a_aid',
'affPhone','promoCode','prevCustID');
ppSessionPopulate($_POST, $whiteList);
$_SESSION['no_nav'] = "true"; 
get_header('customer-checkout');

$nameArray = explode("|",$_SESSION['nameString']);
$priceArray = explode("|",$_SESSION['priceString']);
// Prices changed to float
$priceArray = array_map(create_function('$price','return (float) $price;'), $priceArray);
$totalcost = $_SESSION['totalCost'];
$totalcostTemp = $totalcost;
$totalcostDisplay = $totalcostTemp;

$promoDiscount = 0;

    //CHECK FOR PROMO CODE INPUT
    $promoMsg="";

    if("applyCode" === $_POST['promoAction']) {
        if(!applyPromoCode($_POST['promoCode'])) {
            $promoMsg = invalidPromoCodeMsg();
        } else {
            $promoID = ppClean($_POST['promoCode']);
        }
    }
   if ($_SESSION['promoCode'])
   {
      $promoCode = $_SESSION['promoCode'];
      //$query = "select * from promo_codes where code='$promoCode'";
      $query = "SELECT * FROM promo_codes WHERE code=%s";
      //if (!$result = mysql_query($query)) die ("Query failed: $query ");
      $row = $wpdb->get_row($wpdb->prepare($query, $promoCode), ARRAY_A);
      //$row = mysql_fetch_array($result);
      //$rowcount = mysql_num_rows($result);
      //if ($rowcount)
      if ($row) {
        $promoID = $row['id'];
        $discount_amount = $row['discount_amount'];
        $discount_percentage = $row['discount_percentage'];
        
        if ($discount_amount >= 1)
        {
           $promoDiscount = $discount_amount;
           $totalcostDisplay = $totalcostTemp - $discount_amount;
           $discountText = "$".$discount_amount;
        }
        
        if ($discount_percentage >= 1)
        {
           $discount_percentageDec = $discount_percentage/100;
           $discount_amount = $totalcostTemp*$discount_percentageDec;
           $totalcostDisplay = $totalcostTemp - $discount_amount;
           $promoDiscount = $discount_amount;
           $discountText = $discount_percentage."%"; 
        }
      } else {
         ppLog('[error] 400 Could not find promo code deciding not to give discount: ' . $_SESSION['promoCode']);
         //$discount_percentage = "50";
         //$discount_percentageDec = $discount_percentage/100;
         //$discount_percentageAmount = $totalcostTemp*$discount_percentageDec;
         //$totalcostDisplay = $totalcostTemp-$discount_percentageAmount;
         //$promoDiscount = $discount_percentageAmount;
         //$discountText = $discount_percentage."%";
         //$promoID = $_SESSION['promoCode'];
      }
      if($discountText) {
          $promoMsg = $discountText . ' discount has been applied';
      }
   }

    if (isset($_SESSION['payOptVal'])) {
        $payOptVal = $_SESSION['payOptVal']; 
    }
    else {
        $payOptVal = "now";
    }

  if ($_SESSION['gender'] == "Male") $maleCheckedVal='selected="selected"';
  if ($_SESSION['gender'] == "Female") $femaleCheckedVal='selected="selected"';
  
  if ($_POST['action'] != "checkout" && !$_SESSION['custID']) 
  {
      //echo "<script>window.location='http://$domainNameRedir'</script>\r\n";
      //die();
  }
  
  $LocationFull = $_SESSION['labName']."<br />".$_SESSION['labAddr']."<br />".$_SESSION['labCity'].", ".$_SESSION['labState']." ".$_SESSION['labZipcode'];
  
  $anonChecked = "";
  if ($_SESSION['anon']) $anonChecked = "checked";
  
  $termsChecked = "";
  if ($_SESSION['termsChecked']) $termsChecked = "checked";
?>
<div id="main_content">
<div style="clear: both;"></div>
    <!--START LEFT SIDE TABLE-->
    <table cellspacing="0" cellpadding="0" border="0">
    <tr>
     <td align="left"><h2 style="padding-bottom:0px;margin-bottom:0px;padding-top:10px"><span class="sectionTitle1">Customer</span> <span class="sectionTitle2">Information</span></h2></td>
    
    <td valign="top" style="padding-left:20px" rowspan="3">
      <table>
      <tr>
          <td style="padding-right:10px;padding-top:20px" valign="top">
            <!--START ORDER DETAILS MAIN TABLE-->
                    <table border="0" cellpadding="0" cellspacing="0" width="230">
                    <tr>
                    <td class="roundedGrayBox" bgcolor="#F7F8FA" style="border-left:1px #CECECE solid;border-right:1px #CECECE solid;">
                        <table cellspacing="0" cellpadding="4" style="padding-left:5px">
                        <tr>
                        <td colspan="2"><b>STD TEST SELECTIONS</b> (<a id="change_test_cc" href="<?php echo site_url('/order', 'http')?>" style="color:#5DAECB">change</a>)</td>
                        </tr> 
                        <?
                         if ($_SESSION['packageType'] == "group"):?>
                            <tr>
                            <td style="padding-bottom:1px"><span style="font-weight: bold;"><?=$_SESSION['packageName']?></span></td><td style="padding-bottom:1px">$<?=number_format($totalcost,2)?></td>
                            </tr>
                            <tr>
                            <?php
                            $nameStringTemp = str_ireplace("|",",",$_SESSION['nameString']);
                            $nameStringTemp = str_ireplace(",",", ",$nameStringTemp);
                            ?>
                            <td colspan="2" style="font-size:8pt;padding-top:0;word-wrap:break-word;max-width:200px;"><span style="color: grey;"><?=$nameStringTemp?></span></td>
                            </tr>
                   <?php else:
                                 foreach($nameArray as $key=>$val):?>
                                   <tr>
                                   <td style="color: black; font-size: 80%;"><?php echo $val?></td><td style="">$<?=number_format($priceArray[$key],2)?></td>
                                   </tr>
                         <?php   endforeach; 
                         endif;
                         
                         if ($_SESSION['athome'])
                         {
                            echo "<tr>\r\n";
                            echo "<td style=\"\">Priority Shipping</td><td style=\"\">\$15.00</td>\r\n";
                            echo "</tr>\r\n";
                            $totalcost = $totalcost+15;
                         }
                         
                         
                         if (count($nameArray)>1)
                         {
                            $savings = array_sum($priceArray) - $_SESSION['totalCost'];

                            if ($_SESSION['packageType'] == "group")
                            {
                              $discountText = "Package Savings";
                              $savingsText = "($".$savings.".00)"; 
                            }
                            
                            echo "<tr>\r\n";
                            echo "<td style=\"\"><span style=\"font-weight: bold;\">$discountText</span></td><td style=\"font-weight:bold;color:blue;font-size: 95%;\">$savingsText</td>\r\n";
                            echo "</tr>\r\n\r\n";
                         }
                         
                         if ($_SESSION['a_aid'])
                         {
                              $dicountPerc = $_SESSION['discountPerc']/100;
                              $totalDiscount = $_SESSION['totalCost']*$dicountPerc;
                              $totalcost = $totalcost-$totalDiscount;
                              
                              $promoDiscountText =  $_SESSION['discountPerc']."%";
                              
                              echo "<tr>\r\n";
                              echo "<td style=\"\">Promo Discount</td><td style=\"font-weight:bold;color:blue\" align=\"left\">$promoDiscountText</td>\r\n";
                              echo "</tr>\r\n\r\n";
                         }
                        ?>
                        <tr>
                        <td style=""><span style="color: black; font-weight: bold; font-size: 87%;">Phone Doctor Consultation</span></td><td style="font-weight:bold;color:blue" align="left">Free</td>
                        </tr>
                        <tr>
                        <td><span style="color: grey; font-size: 80%;">After testing, get answers and treatment for curable STDs through our in-house physician network. Free phone consultation included.</span></td><td></td>
                        </tr>
                        <?php getPromoInput($promoCode, $promoMsg);?>
                        <tr>
                          <td colspan="2" style="text-align: right;font-weight:bold;padding-top:10px;color:blue" id="totalLbl">
                            <span style="color: green;"><?=$promoMsg?></span><br />
                            <?php echo displayPromo($totalcost, $totalcostDisplay);?>&nbsp;&nbsp;&nbsp;<span style="color:black">$<?=number_format($totalcostDisplay,2)?></span>
                          </td>
                        </tr>
                          <?if ($_SESSION['athome']) { ?>
                            <tr>
                            <td style="padding-top:15px" colspan="2"><b>Testing Location</b></td>
                            </tr>
                            <tr>
                            <td colspan="2">At-Home Testing</td>
                            </tr>
                        <?} else {?>
                            <tr>
                            <td style="padding-top:15px" colspan="2"><b>Testing Location</b> (<a id="change_location_cc"  href="<?php echo site_url('/select-testing-center', 'http'); ?>" style="color:#5DAECB">change</a>)</td>
                            </tr>
                            <tr>
                            <td colspan="2"><?=$LocationFull?></td>
                            </tr>
                          <?}?>
                        </table>
                    </td>
                    </tr>
                    </table>
                    <!--END ORDER DETAILS MAIN TABLE-->
        </td>
      </tr>
      <tr>
      <td style="">
          <!--START FAQ TABLE-->
          <table border="0" cellpadding="0" cellspacing="0" width="230">
					<tr>
					  <td class="middle_howitswork_bg roundedGrayBox">
						    <table cellpadding="0" cellspacing="0" width="100%" style="padding-left:10px;padding-right:10px">
						    <tr>
						    <td align="center" colspan="2" style="color:#0E7B77;font-weight:bold;font-size:14pt">FAQs</td>
						    </tr>
						    <tr>
						    <td style="color:#0E7B77;font-weight:bold;padding-top:10px">Q. What happens next?</td>
						    </tr>
						    <?if ($_SESSION['athome']) {?>
  						    <tr>
  						    <td><b>A.</b> Once you place your order you will receive a confirmation email containing your results login information. Your kit will be mailed out the same business day by priority mail.</td>
  						    </tr>
  						  <?} else {?>
  						    <tr>
  						    <td><b>A.</b> Once you place your order you will receive an email containing your lab order and detailed instructions.</td>
  						    </tr>
  						  <?}?>
						    <tr>
						    <td style="color:#0E7B77;font-weight:bold;padding-top:10px">Q. What if I am positive?</td>
						    </tr>
						    <tr>
						    <td><b>A.</b> For Chlamydia, Gonorrhea, Trichomoniasis and Herpes, we offer a simple and fast online prescription system. We also provide free doctor consultation with all orders and have expert advisors on staff for counseling.</td>
						    </tr>
						    <tr>
						    <td style="color:#0E7B77;font-weight:bold;padding-top:10px">Q. When will my results be available?</td>
						    </tr>
						    <tr>
						    <td><b>A.</b> Results are ready within 1 to 3 business days after your sample is collected. You will be notified when results are ready and will be able to view them securely online.</td>
						    </tr>
						    <tr>
						    <td style="font-weight:bold;padding-top:10px"><span style="color:#DE5227">Still have questions?</span><br />Call Us at <?php echo affPhoneNumber();?><br /><span style="font-size:10px;color:black">M-F 7am-8pm | Sat-Sun 9am-3pm CST</span></td>
						    </tr>
						    </table>
					  </td>
					</tr>
          </table>
          <!--END FAQ TABLE-->
      </td>
      </tr>
      <!--
      <tr>
      <td style="padding-top:30px"><img src="images/OrderWithConf.jpg"></td>
      </tr>
      -->
      </table>
</td>
    
    </tr>
    
    <tr>
    <td align="left" style="padding-top:5px">
      <span class="smallPrivacyTxt" style="font-weight:normal;">
      <img alt="" src="<?php ssl(); ?>/padlock.jpg" style="float: left" /> <span style="color:red;font-size:10pt">PRIVACY GUARANTEE:</span> We are committed to protecting your privacy. Your information is strictly confidential. Charges will appear on your credit card as <img style="vertical-align: text-top;" src="<?php ssl(); ?>/billingname.gif" alt="" />.
      </span>
    </td>
    </tr>
    
    <tr>
    <td valign="top" style="padding-top:5px;padding-bottom:0px;padding-left:15px">
    <!--CUSTOMER INFO & ORDER SUMMARY SECTION-->
    <form id="orderfrm" name="orderfrm" action="" method="post">
    <!--START HIDDEN FAORM VALUES -->
    <input type="hidden" name="athome" value="<?=$_SESSION['athome']?>" />
    <table>
    <tr>
    <td valign="top">
                  <!--CUSTOMER INFO SECTION-->
                  <table cellpadding="0" cellspacing="0" style="font-family:arial;font-size:10pt;font-weight:bold">
                  <?if ($domainName == "getstdtested" && $_SESSION['labType'] == "119") {?>
                  <tr>
              		<td style="padding-bottom:11px;font-weight:normal" valign="top"><input type="checkbox" <?=$anonChecked?> name="anon" value="makeAnon" onclick="if (this.checked) {document.mainPaymentFrm.anon.value=this.value} else {document.mainPaymentFrm.anon.value=0}" /> keep me anonymous at the lab <span class="smallPrivacyTxt">(form is printed with a privacy ID instead of name provided)</span> </td>
                  </tr>
                  <?}?>
              		<tr>
              		<td style="padding-bottom:0px;padding-left:100px" valign="top">
              			<table cellpadding="0" cellspacing="10" border="0">
              			<tr>
              			<td id="fnameLbl">First Name:</td><td><input type="text" name="fname" size="25" value="<?=$_SESSION['fname']?>" /></td>
                    </tr>
                    <tr>
                    <td id="lnameLbl">Last Name:</td><td><input type="text" name="lname" size="25" value="<?=$_SESSION['lname']?>" /></td>
                    </tr>
                    <tr>
              			<td id="emailLbl">Email:</td><td><input type="text" name="email" size="25" value="<?=$_SESSION['email']?>" /></td>
              			</tr>
              			<tr>
                    		 <td><label id="phonelabel" for="phone1">Phone Number:</label></td>
                    		 <td>
                                <input name="areacode" type="text" value="<?=$_SESSION['areacode']?>" style="width: 30px" maxlength="3" /> - 
                                <input name="phone1" type="text" value="<?=$_SESSION['phone1']?>" style="width: 30px" maxlength="3" /> - 
                                <input name="phone2" type="text" value="<?=$_SESSION['phone2']?>" style="width: 50px" maxlength="4" />
                         </td>
              			</tr>						
              			<tr>
              			<td id="genderLbl">Gender:</td>
                    <td style="font-weight:normal">
                    <select name="gender">
                    <option value="" />
                    <option <?=$maleCheckedVal?> value="Male">Male</option>
                    <option <?=$femaleCheckedVal?> value="Female">Female</option>
                    </select>
                    </td>
              			</tr>
              			<tr>
              			<td id="dobLbl">Date of Birth:</td>
              			<td valign="top">
                        <select name="dobmonth">
              	              <?
                              if ($_SESSION['dobmonth'] == "01") echo "<option selected=\"selected\" value=\"01\">Jan</option>\r\n";
                              else echo "<option value=\"01\">Jan</option>\r\n";
                              
              	              if ($_SESSION['dobmonth'] == "02") echo "<option selected=\"selected\" value=\"02\">Feb</option>\r\n";
                              else echo "<option value=\"02\">Feb</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "03") echo "<option selected=\"selected\" value=\"03\">Mar</option>\r\n";
                              else echo "<option value=\"03\">Mar</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "04") echo "<option selected=\"selected\" value=\"04\">Apr</option>\r\n";
                              else echo "<option value=\"04\">Apr</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "05") echo "<option selected=\"selected\" value=\"05\">May</option>\r\n";
                              else echo "<option value=\"05\">May</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "06") echo "<option selected=\"selected\" value=\"06\">June</option>\r\n";
                              else echo "<option value=\"06\">June</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "07") echo "<option selected=\"selected\" value=\"07\">July</option>\r\n";
                              else echo "<option value=\"07\">July</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "08") echo "<option selected=\"selected\" value=\"08\">Aug</option>\r\n";
                              else echo "<option value=\"08\">Aug</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "09") echo "<option selected=\"selected\" value=\"09\">Sep</option>\r\n";
                              else echo "<option value=\"09\">Sep</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "10") echo "<option selected=\"selected\" value=\"10\">Oct</option>\r\n";
                              else echo "<option value=\"10\">Oct</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "11") echo "<option selected=\"selected\" value=\"11\">Nov</option>\r\n";
                              else echo "<option value=\"11\">Nov</option>\r\n";
                              
                              if ($_SESSION['dobmonth'] == "12") echo "<option selected=\"selected\" value=\"12\">Dec</option>\r\n";
                              else echo "<option value=\"12\">Dec</option>\r\n";
              	              ?>
              	            </select>
              				<select name="dobday">
              				<?
                       for ($i=1;$i<32;$i++)
                       {
                          if ($i < 10) $numVal = "0".$i;
                          else $numVal=$i;
                          if ($_SESSION['dobday'] == $numVal) echo "<option selected=\"selected\" value=\"$numVal\">$numVal</option>\r\n";
                          else echo "<option value=\"$numVal\">$numVal</option>\r\n"; 
                       }
                      ?>
              	            </select>
              				
              				<select name="dobyear" >
              				<?
              		      $startYear = date('Y',strtotime('-18 years'));
              		      $endYear = date('Y',strtotime('-75 years'));
              		  
              		 
              		  while ($startYear > $endYear)
              		  {
              		  	$s="";
              			  if ($startYear == $_SESSION['dobyear']) $s = "selected=\"selected\"";
              			  echo "<option $s value=\"$startYear\">$startYear</option>";
              			   $startYear--;
              		  }
              		 
              		  ?>
              	            
              	            </select> <span class="smallPrivacyTxt" style="font-weight:normal">(must be 18)</span>
                    </td>
              			</tr>
              			<tr>
              			<td style="font-weight:bold" id="posRsltLbl">Contact Preference:</td>
              			
              			<td style="font-weight:normal">
              			<?
                       $emailChecked="selected=\"selected\"";
                       if ($_SESSION['posRslt'] == "phone")
                       {
                           $emailChecked="";
                           $phoneChecked="selected=\"selected\"";
                       } 
                    ?>
              			<select name="posRslt">
                        <option value="email" <?=$emailChecked?>> Email</option> 
                        <option value="phone" <?=$phoneChecked?>> Phone</option>
                     </select>	 
              			</td>
              			</tr>
              				<?if ($_SESSION['athome']) {?>
              		<tr>
              		<td id="addressLbl">Address:</td>
              		<td><input type="text" name="address" value="<?=$_SESSION['address']?>" size="50" /></td>
              		</tr>
              		
              		<tr>
                  <td id="cityLbl">City:</td><td><input type="text" name="city" size="15" value="<?=$_SESSION['city']?>" /></td>
                  </tr>
                  
                  <tr><td id="stateLbl">State:</td>
                  <td>
                      <select name="state">
                      <?
                       if ($_SESSION['state'])
                       {
                          $stateT = $_SESSION['state'];
                          echo "<option selected=\"selected\" value=\"$stateT\">$stateT</option>";
                       }
                      ?>
          						<option value="AL">Alabama</option>
          						<option value="AK">Alaska</option>
          						<option value="AZ">Arizona</option>
          						<option value="AR">Arkansas</option>
          						<option value="CA">California</option>
          						<option value="CO">Colorado</option>
          						<option value="CT">Connecticut</option>
          						<option value="DE">Delaware</option>
          						<option value="DC">District of Columbia</option>
          						<option value="FL">Florida</option>
          						<option value="GA">Georgia</option>
          						<option value="HI">Hawaii</option>
          						<option value="ID">Idaho</option>
          						<option value="IL">Illinois</option>
          						<option value="IN">Indiana</option>
          						<option value="IA">Iowa</option>
          						<option value="KS">Kansas</option>
          						<option value="KY">Kentucky</option>
          						<option value="LA">Louisiana</option>
          						<option value="ME">Maine</option>
          						<!--<option value="MD">Maryland</option>-->
          						<option value="MA">Massachusetts</option>
          						<option value="MI">Michigan</option>
          						<option value="MN">Minnesota</option>
          						<option value="MS">Mississippi</option>
          						<option value="MO">Missouri</option>
          						<option value="MT">Montana</option>
          						<option value="NE">Nebraska</option>
          						<option value="NV">Nevada</option>
          						<option value="NH">New Hampshire</option>
          						<!--<option value="NJ">New Jersey</option>-->
          						<option value="NM">New Mexico</option>
          						<!--<option value="NY">New York</option>-->
          						<option value="NC">North Carolina</option>
          						<option value="ND">North Dakota</option>
          						<option value="OH">Ohio</option>
          						<option value="OK">Oklahoma</option>
          						<option value="OR">Oregon</option>
          						<option value="PA">Pennsylvania</option>
          						<!--<option value="RI">Rhode Island</option>-->
          						<option value="SC">South Carolina</option>
          						<option value="SD">South Dakota</option>
          						<option value="TN">Tennessee</option>
          						<option value="TX">Texas</option>
          						<option value="UT">Utah</option>
          						<option value="VT">Vermont</option>
          						<option value="VA">Virginia</option>
          						<option value="WA">Washington</option>
          						<option value="WV">West Virginia</option>
          						<option value="WI">Wisconsin</option>
          						<option value="WY">Wyoming</option>
          					</select>
                      </td>
                      </tr>
                  
                    <tr>
                    <td id="zipcodeLbl">Zipcode:</td><td><input type="text" name="zipcode" size=10 value="<?=$_SESSION['zipcode']?>" /></td>
              		  </tr>
              		<?}?>
              			</table>
              		</td>
              		</tr>
            <tr>
            <td valign="top" style="padding-left:40px"></td>
            </tr>
            </table>
      </td>
      </tr>
              	<!--END CUSTOMER INFO SECTION-->
      <tr>
      <td style="border-top: 1px dotted #ddd"><h2 style="padding-bottom:0px;margin-bottom:8px;padding-top:10px"><span class="sectionTitle1">Payment</span> <span class="sectionTitle2">Options</span></h2></td>
      </tr>
      <tr>
      <td>
        
        <?
         $ccTabActive="";
         $ccTabContent="tabcontent hide";
         $PNMTabActive="";
         $PNMTabContent="tabcontent hide";
         $googleCheckoutTabActive="";
         $googleCheckoutTabContent="tabcontent hide";
         $eCheckTabActive="";
         $eCheckTabContent="tabcontent hide";
         if ($payOptVal == "now") 
         {
            $ccTabActive="activeLink";
            $ccTabContent = "tabcontent";   
        }
         if ($payOptVal == "pnm") 
         {
            $PNMTabActive="activeLink";
            $PNMTabContent="tabcontent"; 
          }
          if ($payOptVal == "googleCheckout") 
         {
            $googleCheckoutTabActive="activeLink";
            $googleCheckoutTabContent="tabcontent"; 
          }
          if ($payOptVal == "eCheck")
         {
            $eCheckTabActive="activeLink";
            $eCheckTabContent="tabcontent"; 
          }
        ?>
        <div class="tab-box"> 
        <a href="javascript:;" class="tabLink <?=$ccTabActive?>" id="cont-3" onclick="document.mainPaymentFrm.payOptVal.value='now'"><img alt="Credit Card" src="<?php ssl(); ?>/creditcard.png" border="0" /></a>
        <? //if (!$_SESSION['athome']) { ?>
        <!--<a href="javascript:;" class="tabLink <?=$PNMTabActive?>" id="cont-2" onclick="document.mainPaymentFrm.payOptVal.value='PNM'"><img alt="Cash Payment" src="<?php ssl(); ?>/cashPayment2.gif" border="0" /></a>-->
        <!--<a href="javascript:;" class="tabLink " id="cont-1" onclick="document.mainPaymentFrm.payOptVal.value='later'"><img src="images/payaftertest.png" width="146" height="28" border="0" /></a>--> 
        <? //} else {?>
                <!--<a href="javascript:;" class="tabLink <?=$eCheckTabActive?>" id="cont-2" onclick="document.mainPaymentFrm.payOptVal.value='eCheck'"><img alt="Check" src="images/eCheck2.png" border="0" /></a>-->
        <? //}?>
        <!--<a href="javascript:;" class="tabLink " id="cont-2"><img src="images/paypal.png" width="103" height="28" border="0" /></a> -->
    		 </div>
      </td>
      </tr>
      <tr>
      <td colspan="3">
              <!--START CC SECTION-->
              <div class="<?=$ccTabContent?>" id="cont-3-1"> 
              <table width="100%" border="0" cellspacing="5" cellpadding="0">
			  
			  
              	<?php if (!$_SESSION['athome']) { ?>
              		<tr>
						<td id="addressLbl" style="padding-left: 16px;">Address:</td>
						<td style="padding-top: 4px;"><input type="text" name="address" value="<?=$_SESSION['address']?>" size="50" /></td>
              		</tr>
					<tr>
						<td id="cityLbl" style="padding-left: 16px;">City:</td>
						<td style="padding-top: 4px;"><input type="text" name="city" size="15" value="<?=$_SESSION['city']?>" /></td>
					</tr>
					<tr>
						<td id="stateLbl" style="padding-left: 16px;">State:</td>
						<td style="padding-top: 4px;">
							<select name="state">
							<?
								if ($_SESSION['state'])
								{
									$stateT = $_SESSION['state'];
									echo "<option selected=\"selected\" value=\"$stateT\">$stateT</option>";
								}
							?>
          						<option value="AL">Alabama</option>
          						<option value="AK">Alaska</option>
          						<option value="AZ">Arizona</option>
          						<option value="AR">Arkansas</option>
          						<option value="CA">California</option>
          						<option value="CO">Colorado</option>
          						<option value="CT">Connecticut</option>
          						<option value="DE">Delaware</option>
          						<option value="DC">District of Columbia</option>
          						<option value="FL">Florida</option>
          						<option value="GA">Georgia</option>
          						<option value="HI">Hawaii</option>
          						<option value="ID">Idaho</option>
          						<option value="IL">Illinois</option>
          						<option value="IN">Indiana</option>
          						<option value="IA">Iowa</option>
          						<option value="KS">Kansas</option>
          						<option value="KY">Kentucky</option>
          						<option value="LA">Louisiana</option>
          						<option value="ME">Maine</option>
          						<!--<option value="MD">Maryland</option>-->
          						<option value="MA">Massachusetts</option>
          						<option value="MI">Michigan</option>
          						<option value="MN">Minnesota</option>
          						<option value="MS">Mississippi</option>
          						<option value="MO">Missouri</option>
          						<option value="MT">Montana</option>
          						<option value="NE">Nebraska</option>
          						<option value="NV">Nevada</option>
          						<option value="NH">New Hampshire</option>
          						<!--<option value="NJ">New Jersey</option>-->
          						<option value="NM">New Mexico</option>
          						<!--<option value="NY">New York</option>-->
          						<option value="NC">North Carolina</option>
          						<option value="ND">North Dakota</option>
          						<option value="OH">Ohio</option>
          						<option value="OK">Oklahoma</option>
          						<option value="OR">Oregon</option>
          						<option value="PA">Pennsylvania</option>
          						<!--<option value="RI">Rhode Island</option>-->
          						<option value="SC">South Carolina</option>
          						<option value="SD">South Dakota</option>
          						<option value="TN">Tennessee</option>
          						<option value="TX">Texas</option>
          						<option value="UT">Utah</option>
          						<option value="VT">Vermont</option>
          						<option value="VA">Virginia</option>
          						<option value="WA">Washington</option>
          						<option value="WV">West Virginia</option>
          						<option value="WI">Wisconsin</option>
          						<option value="WY">Wyoming</option>
          					</select>
						</td>
					</tr>                  
                    <tr>
						<td id="zipcodeLbl" style="padding-left: 16px;">Zipcode:</td>
						<td style="padding-top: 4px; padding-bottom: 8px;"><input type="text" name="zipcode" size=10 value="<?=$_SESSION['zipcode']?>" /></td>
					</tr>
              	<?php } ?>


            <tr>
              <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="45%" valign="top" style="border-right:1px #EAEAEA solid; padding-right:20px;"><table width="100%" border="0" cellspacing="3" cellpadding="0">
          		<tr>
                      <td style="padding-bottom:0px;" id="ccNumLbl"><strong>Credit Card Number</strong></td>
                      </tr>
                    <tr>
                      <td style="padding-bottom:3px;">
                        <input name="textfield" id="ccNum" type="text" maxlength="16" value="<?=$_SESSION['ccnumber']?>" style="width:150px; font-family:Arial, Helvetica, sans-serif; font-size:12px; padding-top:3px; color:#333333; padding-left:5px;" /></td>
                      </tr>
                    <tr>
                      <td style="padding-top:5px;"><img alt="Card" src="<?php ssl(); ?>/card.gif" width="283" height="21" /></td>
                      </tr>
          			
                  </table></td>
                  <td width="55%" colspan="2" rowspan="2" valign="top" style="padding-left:20px; padding-right:10px; padding-bottom:10px;"><table width="100%" border="0" cellspacing="3" cellpadding="0">
          		
          		<tr>
                      <td width="110" height="20" valign="top" id="expDateLbl"><strong>Expiration
                        </strong></td>
                      <td width="90">&nbsp;</td>
                      <td width="60" id="cvv2Lbl"><strong>CVV2 </strong></td>
                      <td >&nbsp;</td>
                    </tr>
          		  
          		  
                    <tr>
                      <td>
                        <select name="comMonth" id="comMonth">
          				<option value="">Month</option>
                  <?
                    $month = array('01'=>'January (01)','02'=>'February (02)','03'=>'March (03)','04'=>'April (04)','05'=>'May (05)','06'=>'June (06)','07'=>'July (07)','08'=>'August (08)','09'=>'September (09)','10'=>'October (10)','11'=>'November (11)','12'=>'December (12)');
                  	foreach($month as $key=>$val)
            				{
            					$selected = ($key == $_SESSION['expMonth'])?'selected="selected"':'';
            					echo "<option value=\"$key\" $selected>$val</option>\r\n";
            				}
                  ?>
          				</select>
          			</td>
                      <td>
                        <select name="comYear" id="comYear">
          				<option value="">Year</option>
          				  <?	
                    for($i=date('Y'); $i<=date('Y')+20; $i++)
                		{
                			$selected = ($i == $_SESSION['expYear'])?'selected="selected"':'';
                			echo "<option value=\"$i\" $selected>$i</option>\r\n";
                		}
                    ?>
          				</select>
          			</td>
                      <td><span style="padding-bottom:3px;">
                        <input name="textfield2" id="cvv2" type="text" maxlength="4" value="<?=$_SESSION['cvv2']?>" style="width:50px; height:17px; font-family:Arial, Helvetica, sans-serif; font-size:12px; padding-top:3px; color:#333333; padding-left:5px;" />
                      </span></td>
                      <td align="left"><a href="javascript: void(0)" onclick="window.open('cvvPop.html','cvv','location=0,status=0,scrollbars=1,width=650,height=400')" style="font-size:7pt;color:black;"><img alt="Question" src="<?php ssl(); ?>/question.jpg" width="19" height="20" border="0" /></a></td>
                    </tr>
                    <tr>
                      <td colspan="4" class="smallPrivacyTxt">* For your privacy, this service is billed as <img style="vertical-align: text-top;" src="<?php ssl(); ?>/billingname.gif" alt="Billing Name" />.</td>
                      </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
            </table> 
            </div>
            <!--END CC SECTION-->
            
           
            <? if (!$_SESSION['athome']) { ?>
            
            <!--START PAYNEARME SECTION--> 
             <div class="<?=$PNMTabContent?>" id="cont-2-1"> 
             <table border="0" cellspacing="5" cellpadding="0">
            <tr>
            <td>
                <table>
                <tr>
                <td><span style="font-weight:bold;font-size:12pt">Pay Cash At </span></td>
                <td><img alt="7 Eleven" src="<?php ssl(); ?>/7-Eleven.jpg" width="45" /></td>
                </tr>
                </table>
            </td>
            </tr>
            <tr>
            <td>
            Our customers can now pay for testing with cash at local 7-Elevens. Included in your confirmation email will be a payment slip which you can take to any of the participating locations. <a href="javascript: void(0)" onclick="window.open('http://<?php echo domainNameRedir();?>/paynearme-locations?zipcode=<?=$_SESSION['zipinput']?>','pnm','location=0,status=0,scrollbars=1,width=850,height=800')">Click here</a> to see 7-Eleven locations in your area. For your convenience, nearby locations will be included in your payment slip as well.<br />
            <label style="font-weight: bold;"><input type="checkbox" name="cash" style="margin-right: 10px;vertical-align: bottom;position: relative; width: 13px;"/>Select Cash Payment Option. I will pay at a local 7-11 store.</label>
            <br />
            <br />
            <span style="font-style:italic;font-weight:bold"><span style="color:red">*</span> Note: Payment Slip Has No Product Or Customer Information Listed On It And Is 100% Anonymous <span style="color:red">*</span></span>
            </td>
            </tr>
            </table>
          
            </div>
            <!--END PAYNEARME SECTION-->
            
              <!--START PAYLATER SECTION--> 
             <div class="tabcontent hide" id="cont-1-1"> 
             <table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr>
              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="56%" style="padding-right:20px;">We offer the option of placing your order and getting tested without having to pay until your results are ready to view.</td>
                </tr>
          	  
          	    <tr>
                  <td width="56%" valign="top" style="padding-top:20px; padding-right:20px; padding-bottom:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td style="background-color:#EAEAEA; padding:5px; border:1px #CCCCCC solid;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="6%">
                            <input id="payLaterAgree" type="checkbox" name="checkbox" value="checkbox" />                </td>
                          <td width="94%" id="payLaterAgreeLbl">I understand that I must pay in full before receiving my results.</td>
                        </tr>
                      </table></td>
                    </tr>
                  </table></td>
                  </tr>
              </table></td>
            </tr>
            </table>
            </div>
            <!--END PAYLATER SECTION-->
            <?} else {
            
            if ($_SESSION['routingNum'] && $_SESSION['routingNum']!="undefined") $routingVal=$_SESSION['routingNum'];
            else $routingVal="";
            if ($_SESSION['accountNum'] && $_SESSION['accountNum']!="undefined") $accountVal=$_SESSION['routingNum'];
            else $accountVal="";
            ?>
            <!--START eCHECK SECTION--> 
             <div class="<?=$eCheckTabContent?>" id="cont-2-1"> 
             <table width="500" border="0" cellspacing="5" cellpadding="0">
            <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td>
                    <table>
                    <tr>
                    <td id="routingNumLbl"><strong>Routing Number</strong>  <a href="javascript: void(0)" onclick="window.open('bankNumbers.html','bankNums','width=600,height=400,toolbar=no,directories=no,')" style="font-size:7pt;color:black;"><img alt="Question" src="<?php ssl(); ?>/question.jpg" width="19" height="20" border="0" /></a></td><td id="accountNumLbl"><strong>Account Number</strong> <a href="javascript: void(0)" onclick="window.open('bankNumbers.html','bankNums','width=600,height=400,toolbar=no,directories=no,')" style="font-size:7pt;color:black;"><img src="images/question.jpg" width="19" height="20" border="0" /></a></td>
                    </tr>
                    <tr>
                    <td style="padding-right:15px"><input type="text" id="routingNum" name="routingNum" value="<?=$routingVal?>" size="22" maxlength="20" /></td><td><input type="text" id="accountNum" name="accountNum" value="<?=$accountVal?>" size="22" maxlength="20" /></td>
                    </tr>
                    </table>
                </td>
                </tr>
                </table>
            </td>
            </tr>
            </table>
            </div>
            <!--END eCHECK SECTION-->
            <?}?>
            
            
      </td>
      </tr>
      <tr>
      <td colspan="3" width="100%">
            <table width="100%">
            <tr>
            <td width="80%">
            <table>
              <tr>
      			   <td>
      				  <input type="checkbox" name="chkTermCondition" id="chkTermCondition" <?=$termsChecked?> value="1" />
      			   </td>
      			   <td id="termsLbl">I agree to the <a href="#" onclick="window.open('terms-of-service-b','mywindow','width=800,height=500,scrollbars=yes'); return false;" class="blue_link_dark">terms of service.</a></td>
      			   </tr>
            </table>
      			</td>
            </tr>
            </table>
            </td>
        </tr>
      <tr>
      <td align="center" width="100%" style="padding-top:0px;padding-bottom:10px"><a href="javascript:void(0)" onclick="return validateCustInfoForm();"><img alt="Place Order" src="<?php ssl(); ?>/place-order.jpg" border="0" /></a></td>
      </tr>
      
      <tr>
      <td align="left" style="padding-top:20px">
          <table>
          <tr>
          <td style="padding-right:20px"><span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=h33fNmXpfg38NQzoEJYunIVSzULU8jXM66msvj0oZd1rgMVkgUc4lq"></script></span></td>
          </tr>
          </table>
      </td>
      </tr>
    </table>
          </form>
    <!--END LEFT SIDE TABLE-->
</td>
</tr>
</table>


<form name="mainPaymentFrm" action="<?php echo site_url('/processOrder.php', 'https');?>" method="post">
<input type="hidden" name="action" value="processOrder" />
<input type="hidden" name="custEmail" value="" />
<input type="hidden" name="custFname" value="" />
<input type="hidden" name="custLname" value="" />
<input type="hidden" name="custAddress" value="" />
<input type="hidden" name="custCity" value="" />
<input type="hidden" name="custState" value="" />
<input type="hidden" name="custZipcode" value="" />
<input type="hidden" name="anon" value="anonYes" />
<input type="hidden" name="labID" value="<?=$_SESSION['labID']?>" />
<input type="hidden" name="labName" value="<?=$_SESSION['labName']?>" />
<input type="hidden" name="labAddr" value="<?=$_SESSION['labAddr']?>" />
<input type="hidden" name="labCity" value="<?=$_SESSION['labCity']?>" />
<input type="hidden" name="labState" value="<?=$_SESSION['labState']?>" />
<input type="hidden" name="labZipcode" value="<?=$_SESSION['labZipcode']?>" />
<input type="hidden" name="labHours" value="<?=$_SESSION['labHours']?>" />
<input type="hidden" name="labPhone" value="<?=$_SESSION['labPhone']?>" />
<input type="hidden" name="labType" value="<?=$_SESSION['labType']?>" />
<input type="hidden" name="athome" value="<?=$_SESSION['athome']?>" />
<input type="hidden" name="gender" value="" />
<input type="hidden" name="dobDay" value="<?=$_SESSION['dobday']?>" />
<input type="hidden" name="dobMon" value="<?=$_SESSION['dobmonth']?>" />
<input type="hidden" name="dobYear" value="<?=$_SESSION['dobyear']?>" />
<input type="hidden" name="zipcode" value="<?=$_SESSION['zipcode']?>" />
<input type="hidden" name="custPhone" value="" />
<input type="hidden" name="areacode" value="" />
<input type="hidden" name="phone1" value="" />
<input type="hidden" name="phone2" value="" />
<input type="hidden" name="resultPref" value="" />
<input type="hidden" name="totalCost" value="<?=$totalcost?>" />
<input type="hidden" name="codeString" value="<?=$_SESSION['codeString']?>" />
<input type="hidden" name="nameString" value="<?=$_SESSION['nameString']?>" />
<input type="hidden" name="payOptVal" value="<?=$payOptVal?>" />
<input type="hidden" name="ccNum" />
<input type="hidden" name="expMon" />
<input type="hidden" name="expYear" />
<input type="hidden" name="cvv2" />
<input type="hidden" name="routingNum" />
<input type="hidden" name="accountNum" />
<input type="hidden" name="environment" value="<?=$_SESSION['environment']?>" />
<input type="hidden" name="testCode" value="std25" />
<input type="hidden" name="testRec" value="<?=$_SESSION['testRec']?>" />
<input type="hidden" name="promoCode" id="promoCode" value="<?=$_SESSION['promoCode']?>" />
<input type="hidden" name="promoID" id="promoID" value="<?=$promoID?>" />
<input type="hidden" name="promoDiscount" id="promoDiscount" value="<?=$promoDiscount?>" />
<input type="hidden" name="originalCustID" value="<?php echo $_SESSION['prevCustID'] ?>" />
<input id="verify" type="hidden" name="verify" value="<?php $verify = mt_rand(1, 1000); $_SESSION['verify'] = $verify; echo $_SESSION['verify']; ?>" />
<?php wp_nonce_field('processOrder','nonce'); ?>
</form>

<script type="text/javascript">
togglePhoneNum();
</script>
<?
$dateArray = getdate();
$hour = $dateArray['hours'];
$wday = $dateArray['wday'];
$showPromo=0;
if ($wday == '0' || $wday == '6')
{
    if ($hour > "8" && $hour < "15") $showPromo=1;
}
else
{
    if ($hour > "6" && $hour < "20") $showPromo=1;
}
$msgTriggeredVal=0;
if ($_SESSION['promoCode']) $msgTriggeredVal=1;
?>
<form name="posForm" action="">
<input type="hidden" value="" name="yPos" />
<input type="hidden" value="<?=$msgTriggeredVal?>" name="msgTriggered" />
<input type="hidden" value="<?=$showPromo?>" name="showPromo" />
</form>
</div>
<?php
get_footer();

