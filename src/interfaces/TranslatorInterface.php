<?php

namespace TopviewDigital\TranslationHelper\Interfaces;

interface TranslatorInterface
{
    public function word(string $word);

    public function targetLocale(string $target_locale);

    public function sourceLocale(string $source_locale);

    public function translate();
}
