# Script called by Certify the Web to process the generated certificate
# It calls the PHP scripts and returns.
#
# Author: Bill Seddon
# License: GPL 3.0
#

$root = 'C:\ProgramData\Certify\certes\assets\'
$phpEXE = "C:\Web\PHP\v7.0.5\php.exe"
$phpScript = "$($root)php\extract-certificate.php"
Start-Process -NoNewWindow -FilePath $phpEXE -ArgumentList "-f $phpScript" -RedirectStandardOutput "$($root)pfx\output.txt"
