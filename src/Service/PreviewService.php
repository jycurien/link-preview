<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PreviewService
{
    private const TAGS = [
        'cover' => [
            ['selector' => 'meta[property="og:image"]', 'attribute' => 'content'],
            ['selector' => 'meta[itemprop="image"]', 'attribute' => 'content'],
            ['selector' => 'meta[property="twitter:image"]', 'attribute' => 'content'],
        ],
        'title' => [
            ['selector' => 'title'],
            ['selector' => 'meta[property="og:title"]', 'attribute' => 'content'],
            ['selector' => 'meta[itemprop="name"]', 'attribute' => 'content'],
            ['selector' => 'meta[property="twitter:title"]', 'attribute' => 'content'],
        ],
        'description' => [
            ['selector' => 'meta[name="description"]', 'attribute' => 'content'],
            ['selector' => 'meta[property="og:description"]', 'attribute' => 'content'],
            ['selector' => 'meta[itemprop="description"]', 'attribute' => 'content'],
            ['selector' => 'meta[property="twitter:description"]', 'attribute' => 'content'],
        ],
    ];

    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * @param string $url
     * @return null[]|string[]
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPreview(string $url): array
    {
        $html = $this->client->request('GET', $url);
        $crawler = new Crawler($html->getContent());

        return [
            'cover' => $this->parseHTML($crawler, self::TAGS['cover']),
            'title' => $this->parseHTML($crawler, self::TAGS['title']),
            'description' => $this->parseHTML($crawler, self::TAGS['description']),
        ];
    }

    /**
     * @param Crawler $crawler
     * @param array $selectors
     * @return string|null
     */
    private function parseHTML(Crawler $crawler, array $selectors): ?string
    {
        foreach ($selectors as $selector) {
            $el = $crawler->filter($selector['selector'])->first();
            if ($el->count() > 0) {
                if ($selector['attribute'] ?? false) {
                    return $el->first()->attr($selector['attribute']);
                }
                return $el->first()->text();
            }
        }

        return null;
    }
}