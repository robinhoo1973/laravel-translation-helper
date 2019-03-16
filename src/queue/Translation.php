<?php

namespace TopviewDigital\TranslationHelper\Queue;

use Campo\UserAgent;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TopviewDigital\TranslationHelper\Model\VocabTerm;

class Translation extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $break = 0;
    protected $term;
    protected $locale;
    protected $translation;

    public function __construct(VocabTerm $term = null, array $locale = [])
    {
        $this->locale = array_unique(
            array_merge($locale, [
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
            array_map(function ($u) {
                self::dispatch($u, $this->locales)->onQueue('translation');
            }, VocabTerm::get()->all());
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
                $translation = $term->translation;
                foreach ($this->locales as $locale) {
                    if (!array_key_exists($locale, $term->translation)) {
                        $translation[$locale] = $language
                            ->setOptions($this->randomUserAgent())
                            ->setSource(config('app.locale'))
                            ->setTarget($locale)
                            ->translate($term->term);
                        $this->called++;
                    }
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
