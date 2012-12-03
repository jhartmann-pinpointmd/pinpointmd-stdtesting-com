// JavaScript Document



// Ready DOM
jQuery(document).ready(function(){
	jQuery(".tabLink").each(function(){
      jQuery(this).click(function(){
        tabeId = jQuery(this).attr('id');
        jQuery(".tabLink").removeClass("activeLink");
        jQuery(this).addClass("activeLink");
        jQuery(".tabcontent").addClass("hide");
        jQuery("#"+tabeId+"-1").removeClass("hide");
        return false;
      });
    });
}); 


function trim(s){
  return s.replace(/^\s+|\s+$/, '');
}

function isExpiryDate() {
var argv = isExpiryDate.arguments;
var argc = isExpiryDate.arguments.length;

year = argc > 0 ? argv[0] : this.year;
month = argc > 1 ? argv[1] : this.month;

if (!isNum(year+""))
return false;
if (!isNum(month+""))
return false;
today = new Date();
expiry = new Date(year, month);
if (today.getTime() > expiry.getTime())
return false;
else
return true;
}

function isNum(argvalue) {
argvalue = argvalue.toString();

if (argvalue.length == 0)
return false;

for (var n = 0; n < argvalue.length; n++)
if (argvalue.substring(n, n+1) < "0" || argvalue.substring(n, n+1) > "9")
return false;

return true;
}

function validateCustInfoForm()
{ 
  theForm = document.orderfrm;
  var athome = theForm.athome.value;
  var fieldsGood = true;
	var z = 0;
	
	var requiredFields = new Array();
	
  document.getElementById('fnameLbl').style.color = 'black'; 
	if (!theForm.fname.value)  
  {
      requiredFields[z] = 'fnameLbl';
      z++;   
  }
  else document.mainPaymentFrm.custFname.value = theForm.fname.value;
  
  document.getElementById('lnameLbl').style.color = 'black'; 
	if (!theForm.lname.value)
  {
    requiredFields[z] = 'lnameLbl';
    z++;
  }
  else document.mainPaymentFrm.custLname.value = theForm.lname.value;
  
  document.getElementById('emailLbl').style.color = 'black';
	if (!theForm.email.value)
	{
     requiredFields[z] = 'emailLbl';
     z++;
  }
  else document.mainPaymentFrm.custEmail.value = theForm.email.value;


    
  
    var genderVal = theForm.gender.options[theForm.gender.selectedIndex].value;
  
  document.getElementById('genderLbl').style.color = 'black';
	if (!genderVal)
	{
     requiredFields[z] = 'genderLbl';
     z++; 
  }
  else document.mainPaymentFrm.gender.value = genderVal;
	
  var posRsltVal = theForm.posRslt.options[theForm.posRslt.selectedIndex].value;
  document.getElementById('posRsltLbl').style.color = 'black';
	if (!posRsltVal)
	{
     requiredFields[z] = 'posRsltLbl';
     z++;  
  }
  else document.mainPaymentFrm.resultPref.value = posRsltVal;
  
	
	document.getElementById('phonelabel').style.color = 'black';
	//if (posRsltVal == 'phone' && (!theForm.areacode.value || !theForm.phone1.value || !theForm.phone2.value))
	if (!theForm.areacode.value || !theForm.phone1.value || !theForm.phone2.value)
	{
     requiredFields[z] = 'phonelabel';
     z++; 
  }
    
//  if (athome >= 1)
//  {
      document.getElementById('addressLbl').style.color = 'black';
    	if (!theForm.address.value)
    	{
         requiredFields[z] = 'addressLbl';
         z++;  
      }
      else document.mainPaymentFrm.custAddress.value = theForm.address.value;
      
      document.getElementById('cityLbl').style.color = 'black';
    	if (!theForm.city.value)
    	{
         requiredFields[z] = 'cityLbl';
         z++;  
      }
      else document.mainPaymentFrm.custCity.value = theForm.city.value;
      
      document.getElementById('zipcodeLbl').style.color = 'black';
    	if (!theForm.zipcode.value)
    	{
         requiredFields[z] = 'zipcodeLbl';
         z++;  
      }
      else document.mainPaymentFrm.custZipcode.value = theForm.zipcode.value;
      
      document.mainPaymentFrm.custState.value = theForm.state.value;
//  }
  
	if (requiredFields.length>=1)
	{
     alert ('Please fill in all required fields before proceeding');
	   
     
     for (counter=0; counter<requiredFields.length; counter++) 
     {
        lblVal = requiredFields[counter];
        document.getElementById(lblVal).style.color = 'red';  
     }
     return false;
  }


        // Disabled ordering for Maryland (MD) 11/28/2012 -- JH
        var labState = document.mainPaymentFrm.labState.value;
        var custState = document.mainPaymentFrm.custState.value;
        var stateMD = "MD";
        if ((labState == stateMD) || (custState == stateMD)) {
            alert ('We have disabled ordering capability for Maryland. Please try a different zipcode or call us for assistance.');
            return false;
        }
  
	//Validate Age
	var todaysDate = new Date();
	var monthfield = theForm.dobmonth.value;
	var dayfield = theForm.dobday.value;
	var yearfield = theForm.dobyear.value;
	var dayobj = new Date(yearfield, monthfield-1, dayfield);
	
	//will do a validation of age
	document.getElementById('dobLbl').style.color = 'black';
	var eighteenCheck = new Date(Number(yearfield)+18, monthfield-1, dayfield);
	var todayCheck = new Date(todaysDate.getFullYear(), todaysDate.getMonth(), todaysDate.getDate());
	if (eighteenCheck > todayCheck) 
  {
		alert('You must be at least 18 to place an order');
		document.getElementById('dobLbl').style.color = 'red'; 
	  return false; 	
	}
	else
  {
      document.mainPaymentFrm.dobDay.value = dayfield;
      document.mainPaymentFrm.dobMon.value = monthfield;
      document.mainPaymentFrm.dobYear.value = yearfield;    
  }
	
	  var tfld = trim(theForm.email.value);
    var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
    var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;
	
    if (!emailFilter.test(tfld) || theForm.email.value.match(illegalChars)) 
	{    //test email for illegal characters
        alert ('Please enter a valid Email address');
        document.getElementById('emailLbl').style.color = 'red';
		return false;
    }
    
	
	myAreaCode = theForm.areacode.value;
	myPhone1 = theForm.phone1.value;
	myPhone2 = theForm.phone2.value;
					
	firstChar = myAreaCode.charAt(0);
				
	if (posRsltVal == 'phone')
	{
      if ((firstChar == "1") || (firstChar == "0") || isNaN(myAreaCode) || isNaN(myPhone1) || isNaN(myPhone2) || myAreaCode.length<3 || myPhone1.length<3 || myPhone2.length<4)
	     {
			 alert ('Please enter a valid phone number');
			 document.getElementById('phonelabel').style.color = 'red';
			 return false;
	     }
	     else
       {
          phoneNumTemp = myAreaCode+'-'+myPhone1+'-'+myPhone2;
          document.mainPaymentFrm.custPhone.value = phoneNumTemp;
        }  
	}
	
	submitPaymentForm();
}


