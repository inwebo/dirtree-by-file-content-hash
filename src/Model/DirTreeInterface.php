<?php

declare(strict_types=1);

namespace Inwebo\FileNameHashing\Model;

interface DirTreeInterface
{
    public function toArray(bool $includeFile = false): array;

    public function toString(bool $includeFile = false): string;
}
