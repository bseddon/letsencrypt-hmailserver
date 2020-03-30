<?php

/**
 * This script uses COM to stop and start the hMailServer service
 * so a new certificate will be loaded. 
 *
 * Author: Bill Seddon
 * Copyright: GPL 3.0
 */

$hmail = new com("hMailServer.Application") or die("Unable to instantiate hMail");
$hmail->Authenticate ("Administrator", "60803812aws");
echo "authenticated\n";
$hmail->stop();
echo "stopped\n";
sleep(5);
echo "starting\n";
$hmail->start();

$hmail = null;
