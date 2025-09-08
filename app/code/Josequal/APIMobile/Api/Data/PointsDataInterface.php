<?php
namespace Josequal\APIMobile\Api\Data;

/**
 * Points Data Interface
 * @api
 */
interface PointsDataInterface
{
    const POINTS = 'points';
    const REMOVE = 'remove';

    /**
     * Get points
     * @return int|null
     */
    public function getPoints();

    /**
     * Set points
     * @param int $points
     * @return $this
     */
    public function setPoints($points);

    /**
     * Get remove flag
     * @return int|null
     */
    public function getRemove();

    /**
     * Set remove flag
     * @param int $remove
     * @return $this
     */
    public function setRemove($remove);
}
