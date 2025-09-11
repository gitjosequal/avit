<?php
namespace Josequal\APIMobile\Controller\Product;

class SearchSuggestionsFrontend extends \Josequal\APIMobile\Controller\Action\Action
{
    /**
     * Execute search suggestions action (Frontend Route)
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getData();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Josequal\APIMobile\Model\V1\Catalog');

        $info = $model->getSearchSuggestions($data);

        $this->printResult($info);
    }
}
