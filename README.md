# CorpSMS notifications channel for Laravel 5.3+

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/corp-sms.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/corp-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/corp-sms/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/corp-sms)
[![StyleCI](https://styleci.io/repos/65589451/shield)](https://styleci.io/repos/65589451)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/aceefe27-ba5a-49d7-9064-bc3abea0abeb.svg?style=flat-square)](https://insight.sensiolabs.com/projects/aceefe27-ba5a-49d7-9064-bc3abea0abeb)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/corp-sms.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/corp-sms)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/corp-sms/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/corp-sms/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/corp-sms.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/corp-sms)

This package makes it easy to send notifications using [CorpSMS.ru](//CorpSMS.ru) (aka СМС–Центр) with Laravel 5.3+.

## Contents

- [Installation](#installation)
    - [Setting up the CorpSMS service](#setting-up-the-corpsms-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require laravel-notification-channels/corp-sms
```

Then you must install the service provider:
```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\CorpSMS\CorpSMSServiceProvider::class,
],
```

### Setting up the CorpSMS service

Add your CorpSMS login, secret key (hashed password) and default sender name (or phone number) to your `config/services.php`:

```php
// config/services.php
...
'corpsms' => [
    'login'  => env('SMS_AUTH_USER'),
    'secret' => env('SMS_AUTH_PASSWORD'),
    'sender' => 'CorpSMS'
],
...
```

> If you want use other host than `CorpSMS.ru`, you MUST set custom host WITH trailing slash.

```
// .env
...
SMS_ENDPOINT=http://203.151.230.33/CorporateSMS_API/
...
```

```php
// config/services.php
...
'corpsms' => [
    ...
    'host' => env('SMS_ENDPOINT'),
    ...
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\CorpSMS\CorpSMSMessage;
use NotificationChannels\CorpSMS\CorpSMSChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [CorpSMSChannel::class];
    }

    public function toCorpSMS($notifiable)
    {
        return CorpSMSMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForCorpSMS()` method, which returns a phone number
or an array of phone numbers.

```php
public function routeNotificationForCorpSMS()
{
    return $this->phone;
}
```

### Available methods

`from()`: Sets the sender's name or phone number.

`content()`: Set a content of the notification message.

`sendAt()`: Set a time for scheduling the notification message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email jarak.krit@gmail.com instead of using the issue tracker.


## Credits

- [Jarak Kritkiattisak](https://github.com/mycools)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.