# Dir tree by SHA512 hash from content's file
How can one store a large number of files while maintaining a high level of performance during access? One solution is file content hashing.

This class will generate SHA 512 hash from the content of a file, and it will return you a directory structure based on
the file hash.

## How to

```php
<?php

include './vendor/autoload.php';

use Inwebo\DirTree\FromSplFileObject;

try {
    // Its content is null,  its hash is cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e
    $dirTree = (new FromSplFileObject('./tests/empty-file.txt'));

    var_dump($dirTree->toArray());
    // array(2) {
    //  [0] =>
    //  string(2) "cf"
    //  [1] =>
    //  string(2) "83"
    //}

    var_dump($dirTree->toArray(true));
    // array(3) {
    //  [0] =>
    //  string(2) "cf"
    //  [1] =>
    //  string(2) "83"
    //  [2] =>
    //  string(10) "empty-file.txt"
    // }
    
    var_dump($dirTree->toString());
    // string (5) cf/83
    var_dump($dirTree->toString(true));
    // string(20) "cf/83/empty-file.txt"

    $dirTree->setDirTreeDepth(6);
    var_dump($dirTree->toArray());
    // array(6) {
    //  [0] =>
    //  string(2) "cf"
    //  [1] =>
    //  string(2) "83"
    //  [2] =>
    //  string(2) "e1"
    //  [3] =>
    //  string(2) "35"
    //  [4] =>
    //  string(2) "7e"
    //  [5] =>
    //  string(2) "ef"
    //}

    $dirTree->setDirTreeDepth(6);
    $dirTree->setDirNameLength(6);

    var_dump($dirTree->toArray());
    // array(6) {
    //  [0] =>
    //  string(6) "cf83e1"
    //  [1] =>
    //  string(6) "357eef"
    //  [2] =>
    //  string(6) "b8bdf1"
    //  [3] =>
    //  string(6) "542850"
    //  [4] =>
    //  string(6) "d66d80"
    //  [5] =>
    //  string(6) "07d620"
    //}

    var_dump($dirTree->toString(true));
    // string(56) "cf83e1/357eef/b8bdf1/542850/d66d80/07d620/empty-file.txt"

    // Invalid strategy, because 128 - 2 * 128  = -128 is not equal or superior to 0
    $dirTree->setDirNameLength(2);
    $dirTree->setDirTreeDepth(128);

    // Throw an RuntimeException
    $dirTree->toArray();
} catch (\Exception $e) {
    // Do something
}
```

## Install

```bash
composer req inwebo/dirtree-by-file-content-hash
```

## Web
- [File nam hashing](https://medium.com/eonian-technologies/file-name-hashing-creating-a-hashed-directory-structure-eabb03aa4091)