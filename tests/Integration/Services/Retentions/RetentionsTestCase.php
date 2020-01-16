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
    /** @var string|null */
    protected static $staticCurrentStampPrecfdi;

    /** @var StampResult|null */
    protected static $staticCurrentStampResult;

    protected function currentRetentionsPreCfdi(): string
    {
        if (null === static::$staticCurrentStampPrecfdi) {
            static::$staticCurrentStampPrecfdi = $this->newRetentionsPreCfdi();
        }
        return static::$staticCurrentStampPrecfdi;
    }

    protected function currentRetentionsStampResult(): StampResult
    {
        if (null === static::$staticCurrentStampResult) {
            static::$staticCurrentStampResult = $this->stampRetentionPreCfdi($this->currentRetentionsPreCfdi());
        }
        return static::$staticCurrentStampResult;
    }

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
