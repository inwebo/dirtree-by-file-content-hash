<?php

declare(strict_types=1);

namespace Inwebo\DirTree\Model;

interface DirTreeInterface
{
    public function toArray(bool $includeFile = false): array;

    public function toString(bool $includeFile = false): string;
}
