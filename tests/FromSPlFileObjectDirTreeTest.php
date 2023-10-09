<?php

declare(strict_types=1);

namespace Inwebo\DirTree\Tests;

use Inwebo\DirTree\FromSplFileObject;
use PHPUnit\Framework\TestCase;
use SplFileObject as BaseSplFileObject;

class FromSPlFileObjectDirTreeTest extends TestCase
{
    private const EMPTY_FILE = __DIR__.'/empty-file.txt';
    private const EMPTY_FILE_HASH = 'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e';

    public function testIsInvalidFile(): void
    {
        $this->expectException(\RuntimeException::class);
        new FromSplFileObject(new BaseSplFileObject('foo'));
    }

    public function testIsValidFile(): void
    {
        $dirTreeByHash = new FromSplFileObject(new BaseSplFileObject(self::EMPTY_FILE));

        $this->assertEquals(self::EMPTY_FILE_HASH, $dirTreeByHash->getHash());
    }

    public function testInvalidDirDepth(): void
    {
        $this->expectException(\RuntimeException::class);
        (new FromSplFileObject(new BaseSplFileObject(self::EMPTY_FILE)))
            ->setDirTreeDepth(129)
            ->toArray()
        ;
    }

    public function testInvalidDirFileNameLength(): void
    {
        $this->expectException(\RuntimeException::class);
        (new FromSplFileObject(new BaseSplFileObject(self::EMPTY_FILE)))
            ->setDirNameLength(-1)
            ->toArray()
        ;
    }

    public function testInvalidDirStrategy(): void
    {
        $this->expectException(\RuntimeException::class);
        (new FromSplFileObject(new BaseSplFileObject(self::EMPTY_FILE)))
            ->setDirNameLength(64)
            ->setDirTreeDepth(3)
            ->toArray();
    }

    public function testValidDirStrategy(): void
    {
        $dirTreeByHashAsArray = (new FromSplFileObject(new BaseSplFileObject(self::EMPTY_FILE)))
            ->setDirNameLength(20)
            ->setDirTreeDepth(2)
            ->toArray()
        ;

        $this->assertCount(2, $dirTreeByHashAsArray);

        foreach ($dirTreeByHashAsArray as $dirName) {
            $this->assertEquals(20, strlen($dirName));
        }
    }

    public function testValidArray(): void
    {
        $dirTreeByHash = (new FromSplFileObject(new BaseSplFileObject(self::EMPTY_FILE)));
        $dirTreeByHashAsArray = $dirTreeByHash
            ->toArray()
        ;

        $this->assertEquals($dirTreeByHashAsArray[0], str_split(self::EMPTY_FILE_HASH, $dirTreeByHash->getDirNameLength())[0]);
        $this->assertEquals($dirTreeByHashAsArray[1], str_split(self::EMPTY_FILE_HASH, $dirTreeByHash->getDirNameLength())[1]);
    }
}
