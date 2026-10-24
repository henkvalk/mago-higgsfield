<?php
/**
 * Copyright © Mago Assistant
 */
declare(strict_types=1);

namespace MagoAssistant\Higgsfield\Test\Unit\Service\Skills\ProductMedia;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use MagoAssistant\Higgsfield\Service\Skills\ProductMedia\AttachImageAction;
use MagoAssistant\Higgsfield\Service\Skills\ProductMedia\MediaStorage;
use MagoAssistant\Higgsfield\Service\Skills\ProductMedia\ProductImageSource;
use MagoAssistant\Mago\Service\Url\SecureAdminUrl;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class AttachImageActionTest extends TestCase
{
    private const ID = 'd7e6c0f3-6699-4f6c-bb45-2ad7fd9158ff';

    /**
     * @var MediaStorage&Stub
     */
    private MediaStorage $storage;

    /**
     * @var ProductImageSource&Stub
     */
    private ProductImageSource $source;

    protected function setUp(): void
    {
        $this->storage = $this->createStub(MediaStorage::class);
        $this->storage->method('isValidRequestId')->willReturnCallback(
            static fn (string $id): bool => $id === self::ID
        );
        $this->storage->method('isImage')
            ->willReturnCallback(static fn (string $file): bool => !str_ends_with($file, '.mp4'));
        $this->source = $this->createStub(ProductImageSource::class);
        $this->source->method('product')->willReturn(['product_id' => 5, 'sku' => 'MUG-1', 'product_name' => 'Mug']);
    }

    #[Test]
    public function itRefusesAnythingButARequestId(): void
    {
        self::assertSame(
            ['error' => 'request_id must be the id returned by generate_image'],
            $this->action()->findRefusal(['sku' => 'MUG-1', 'request_id' => '../../app/etc/env'])
        );
    }

    #[Test]
    public function itAsksForCheckStatusBeforeAnythingIsSaved(): void
    {
        $this->storage->method('files')->willReturn([]);

        self::assertSame(
            ['error' => 'No saved result for this request. Run check_status first until it is completed.'],
            $this->action()->findRefusal(['sku' => 'MUG-1', 'request_id' => self::ID])
        );
    }

    #[Test]
    public function itRefusesAnImageNumberOutOfRange(): void
    {
        $this->storage->method('files')->willReturn(['mago/higgsfield/' . self::ID . '/1.png']);

        self::assertSame(
            ['error' => 'This request has 1 file(s); image_number 2 does not exist.'],
            $this->action()->findRefusal(['sku' => 'MUG-1', 'request_id' => self::ID, 'image_number' => 2])
        );
    }

    #[Test]
    public function itRefusesAVideo(): void
    {
        $this->storage->method('files')->willReturn(['mago/higgsfield/' . self::ID . '/1.mp4']);

        self::assertSame(
            ['error' => 'Only images can be added to the gallery; a video stays available through its link.'],
            $this->action()->findRefusal(['sku' => 'MUG-1', 'request_id' => self::ID])
        );
    }

    #[Test]
    public function itAcceptsAStoredImageOfAnExistingProduct(): void
    {
        $this->storage->method('files')->willReturn(['mago/higgsfield/' . self::ID . '/1.png']);

        self::assertNull($this->action()->findRefusal(['sku' => 'MUG-1', 'request_id' => strtoupper(self::ID)]));
    }

    private function action(): AttachImageAction
    {
        return new AttachImageAction(
            $this->storage,
            $this->source,
            $this->createStub(ProductAttributeMediaGalleryManagementInterface::class),
            $this->createStub(ProductAttributeMediaGalleryEntryInterfaceFactory::class),
            $this->createStub(ImageContentInterfaceFactory::class),
            $this->createStub(SecureAdminUrl::class),
            $this->createStub(StoreManagerInterface::class)
        );
    }
}
