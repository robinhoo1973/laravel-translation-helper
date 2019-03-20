<?php

namespace TopviewDigital\TranslationHelper\Service;

use Campo\UserAgent;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TopviewDigital\TranslationHelper\Interfaces\TranslatorInterface;

class GoogleTranslator implements TranslatorInterface
{
    protected $break = 0;
    protected $called = 0;
    protected $word;
    protected $source_locale = null;
    protected $target_locale;

    public function __construct()
    {
        $this->target_locale = config('app.locale');
    }

    public function word(string $word)
    {
        $this->word = $word;

        return $this;
    }

    public function targetLocale(string $target_locale)
    {
        $this->target_locale = $target_locale;

        return $this;
    }

    public function sourceLocale(string $source_locale)
    {
        $this->source_locale = $source_locale;

        return $this;
    }

    private function randomUserAgent()
    {
        sleep(1);
        $this->called++;

        return [
            'headers' => [
                'User-Agent' => UserAgent::random(),
            ],
        ];
    }

    public function translate()
    {
        $translated = '';
        $translator = new GoogleTranslate();
        while (empty($translated) && !empty($this->word)) {
            $this->called = 0;

            try {
                $translated = is_null($this->source_locale)
                    ? $translator
                    ->setOptions($this->randomUserAgent())
                    ->setSource()
                    ->setTarget($this->target_locale)
                    ->translate($this->word)
                    : $translator
                    ->setOptions($this->randomUserAgent())
                    ->setSource($this->source_locale)
                    ->setTarget($this->target_locale)
                    ->translate($this->word);
            } catch (\Exception $e) {
                $this->break++;
                $mins = rand(
                    floor($this->called),
                    floor($this->called * rand(2, 5))
                ) * $this->break;
                sleep($mins * 60);
            }
        }

        return $translated;
    }
}
