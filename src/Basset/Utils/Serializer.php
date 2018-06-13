<?php


namespace Basset\Utils;


class Serializer {

    private $algorithm;

    public function __construct()
    {
        $this->algorithm = 'sha512';
    }

    /**
     * Secure serializing.
     *
     * @param mixed $data The data for serializing.
     * @param string $key The secret key of HMAC.
     * @param string $algo The algorithm for generatingg the hash code.
     * @return string Serialized string with hash, or false if failed.
     */
    public function serialize($data, $key)
    {
        $algo = $this->algorithm;
        $str = serialize($data);
        $hash = hash_hmac($this->algorithm, $str, $key);
        return $hash !== false ? $hash.'|'.$str : false;
    }
    /**
     * Secure unserializing.
     *
     * @param mixed $str Secure serialized data from Serializer::serialize().
     * @param string $key The secret key of HMAC.
     * @param string $algo The algorithmfor generatingg the hash code.
     * @return mixed Unserialized data, or false if the expected hash and the hash value are different.
     */
    public function unserialize($str, $key)
    {
        $algo = $this->algorithm;
        list($hash, $str) = explode('|', $str, 2);
        $hash_confirm = hash_hmac($this->algorithm, $str, $key);
        return hash_equals($hash, $hash_confirm) ? unserialize($str) : false;
    }

}