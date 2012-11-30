<?php

    require_once './wp-load.php';

    require_once ABSPATH.'includes/processOrderFunctions.php';

    echo "Refeshing this page should send an e-mail to jhartmann, using the same function(s) that the checkout page uses for emails.";

    $custID = 'MOOT';
    $email = 'jhartmann@pinpointmd.com';
    $subject = 'PinPointMD STD WordPress Email Test';
    $emailmessage = "<p>Only a test:<br />Cust ID: {$custID}<br />Email: {$email}<br />Subject: {$subject}</p>";

    echo $emailmessage;
    
    if(sendCustomerEmail($custID, $email, $subject, $emailmessage))
    {
        echo "!!!";
    } else {
        echo "???";
    }

?>