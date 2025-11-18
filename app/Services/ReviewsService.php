<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ReviewsService 
{
    private array $months = [
        'января' => 1, 'февраля' => 2, 'марта' => 3, 'апреля' => 4,
        'мая' => 5, 'июня' => 6, 'июля' => 7, 'августа' => 8,
        'сентября' => 9, 'октября' => 10, 'ноября' => 11, 'декабря' => 12
    ];

    /**
     * Основной метод для парсинга отзывов
     * @param string $url
     * @return array
     */
    public function parseReviews(string $url): array
    {
        try {
            $client = new Client([
                'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
                'timeout' => 30,
            ]);

            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $reviews = $this->extractReviews($crawler);
            $summary = $this->parseSummary($crawler);

            return compact('reviews', 'summary');
            
        } catch (\Exception $e) {
            \Log::error('Yandex parsing error: ' . $e->getMessage());
            return $this->emptyResult();
        }
    }

    /**
     * Извлекакет отзывы из DOM-дерева
     * @param Crawler $crawler
     * @return array[]
     */
    private function extractReviews(Crawler $crawler): array
    {
        $reviews = [];

        $crawler->filter('.business-reviews-card-view__review')->each(function (Crawler $node) use (&$reviews) {
            try {
                $dateText = $this->cleanText($node->filter('.business-review-view__date')->text(''));
                
                $review = [
                    'author' => $this->cleanText($node->filter('.business-review-view__author-name')->text('')),
                    'text' => $this->cleanText($node->filter('.business-review-view__body')->text('')),
                    'rating' => $node->filter('.business-rating-badge-view__star._full')->count(),
                    'date' => $this->formatDate($dateText),
                ];

                if (!empty($review['text']) && strlen($review['text']) > 10) {
                    $review['timestamp'] = $this->parseDateToTimestamp($dateText);
                    $reviews[] = $review;
                }
            } catch (\Exception $e) {
                // Пропускаем некорректные отзывы
            }
        });

        // Сортируем и возвращаем 10 последних
        usort($reviews, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        
        return array_map(fn($review) => array_diff_key($review, ['timestamp' => true]), 
                       array_slice($reviews, 0, 10));
    }

    /**
     * Извлекает общую статистику из страницы
     * @param Crawler $crawler
     * @return array{average_rating: float, total_reviews: int|array{average_rating: int, total_ratings: int, total_reviews: int}}
     */
    private function parseSummary(Crawler $crawler): array
    {
        try {
            return [
                'average_rating' => $this->extractAverageRating($crawler),
                'total_reviews' => $this->extractTotalReviews($crawler),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to parse summary: ' . $e->getMessage());
            return ['average_rating' => 0, 'total_reviews' => 0, 'total_ratings' => 0];
        }
    }

    /**
     * Извлекает средний рейтинг из DOM-дерева
     * @param Crawler $crawler
     * @return float
     */
    private function extractAverageRating(Crawler $crawler): float
    {
        $ratingParts = [];
        $crawler->filter('.business-summary-rating-badge-view__rating-text')->each(function (Crawler $node) use (&$ratingParts) {
            $text = $this->cleanText($node->text());
            if ($text !== ',' && !empty($text)) $ratingParts[] = $text;
        });

        return match(count($ratingParts)) {
            2 => floatval($ratingParts[0] . '.' . $ratingParts[1]),
            1 => floatval($ratingParts[0]),
            default => 0
        };
    }

    /**
     * Извлекает общее количество отзывов из meta-тега
     * @param Crawler $crawler
     * @return int
     */
    private function extractTotalReviews(Crawler $crawler): int
    {
        $meta = $crawler->filter('meta[itemprop="reviewCount"]');
        return $meta->count() > 0 ? (int)$meta->attr('content') : 0;
    }

    /**
     * Очищает текст от лишних пробелов и переносов строк
     * @param string $text
     * @return string
     */
    private function cleanText(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Форматирует дату в формат dd.mm.yyyy
     * @param string $dateText
     * @return string
     */
    private function formatDate(string $dateText): string
    {
        $timestamp = $this->parseDateToTimestamp($dateText);
        return $timestamp > 0 ? date('d.m.Y', $timestamp) : $dateText;
    }

    /**
     * Парсит русскую текстовую дату в Unix timestamp
     * @param string $dateText
     * @return int
     */
    private function parseDateToTimestamp(string $dateText): int
    {
        $dateText = trim($dateText);
        $currentYear = date('Y');

        foreach ($this->months as $monthRu => $monthNum) {
            if (str_contains(mb_strtolower($dateText), $monthRu)) {
                $pattern = '/(\d{1,2})\s+' . $monthRu . '(?:\s+(\d{4}))?/u';
                if (preg_match($pattern, $dateText, $matches)) {
                    $day = (int)$matches[1];
                    $year = $matches[2] ?? $currentYear;
                    
                    $timestamp = mktime(0, 0, 0, $monthNum, $day, $year);
                    return $timestamp > time() ? mktime(0, 0, 0, $monthNum, $day, $year - 1) : $timestamp;
                }
            }
        }

        return 0;
    }

    /**
     * Возвращает пустой результат для случаев ошибок
     * @return array{reviews: array, summary: array{average_rating: int, total_ratings: int, total_reviews: int}}
     */
    private function emptyResult(): array
    {
        return [
            'reviews' => [],
            'summary' => ['average_rating' => 0, 'total_reviews' => 0, 'total_ratings' => 0]
        ];
    }
}