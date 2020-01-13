<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services;

use PhpCfdi\Finkok\Services\AbstractResult;

class TestingResult extends AbstractResult
{
    /**
     * @param string ...$search
     * @return mixed
     */
    public function exposeFindInData(string ...$search)
    {
        return $this->findInDescendent($this->data, ...$search);
    }

    public function exposeGet(string $keyword): string
    {
        return $this->get($keyword);
    }
}
