// JavaScript Document

var map;
var zipvalue;
var geocoder;

  function initialize() {
    var myLatlng = new google.maps.LatLng(40, -100);
    var zipvalue = $('#addressInput').val();
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom: 3,
      disableDefaultUI: true,
      navigationControl: true,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    if (zipvalue) searchLocations('');
  }
  
  function codeAddress() { 
  var address = jQuery('#addressInput').val(); 
  geocoder.geocode( { 'address': address}, function(results, status) { 
  if (status == google.maps.GeocoderStatus.OK) { 
  map.setCenter(results[0].geometry.location); 
  var marker = new google.maps.Marker({ 
  map: map, 
  position: results[0].geometry.location 
  });
  map.setZoom(11); 
  } 
  }); 
  }
  
	function searchLocations(affPhone) 
	{
    if (!affPhone || affPhone=='') affPhone='866-236-8491';
    zipvalue = $('#addressInput').val();
		var zipEntry = document.getElementById('addressInput').value;
		if(!zipEntry || zipEntry=='Zip Code')
		{
			alert('Please enter valid Zipcode');
			$('#addressInput').focus();
			return false;
		}
		else
		{
          $("#map-results").html('<div id="LoadLocation" align="center"><img src="http://c0001470.cdn1.cloudfiles.rackspacecloud.com/ajaxloading.gif" alt="loading" /></div>');
          searchLocationsNear(affPhone);
          return false;
		}
	}
	
	function searchLocationsNear(affPhone) 
	{
      downloadUrl("phpsqlsearch_genxml2.php?zip="+zipvalue, function(data) {
      var markers = data.documentElement.getElementsByTagName("marker");
      var mapResults = document.getElementById('map-results');
      var mapResultsString="";
      
      if ((zipvalue >= '10000' && zipvalue <= '14999') || (zipvalue >= '02800' && zipvalue <= '02999') || (zipvalue >= '07000' && zipvalue <= '08999'))
        {
            codeAddress();
            mapResults.innerHTML = '';
            mapResults.innerHTML = "<table style=\"padding-left:5px;padding-right:5px;font-size:14px;font-weight:bold;padding-top:20px\"><tr><td style=\"font-weight:bold\"><span style=\"color:blue\">GREAT NEWS!</span> We have locations in your zipcode. Due to laws in NY, NJ and RI we can only place your order by phone. <span style=\'color:red;background-image:none\'>Please call us at 877-317-3178 </span> (M-F 8am-9pm | Sat 10am-4pm EST) so you can get tested today!</span></td></tr></table>";
            return false;
        }

      // 3 ZIP ranges for MD per http://www.structnet.com/instructions/zip_min_max_by_state.html -- JH
      if ((zipvalue == 20331) || (zipvalue >= 20335 && zipvalue<=20797) || (zipvalue >= 20812 && zipvalue<=21930))
        {
            codeAddress();
            mapResults.innerHTML = '';
            mapResults.innerHTML = "<table style=\"padding-left:5px;padding-right:5px;font-size:15px;font-weight:bold;padding-top:20px\"><tr><td style=\"font-weight:bold\"><span style=\"color:red\">SORRY!</span> We have disabled ordering capability for Maryland. Please try a different zipcode or call us for assistance. </span></td></tr></table>";
            return false;
        }
        
        if (!markers.length)
        {
           mapResults.innerHTML = '';
           mapResults.innerHTML = "<table style=\"padding-left:5px;padding-right:5px;font-size:14px;font-weight:bold;padding-top:20px\"><tr><td style=\"font-weight:bold\">Sorry, no results found for this zipcode. Please try a different zipcode or call us for assistance.</td></tr></table>";
           return false;
        }
        
        try {(markers[1].getAttribute('id'))}
        catch (e)
        {
            mapResults.innerHTML = '';
            mapResults.innerHTML = "<table style=\"padding-left:5px;padding-right:5px;font-size:14px;font-weight:bold;padding-top:20px\"><tr><td style=\"font-weight:bold\">Sorry, no results found for this zipcode. Please try a different zipcode or call us for assistance.</td></tr></table>";
            return false;
        }

      for (var i = 1; i < markers.length; i++) 
      {
        if (i==1) {markerLetter = 'A';classNum='one';}
        if (i==2) {markerLetter = 'B';classNum='two';}
        if (i==3) {markerLetter = 'C';classNum='three';}
        if (i==4) {markerLetter = 'D';classNum='four';}
        if (i==5) {markerLetter = 'E';classNum='five';}
        if (i==6){markerLetter = 'F';classNum='six';}
        if (i==7) {markerLetter = 'G';classNum='seven';}
        if (i==8) {markerLetter = 'H';classNum='eight';}
        if (i==9) {markerLetter = 'I';classNum='nine';}
        if (i==10) {markerLetter = 'J';classNum='ten';}
        if (i==11) {markerLetter = 'K';classNum='eleven';}
        if (i==12) {markerLetter = 'L';classNum='twelve';}
        if (i==13) {markerLetter = 'M';classNum='thirteen';}
        if (i==14) {markerLetter = 'N';classNum='fourteen';}
        if (i==15) {markerLetter = 'O';classNum='fifteen';}  
        
        var image = new google.maps.MarkerImage('http://www.google.com/mapfiles/marker'+markerLetter+'.png', 
                      new google.maps.Size(20, 34), 
                      new google.maps.Point(0, 0), 
                      new google.maps.Point(10, 34)); 

        var markerImg =  'http://www.google.com/mapfiles/marker'+markerLetter+'.png';
        
        var latlng = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")),
                                    parseFloat(markers[i].getAttribute("lng")));
        
        if (i==1) var latPrime = latlng;
        
          var locid = markers[i].getAttribute('id');
					var name = markers[i].getAttribute('name');
					var labAddr = markers[i].getAttribute('address');
					var labCity = markers[i].getAttribute('city');
					var labState = markers[i].getAttribute('state');
					var labZipcode = markers[i].getAttribute('zip');
					var labHours = markers[i].getAttribute('hours');
					var labPhone = markers[i].getAttribute('telephone');
					var labType = markers[i].getAttribute('lab-id');
          var distance = parseFloat(markers[i].getAttribute('distance'));
          
          var labName = "Quest Diagnostics";
          if (labType == "129") labName = "Labcorp"; 
          
          var marker = new google.maps.Marker
        ({
          position: latlng, map: map,
          icon: image,
          title: labName+' - '+labAddr
        });
          if (i==1) mapResults.innerHTML = '';
          var mapEntry = createAddressHTML(name, locid, labType, labAddr, labCity, labState, labZipcode, labPhone, labType, distance, labHours,i,markerLetter,classNum,markerImg,affPhone);
          //mapResults.appendChild(mapEntry);
          mapResultsString = mapResultsString+mapEntry;
       }
       $("#map-results").html(mapResultsString); 
       map.setCenter(latPrime);
       map.setZoom(11);
     });
	}
	
	
	function createAddressHTML(name, locid, labType, labAddr, labCity, labState, labZipcode, labPhone, labType, distance, labHours,i,markerLetter,classNum,markerImg,affPhone)
	{
      //var mapEntry = document.createElement('li');
      //mapEntry.className = classNum;
      var labName = "Quest Diagnostics";
      if (labType == "129") labName = "Labcorp"; 
      distance = distance.toFixed(1);
 
      var hoursArray = labHours.split('|');
      var hoursHTML='';
      for (i=0;i<hoursArray.length;i++)
      {
        hoursHTML = hoursHTML + "<tr><td>"+hoursArray[i]+"</td></tr>";
      } 

      var selectLocID = 'selectLoc'+markerLetter;
      // alert (hoursHTML);
      //labHours str.replace("Microsoft", "W3Schools");
      
      //var clickFuntion = onclick='selectLocation('"+locid+"','"+name+"','"+labAddr+"','"+labCity+"','"+labState+"','"+labZipcode+"','"+labHours+"','"+labPhone+"','"+labType+"')'
      markerImg = 'http://www.google.com/mapfiles/marker'+markerLetter+'.png';
      var html = "<table style=\"margin-top:10px;border-bottom:1px solid #0E7B77;padding-bottom:5px;padding-left:5px;font-size:10px;width:100%\"><tr><td valign=\"top\" style=\"vertical-align:top;padding-right:10px;width:25px\"><img src=\""+markerImg+"\"</td> <td style=\"vertical-align:top;width:150px\" valign=\"top\"><table style=\"\"><tr><td colspan=3 style=\"font-weight:bold;font-size:13px;padding-bottom:10px\">"+labAddr+"</td></tr><tr> <td style=\"vertical-align:top\" valign=\"top\"><table style=\"font-weight:11px;font-family:arial;vertical-align:top;padding:0px\"><tr></tr><tr><td style=\"font-weight:bold\">"+distance+" MILES AWAY</td></tr><tr><td>"+labName+"</td></tr><tr><td>"+labCity+", "+labState+" "+labZipcode+"</td></tr><tr><td style=\"font-size:12px;color:#289674;font-weight:bold\">"+affPhone+"</td></tr></table></td></tr><tr><td style=\"vertical-align:top\" valign=\"top\"> <table><tr><td style=\"font-weight:bold\">Hours</td></tr>"+hoursHTML+"</table></td></tr><tr><td style=\"vertical-align:text-top;text-align:left;padding-top:10px;padding-bottom:10px\"><a href=\"javascript:void(0)\" onclick=\"selectLocation('"+locid+"','"+name+"','"+labAddr+"','"+labCity+"','"+labState+"','"+labZipcode+"','"+labHours+"','"+labPhone+"','"+labType+"');\"><img src=\"images/select-std-testing-location.gif\"></a></td></tr></table> </td></tr></table>";
      
      
      
      //mapEntry.innerHTML = html;
      //alert (html);
      //document.getElementById(selectLocID).onclick = function () { alert('here'); };
  		
  		return html;
	}
	
	function submitForm(word)
	{
     alert(word);
  }
	
	function selectLocation(locid, name, labAddr, labCity, labState, labZipcode, labHours, labPhone, labType) 
    {
              var zipinput = $('#addressInput').val();
    	        
              document.locationForm.labPhone.value = labPhone;
              document.locationForm.labHours.value = labHours;
              document.locationForm.labName.value = name;
              document.locationForm.labAddr.value = labAddr;
              document.locationForm.labCity.value = labCity;
              document.locationForm.labState.value = labState;
              document.locationForm.labZipcode.value = labZipcode;
              document.locationForm.labID.value = locid;
              document.locationForm.zipinput.value = zipinput;
              document.locationForm.labType.value = labType;
      		    document.locationForm.submit();
    }
    
