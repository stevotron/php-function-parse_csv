# php-function-csv_parse

Returns multidimensional array from CSV string.

### An example

```php
$string = <<<KAKJNBWDCV
abc,def,ghi
jkl,mno,pqr
stu,vwx,yz
KAKJNBWDCV;

$array = parse_csv($string);

//  $array = [
//    0 => [
//      0 => 'abc',
//      1 => 'def',
//      2 => 'ghi'
//    ],
//    1 => [
//      0 => 'jkl',
//      1 => 'mno',
//      2 => 'pqr'
//    ],
//    2 => [
//      0 => 'stu',
//      1 => 'vwx',
//      2 => 'yz'
//    ]
//  ]
```

### New lines in encapsulated fields are no problem

```php
$string = <<<KAKJNBWDCV
abc,def,"This, seems

to confuse
some parsers"
jkl,mno,pqr
KAKJNBWDCV;

$array = parse_csv($string);

//  $array = [
//    0 => [
//      0 => 'abc',
//      1 => 'def',
//      2 => 'This, seems
//
// to confuse
// some parsers'
//    ],
//    1 => [
//      0 => 'jkl',
//      1 => 'mno',
//      2 => 'pqr'
//    ]
```

### Define custom field separaters, encapsulators etc...

```php
$string = <<<KAKJNBWDCV
abc|def|ghi*ROW*jkl|-mn\-o-|pqr
KAKJNBWDCV;

$array = parse_csv($string, '|', '-', '\-', '*ROW*');
// parse_csv($string, $field_delimiter, $field_encapsulator, $escaped_field_encapsulator, $row_delimiter)

//  $array = [
//    0 => [
//      0 => 'abc',
//      1 => 'def',
//      2 => 'ghi'
//    ],
//    1 => [
//      0 => 'jkl',
//      1 => 'mn-o',
//      2 => 'pqr'
//    ]
```
