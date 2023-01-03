<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCustomersCommand;
use PhpCfdi\Finkok\Services\Registration\ObtainCustomersService;

final class ObtainCustomersServiceTest extends RegistrationIntegrationTestCase
{
    protected function createService(): ObtainCustomersService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new ObtainCustomersService($settings);
    }

    public function testConsumeObtainCustomersServiceObtainPage(): void
    {
        $service = $this->createService();
        $result = $service->obtainPage(new ObtainCustomersCommand(1));

        $this->assertMatchesRegularExpression('/^Showing \d+ to \d+ of \d+ entries$/', $result->message());
        $this->assertGreaterThanOrEqual(0, count($result->customers()));
    }

    public function testConsumeObtainCustomersServiceObtainAll(): void
    {
        $service = $this->createService();
        $result = $service->obtainAll();

        $this->assertGreaterThanOrEqual(0, count($result));

        $this->assertNull($result->findByRfc(self::CUSTOMER_NON_EXISTENT));

        $customer = $result->getByRfc('EKU9003173C9');
        $this->assertSame('EKU9003173C9', $customer->rfc());
    }
}
