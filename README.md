# Lottery by Nodeloc

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/nodeloc/lottery.svg)](https://packagist.org/packages/nodeloc/lottery) [![OpenCollective](https://img.shields.io/badge/opencollective-fof-blue.svg)](https://opencollective.com/fof/donate) [![Patreon](https://img.shields.io/badge/patreon-datitisev-f96854.svg?logo=patreon)](https://patreon.com/datitisev)

A [Flarum](http://flarum.org) extension. A Flarum extension that adds lottery to your discussions.

### Installation

```sh
composer require nodeloc/lottery:"*"
```

#### Migrating from ReFlar Lottery

Make sure you've updated to the latest `reflar/lottery` version and run `php flarum migrate` BEFORE installing `nodeloc/lottery`.
You will not be able to install this extension if you have a version of ReFlar Lottery older than v1.3.4 as well.

```sh
$ composer require nodeloc/lottery
$ php flarum migrate
```

### Updating

```sh
composer update nodeloc/lottery
```

### Metadata update

To improve performance, Lottery calculates and stores the number of votes when it changes.

As long as the extension is active, Lottery will automatically keep those numbers up to date and you don't need to do anything.

If you are updating from a version prior to 0.3.3, if you disabled the extension for a while or if you made manual changes to the database you should run the following command to refresh the numbers:

```sh
php flarum nodeloc:lottery:refresh
```

You can only run the command when the extension is enabled in the admin panel.

### Links

- [Packagist](https://packagist.org/packages/nodeloc/lottery)
- [GitHub](https://github.com/packages/Nodeloc/lottery)
- [Discuss](https://www.nodeloc.com)

An extension by [Nodeloc](https://github.com/Nodeloc).
