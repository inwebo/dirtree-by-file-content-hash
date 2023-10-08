<?php

declare(strict_types=1);

namespace Inwebo\FileNameHashing;

use Inwebo\FileNameHashing\Model\DirTreeInterface;

class DirTree implements DirTreeInterface
{
    /**
     * @var int SHA512 max hash string length, 64 chars
     */
    public const HASH_LENGTH = 128;
    private \SplFileObject $splFileObject;
    private int $dirTreeDepth = 2;
    private int $dirNameLength = 2;
    private string $fileContentHash;

    public function getSplFileObject(): \SplFileObject
    {
        return $this->splFileObject;
    }

    public function getDirTreeDepth(): int
    {
        return $this->dirTreeDepth;
    }

    public function setDirTreeDepth(int $dirTreeDepth): static
    {
        $this->dirTreeDepth = $dirTreeDepth;

        return $this;
    }

    public function getDirNameLength(): int
    {
        return $this->dirNameLength;
    }

    public function setDirNameLength(int $dirNameLength): static
    {
        $this->dirNameLength = $dirNameLength;

        return $this;
    }

    public function getFileContentHash(): string
    {
        return $this->fileContentHash;
    }

    protected function validateDirNameLength(): bool
    {
        return $this->dirNameLength > 0 && $this->dirNameLength <= self::HASH_LENGTH;
    }

    protected function validateDirTreeDepth(): bool
    {
        return $this->dirTreeDepth > 0 && $this->dirTreeDepth <= self::HASH_LENGTH;
    }

    protected function validateDirStrategy(): bool
    {
        return static::HASH_LENGTH - $this->dirNameLength * $this->dirTreeDepth >= 0;
    }

    /**
     * @throws \RuntimeException
     */
    protected function validateStrategy(): bool
    {
        if (false === $this->validateDirTreeDepth()) {
            throw new \RuntimeException(sprintf('dirDepth MUST be superior to 0 AND inferior or equal to %s. dirDepth was `%s`', static::HASH_LENGTH, $this->dirTreeDepth));
        }

        if (false === $this->validateDirNameLength()) {
            throw new \RuntimeException(sprintf('dirNameLength MUST be superior to 0 AND inferior or equal to %s. dirNameLength was `%s`', static::HASH_LENGTH, $this->dirNameLength));
        }

        if (false === $this->validateDirStrategy()) {
            throw new \RuntimeException(sprintf('dirNameLength x dirDepth MUST be superior or equal to zero, was `%s`', static::HASH_LENGTH - $this->dirNameLength * $this->dirTreeDepth));
        }

        return true;
    }

    public function __construct(
        private readonly string $filePath,
    ) {
        $this->splFileObject = new \SplFileObject($this->filePath);
        $this->fileContentHash = hash_file('sha512', $this->splFileObject->getPathname());
    }

    /**
     * @throws \RuntimeException
     */
    public function toArray(bool $includeFile = false): array
    {
        $this->validateStrategy();
        $pathArray = str_split($this->fileContentHash, $this->dirNameLength);

        return (false === $includeFile) ?
            array_splice($pathArray, 0, $this->dirTreeDepth) :
            array_merge(array_splice($pathArray, 0, $this->dirTreeDepth), [$this->splFileObject->getBasename()])
        ;
    }

    public function toString(bool $includeFile = false): string
    {
        return implode('/', $this->toArray($includeFile));
    }
}
