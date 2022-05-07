<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        

        $encrypter = service('encrypter');
        $plainText = 'This is a plain-text message!';
        $ciphertext = $encrypter->encrypt($plainText);
        // echo $ciphertext;
        echo $encrypter->decrypt($ciphertext);
        return view('welcome_message');
    }
}
