<?php
namespace App\Services;

use App\Helpers\Env;

/**
 * Pure PHP cURL Google Gemini API Client
 * Compatible with Gemini 1.5 Flash / Gemini 2.0 Flash
 */
class GeminiService {
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->apiKey = Env::get('GEMINI_API_KEY', '');
        $this->model  = Env::get('GEMINI_MODEL', 'gemini-3.5-flash');
    }

    public function isConfigured(): bool {
        return !empty($this->apiKey);
    }

    /**
     * Send raw prompt to Gemini API with automated resilient fallback
     */
    public function generate(string $prompt, float $temperature = 0.7): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error'   => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your environment.'
            ];
        }

        $models = array_unique([$this->model, 'gemini-3.5-flash', 'gemini-2.5-flash-lite', 'gemini-flash-latest']);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => $temperature,
                'maxOutputTokens' => 1024
            ]
        ];

        $lastError = 'Service temporarily unavailable';

        foreach ($models as $candidateModel) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:generateContent?key=" . urlencode($this->apiKey);

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                $lastError = "Network error: {$curlError}";
                continue;
            }

            $data = json_decode($response, true);

            if ($httpCode === 200 && !empty($data['candidates'][0]['content']['parts'][0]['text'])) {
                $text = trim($data['candidates'][0]['content']['parts'][0]['text']);
                return [
                    'success' => true,
                    'text'    => $text
                ];
            }

            $lastError = $data['error']['message'] ?? "API error (HTTP {$httpCode})";
        }

        return ['success' => false, 'error' => $lastError];
    }

    /**
     * Generate 3-4 bullet-point TL;DR summary of blog content
     */
    public function summarizePost(string $content): array {
        $prompt = "You are an expert editorial AI. Read the following blog post article and provide a crisp, highly engaging 3-bullet-point TL;DR executive summary.\n\n"
                . "Format strictly as:\n"
                . "• [First key insight]\n"
                . "• [Second key insight]\n"
                . "• [Third actionable takeaway]\n\n"
                . "Article Content:\n" . substr($content, 0, 4000);

        return $this->generate($prompt, 0.4);
    }

    /**
     * AI Writing Copilot Actions
     */
    public function copilotAction(string $action, string $text, string $context = ''): array {
        switch ($action) {
            case 'generate_draft':
                $prompt = "You are a professional tech blog writer. Write a comprehensive, well-structured blog post in Markdown based on this title/topic: \"{$text}\". Use headers (##, ###), bullet points, and an engaging introduction and conclusion.";
                break;
            case 'fix_grammar':
                $prompt = "You are an expert copyeditor. Fix any grammar, tone, spelling, and phrasing issues in the following text while keeping its original meaning intact. Return only the polished text:\n\n\"{$text}\"";
                break;
            case 'expand':
                $prompt = "Expand the following draft section into an insightful, informative paragraph with depth and practical examples:\n\n\"{$text}\"";
                break;
            case 'catchy_titles':
                $prompt = "Generate 5 high-converting, SEO-optimized, captivating blog post titles for an article about: \"{$text}\". Return only a numbered list from 1 to 5.";
                break;
            case 'seo_meta':
                $prompt = "Generate an optimized Google SEO meta description (under 155 characters) and 5 trending comma-separated tags for this content:\n\n" . substr($text, 0, 2000);
                break;
            default:
                $prompt = "Improve the following blog text:\n\n{$text}";
        }

        return $this->generate($prompt, 0.7);
    }

    /**
     * Auto-suggest tags and category
     */
    public function suggestTags(string $content): array {
        $prompt = "Analyze this blog post and return EXACTLY 5 relevant, trending comma-separated tags (e.g. AI, Web Development, PHP, Technology, Cloud). Return ONLY the comma-separated tags, nothing else.\n\nArticle:\n" . substr($content, 0, 2000);
        return $this->generate($prompt, 0.3);
    }
}
