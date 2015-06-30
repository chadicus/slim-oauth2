# slim-oauth2

This library is in it's very earliest stages. Most of the code doesn't work. Just an idea I had and wanted to get something started.

[![Build Status](http://img.shields.io/travis/chadicus/slim-oauth2.svg?style=flat)](https://travis-ci.org/chadicus/slim-oauth2)            
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/chadicus/slim-oauth2.svg?style=flat)](https://scrutinizer-ci.com/g/chadicus/slim-oauth2/)
[![Code Coverage](http://img.shields.io/coveralls/chadicus/slim-oauth2.svg?style=flat)](https://coveralls.io/r/chadicus/slim-oauth2)       
[![Latest Stable Version](http://img.shields.io/packagist/v/chadicus/slim-oauth2.svg?style=flat)](https://packagist.org/packages/chadicus/slim-oauth2)
[![Total Downloads](http://img.shields.io/packagist/dt/chadicus/slim-oauth2.svg?style=flat)](https://packagist.org/packages/chadicus/slim-oauth2)  
[![License](http://img.shields.io/packagist/l/chadicus/slim-oauth2.svg?style=flat)](https://packagist.org/packages/chadicus/slim-oauth2)           
[![Documentation](https://img.shields.io/badge/reference-phpdoc-blue.svg?style=flat)](http://chadicus.github.io/slim-oauth2) 

Routes and Middleware for Using OAuth2 within a Slim Framework API



## The plan:

* Set up your slim app normally
* Create your [oauth2 server](https://github.com/bshaffer/oauth2-server-php) normally. (i.e. storage)
* Add Middleware
```php
$app->add(new \Slim\OAuth2\Middleware\Authorization($app, $server);
```
* Add Routes
```php
$app->post('/token', new \Slim\OAuth2\Routes\Token($app, $server));
$app->post('/authorize', new \Slim\OAuth2\Routes\Authorize($app, $server));
```
* Works



## Still Needed

* /resource endpoint
* /recieveCode endpoint
* Bootstrapper that would set up the slim app properly with one call?
* Lots of other things

