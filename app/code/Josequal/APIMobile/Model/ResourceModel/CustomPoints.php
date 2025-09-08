<?php
namespace Josequal\APIMobile\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Custom Points Resource Model
 */
class CustomPoints extends AbstractDb
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('custom_points', 'id');
    }
}
