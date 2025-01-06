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

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionInterface
{
    public function getDataNormalizerIdentifier(): string;

    public function setCurrentLevel(string|int $currentLevel): void;

    public function setDocumentConfiguration(array $documentConfiguration): void;

    public function getDocumentConfiguration(): array;

    /**
     * @throws \Exception
     */
    public function addOptionFieldDefinition(array $definition): static;

    public function getOptionFieldDefinitions(): array;

    /**
     * @throws \Exception
     */
    public function addSimpleDocumentFieldDefinition(array $definition): static;

    /**
     * @throws \Exception
     */
    public function addPreProcessFieldDefinition(array $definition, \Closure $closure): static;

    public function getDocumentFieldDefinitions(): array;
}
