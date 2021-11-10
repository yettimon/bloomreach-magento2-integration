<?php
/**
 * @author Bloomreach
 * @copyright Copyright (c) Bloomreach (https://www.bloomreach.com/)
 */
declare(strict_types=1);

namespace Bloomreach\EngagementConnector\Model\Export\Transporter\Event;

use Bloomreach\EngagementConnector\Api\Data\ExportQueueInterface;
use Bloomreach\EngagementConnector\Model\Export\Transporter\TransporterInterface;
use Bloomreach\EngagementConnector\Service\Integration\UpdateCustomerRequest;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Updates customer on the Bloomreach
 */
class UpdateCustomer implements TransporterInterface
{
    /**
     * @var UpdateCustomerRequest
     */
    private $updateCustomerRequest;

    /**
     * @var SerializerInterface
     */
    private $jsonSerializer;

    /**
     * @param UpdateCustomerRequest $updateCustomerRequest
     * @param SerializerInterface $jsonSerializer
     */
    public function __construct(
        UpdateCustomerRequest $updateCustomerRequest,
        SerializerInterface $jsonSerializer
    ) {
        $this->updateCustomerRequest = $updateCustomerRequest;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Sends event data to the Bloomreach service
     *
     * @param ExportQueueInterface $exportQueue
     *
     * @return bool
     *
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function send(ExportQueueInterface $exportQueue): bool
    {
        $response = $this->updateCustomerRequest->execute($this->buildEventBody($exportQueue));

        if ((int) $response->getStatusCode() !== 200) {
            throw new LocalizedException(__($response->getReasonPhrase()));
        }

        return true;
    }

    /**
     * Build event body
     *
     * @param ExportQueueInterface $exportQueue
     *
     * @return array
     */
    private function buildEventBody(ExportQueueInterface $exportQueue): array
    {
        $properties = $this->jsonSerializer->unserialize($exportQueue->getBody());

        if (is_array($properties)) {
            $this->deleteUnusedFields($properties);
        }

        return [
            'customer_ids' => $this->jsonSerializer->unserialize($exportQueue->getRegistered()),
            'properties' => $properties,
            'update_timestamp' => strtotime($exportQueue->getCreatedAt())
        ];
    }

    /**
     * Delete unused fields
     *
     * @param array $properties
     *
     * @return void
     */
    private function deleteUnusedFields(array &$properties): void
    {
        if (isset($properties['registered'])) {
            unset($properties['registered']);
        }

        if (isset($properties['customer_id'])) {
            unset($properties['customer_id']);
        }
    }
}
