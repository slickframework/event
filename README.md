# Slick Event

[![Latest Version](https://img.shields.io/github/release/slickframework/event.svg?style=flat-square)](https://github.com/slickframework/event/releases)
[![License: MIT](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![CI Status](https://img.shields.io/github/actions/workflow/status/slickframework/event/continuous-integration.yml?style=flat-square)](https://github.com/slickframework/event/actions/workflows/continuous-integration.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/event/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/event/)
[![Downloads](https://img.shields.io/packagist/dt/slick/event.svg?style=flat-square)](https://packagist.org/packages/slick/event)

**Slick Event** is a modern and lightweight PHP library that provides a clean,
flexible, and PSR-14-compliant event dispatching system. Built for developers
who value simplicity, testability, and performance, it allows you to decouple
your application logic using event-driven architecture without the overhead of heavier frameworks.

Whether you're working with a microservice, a modular monolith, or a full-stack application, Slick Event integrates seamlessly and gives you the tools to manage domain events, listeners, and dispatchers with ease.

---

### ✨ Features

* ✅ **PSR-14 compliant**: Follows the PHP-FIG Event Dispatcher standard for interoperability.
* 🔁 **Synchronous event dispatching** with support for multiple listeners.
* ⚙️ **Simple and intuitive API** for registering and dispatching events.
* 🧪 **Spec-driven development** using PHPSpec ensures reliability and clean design.
* 🧩 **Framework-agnostic**: Use it in any PHP project, regardless of framework.

This package follows:

* [PSR-2: Coding Style Guide](https://www.php-fig.org/psr/psr-2/)
* [PSR-4: Autoloading Standard](https://www.php-fig.org/psr/psr-4/)
* [Semantic Versioning 2.0.0](https://semver.org)

---

## 🚀 Installation

Install via Composer:

```bash
composer require slick/event
```

---

## 🧩 Basic Usage

```php
use Slick\Event\EventDispatcher;
use Slick\Event\Event;

$dispatcher = new EventDispatcher();

$dispatcher->listen(SomeEvent::class, function (SomeEvent $event) {
    // Handle event
});

// Dispatch event
$dispatcher->dispatch(new SomeEvent());
```

---

## ✅ Usage

### 1. Define an Event and a Listener

```php
namespace App\Domain\Event;

final class UserWasRegistered
{
    public function __construct(public string $email) {}
}
```

```php
namespace App\Application\Listener;

use App\Domain\Event\UserWasRegistered;
use Slick\Event\Application\Attribute\AsEventListener;

#[AsEventListener(event: UserWasRegistered::class)]
final class SendWelcomeEmail
{
    public function __invoke(UserWasRegistered $event): void
    {
        // Send welcome email to $event->email
    }
}
```

---

### 2. Set Up the ListenerProvider and Dispatcher

```php
use Slick\Event\Infrastructure\AttributeListenerResolver;
use Slick\Event\Infrastructure\AttributeListenerProvider;
use Slick\Event\EventDispatcher;
use App\YourContainer;

$container = new YourContainer(); // PSR-11 compliant

$resolver = new AttributeListenerResolver(
    __DIR__.'/src/Application/Listener',
    $container
);

$provider = new AttributeListenerProvider($resolver);
$dispatcher = new EventDispatcher($provider);
```

---

### 3. Dispatch the Event

```php
$dispatcher->dispatch(new UserWasRegistered('user@example.com'));
```

All listeners discovered via `#[AsEventListener]` will be automatically executed.

---

## ✅ Testing

We use [PHPSpec](http://www.phpspec.net/) for unit testing.

Run tests with:

```bash
vendor/bin/phpspec run
```

or

```bash
composer test
```
---

## 🤝 Contributing

Please read our [CONTRIBUTING.md](CONTRIBUTING.md) guidelines.

---

## 🛡 Security

If you discover a security vulnerability, please email us at **[slick.framework@gmail.com](mailto:slick.framework@gmail.com)** instead of using the issue tracker.

---

## 🙏 Credits

* [Slick Framework Team](https://github.com/slickframework)
* [All Contributors](https://github.com/slickframework/event/graphs/contributors)

---

## 📄 License

This package is open-source software licensed under the [MIT License](LICENSE).