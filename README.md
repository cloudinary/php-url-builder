[![Build Status](https://app.travis-ci.com/cloudinary/php-url-builder.svg)](https://app.travis-ci.com/cloudinary/php-url-builder)
[![license](https://img.shields.io/github/license/cloudinary/php-url-builder.svg?maxAge=2592000)](https://github.com/cloudinary/php-url-builder/blob/main/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/cloudinary/url-builder.svg?maxAge=2592000)](https://packagist.org/packages/cloudinary/url-builder)
[![Packagist](https://img.shields.io/packagist/dt/cloudinary/url-builder.svg?maxAge=2592000)](https://packagist.org/packages/cloudinary/url-builder/stats)

Cloudinary PHP URL Builder SDK
==================

## About

The Cloudinary PHP URL Builder SDK allows you to quickly and easily integrate your application with Cloudinary.
Effortlessly optimize and transform cloud's assets.

#### Note

This Readme provides basic installation and usage information. For the complete documentation, see
the [URL Builder SDK Guide](https://cloudinary.com/documentation/media_editing_api_sdks#installing_url_builder_sdks).

## Table of Contents

- [Key Features](#key-features)
- [Version Support](#Version-Support)
- [Installation](#installation)
- [Usage](#usage)
    - [Setup](#Setup)
    - [Transform and Optimize Assets](#Transform-and-Optimize-Assets)

## Key Features

- [Transform](https://cloudinary.com/documentation/php_video_manipulation#video_transformation_examples) and
  [optimize](https://cloudinary.com/documentation/php_image_manipulation#image_optimizations) assets.
- [Secure URLs](https://cloudinary.com/documentation/video_manipulation_and_delivery#generating_secure_https_urls_using_sdks)
  .

## Version Support

| SDK Version | PHP 5.x | PHP 7.x | PHP 8.0 | PHP 8.1 |
|-------------|---------|---------|---------|---------|
| 0.x         | x       | x       | v       | v       |

## Installation

```bash
composer require "cloudinary/url-builder"
```

# Usage

### Setup

```php
use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary();
```

### Transform and Optimize Assets

- [See full documentation](https://cloudinary.com/documentation/media_editing_api_sdks#url_builder_sdk_methods).

```php
$cloudinary->image('sample.jpg')->resize(Resize::fill()->width(100)->height(150))->format(Format::auto());
```

### Security options

- [See full documentation](https://cloudinary.com/documentation/solution_overview#security).

## Contributions

- Ensure tests run locally
- Open a PR and ensure Travis tests pass

## Get Help

If you run into an issue or have a question, you can either:

- Issues related to the SDK: [Open a GitHub issue](https://github.com/cloudinary/php-url-builder/issues).
- Issues related to your account: [Open a support ticket](https://cloudinary.com/contact)

## About Cloudinary

Cloudinary is a powerful media API for websites and mobile apps alike, Cloudinary enables developers to efficiently
manage, transform, optimize, and deliver images and videos through multiple CDNs. Ultimately, viewers enjoy responsive
and personalized visual-media experiences—irrespective of the viewing device.

## Additional Resources

- [Cloudinary Transformation and REST API References](https://cloudinary.com/documentation/cloudinary_references):
  Comprehensive references, including syntax and examples for all SDKs.
- [MediaJams.dev](https://mediajams.dev/): Bite-size use-case tutorials written by and for Cloudinary Developers
- [DevJams](https://www.youtube.com/playlist?list=PL8dVGjLA2oMr09amgERARsZyrOz_sPvqw): Cloudinary developer podcasts on
  YouTube.
- [Cloudinary Academy](https://training.cloudinary.com/): Free self-paced courses, instructor-led virtual courses, and
  on-site courses.
- [Code Explorers and Feature Demos](https://cloudinary.com/documentation/code_explorers_demos_index): A one-stop shop
  for all code explorers, Postman collections, and feature demos found in the docs.
- [Cloudinary Roadmap](https://cloudinary.com/roadmap): Your chance to follow, vote, or suggest what Cloudinary should
  develop next.
- [Cloudinary Facebook Community](https://www.facebook.com/groups/CloudinaryCommunity): Learn from and offer help to
  other Cloudinary developers.
- [Cloudinary Account Registration](https://cloudinary.com/users/register/free): Free Cloudinary account registration.
- [Cloudinary Website](https://cloudinary.com): Learn about Cloudinary's products, partners, customers, pricing, and
  more.

## Licence

Released under the MIT license.
