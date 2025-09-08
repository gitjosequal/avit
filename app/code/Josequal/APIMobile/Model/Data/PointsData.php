<?php
namespace Josequal\APIMobile\Model\Data;

use Josequal\APIMobile\Api\Data\PointsDataInterface;

/**
 * Points Data Model
 */
class PointsData implements PointsDataInterface
{
    /**
     * @var int
     */
    private $points;

    /**
     * @var int
     */
    private $remove;

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints($points)
    {
        $this->points = $points;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemove()
    {
        return $this->remove;
    }

    /**
     * {@inheritdoc}
     */
    public function setRemove($remove)
    {
        $this->remove = $remove;
        return $this;
    }
}
