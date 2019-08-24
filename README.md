BEAR.FirebaseAuthenticationModule
================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/badges/build.png?b=master)](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/build-status/master)
[![Build Status](https://travis-ci.org/piotzkhider/BEAR.FirebaseAuthenticationModule.svg?branch=master)](https://travis-ci.org/piotzkhider/BEAR.FirebaseAuthenticationModule.svg?branch=master)

[Firebase](https://github.com/kreait/firebase-php) Authentication Module for [BEAR.Sunday](https://github.com/bearsunday/BEAR.Sunday)

## Installation

### Composer install

```bash
$ composer require piotzkhider/firebase-authentication-module
```
 
### Module install

```php
use Piotzkhider\FirebaseAuthenticationModule\FirebaseAuthenticationModule;
```

```php
class AppModule extends AbstractAppModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $appDir = $this->appMeta->appDir;
        require_once $appDir . '/env.php';
        ...
        $this->install(new FirebaseAuthenticationModule(getenv('GOOGLE_APPLICATION_CREDENTIALS')));
        ...
    }
}
```

## @Auth

`@Auth`は認証のためのアノテーションです。  
メソッドの実行前に認証処理が実行され、認証に失敗すると[RFC6750](https://tools.ietf.org/html/rfc6750)に準拠した（つもりの）レスポンスを返却します。

```php
class Tasks extends ResourceObject
{
    /**
     * @Auth
     */
    public function onGet(): ResourceObject
    {
```

認証されたユーザをメソッドの引数に注入することができます。  
そのためには依存を受け取る引数を`@Auth`アノテーションのプロパティに指定し、[`@Assisted`](https://github.com/ray-di/Ray.Di#assisted-injection)による注入と同様に引数リストの終わりに移動して`null`をデフォルトとして与える必要があります。

```php
class Tasks extends ResourceObject
{
    /**
     * @Auth(user="user")
     */
    public function onGet(UserRecord $user = null): ResourceObject
    {
```

