<?php
/**
 * Copyright © Mago Assistant
 */
declare(strict_types=1);

namespace MagoAssistant\Higgsfield\Test\Unit\Service\Skills;

use Magento\Framework\AuthorizationInterface;
use MagoAssistant\Higgsfield\Service\Skills\ProductMedia;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProductMediaTest extends TestCase
{
    #[Test]
    public function itNamesItsOwnStatusLinePerAction(): void
    {
        $skill = new ProductMedia($this->createStub(AuthorizationInterface::class));

        self::assertSame('Starting video generation...', $skill->getStatusMessage('generate_video', []));
        self::assertSame('Adding image to product...', $skill->getStatusMessage('attach_image', []));
        self::assertNull($skill->getStatusMessage('unknown', []));
    }

    #[Test]
    public function itIsGuardedByTheCatalogProductsResource(): void
    {
        $skill = new ProductMedia($this->createStub(AuthorizationInterface::class));

        self::assertSame('product_media', $skill->getName());
        self::assertSame('Magento_Catalog::products', $skill->getMagentoAcl());
    }
}
