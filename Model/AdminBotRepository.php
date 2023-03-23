<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model;

use MageDad\AdminBot\Api\AdminBotRepositoryInterface;
use MageDad\AdminBot\Api\Data\AdminBotInterface;
use MageDad\AdminBot\Api\Data\AdminBotInterfaceFactory;
use MageDad\AdminBot\Api\Data\AdminBotSearchResultsInterfaceFactory;
use MageDad\AdminBot\Model\ResourceModel\AdminBot as ResourceAdminBot;
use MageDad\AdminBot\Model\ResourceModel\AdminBot\CollectionFactory as AdminBotCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class AdminBotRepository implements AdminBotRepositoryInterface
{

    /**
     * @var AdminBot
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceAdminBot
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var AdminBotCollectionFactory
     */
    protected $adminBotCollectionFactory;

    /**
     * @var AdminBotInterfaceFactory
     */
    protected $adminBotFactory;


    /**
     * @param ResourceAdminBot $resource
     * @param AdminBotInterfaceFactory $adminBotFactory
     * @param AdminBotCollectionFactory $adminBotCollectionFactory
     * @param AdminBotSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceAdminBot $resource,
        AdminBotInterfaceFactory $adminBotFactory,
        AdminBotCollectionFactory $adminBotCollectionFactory,
        AdminBotSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->adminBotFactory = $adminBotFactory;
        $this->adminBotCollectionFactory = $adminBotCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(AdminBotInterface $adminBot)
    {
        try {
            $this->resource->save($adminBot);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the adminBot: %1',
                $exception->getMessage()
            ));
        }
        return $adminBot;
    }

    /**
     * @inheritDoc
     */
    public function get($adminBotId)
    {
        $adminBot = $this->adminBotFactory->create();
        $this->resource->load($adminBot, $adminBotId);
        if (!$adminBot->getId()) {
            throw new NoSuchEntityException(__('admin_bot with id "%1" does not exist.', $adminBotId));
        }
        return $adminBot;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->adminBotCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(AdminBotInterface $adminBot)
    {
        try {
            $adminBotModel = $this->adminBotFactory->create();
            $this->resource->load($adminBotModel, $adminBot->getAdminBotId());
            $this->resource->delete($adminBotModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the admin_bot: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($adminBotId)
    {
        return $this->delete($this->get($adminBotId));
    }
}

