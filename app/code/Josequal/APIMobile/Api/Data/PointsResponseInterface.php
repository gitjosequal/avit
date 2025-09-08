<?php
namespace Josequal\APIMobile\Api\Data;

/**
 * Points Response Interface
 * @api
 */
interface PointsResponseInterface
{
    const STATUS = 'status';
    const MESSAGE = 'message';
    const DATA = 'data';

    /**
     * Get status
     * @return bool|null
     */
    public function getStatus();

    /**
     * Set status
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get data
     * @return array|null
     */
    public function getData();

    /**
     * Set data
     * @param array $data
     * @return $this
     */
    public function setData($data);
}
