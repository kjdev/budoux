BudouX for PHP
==============

[BudouX][] PHP implementation.

[BudouX][] is the machine learning powered line break organizer tool.

[BudouX]: https://github.com/google/budoux.git

Usage
-----

You can get a list of phrases by feeding a sentence to the parser.

``` php
use BudouX\Parser;
$parser = Parser::loadDefaultJapanese();
print_r($parser->parse('今日は天気です。'));
// Array
// (
//     [0] => 今日は
//     [1] => 天気です。
// )
```

If you have a custom model, you can use it as follows.

``` php
use BudouX\Parser;
$model = json_decode(file_get_contents('/path/to/your/model.json'), true);
$parser = new Parser($model);
```
