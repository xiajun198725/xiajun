<?php
include('Crypt/RSA.php');

$rsa = new Crypt_RSA();
$pubKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCG6IR5fWDnngh4BxWIP1Jcio9sGOQurkS3bJxRng6ZjlM8nmg0ZDVIxZ9AVgPCFeYpz3DOiNN1+3wQ+F8iFpKjL42rDxN2H8fjmtDCZBmHzodefTAtmql6iX/Gmo4nPjtq3g9lYZXH3yXDrRdJshQuQf5Dgxf1z4M+i7FJ1EPiBQIDAQAB';
$rsa->loadKey($pubKey, CRYPT_RSA_PRIVATE_FORMAT_PKCS1); // public key
$plaintext = 'attach=&bank_card_no=123&body=&cred_code=456&cred_type=1&curId=156&order_type=1&out_order_time=20150701120622&out_trade_no=1435723582545554&payer_mob_no=789&payer_name=bfmm&subject=membership&total_fee=10000';
$plaintext = 'attach=&bank_card_no=123&body=&cred_code=789&cred_type=1&curId=156&order_type=1&out_order_time=20150709183517&out_trade_no=1436438117835554&payer_mob_no=000&payer_name=456&subject=&total_fee=10000';
$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_OAEP);
$ciphertext = $rsa->encrypt($plaintext);
echo base64_encode($ciphertext);
echo "\n<br/>\n<br/>";

$pubKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCA1dpTeBYU/1vbicX3wvRDmZZpaqPqgERl/Ay2TSUeZ5sYTxjeoeARIj6OC7VYlSANXEnWrVZb6fzkPCAd39Gnkn3Rkm5wt0KyF3Q1u02wa4GCgnxEAOeMub8luqVt6fNz9h2FdpiE9IzIiKXNFkASxLzO3ui5ygQnlN1wqQA5/wIDAQAB';
$rsa->loadKey($pubKey, CRYPT_RSA_PRIVATE_FORMAT_PKCS1); // public key
$plaintext = 'attach=&bank_card_no=6214830280658888&body=&cred_code=510112199001010000&cred_type=1&curId=156&order_type=1&out_order_time=20141230151618&out_trade_no=no9400234539532526&payer_mob_no=13888886666&payer_name=张三&subject=话费充值&total_fee=100';
$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
$ciphertext = $rsa->encrypt($plaintext);
echo base64_encode($ciphertext);

exit;

$privKey = file_get_contents('amaxproducts.ppk');
$rsa->loadKey($privKey); // private key
echo $rsa->decrypt($ciphertext);
