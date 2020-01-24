<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\StampCommand;
use PhpCfdi\Finkok\Services\Retentions\StampResult;
use PhpCfdi\Finkok\Services\Retentions\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdiRetention;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

abstract class RetentionsTestCase extends IntegrationTestCase
{
    protected function newRetentionsPreCfdi(): string
    {
        return (new RandomPreCfdiRetention())->createValid();
    }

    protected function createStampService(): StampService
    {
        return new StampService($this->createSettingsFromEnvironment());
    }

    protected function stampRetentionPreCfdi(string $precfdi): StampResult
    {
        $command = new StampCommand($precfdi);
        $service = $this->createStampService();
        return $service->stamp($command);
    }
}
