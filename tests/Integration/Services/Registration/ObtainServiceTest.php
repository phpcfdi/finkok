<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCommand;
use PhpCfdi\Finkok\Services\Registration\ObtainService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class ObtainServiceTest extends IntegrationTestCase
{
    protected function createService(): ObtainService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new ObtainService($settings);
    }

    public function testConsumeObtainServiceGettingNonExistentRecord(): void
    {
        $service = $this->createService();
        $result = $service->obtain(new ObtainCommand('ABCD010101AAA'));

        $this->assertSame('', $result->message());
        $this->assertGreaterThanOrEqual(0, count($result->customers()));
    }

    public function testConsumeObtainServiceGettingOwnRfc(): void
    {
        $service = $this->createService();
        $result = $service->obtain(new ObtainCommand('TCM970625MB1'));

        $this->assertSame('', $result->message());
        $this->assertSame(1, count($result->customers()));
        $this->assertSame('TCM970625MB1', $result->customers()->getByRfc('TCM970625MB1')->rfc());
    }

    public function testConsumeObtainServiceGettingAllRecords(): void
    {
        $service = $this->createService();
        $result = $service->obtain(new ObtainCommand());

        $this->assertSame('', $result->message());
        $this->assertGreaterThanOrEqual(1, count($result->customers()));
        $this->assertSame('TCM970625MB1', $result->customers()->getByRfc('TCM970625MB1')->rfc());
    }
}
