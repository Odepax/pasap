![This composer package is no longer maintained](https://lh6.googleusercontent.com/PlJHKpFYQGmwLYkBoKTOYVPYXn5TbUkKcFJ_0kSqRXQRy1weyoz1xPhwale7_5fwsm9ZPO-3w2c7KQ=w1920-h942)

# Pasap

Pasap stands for _Php AS A Preprocessor_. This is a library which provides
server side custom tags with PHP.

Custom tags are used to improve code readability and make the HTML more
meaningful.

Remember this **README is a memo**. Check the
[wiki](https://github.com/Odepax/pasap/wiki) for detailed information about the
library's functioning.

## Install

_Pasap_ is available as a
[Composer package](https://packagist.org/packages/odepax/pasap). You can install
it via the CLI:

```bash
composer require odepax/pasap
```

... or pasting this in your project's composer.json file:

```json
"require": {
    "odepax/pasap": "^2.0"
}
```

## Quick Start

**STEP 1.**
Create a classic PHP file. This file is supposed to generate HTML, with the only
difference that there are custom tags in the generated HTML code.

```xml
<document title="I'm Using Custom Tags!">
	<news title="Lorem Ipsum" author="Me, of course">
		<p>Lorem ipsum <em>dolor</em> sit amet...</p>
	</news>
</document>
```

Remark: `<html>` -- `<head>` -- `<body>` structure is not mandatory: you can
create a custom tag and use it as the root element of you document instead of
`<html>`.

**STEP 2.**
Create a definition file for each custom tag you used. Here, you will create a
`custom-tags/document.php` file:

```xml
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title><?= $this->attr("title") ?></title>
	</head>
	<body>
		<?= $this->children() ?>
	</body>
</html>
```

... and a `custom-tags/news.php` file:

```xml
<article class="news">
	<h1 class="news_title"><?= $this->attr('title') ?></h1>
	<em class="news_author"><?= $this->attr('author') ?></em>
	<div class="news_content">
		<?= $this->children() ?>
	</div>
</article>
```

**STEP 3.**
Now let's configure and run Pasap:

```php
<?php

require './vendor/autoload.php';

// Get generated HTML with custom tags as a string.
ob_start();
include 'our-super-pasap-document.php';
$htmlWithCustomTags = ob_get_clean();

// Convert custom tags into valid HTML using the definition files.
echo \Pasap\Pasap::parse($htmlWithCustomTags, 'custom-tags');
```

**STEP 4.**
Admire the output:

```html
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>I'm Using Custom Tags!</title>
	</head>
	<body>
		<article class="news">
			<h1 class="news_title">Lorem Ipsum</h1>
			<em class="news_author">Me, of course</em>
			<div class="news_content">
				<p>Lorem ipsum <em>dolor</em> sit amet...</p>
			</div>
		</article>
	</body>
</html>
```

## Usage

In a definition file, `$this` represents the custom element. Here are some
methods you can call:

```php
$this->tag(); // string

$this->is('tag'); // boolean (compares with $this->tag())
$this->is('#text'); // boolean
$this->is('#comment'); // boolean
$this->is('#cdata'); // boolean
$this->is('#element'); // boolean

$this->parent(); // Element

$this->children(); // ElementCollection
$this->children('link'); // Shorcut for $this->children()->only('link')

$this->attr(); // AttrCollection
$this->attr('name'); // string | null
$this->attr('name', 'fallback'); // string

$this->data('key'); // mixed | null
$this->data('key', 'fallback'); // mixed

$this->scope('key'); // mixed | null
$this->scope('key', 'fallback'); // mixed
```

Find more examples in the
[tests folder](https://github.com/Odepax/pasap/tree/master/test/Parsing/parsed).

_Pasap_ supports some configuration options. They are handled by the `Configure`
static class:

```php
Configure::namespaceSource('', './element');
Configure::namespaceSource('intuitive-form', './vendor/odepax/pasap-intuitive-form/element');

Configure::nativeNamespace('html', [ 'br', 'hr', 'img', 'meta' ]);
Configure::nativeNamespace('svg', [ 'path' ]);

Configure::output(Configure::LEFT_AS_THIS | Configure::PRETTIFY | Configure::MINIFY);

Configure::doctype(Configure::LEFT_AS_THIS | Configure::ALWAYS_HTML5);
```

## Limitations

**Limit 1.**
You can't use custom tags in a definition file.
However, it's still possible to use a custom tag as a child of another custom
tag.

**Limit 2.**
This is XML, not HTML.
It means that even this is a valid **HTML5** code:

```html
<head>
    <meta charset="UTF-8">
    <title>...</title>
</head>
```

... this is not a valid **XML** code since XML is more strict about self-closing
tags: they must end with `/>`, so you' will need to turn your `<meta>` and
`<input>` into `<meta/>` and `<input/>`.

Even you are not contained by the `<html>` -- `<head>` -- `<body>` structure
anymore, remember you can only have **one root element**.

**Limit 3.**
Since the custom tags are managed server side, your CSS and your JavaScript are
not aware of them. I'm working on it.
