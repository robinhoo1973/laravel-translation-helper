<?php

namespace TopviewDigital\TranslationHelper\Service;

use TopviewDigital\TranslationHelper\Model\VocabTerm;
use TopviewDigital\TranslationHelper\Interfaces\AsyncBrokerInterface;

class Translation implements AsyncBrokerInterface
{

    protected $locales = [];
    protected $words = [];

    public function __construct(VocabTerm $vocab = null)
    {
        $this->locales = [
            app()->getLocale(),
            config('app.locale'),
            config('app.fallback_locale'),
            config('app.faker_locale'),
        ];
        $this->words = $vocab ? [$vocab->id] : $this->words;
    }

    public function targetLocales($locales = [])
    {
        $this->locales = array_unique(
            array_merge(
                $this->locales,
                $locales
            )
        );
        return $this;
    }

    public function words($words = null)
    {

        $words = $words ? VocabTerm::whereIn('term', (array)words)->pluck('id')->toarray() : $words;
        $this->words = $words ?? ($this->words ?: VocabTerm::pluck('id')->toArray());
        return $this;
    }



    public function handle()
    {
        foreach ($this->words as $word) {
            $this->translation(VocabTerm::find($word));
        }
    }

    private function translation(VocabTerm $word)
    {
        $translator = config('trans-helper.translation.broker');
        $translator = new $translator;
        $translated = $word->translation;
        $this->locales = array_unique($this->locales);
        foreach ($this->locales as $locale) {
            if (!array_key_exists($locale, $translated)) {
                $translated[$locale] = $translator->word($word->term)->targetLocale($locale)->translate();
            }
        }
        $word->translation = $translated;
        $word->save();
    }
}
