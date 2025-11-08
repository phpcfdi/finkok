<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelAnswer;

class AcceptRejectUuidItem
{
    public function __construct(
        private string $uuid,
        private AcceptRejectUuidStatus $status,
        private CancelAnswer $answer,
    ) {
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function status(): AcceptRejectUuidStatus
    {
        return $this->status;
    }

    public function answer(): CancelAnswer
    {
        return $this->answer;
    }
}
