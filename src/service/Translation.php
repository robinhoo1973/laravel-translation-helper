<?php

namespace TopviewDigital\TranslationHelper\Service;

use Campo\UserAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TopviewDigital\TranslationHelper\Model\VocabTerm;

class Translation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $break = 0;
    protected $term;
    protected $locales;
    protected $translation;

    public function __construct(VocabTerm $term = null, array $locales = [])
    {
        $this->locales = array_unique(
            array_merge($locales, [
                app()->getLocale(),
                config('app.locale'),
                config('app.fallback_locale'),
                config('app.faker_locale'),
            ])
        );
        $this->term = $term;
    }

    public function handle()
    {
        if (empty($this->term)) {
            sweep();
            foreach (VocabTerm::get()->all() as $u) {
                $this->translation($u, $this->locales);
            }
        } else {
            $this->translation($this->term);
        }
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

    private function translation(VocabTerm $term)
    {
        $row = 0;
        while ($row < 1) {
            $this->called = 0;

            try {
                $language = new GoogleTranslate();
                // $language->setOptions($this->randomUserAgent())
                //     ->setSource()
                //     ->setTarget(config('app.locale'))
                //     ->translate($term->term);
                // $default_locale = $language->setOptions($this->randomUserAgent())->getLastDetectedSource();
                $translation = empty($term->translation) ? [] : $term->translation;
                foreach ($this->locales as $locale) {
                    if (!array_key_exists($locale, $term->translation)) {
                        $translation[$locale] = $language
                            ->setOptions($this->randomUserAgent())
                            ->setSource()
                            ->setTarget($locale)
                            ->translate($term->term);
                        $this->called++;
                    }
                }
                $locale = $language->setOptions($this->randomUserAgent())->getLastDetectedSource();
                if (!array_key_exists($locale, $term->translation)) {
                    $translation[$locale] = $term->term;
                }
                $term->translation = $translation;
                $language = null;
                $term->save();
                $row++;
            } catch (Exception $e) {
                $this->break++;
                $mins = rand(
                    floor($this->called / ($row + 1)),
                    floor($this->called / ($row + 1) * 2)
                ) * $this->break;
                sleep($mins * 60);
            }
        }
    }
}
