BEAR.FirebaseAuthenticationModule
================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/badges/build.png?b=master)](https://scrutinizer-ci.com/g/piotzkhider/BEAR.FirebaseAuthenticationModule/build-status/master)
[![Build Status](https://travis-ci.org/piotzkhider/BEAR.FirebaseAuthenticationModule.svg?branch=master)](https://travis-ci.org/piotzkhider/BEAR.FirebaseAuthenticationModule)

[Japanese](README.ja.md)

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

## @Authenticate

`@Authenticate` is a annotation for authentication.  
Authentication process is executed before process the method.

```php
class Tasks extends ResourceObject
{
    /**
     * @Authenticate
     */
    public function onGet(): ResourceObject
    {
```
The authenticated user can be defined directly as a method argument.  
For that purpose it need specified in `@Authenticate` attribute.
And it set `null` of default parameter in last of arguments like [`@Assisted`](https://github.com/ray-di/Ray.Di#assisted-injection).

```php
class Tasks extends ResourceObject
{
    /**
     * @Authenticate(user="user")
     */
    public function onGet(UserRecord $user = null): ResourceObject
    {
```
