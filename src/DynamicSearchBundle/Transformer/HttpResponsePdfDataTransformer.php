<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Transformer\Field\Type\KeywordType;
use DynamicSearchBundle\Transformer\Field\Type\StringType;
use DynamicSearchBundle\Transformer\Field\Type\TextType;
use Pimcore\Document\Adapter\Ghostscript;
use Pimcore\Model\Asset;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VDB\Spider\Resource as DataResource;

class HttpResponsePdfDataTransformer implements DataTransformerInterface
{
    /**
     * @var ContextDataInterface
     */
    protected $contextData;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $assetTmpDir;

    /**
     * {@inheritDoc}
     */
    public function isApplicable($data): bool
    {
        if (!$data instanceof DataResource) {
            return false;
        }

        $contentTypeInfo = $data->getResponse()->getHeaderLine('Content-Type');
        $parts = explode(';', $contentTypeInfo);
        $mimeType = trim($parts[0]);

        if ($mimeType === 'application/pdf') {
            return true;
        }

        return false;
    }

    public function getAlias(): string
    {
        return 'http_response_pdf';
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $defaults = [
            'boost'                           => 1,
            'restriction'                     => [],
            'content_start_indicator'         => '<!-- main-content -->',
            'content_end_indicator'           => '<!-- /main-content -->',
            'content_exclude_start_indicator' => null,
            'content_exclude_end_indicator'   => null,
        ];

        $resolver->setDefaults($defaults);
        $resolver->setRequired(array_keys($defaults));

        $resolver->setAllowedTypes('boost', ['int']);
        $resolver->setAllowedTypes('restriction', ['array']);
    }

    /**
     * {@inheritDoc}
     */
    public function transformData(ContextDataInterface $contextData, $data)
    {
        $this->contextData = $contextData;

        $host = $data->getUri()->getHost();
        $uri = $data->getUri()->toString();

        $statusCode = $data->getResponse()->getStatusCode();

        if ($statusCode !== 200) {
            $this->log('debug', sprintf('skip indexing [ %s ] because of wrong status code [ %s ]', $uri, $statusCode));
            return false;
        }

        return $this->parsePdf($data, $host);
    }

    protected function parsePdf(DataResource $resource, $host)
    {
        $predefinedOptions = $this->contextData->getDataTransformOptions($this->getAlias());

        try {
            $pdfToTextBin = Ghostscript::getPdftotextCli();
        } catch (\Exception $e) {
            $pdfToTextBin = false;
        }

        if ($pdfToTextBin === false) {
            return false;
        }

        $textFileTmp = uniqid('t2p-');

        $tmpFile = $this->assetTmpDir . DIRECTORY_SEPARATOR . $textFileTmp . '.txt';
        $tmpPdfFile = $this->assetTmpDir . DIRECTORY_SEPARATOR . $textFileTmp . '.pdf';

        $stream = $resource->getResponse()->getBody();
        $stream->rewind();
        $contents = $stream->getContents();

        file_put_contents($tmpPdfFile, $contents);

        $verboseCommand = \Pimcore::inDebugMode() ? '' : '-q ';

        try {
            $cmd = $verboseCommand . $tmpPdfFile . ' ' . $tmpFile;
            exec($pdfToTextBin . ' ' . $cmd);
        } catch (\Exception $e) {
            $this->log('ERROR', $e->getMessage());
        }

        $uri = $resource->getUri()->toString();
        $assetMeta = $this->getAssetMeta($resource);

        if (!is_file($tmpFile)) {
            return false;
        }

        $fileContent = file_get_contents($tmpFile);

        $text = preg_replace("/\r|\n/", ' ', $fileContent);
        $text = preg_replace('/[^\p{Latin}\d ]/u', '', $text);
        $text = preg_replace('/\n[\s]*/', "\n", $text);

        $title = $assetMeta['key'] !== false ? $assetMeta['key'] : basename($uri);

        $boost = $predefinedOptions['boost'];

        $document = new IndexDocument($boost);

        $document->addField(KeywordType::class, 'language', $assetMeta['language'], []);
        $document->addField(KeywordType::class, 'country', $assetMeta['country'], []);
        $document->addField(KeywordType::class, 'url', $uri, []);
        $document->addField(KeywordType::class, 'host', $host, []);

        $document->addField(StringType::class, 'title', $title, []);

        $document->addField(TextType::class, 'content', $text, []);

        @unlink($tmpFile);
        @unlink($tmpPdfFile);

        return $document;
    }

    /**
     * @param DataResource $resource
     *
     * @return array
     */
    protected function getAssetMeta(DataResource $resource)
    {
        $link = $resource->getUri()->toString();

        $assetMetaData = [
            'language'     => 'all',
            'country'      => 'all',
            'key'          => false,
            'restrictions' => false,
            'id'           => false
        ];

        if (empty($link) || !is_string($link)) {
            return $assetMetaData;
        }

        $restrictions = false;
        $pathFragments = parse_url($link);
        $assetPath = $pathFragments['path'];

        $asset = Asset::getByPath($assetPath);

        if (!$asset instanceof Asset) {
            return $assetMetaData;
        }

        $assetMetaData['restrictions'] = $restrictions;

        //check for assigned language
        $languageProperty = $asset->getProperty('assigned_language');
        if (!empty($languageProperty)) {
            $assetMetaData['language'] = $languageProperty;
        }

        //checked for assigned country
        $countryProperty = $asset->getProperty('assigned_country');
        if (!empty($countryProperty)) {
            $assetMetaData['country'] = $countryProperty;
        }

        $assetMetaData['key'] = $asset->getKey();
        $assetMetaData['id'] = $asset->getId();

        return $assetMetaData;
    }

    /**
     * @param string $level
     * @param string $message
     */
    protected function log($level, $message)
    {
        $this->logger->log($level, $message, $this->getAlias(), $this->contextData->getName());
    }
}