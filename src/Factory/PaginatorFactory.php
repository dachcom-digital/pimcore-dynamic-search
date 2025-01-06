<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\Paginator\AdapterInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PaginatorFactory implements PaginatorFactoryInterface
{
    public function __construct(protected PaginatorInterface $paginator)
    {
    }

    public function create(
        string $adapterClass,
        int $itemCountPerPage,
        int $currentPageNumber,
        string $outputChannelName,
        RawResultInterface $rawResult,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    ): PaginationInterface {
        /** @var AdapterInterface $adapter */
        $adapter = new $adapterClass($rawResult);
        $adapter->setContextDefinition($contextDefinition);
        $adapter->setOutputChannelName($outputChannelName);
        $adapter->setDocumentNormalizer($documentNormalizer);
        $adapter->setItemCountPerPage($itemCountPerPage);
        $adapter->setCurrentPageNumber($currentPageNumber);

        return $this->paginator->paginate($adapter, $currentPageNumber, $itemCountPerPage);
    }
}
