# Pasap

Pasap stands for _Php AS A Preprocessor_. This is a library which provides server
side custom tags with PHP.

Custom tags are used to improve code readability and make the HTML more meaningful.

## Usage

**STEP 1.**
Create a classic PHP file. This file is supposed to generate HTML, with the only
difference that there are custom tags in the generated HTML code.

```php
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>I'm Using Custom Tags!</title>
	</head>
	<body>
		<news title="Lorem Ipsum" author="Me, of course">
			<p>Lorem ipsum <em>dolor</em> sit amet...</p>
		</news>
	</body>
</html>
```

**STEP 2.**
Create a definition file for each custom tag you used. Here, you will create a
`custom-tags/news.php` file:

```php
<article class="news">
	<h1 class="news_title"><?= $this->attr('title') ?></h1>
	<em class="news_author"><?= $this->attr('author') ?></em>
	<div class="news_content">
		<?= $this->children() ?>
	</div>
</article>
```

**STEP 3.**
Now let's configure and run Pasap parser:

```php
<?php

require "./vendor/autoload.php";

// Get generated HTML with custom tags as a string.
ob_start();
include 'pasap-document.php';
$htmlWithCustomTags = ob_get_clean();

// Convert custom tags into valid HTML using the definition files.
echo \Pasap\Pasap::parse($htmlWithCustomTags, "custom-tags");
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

## Documentation

In a definition file, `$this` represents the custom element. `$this` is a
`\Pasap\Element`. Here are the methods you can call:

- `$this->tag():string` returns the tag name of the custom element;
- `$this->is($tag:string):bool` is a shortcut to `$this->tag() === $tag`;
- `$this->attr($name:string):string` returns the value of the specified
  attribute, or `NULL` of the attribute does not exist.
- `$this->attr():\Pasap\AttrCollection` and `$this->children():\Pasap\ElementCollection`
  return respectively all the attributes and all the child nodes of `$this`.

Since `\Pasap\AttrCollection` and `\Pasap\ElementCollection` implement a
`__toString` method and the `\Iterator` interface, you can easily `echo` them or
iterate through them with a `foreach($this->attr() as $name => $value)` and a
`foreach($this->children() as $element)` respectively.

Note that, like `$this`, `$element` is a `\Pasap\Element`, which means you can
call `tag`, `is`, `attr`, and `children` methods on it.

## Limitations

**Limit 1.**
You can't use custom tags in a definition file.
However, it's still possible to use a custom tag as a child of another custom tag.

**Limit 2.**
This is HTML. This means the basic `<!DOCTYPE>` -- `<html>` -- `<head>` -- `<body>`
structure is still mandatory.
Except these basics, you can create all the custom tags you want.

## Functioning

![With and Without Pasap](http://aygix.free.fr/down.php?path=github/Odepax/pasap/with-without.png)

<!--
      +~~~~~~~~~~~~~~~~~~~~+ <~~~~ Data from DB           +~~~~~~~~~~~~~~~~~~~+ <~~~~ Data from DB
PHP { | HTML Preprocessing | <~~~~ Session          PHP { | XML Preprocessing | <~~~~ Session
      +~~~~~~~~~~~~~~~~~~~~+ <~~~~ APIs                   +~~~~~~~~~~~~~~~~~~~+ <~~~~ APIs
                |                                                  |
                |                                                  v
                |                                               +~~~~~+  <news title="Lorem">
                |                                               | XML |     ...
                |                                |              +~~~~~+  </news>
                |                                |                 |
                |                                |                 v
                |                                |             +~~~~~~~+
                |                                |             | Pasap | <~~~~ Custom elements
                |                                              +~~~~~~~+
                |                                                  |
                v                                                  v      <article class="news">
             +~~~~~~+                                           +~~~~~~+     <h1> Lorem </h1>
             | HTML |                                           | HTML |     ...
             +~~~~~~+                                           +~~~~~~+  </article>
                                   Without Pasap    With Pasap
-->
