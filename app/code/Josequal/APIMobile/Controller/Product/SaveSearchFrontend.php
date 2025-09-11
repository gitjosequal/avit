<?php
namespace Josequal\APIMobile\Controller\Product;

class SaveSearchFrontend extends \Josequal\APIMobile\Controller\Action\Action
{
    /**
     * Execute save search query action (Frontend Route)
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getData();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Josequal\APIMobile\Model\V1\Catalog');

        $info = $model->saveSearchQuery($data);

        $this->printResult($info);
    }
}
