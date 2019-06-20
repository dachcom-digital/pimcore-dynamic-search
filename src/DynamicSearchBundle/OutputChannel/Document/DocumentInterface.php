<?php

namespace DynamicSearchBundle\OutputChannel\Document;

interface DocumentInterface
{
    public function getField(string $name);
}