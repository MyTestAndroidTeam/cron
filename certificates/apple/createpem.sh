#!/bin/bash


#TESTS

if [[ -z "$1" ]]
then
	echo "Please insert the private key as first argument (a p12 file)";
	exit;
fi

if [ ! -f $1 ];
then
	echo "Private key ($1) does not exist";
	exit;
fi

if [[ -z "$2" ]]
then
	echo "Please enter the certificate in this folder (aps_production.cer or aps_development.cer)";
	exit;
fi

if [ ! -f $2 ];
then
	echo "Certificate file does not exist";
	exit;
fi

#START

echo "Creating pem certificate file";
openssl x509 -in $2 -inform der -out certificate.pem

echo "Creating pem key file. Remember your password!";
openssl pkcs12 -nocerts -out key.pem -in $1 

if [ ! -f $2 ];
then
	echo "Please place the production certificate in this folder ($2)";
	exit;
fi

echo "Creating keycertificate pem file";
cat certificate.pem key.pem > keycert.pem

echo "Creating directory /result";
mkdir result

echo "Moving files";
mv keycert.pem result
mv certificate.pem result
mv key.pem result

echo "";
echo "";
echo "Ok, keycert.pem has been generated.";
echo "Please test your results";
echo "";
echo "First, check if connection can be made with apple:";
echo "telnet gateway[.sandbox].push.apple.com 2195 (add .sandbox part if development certificate)";
echo "";
echo "After that, try connecting using your new certificate:";
echo "openssl s_client -connect gateway[.sandbox].push.apple.com:2195 -cert certificate.pem -key key.pem";
echo "";

cd result/
