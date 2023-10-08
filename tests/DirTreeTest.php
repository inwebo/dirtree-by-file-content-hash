<?php

declare(strict_types=1);

namespace Inwebo\FileNameHashing\Tests;

use Inwebo\FileNameHashing\DirTree;
use PHPUnit\Framework\TestCase;

class DirTreeTest extends TestCase
{
    private const EMPTY_FILE = __DIR__.'/empty-file';
    private const EMPTY_FILE_HASH = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';

    public function testIsInvalidFile(): void
    {
        $this->expectException(\RuntimeException::class);
        new DirTree('foo');
    }

    public function testIsValidFile(): void
    {
        $dirTreeByHash = new DirTree(self::EMPTY_FILE);

        $this->assertEquals(self::EMPTY_FILE_HASH, $dirTreeByHash->getFileContentHash());
    }

    public function testInvalidDirDepth(): void
    {
        $this->expectException(\RuntimeException::class);
        (new DirTree(self::EMPTY_FILE))
            ->setDirTreeDepth(65)
            ->toArray()
        ;
    }

    public function testInvalidDirFileNameLength(): void
    {
        $this->expectException(\RuntimeException::class);
        (new DirTree(self::EMPTY_FILE))
            ->setDirNameLength(-1)
            ->toArray()
        ;
    }

    public function testInvalidDirStrategy(): void
    {
        $this->expectException(\RuntimeException::class);
        (new DirTree(self::EMPTY_FILE))
            ->setDirNameLength(32)
            ->setDirTreeDepth(3)
            ->toArray();
    }

    public function testValidDirStrategy(): void
    {
        $dirTreeByHashAsArray = (new DirTree(self::EMPTY_FILE))
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
        $dirTreeByHash = new DirTree(self::EMPTY_FILE);
        $dirTreeByHashAsArray = $dirTreeByHash
            ->toArray()
        ;

        $this->assertEquals($dirTreeByHashAsArray[0], str_split(self::EMPTY_FILE_HASH, $dirTreeByHash->getDirNameLength())[0]);
        $this->assertEquals($dirTreeByHashAsArray[1], str_split(self::EMPTY_FILE_HASH, $dirTreeByHash->getDirNameLength())[1]);
    }
}
