<?php

namespace App\Libraries;

class Encrypter
{

    private $ciphering = "AES-128-CTR";
    private $encryptionIV = "mIndyOUrOwNbuSNs";
    private $encryptionKey = "RSoft";

    public function encrypt($text)
    {
        return openssl_encrypt($text, $this->ciphering, $this->encryptionKey, 0, $this->encryptionIV);
    }

    public function decrypt($encryptedText)
    {
        return openssl_decrypt($encryptedText, $this->ciphering, $this->encryptionKey, 0, $this->encryptionIV);
    }
}
