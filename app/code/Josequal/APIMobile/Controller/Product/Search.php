<?php
namespace Josequal\APIMobile\Controller\Product;

class Search extends \Josequal\APIMobile\Controller\Action\Action
{
    /**
     * Execute search products action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getData();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Josequal\APIMobile\Model\V1\Catalog');

        $info = $model->searchProducts($data);

        $this->printResult($info);
    }
}