function submitPaymentForm()
{
    var ccNum = document.getElementById('ccNum').value;
    var cvv2 = document.getElementById('cvv2').value;
    var expMon = document.getElementById('comMonth').value;
    var expYear = document.getElementById('comYear').value;
    var payLaterAgree = document.getElementById('payLaterAgree');
    var payOptVal = document.mainPaymentFrm.payOptVal.value;
    
    if (payOptVal == 'other')
    {
        alert ('Please select a payment option');
    		return false;
    }
    
    if (payOptVal == 'now')
    {
		var valid = "0123456789";  // Valid digits in a credit card number
		var len = ccNum.length;  // The length of the submitted cc number
		var iCCN = parseInt(ccNum);  // integer of ccNumb
		var sCCN = ccNum.toString();  // string of ccNumb
		sCCN = sCCN.replace (/^\s+|\s+$/g,'');  // strip spaces
		var iTotal = 0;  // integer total set at zero
		var bNum = true;  // by default assume it is a number
		var bResult = false;  // by default assume it is NOT a valid cc
		var temp;  // temp variable for parsing string
		var calc;  // used for calculation of each digit
		// Determine if the ccNumb is in fact all numbers
		
		for (var j=0; j<len; j++) 
		{
			temp = "" + sCCN.substring(j, j+1);
			if (valid.indexOf(temp) == "-1"){bNum = false;}
		}
	
		if(!bNum || len < 12)
		{
			alert ('Please enter a valid Credit Card Number');
			document.getElementById('ccNumLbl').style.color = 'red';
			return false;
		}
		else document.getElementById('ccNumLbl').style.color = 'black';
		
		if (!expMon || !expYear)
		{
			alert ('Please enter a credit card expiration date');
			document.getElementById('expDateLbl').style.color = 'red';
			return false; 
		}
		else document.getElementById('expDateLbl').style.color = 'black';
	
		
		tmpyear = expYear;
		tmpmonth = expMon;
		
		
		if (!isExpiryDate(tmpyear, tmpmonth)) 
		{
			alert ('This card has already expired');
			return false;
		}
		
		if (!cvv2)
		{
			alert ('Please enter a CVV2 code');
			document.getElementById('cvv2Lbl').style.color = 'red';
			return false;
		}
		
		if (cvv2)
		{
			var stripped = cvv2.replace(/[\(\)\.\-\ ]/g, '');
			if (isNaN(stripped) || stripped.length > 4 || stripped.length < 3) 
			{
				alert ('Please enter a valid CVV2 code');
				document.getElementById('cvv2Lbl').style.color = 'red';
				return false;
			}
			else document.getElementById('cvv2Lbl').style.color = 'black';
		}
		else document.getElementById('cvv2Lbl').style.color = 'black';

    }
    
    if (payOptVal == 'eCheck')
    {
        var routingNum = document.getElementById('routingNum').value;
        var accountNum = document.getElementById('accountNum').value;
        if (!routingNum)
    		{
           alert ('Please enter a valid routing number');
           document.getElementById('routingNumLbl').style.color = 'red';
    			 return false; 
        }
        else document.getElementById('routingNumLbl').style.color = 'black';
        
        if (!accountNum)
    		{
           alert ('Please enter a valid account number');
           document.getElementById('accountNumLbl').style.color = 'red';
    			 return false; 
        }
        else document.getElementById('accountNumLbl').style.color = 'black';
    }
    
    if (payOptVal == 'later')
    {
        if (!payLaterAgree.checked) 
        {
          alert ('You must check the pay later conditional box before proceeding');
          document.getElementById('payLaterAgreeLbl').style.color = 'red';
          return false; 
        }
        else document.getElementById('payLaterAgreeLbl').style.color = 'black';
    }
    
    if (!document.orderfrm.chkTermCondition.checked) 
    {
      alert ('You must agree to the terms and conditions before proceeding.');
      document.getElementById('termsLbl').style.color = 'red';
      return false; 
    }
    else document.getElementById('termsLbl').style.color = 'black';
    
    
    
    
    document.mainPaymentFrm.expMon.value = expMon;
    document.mainPaymentFrm.expYear.value = expYear;
    document.mainPaymentFrm.ccNum.value = ccNum;
    document.mainPaymentFrm.cvv2.value = cvv2;
    document.mainPaymentFrm.routingNum.value = routingNum;
    document.mainPaymentFrm.accountNum.value = accountNum;
    
    document.mainPaymentFrm.submit();
}


