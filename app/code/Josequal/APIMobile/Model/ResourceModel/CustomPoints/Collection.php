<?php
namespace Josequal\APIMobile\Model\ResourceModel\CustomPoints;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Josequal\APIMobile\Model\CustomPoints;
use Josequal\APIMobile\Model\ResourceModel\CustomPoints as CustomPointsResource;

/**
 * Custom Points Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize collection
     */
    protected function _construct()
    {
        $this->_init(CustomPoints::class, CustomPointsResource::class);
    }
}
