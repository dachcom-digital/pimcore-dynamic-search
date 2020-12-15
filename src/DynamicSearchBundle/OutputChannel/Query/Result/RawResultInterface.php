<?php

namespace DynamicSearchBundle\OutputChannel\Query\Result;

interface RawResultInterface
{
    /**
     * @param mixed $data
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param int $hitCount
     */
    public function setHitCount(int $hitCount);

    /**
     * @return int
     */
    public function getHitCount();

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addParameter($key, $value);

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParameter($key, $value);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasParameter($key);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter($key);
}
