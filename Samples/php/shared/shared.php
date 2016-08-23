<?php

    $merchantCredentials = [
        "ID" => "417227771521",
        "KEY" => "I5T2R2K6V1Q3"
    ];
    
    $developerCredentials = [
        "ID" => "GTq2h4mXxLIBtzbOWLO2GwqZfOgK8BbT",
        "KEY" => "ICkrA2n6HIleJ663"
    ];

    function createHmac($toBeHashed, $password, $salt, $iv){
        $encryptHash = hash_pbkdf2("sha1", $password, $salt, 1500, 32, true);
        $encrypted = openssl_encrypt($toBeHashed, "aes-256-cbc", $encryptHash, 0, $iv);
        return $encrypted;
    }
    
    function getNonce(){
        $iv = openssl_random_pseudo_bytes(16);
        return [$iv, base64_encode(bin2hex($iv))];
    }
    
?>