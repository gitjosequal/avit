<?php
namespace Josequal\APIMobile\Controller\Cart;

class PointsReminder extends \Josequal\APIMobile\Controller\Action\Action {

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request
	) {
		parent::__construct($context, $request);
	}

	public function execute() {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Josequal\APIMobile\Model\V1\Points');
		$model->sendPointsReminder();
	}
}
