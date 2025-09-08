<?php
namespace Josequal\APIMobile\Api\V1;

/**
 * Points API Interface
 * @api
 */
interface PointsInterface
{
    /**
     * Get customer points
     * @return \Josequal\APIMobile\Api\Data\PointsResponseInterface
     */
    public function getCustomerPoints();

    /**
     * Apply points to cart
     * @param \Josequal\APIMobile\Api\Data\PointsDataInterface $pointsData
     * @return \Josequal\APIMobile\Api\Data\PointsResponseInterface
     */
    public function applyPoints($pointsData);

    /**
     * Send points reminder to customers
     * @return \Josequal\APIMobile\Api\Data\PointsResponseInterface
     */
    public function sendPointsReminder();
}
