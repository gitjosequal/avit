<?php
namespace Josequal\APIMobile\Controller\Cart;

class TestPoints extends \Josequal\APIMobile\Controller\Action\Action {

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request
	) {
		parent::__construct($context, $request);
	}

	/**
	 * Execute method to test points API
	 *
	 * @return void
	 */
	public function execute() {
		$info = [
			'status' => true,
			'message' => 'Points API is working!',
			'data' => [
				'test' => true,
				'timestamp' => date('Y-m-d H:i:s'),
				'endpoint' => '/apimobile/cart/test-points'
			]
		];

		$this->printResult($info);
	}
}
