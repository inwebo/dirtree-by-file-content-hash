<?php

declare(strict_types=1);

namespace Inwebo\DirTree;

use Inwebo\DirTree\Model\DirTreeInterface;

class FromSplFileObject implements DirTreeInterface
{
    /**
     * @var int SHA512 Hash string length
     */
    public const HASH_STRING_LENGTH = 128;
    private int $dirTreeDepth = 2;
    private int $dirNameLength = 2;
    private string $hash;

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

    public function getHash(): string
    {
        return $this->hash;
    }

    protected function isValidDirNameLength(): bool
    {
        return $this->dirNameLength > 0 && $this->dirNameLength <= self::HASH_STRING_LENGTH;
    }

    protected function isValidDirTreeLength(): bool
    {
        return $this->dirTreeDepth > 0 && $this->dirTreeDepth <= self::HASH_STRING_LENGTH;
    }

    protected function isValidDirTreeStrategy(): bool
    {
        return static::HASH_STRING_LENGTH - $this->dirNameLength * $this->dirTreeDepth >= 0;
    }

    /**
     * @throws \RuntimeException
     */
    public function isValidDirTree(): true
    {
        if (false === $this->isValidDirTreeLength()) {
            throw new \RuntimeException(sprintf('dirTreeDepth MUST BE superior to 0 AND inferior or equal to %s. dirDepth was `%s`', static::HASH_STRING_LENGTH, $this->dirTreeDepth));
        }

        if (false === $this->isValidDirNameLength()) {
            throw new \RuntimeException(sprintf('dirNameLength MUST BE superior to 0 AND inferior or equal to %s. dirNameLength was `%s`', static::HASH_STRING_LENGTH, $this->dirNameLength));
        }

        if (false === $this->isValidDirTreeStrategy()) {
            $message = sprintf('Cant\'t validate DirTreeStrategy : '."\n\t".'- Strategy : (%s::HASH_LENGTH - %s::dirNameLength * %s::dirTreeDepth) >= 0'."\n\t".'- Result : (%s - %s * %s = %s) is not equal or superior to 0'."\n", __CLASS__, __CLASS__, __CLASS__, static::HASH_STRING_LENGTH, $this->dirNameLength, $this->dirTreeDepth, static::HASH_STRING_LENGTH - $this->dirNameLength * $this->dirTreeDepth);
            throw new \RuntimeException($message);
        }

        return true;
    }

    public function __construct(
        private readonly \SplFileObject $splFileObject,
    ) {
        $this->hash = hash_file('sha512', $this->splFileObject->getPathname());
    }

    /**
     * @throws \RuntimeException
     */
    public function toArray(bool $includeFile = false): array
    {
        $this->isValidDirTree();
        $dirTreeAsArray = str_split($this->hash, $this->dirNameLength);

        return (false === $includeFile) ?
            array_splice($dirTreeAsArray, 0, $this->dirTreeDepth) :
            array_merge(array_splice($dirTreeAsArray, 0, $this->dirTreeDepth), [$this->splFileObject->getBasename()])
        ;
    }

    /**
     * @throws \RuntimeException
     */
    public function toString(bool $includeFile = false): string
    {
        return implode(DIRECTORY_SEPARATOR, $this->toArray($includeFile));
    }
}
