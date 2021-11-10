<?php
/**
 * @author Bloomreach
 * @copyright Copyright (c) Bloomreach (https://www.bloomreach.com/)
 */
declare(strict_types=1);

namespace Bloomreach\EngagementConnector\Model\InitialExport\Entity;

use Bloomreach\EngagementConnector\Model\Export\Entity\AddMultipleToExport;
use Bloomreach\EngagementConnector\Model\InitialExport\InitialEntityExportInterface;
use Bloomreach\EngagementConnector\Model\ResourceModel\GetEntityIds;
use Bloomreach\EngagementConnector\Model\ResourceModel\GetEntityIdsFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * The class is responsible for adding product variants to the initial export
 */
class ProductVariants implements InitialEntityExportInterface
{
    /**
     * @var AddMultipleToExport
     */
    private $addMultipleToExport;

    /**
     * @var GetEntityIdsFactory
     */
    private $getEntityIdsFactory;

    /**
     * @param GetEntityIdsFactory $getEntityIdsFactory
     * @param AddMultipleToExport $addMultipleToExport
     */
    public function __construct(
        GetEntityIdsFactory $getEntityIdsFactory,
        AddMultipleToExport $addMultipleToExport
    ) {
        $this->addMultipleToExport = $addMultipleToExport;
        $this->getEntityIdsFactory = $getEntityIdsFactory;
    }

    /**
     * Adds catalog product entity to the initial export
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(): void
    {
        /** @var GetEntityIds $getEntityIds */
        $getEntityIds = $this->getEntityIdsFactory->create();
        $getEntityIds->setPrimaryColumn('entity_id');
        $getEntityIds->setTableName('catalog_product_entity');

        foreach ($getEntityIds->execute() as $batchOfIds) {
            $this->addMultipleToExport->execute('catalog_product_variants', $batchOfIds);
        }
    }
}
