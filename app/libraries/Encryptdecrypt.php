<?php
class Encryptdecrypt{
    private $iv; #Same as in JAVA
    private $key; #Same as in JAVA
    private $secret_key; #Same as in JAVA
    private $version_code = 0;
    private static $OPENSSL_CIPHER_NAME = "aes-128-cbc"; //Name of OpenSSL Cipher
    private static $CIPHER_KEY_LEN = 1024; //128 bits
    function __construct($params = array()){
        $param = $params;
        $this->iv = isset($param['information_key'])?$param['information_key']:'';
        $this->key = isset($param['token'])?$param['token']:'';
        $this->secret_key = isset($param['secret_key'])?$param['secret_key']:'';
        $this->version_code = isset($param['version_code'])?$param['version_code']:0;
    }

    function _decryptPrivateKey($secret_key=''){
        $secret_key = base64_decode($secret_key?:$this->secret_key);
        //$secret_key = base64_decode(base64_decode($secret_key?:$this->secret_key));
        $fp = fopen("./assets/certificates/10.184.38.64.key", "r");
        $privateKey = fread($fp, 8192);
        fclose($fp);
        $res = openssl_get_privatekey($privateKey);
        if(!$res){
            echo "Cannot get public key";die;
        }
        openssl_private_decrypt($secret_key, $decrypted, $privateKey);
        return $decrypted;
    }


    function _decryptpublicCert($secret_key=''){
        $secret_key = base64_decode($secret_key?:$this->secret_key);
        $fp=fopen("./assets/certificates/10.184.38.64.mpesachama.crt","r");
        $pub_key_string=fread($fp,8192);
        fclose($fp);
        $PK=openssl_get_publickey($pub_key_string);
        if (!$PK) {
            echo "Cannot get public key";die;
        }
        openssl_public_decrypt($secret_key,$decrypted,$pub_key_string);
        return $decrypted;
    }

    function _decryptMobilePrivateCert($secret_key=''){
        $secret_key = base64_decode($secret_key?:$this->secret_key);
        $fp=fopen("./assets/certificates/mobile.key","r");
        $privateKey=fread($fp,8192);
        fclose($fp);
        $PK=openssl_get_privatekey($privateKey);
        if (!$PK) {
            echo "Cannot get public key";die;
        }
        openssl_private_decrypt($secret_key,$decrypted,$privateKey);
        return $decrypted;
    }

    function encryptPrivatekey($string=''){
        $fp = fopen("./assets/certificates/10.184.38.64.key", "r");
        $privateKey = fread($fp, 8192);
        fclose($fp);
        $res = openssl_get_privatekey($privateKey);
        if(!$res){
            echo "Cannot get public key";die;
        }
        openssl_private_encrypt($string,$crypttext,$privateKey);
        return(base64_encode($crypttext));
    }

    function encryptPublicCertMobile($string=''){
        $fp = fopen("./assets/certificates/mobile.crt", "r");
        $pub_key_string = fread($fp, 8192);
        fclose($fp);
        $res = openssl_get_publickey($pub_key_string);
        if(!$res){
            echo "Cannot get public key";die;
        }
        openssl_public_encrypt($string,$crypttext,$pub_key_string);
        return(base64_encode($crypttext));
    }

    function _encryptPublicCert($string=''){
        $fp=fopen("./assets/certificates/10.184.38.64.mpesachama.crt","r");
        $pub_key_string=fread($fp,8192);
        fclose($fp);
        $PK=openssl_get_publickey($pub_key_string);
        if (!$PK) {
            echo "Cannot get public key";die;
        }
        openssl_public_encrypt($string,$crypttext,$pub_key_string);
        return(base64_encode($crypttext));
    }


    function encryptPublic($string,$isBinary = false,$key=''){
        $iv = time().'123456';
        $secret_key = '1234656789';
        $key = $this->_encryptPublicCert($key?:$this->secret_key);
        if (strlen($key) < Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", Encryptdecrypt::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = substr($key, 0, Encryptdecrypt::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        $encodedEncryptedData = base64_encode(openssl_encrypt($string, Encryptdecrypt::$OPENSSL_CIPHER_NAME, $key, 1, $this->iv));
        $encodedIV = base64_encode($this->iv);
        $encryptedPayload = $encodedEncryptedData.":".$encodedIV;
        return $encryptedPayload;
    }

    function decryptPrivate($encrypted,$isBinary = false){
        $encyption_key = $this->_decryptPrivateKey();
        if (strlen($encyption_key) < Encryptdecrypt::$CIPHER_KEY_LEN) {
            $encyption_key = str_pad("$encyption_key", Encryptdecrypt::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($encyption_key) > Encryptdecrypt::$CIPHER_KEY_LEN) {
            $encyption_key = substr($str, 0, Encryptdecrypt::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        $parts = explode(':', $encrypted); //Separate Encrypted data from iv.
        $decryptedData = openssl_decrypt(base64_decode($parts[0]), Encryptdecrypt::$OPENSSL_CIPHER_NAME, $encyption_key, 1, base64_decode($parts[1]));
        return $decryptedData;
    }

    function encryptPrivate($key,$data) {
        $iv = time().'123456';
        if (strlen($key) < Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", Encryptdecrypt::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, Encryptdecrypt::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        $encodedEncryptedData = base64_encode(openssl_encrypt($data, Encryptdecrypt::$OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData.":".$encodedIV;
        return $encryptedPayload;
    }

    function decryptPublic($key, $data) {
        $key = $this->_decryptpublicCert($key);
        if (strlen($key) < Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", Encryptdecrypt::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, Encryptdecrypt::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        $parts = explode(':', $data); //Separate Encrypted data from iv.
        $decryptedData = openssl_decrypt(base64_decode($parts[0]), Encryptdecrypt::$OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
        return $decryptedData;
    }

    function decryptMobile($key, $data){
        $key = $this->_decryptMobilePrivateCert($key);
        if (strlen($key) < Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", Encryptdecrypt::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > Encryptdecrypt::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, Encryptdecrypt::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        $parts = explode(':', $data); //Separate Encrypted data from iv.
        $decryptedData = openssl_decrypt(base64_decode($parts[0]), Encryptdecrypt::$OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
        return $decryptedData;
    }

    protected function hex2bin($hexdata){
        $bindata = '';
        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }
}
?>
