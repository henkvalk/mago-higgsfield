<?php
/**
 * Copyright © Mago Assistant
 */
declare(strict_types=1);

namespace MagoAssistant\Higgsfield\Controller\Adminhtml\Oauth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use MagoAssistant\Higgsfield\Service\Client\OAuthService;

/**
 * Removes the Higgsfield tokens and OAuth client from the shop.
 */
class Disconnect extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'MagoAssistant_Higgsfield::config';

    public function __construct(
        Context $context,
        private readonly OAuthService $oauth
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $this->oauth->disconnect();
        $this->messageManager->addSuccessMessage(__('Higgsfield is disconnected.'));

        return $this->resultRedirectFactory->create()
            ->setPath('adminhtml/system_config/edit', ['section' => Connect::CONFIG_SECTION]);
    }
}