function toggleOtherPaymentOptions() 
{   
	//Get Positive Result Value
	var otherPaymentVal = document.mainPaymentFrm.payOptVal.value; 
 
  tbl1 = document.getElementById('eCheckTbl');
  tbl2 = document.getElementById('googleCheckoutTbl');
  tbl3 = document.getElementById('payLaterTbl');

	if (otherPaymentVal == "eCheck") 
	{
    var tbl1Rows = tbl1.rows;   
		for (i = 0; i < tbl1Rows.length; i++) 
		{      
			if (tbl1Rows[i].className != "headerRow") 
			{         
				tbl1Rows[i].style.display = (false) ? "none" : "";      
			}   
		}
	}
  else
  {
    var tbl1Rows = tbl1.rows;   
		for (i = 0; i < tbl1Rows.length; i++) 
		{      
			if (tbl1Rows[i].className != "headerRow") 
			{         
				tbl1Rows[i].style.display = (true) ? "none" : "";      
			}   
		}
  }
  
  if (otherPaymentVal == "googleCheckout") 
	{
    var tbl2Rows = tbl2.rows;   
		for (i = 0; i < tbl2Rows.length; i++) 
		{      
			if (tbl2Rows[i].className != "headerRow") 
			{         
				tbl2Rows[i].style.display = (false) ? "none" : "";      
			}   
		}
	}
  else
  {
    var tbl2Rows = tbl2.rows;   
		for (i = 0; i < tbl2Rows.length; i++) 
		{      
			if (tbl2Rows[i].className != "headerRow") 
			{         
				tbl2Rows[i].style.display = (true) ? "none" : "";      
			}   
		}
  }
  
  if (otherPaymentVal == "later") 
	{
    var tbl3Rows = tbl3.rows;   
		for (i = 0; i < tbl3Rows.length; i++) 
		{      
			if (tbl3Rows[i].className != "headerRow") 
			{         
				tbl3Rows[i].style.display = (false) ? "none" : "";      
			}   
		}
	}
  else
  {
    var tbl3Rows = tbl3.rows;   
		for (i = 0; i < tbl3Rows.length; i++) 
		{      
			if (tbl3Rows[i].className != "headerRow") 
			{         
				tbl3Rows[i].style.display = (true) ? "none" : "";      
			}   
		}
  }      
}