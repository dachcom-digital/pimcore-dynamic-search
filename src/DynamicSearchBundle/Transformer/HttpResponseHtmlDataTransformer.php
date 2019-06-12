<?php

namespace DynamicSearchBundle\Transformer;

use DOMDocument;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Transformer\Field\Type\KeywordType;
use DynamicSearchBundle\Transformer\Field\Type\StringType;
use DynamicSearchBundle\Transformer\Field\Type\TextType;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VDB\Spider\Resource as DataResource;

class HttpResponseHtmlDataTransformer implements DataTransformerInterface
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

        if ($mimeType === 'text/html') {
            return true;
        }

        return false;
    }

    public function getAlias(): string
    {
        return 'http_response_html';
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
        $resolver->setAllowedTypes('content_start_indicator', ['string']);
        $resolver->setAllowedTypes('content_end_indicator', ['string']);
        $resolver->setAllowedTypes('content_exclude_start_indicator', ['string']);
        $resolver->setAllowedTypes('content_exclude_end_indicator', ['string']);
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

        return $this->parseHtml($data, $host);
    }

    protected function parseHtml(DataResource $resource, $host)
    {
        $predefinedOptions = $this->contextData->getDataTransformOptions($this->getAlias());

        /** @var Crawler $crawler */
        $crawler = $resource->getCrawler();
        $uri = $resource->getUri()->toString();
        $stream = $resource->getResponse()->getBody();
        $stream->rewind();
        $html = $stream->getContents();

        $contentTypeInfo = $resource->getResponse()->getHeaderLine('Content-Type');
        $contentLanguage = $resource->getResponse()->getHeaderLine('Content-Language');

        $language = strtolower($this->getLanguageFromResponse($contentLanguage, $html));
        $encoding = strtolower($this->getEncodingFromResponse($contentTypeInfo, $html));

        $language = strtolower(str_replace('_', '-', $language));

        //page has canonical link: do not track if this is not the canonical document
        $hasCanonicalLink = $crawler->filterXpath('//link[@rel="canonical"]')->count() > 0;

        if ($hasCanonicalLink === true) {
            if ($uri != $crawler->filterXpath('//link[@rel="canonical"]')->attr('href')) {
                $this->log('debug', sprintf(
                        'skip indexing [ %s ] because it has canonical link %s',
                        $uri,
                        $crawler->filterXpath('//link[@rel="canonical"]')->attr('href')
                    )
                );
                return false;
            }
        }

        //page has no follow: do not track!
        $hasNoIndex = $crawler->filterXpath('//meta[contains(@content, "noindex")]')->count() > 0;

        if ($hasNoIndex === true) {
            $this->log('debug', sprintf('skip indexing [ %s ] because it has a noindex tag', $uri));
            return false;
        }

        $hasCountryMeta = $crawler->filterXpath('//meta[@name="country"]')->count() > 0;
        $hasTitle = $crawler->filterXpath('//title')->count() > 0;
        $hasDescription = $crawler->filterXpath('//meta[@name="description"]')->count() > 0;
        $hasRestriction = false; //$this->checkRestrictions === true && $crawler->filterXpath('//meta[@name="m:groups"]')->count() > 0;
        $hasCategories = $crawler->filterXpath('//meta[@name="dynamic-search:categories"]')->count() > 0;
        $hasCustomMeta = $crawler->filterXpath('//meta[@name="dynamic-search:meta"]')->count() > 0;
        $hasCustomBoostMeta = $crawler->filterXpath('//meta[@name="dynamic-search:boost"]')->count() > 0;
        $hasDocumentMeta = $crawler->filterXpath('//meta[@name="dynamic-search:documentId"]')->count() > 0;
        $hasObjectMeta = $crawler->filterXpath('//meta[@name="dynamic-search:objectId"]')->count() > 0;

        $title = null;
        $description = null;
        $customMeta = null;
        $restrictions = false;
        $categories = null;
        $country = null;
        $boost = $predefinedOptions['boost'];
        $documentId = null;
        $objectId = null;

        if ($hasTitle === true) {
            $title = $crawler->filterXpath('//title')->text();
        }

        if ($hasDescription === true) {
            $description = $crawler->filterXpath('//meta[@name="description"]')->attr('content');
        }

        if ($hasCountryMeta === true) {
            $country = $crawler->filterXpath('//meta[@name="country"]')->attr('content');
        }

        if ($hasRestriction === true) {
            $restrictions = $crawler->filterXpath('//meta[@name="m:groups"]')->attr('content');
        }

        if ($hasCustomMeta === true) {
            $customMeta = $crawler->filterXpath('//meta[@name="dynamic-search:meta"]')->attr('content');
        }

        if ($hasCategories === true) {
            $categories = $crawler->filterXpath('//meta[@name="dynamic-search:categories"]')->attr('content');
        }

        if ($hasCustomBoostMeta === true) {
            $boost = (int) $crawler->filterXpath('//meta[@name="dynamic-search:boost"]')->attr('content');
        }

        if ($hasDocumentMeta === true) {
            $documentId = (int) $crawler->filterXpath('//meta[@name="dynamic-search:documentId"]')->attr('content');
        }

        if ($hasObjectMeta === true) {
            $objectId = (int) $crawler->filterXpath('//meta[@name="dynamic-search:objectId"]')->attr('content');
        }

        $documentHasDelimiter = false;
        $documentHasExcludeDelimiter = false;

        //now limit to search content area if indicators are set and found in this document
        if (!empty($this->searchStartIndicator)) {
            $documentHasDelimiter = strpos($html, $this->searchStartIndicator) !== false;
        }

        //remove content between exclude indicators
        if (!empty($this->searchExcludeStartIndicator)) {
            $documentHasExcludeDelimiter = strpos($html, $this->searchExcludeStartIndicator) !== false;
        }

        if ($documentHasDelimiter
            && !empty($this->searchStartIndicator)
            && !empty($this->searchEndIndicator)) {
            preg_match_all(
                '%' . $this->searchStartIndicator . '(.*?)' . $this->searchEndIndicator . '%si',
                $html,
                $htmlSnippets
            );

            $html = '';

            if (is_array($htmlSnippets[1])) {
                foreach ($htmlSnippets[1] as $snippet) {
                    if ($documentHasExcludeDelimiter
                        && !empty($this->searchExcludeStartIndicator)
                        && !empty($this->searchExcludeEndIndicator)) {
                        $snippet = preg_replace(
                            '#(' . preg_quote($this->searchExcludeStartIndicator) . ')(.*?)(' . preg_quote($this->searchExcludeEndIndicator) . ')#si',
                            ' ',
                            $snippet
                        );
                    }

                    $html .= ' ' . $snippet;
                }
            }
        }

        //add h1 to index
        $headlines = [];
        preg_match_all('@(<h1[^>]*?>[ \t\n\r\f]*(.*?)[ \t\n\r\f]*' . '</h1>)@si', $html, $headlines);

        $mainHeadline = '';
        if (is_array($headlines[2])) {
            foreach ($headlines[2] as $headline) {
                $mainHeadline .= $headline . ' ';
            }

            $mainHeadline = strip_tags($mainHeadline);
        }

        $content = $this->getPlainTextFromHtml($html);
        $imageTags = $this->extractImageInformation($html);

        $document = new IndexDocument($boost);

        $document->addField(KeywordType::class, 'language', $language, []);
        $document->addField(KeywordType::class, 'country', $country, []);
        $document->addField(KeywordType::class, 'url', $uri, []);
        $document->addField(KeywordType::class, 'host', $host, []);

        $document->addField(StringType::class, 'title', $title, []);
        $document->addField(StringType::class, 'headline', $mainHeadline, [], 10);

        $document->addField(TextType::class, 'meta_description', $description, []);
        $document->addField(TextType::class, 'content', $content, []);

        return $document;

    }

    /**
     * @param $contentLanguage
     * @param $body
     *
     * Try to find the document's language by first looking for Content-Language in Http headers than in html
     * attribute and last in content-language meta tag
     *
     * @return string
     */
    protected function getLanguageFromResponse($contentLanguage, $body)
    {
        $l = $contentLanguage;

        if (empty($l)) {
            //try html lang attribute
            $languages = [];
            preg_match_all('@<html[\n|\r\n]*.*?[\n|\r\n]*lang="(?P<language>\S+)"[\n|\r\n]*.*?[\n|\r\n]*>@si', $body, $languages);
            if ($languages['language']) {
                $l = str_replace(['_', '-'], '', $languages['language'][0]);
            }
        }

        if (empty($l)) {
            //try meta tag
            $languages = [];
            preg_match_all('@<meta\shttp-equiv="content-language"\scontent="(?P<language>\S+)"\s\/>@si', $body, $languages);
            if ($languages['language']) {
                $l = str_replace('_', '', $languages['language'][0]);
            }
        }

        return $l;
    }

    /**
     * @param $contentType
     * @param $body
     * extract encoding either from HTTP Header or from HTML Attribute
     *
     * @return string
     */
    protected function getEncodingFromResponse($contentType, $body)
    {
        $encoding = '';

        //try content-type header
        if (!empty($contentType)) {
            $data = [];
            preg_match('@.*?;\s*charset=(.*)\s*@si', $contentType, $data);

            if ($data[1]) {
                $encoding = trim($data[1]);
            }
        }

        if (empty($encoding)) {
            //try html
            $data = [];
            preg_match('@<meta\shttp-equiv="Content-Type"\scontent=".*?;\s+charset=(.*?)"\s\/>@si', $body, $data);

            if ($data[1]) {
                $encoding = trim($data[1]);
            }
        }

        if (empty($encoding)) {
            //try xhtml
            $data = [];
            preg_match('@<\?xml.*?encoding="(.*?)"\s*\?>@si', $body, $data);

            if ($data[1]) {
                $encoding = trim($data[1]);
            }
        }

        if (empty($encoding)) {
            //try html 5
            $data = [];
            preg_match('@<meta\scharset="(.*?)"\s*>@si', $body, $data);

            if ($data[1]) {
                $encoding = trim($data[1]);
            }
        }

        return $encoding;
    }

    /**
     * removes html, javascript and additional whitespaces from string
     *
     * @param  $html
     *
     * @return mixed|string
     */
    protected function getPlainTextFromHtml($html)
    {
        $doc = new DOMDocument();
        $doc->substituteEntities = true;

        @$doc->loadHTML($html);
        $html = $doc->saveHTML();

        //remove scripts and stuff
        $search = [
            '@(<script[^>]*?>.*?</script>)@si', // Strip out javascript
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments including CDATA
        ];

        $text = preg_replace($search, '', $html);
        //remove html tags
        $text = strip_tags($text);
        //remove additional whitespaces
        $text = preg_replace('@[ \t\n\r\f]+@', ' ', $text);

        return $text;
    }

    /**
     * @param $html
     *
     * @return array
     */
    protected function extractImageInformation($html)
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $data = [];
        $imageTags = [];

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if (empty($html)) {
            return [];
        }

        try {
            $doc->loadHTML($html);
            $imageTags = $doc->getElementsByTagName('img');
        } catch (\Exception $e) {
            //do nothing. just die trying.
        }

        foreach ($imageTags as $tag) {
            $alt = $tag->getAttribute('alt');

            if (in_array($alt, ['', 'Image is not available', 'Image not available'])) {
                continue;
            }

            $data[] = [
                'src'   => $tag->getAttribute('src'),
                'title' => $tag->getAttribute('title'),
                'alt'   => $alt
            ];
        }

        return $data;
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