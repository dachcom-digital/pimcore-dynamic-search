<?php

namespace DynamicSearchBundle\OutputChannel\Result\Document;

interface DocumentInterface
{
    /**
     * @return mixed
     */
    public function getHit();

    /**
     * @return string
     */
    public function getContextName();

    /**
     * @return string
     */
    public function getOutputChannelName();
}
