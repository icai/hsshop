<?php

namespace App\Module\BaiduApp;

/**
 * API入参静态检查类
 * 可以对API的参数类型、长度、最大值等进行校验
 *
 **/
class NuomiRsaSign
{

    CONST RSA_PRIVATE_KEY = 'MIIEowIBAAKCAQEA92GwW8uvOHXq6Ul8SdiQMSvkIjyBHnTBOR1WvfGvetM40IgxZLUDr48n9CIzVHgFvnYN5st5o047nR48Rcy5uuoUl3kGk7AnPh91F3HRTNprJ6FnFoLKIvTvM4irYV5atkJH8ZsyZRr+AZZydOThcLDmn4bK+Uh7pxhWCe5V1vn01IRAum6B5RdT4Lufs5ilfwHh3ivrZdjTZeoB9K3+i6T63WDqpQqIrz7DEss0/XhX4cdH/tiNIDFMISxPKxTOqugEt/T4vFYs+PJ67xTwzNVRxsUtIqPNe5DXuVx3wfZcCtidsJQsHO1B14LY1hVI9vUewr3UOnB+j8WWO+4BJQIDAQABAoIBAQDBQKph+6uFYvDBj+utUKXME+qjKDsCDNmJgbbqqayZOfqvRUW404PumNnwaOtKeHycPmM7wgbjIVGGu0EAkh8huo2diyktJLZzXGW/i+WisRp2misLNd8tEcTvsDuZ0/jvWrYTM1daDo0RtnLxiH/o5fkz9DwKI6qdbXCBT5P1XEPKKBpQQvrPNwzH9pWVAQY9zCKrff7sV5aNOXTaqMwryI5GkeaarXoxYwfd9/+xJJ4vWQcqS/5+Y1qUaTwHmdAG51Kzwru3x7CrnLTEvEwJmuq36rFWzVnh5eeq37m/95dTRwfD+Q9eEdSH5sgaGvV4UEapITPadjD/VcIWcF89AoGBAP1GO9GiYcmDSkQA1eD/B1wrdRpaS+leVw9As9+X0WtzS3SaLTG5ITZZr21hUnNCV0Rbx8j1F248nAIm2L6c99GLfVcylpU7g3g/zVUdX5vcKXmQJrITr7EiBWlHmgegX7YO1yWh/mhO1kFGlirXybwoWkfpgTIN3znSVhPJBJsXAoGBAPoLOIaS0L8SSsT3E7R08NX3KulJ+nKIwwzwyImUBYcgYvRoiIlUsngTvWvRzeS46Zs7RxAYUH6/aWvrxsjCXfl0NHYsHH4P7KZ0tEd6m58Vv6i8hRU632f2hRklPjomSsMxF5Hu+BLsjPdTsHsqejcCuqqPF9s+fke1IFZ69bsjAoGAAUKGLabHIb97cRcn+TSLjtPQg08LrZ+Ag1zpCCWzLvul1nCl5Ods2N5dVwfy5wvfb0GdnsxJT40RoZkb3ubc/Lfa6cIgqaFgKAr5NIEu5pGTyz0CVERwzUrECCAJDhyoHTm5rEBACbjKrAxz1sa4BC2XNWBd+ifDgoAfWX3YfgUCgYAXLZEY0GUvQQfTSD/4W1qzysycgXIIyeqiuXMtZZ45j2P82e/GIybEALhvVSxrxkRJUm5c7JQRm7au/VUY6QODCyWNyrr9aIZ8S5cmhRQF5CM3BfqkJCfvYCeoVA53n3MQsu1HZspyHqFWj9htIlvf243oH4mLljhodz6/JXi/1wKBgAycBdtDRUuz199kZuzsIS6stz0NLPaT8qV71dQljdiguvOWDndUC3VS0bkSNyZ3q1t3TVYbNY7+gdMJN4J0EmMWkEtsXcWgFNSZqCUtK1imDEhFh5UjXJajC4ZvtmjJUqfeLpziIXHvCphKQ3TFu6NUysIxlK/SCrEnIgsPE4vm';
    CONST RSA_PUBLIC_KEY = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA92GwW8uvOHXq6Ul8SdiQMSvkIjyBHnTBOR1WvfGvetM40IgxZLUDr48n9CIzVHgFvnYN5st5o047nR48Rcy5uuoUl3kGk7AnPh91F3HRTNprJ6FnFoLKIvTvM4irYV5atkJH8ZsyZRr+AZZydOThcLDmn4bK+Uh7pxhWCe5V1vn01IRAum6B5RdT4Lufs5ilfwHh3ivrZdjTZeoB9K3+i6T63WDqpQqIrz7DEss0/XhX4cdH/tiNIDFMISxPKxTOqugEt/T4vFYs+PJ67xTwzNVRxsUtIqPNe5DXuVx3wfZcCtidsJQsHO1B14LY1hVI9vUewr3UOnB+j8WWO+4BJQIDAQAB';

    /**
     * @desc 私钥生成签名字符串
     * @param array $assocArr
     * @param $rsaPriKeyStr
     * @return bool|string
     * @throws Exception
     */
    public static function genSignWithRsa(array $assocArr, $rsaPriKeyStr)
    {
        $sign = '';
        if (empty($rsaPriKeyStr) || empty($assocArr)) {
            return $sign;
        }

        if (!function_exists('openssl_pkey_get_private') || !function_exists('openssl_sign')) {
            throw new Exception("openssl扩展不存在");
        }

        $priKey = openssl_pkey_get_private($rsaPriKeyStr);

        if (isset($assocArr['sign'])) {
            unset($assocArr['sign']);
        }

        ksort($assocArr); //按字母升序排序

        $parts = array();
        foreach ($assocArr as $k => $v) {
            $parts[] = $k . '=' . $v;
        }
        $str = implode('&', $parts);
        openssl_sign($str, $sign, $priKey);
        openssl_free_key($priKey);

        return base64_encode($sign);
    }

    /**
     * @desc 公钥校验签名
     * @param array $assocArr
     * @param $rsaPubKeyStr
     * @return bool
     * @throws Exception
     */
    public static function checkSignWithRsa(array $assocArr, $rsaPubKeyStr)
    {
        if (!isset($assocArr['sign']) || empty($assocArr) || empty($rsaPubKeyStr)) {
            return false;
        }

        if (!function_exists('openssl_pkey_get_public') || !function_exists('openssl_verify')) {
            throw new Exception("openssl扩展不存在");
        }

        $sign = $assocArr['sign'];
        unset($assocArr['sign']);

        if (empty($assocArr)) {
            return false;
        }
        ksort($assocArr); //按字母升序排序
        $parts = array();
        foreach ($assocArr as $k => $v) {
            $parts[] = $k . '=' . $v;
        }
        $str = implode('&', $parts);

        $sign = base64_decode($sign);
        $pubKey = openssl_pkey_get_public($rsaPubKeyStr);
        $result = (bool)openssl_verify($str, $sign, $pubKey);
        openssl_free_key($pubKey);

        return $result;
    }

}



