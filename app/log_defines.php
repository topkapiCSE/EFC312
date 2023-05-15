<?php


class LOGIN_LOG_DEFINES{
    CONST TABLE = "log_login";       //Login loglarının tutulduğu tablo ismi
    CONST SUCCESS = "Login.success"; //Başarılı bir şekilde giriş yapıldı
    CONST LOGIN_PAGE = "Login.page"; //Login sayfası görüntülendi
    CONST LOGOUT = "Login.logout"; //Başarılı bir şekilde çıkış yapıldı
    CONST NOT_FOUND_USER = "Login.notFoundUser"; //Giriş yapılmaya çalışılan kullanıcı hesabı kayıtlı değil
    CONST WRONG_PASS = "Login.wrongPass"; //Hatalı Şifre
    CONST OVER_LOGIN_TRY = "Login.overTry"; //Kullanıcı çok fazla başarısız giriş denemesi yaptı ve denemeye devam ediyor
}


class SIGN_LOG_DEFINES{
    CONST TABLE = "log_sign";       //Sign loglarının tutulduğu tablo ismi
    CONST SUCCESS = "Sign.success"; //Başarılı bir şekilde kayıt yapıldı
    CONST ALREADY_REGISTERED = "Sign.alreadyRegistered"; //Kayıt olunmalaya çalışılan e-posta adresi kullanılıyor
    CONST UNKNOWN = "Login.unkown"; //Bir hata meydana geldi. DB işlemleri hatası

}