# Let's Encrypt certificates with hMailServer

This is a repository documenting what has worked for me, how to automate the renewal process and why.  If you do not know what [Let's Encrypt](https://letsencrypt.org/) is or don't use [hMailServer](https://www.hmailserver.com/) this repository is not for you and these terms will not be explained.  This repository also assumes hMailServer is installed and configured.  It is not a tutorial on using the server.

There are lots of ways to approach the problem of using Let's Encrypt SSL certificates with hMailServer.  This is mine.  I'm not advocating as the best way, just as a way.

## Other assumptions

+ IIS is installed on the same Windows server that host hmail
+ PHP is installed and configured for use by IIS. 
+ [Certify the Web](https://certifytheweb.com/) is installed, configured and you can 
+ PowerShell is installed (Certify the Web can run a PowerShell script when a certificate has been generated)

I am using PHP for at least two reasons:

1. I use the phpWebAdmin tool for hMailServer so PHP is a requirement anyway.
2. It's necessary to extract the certificate and private key from a PFX file generated by Certify the Web and PHP can do this out of the box where PowerShell cannot (not easily anyway) and its then not necessary to use another tool such as openssl.

I am using Certify the Web because:

1. Its a good tool
2. It supports domain authorization so IIS does not have to be open to the public
3. It will renew certificate automatically and
4. It can run a script after certificate generation so the certificate can be applied to hMailServer automatically and the server can be restarted

## Steps to be able to use a certificate with hMailServer

1. Get a certificate PFX
2. Extract the certificate and private key as PEM files
3. Add the certificate and private key to hMailServer
4. Add ports for secure access to SMTP, POP3 and IMAP services and apply the certificate to each service
5. Restart the hMailServer service so it will load up the new certificate

Because Let's Encrypt certificates last only up to 90 days steps 1, 2 and 5 need to be repeated everytime the certificate needs to be replaced so it's best if its automated.  Steps 3 and 4 only apply the first time a certificate is applied.

## Installing

Certify the Web creates this folder:

C:\ProgramData\Certify\certes\assets\

which has one existing folder called 'pfx' into which it saves .pfx files containing the generated certificate and private key.  Save the folders and files in this repository to corresponding folders and files under the existing 'assets' folder.  

In practice you can save the files anywhere but these instructions and the scripts assume this location.  If you choose to use an alternative location you will also need to change the path used in the PowerShell [after-generate.ps1](ps/after-generate.ps1) and PHP [extract-certificate.php](php/extract-certificate.php) scripts.

You will also need to edit [after-generate.ps1](ps/after-generate.ps1) to modify the change the location of the PHP executable file.  I installed PHP using the Microsoft Web installer.  If you ave done the same then this file may be in the same location.

Next tell Certify the Web how to find the PowerShell script to run once a certificate has been generated.  The screenshot shows the steps to do this:

![Set a Certify the Web script](http://lyquidity-downloads.s3.amazonaws.com/github-images/certifytheweb.png)

1. Click the 'advanced options' check box
2. Select the scripting option
3. Select the PowerShell [after-generate.ps1](ps/after-generate.ps1)
4. Test

If there is a .pfx file in the 'pfx' folder then after testing there should be three additional files:

1. cert.pem
2. pkeypem
3. output.txt

The file output.txt will contain a review of the steps taken to process the .pfx file.  Any errors will be written to the error log device specified in the relevant php.imi file.
