<?php
namespace Josequal\APIMobile\Controller\Cart;

class Points extends \Josequal\APIMobile\Controller\Action\Action {

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request
	) {
		parent::__construct($context, $request);
	}

	/**
	 * Execute method to get customer points
	 *
	 * @return void
	 */
	public function execute() {
        $this->auth();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Josequal\APIMobile\Model\V1\Points');
		$info = $model->getCustomerPoints();

		$this->printResult($info);
	}
}
