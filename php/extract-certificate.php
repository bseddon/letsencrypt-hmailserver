<?php

/**
 * Certify the Web stores generated certificates as a PFX in the folder:
 * /ProgramData/Certify/certes/assets/pfx/
 * This script will be run after certificate generation to:
 * 1) Find the most recent PFX;
 * 2) If apppropriate extract the certificate as cert.pem and the 
 *    private key as pkey.pem in the same folder; and
 * 3) Restart the hMailServer service
 *
 * Author: Bill Seddon
 * Copyright: GPL 3.0
 */

$store = 'C:/ProgramData/Certify/certes/assets/pfx/';

$files = glob( "{$store}*.pfx" );
if ( ! count( $files ) ) return;

// Find the most recent
$mostRecentTime = 0;
$mostRecentFilename = "";

foreach( $files as $filename )
{
	$timedate = filemtime( $filename );
	if ( $mostRecentTime > $timedate ) continue;
	$mostRecentTime = $timedate;
	$mostRecentFilename = $filename;
}

if ( file_exists( "{$store}cert.pem"  ) )
{
	if ( filemtime( "{$store}cert.pem" ) > $mostRecentTime )
	{
		echo "The certificate has already been extracted from the most recent PFX file\n";
		return;
	}
}

echo "$mostRecentFilename\n";

$pfxFile = $mostRecentFilename;

$data = file_get_contents( $pfxFile );
if ( ! openssl_pkcs12_read($data, $certs, "") )
{
	echo("Failed to read the PFX store\n");
	return;
}
if ( ! isset( $certs['cert'] ) )
{
	$message = "Failed to access the certificate from the PFX store\n";
	echo( $message );
	return;
}

if ( ! isset( $certs['pkey'] ) )
{
	$message = "Failed to access the private key from the PFX store\n";
	echo( $message );
	return;
}

echo "Saving cert.pem\n";
$cert = $certs['cert'];
if ( file_exists("{$store}le-x3.pem") )
{
	echo "  adding le-x3.pem\n";
	$cert .= file_get_contents("{$store}le-x3.pem");
}
file_put_contents( "{$store}cert.pem", $cert );
echo "Saving pkey.pem\n";
file_put_contents( "{$store}pkey.pem", $certs['pkey'] );

include __DIR__ . "/restart-server.php";

echo "Finished\n";

?>
