<?php

use TopviewDigital\TranslationHelper\Queue\Translation;

if (!function_exists('is_json')) {
    function is_json($string)
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}

if (!function_exists('localize')) {
    function localize($languages, string $failback = '')
    {
        if (is_array($languages) || is_json($languages)) {
            $languages = (!is_array($languages)) ? (array)json_decode($languages) : $languages;
            $locales = array_keys($languages);
            $system = \App::getLocale();
            $default = config('app.locale');

            $locale = in_array($system, $locales) ? $system : (in_array($default, $locales) ? $default : null);

            return  $locale ? $languages[$locale] : $failback;
        }
        if (is_string($languages) && empty($failback)) {
            $tracer = (new Exception())->getTrace();
            $cite = [];
            $cite['file'] = preg_replace('/(\.php)(.+)$/', '${1}', $tracer[0]['file']);
            $cite['line'] = $tracer[0]['line'];
            array_shift($tracer);
            $cite['function'] = $tracer[0]['function'] ?? '';
            $cite['class'] = $tracer[0]['class'] ?? '';
            $vocab = [];
            $vocab['namespace'] = preg_replace(
                '/(^' . addcslashes(base_path(), '\/') . ')|(\.php$)/',
                '',
                $cite['file']
            );
            $vocab['term'] = $languages;
            $vocab = config('trans-helper.model.term')::firstOrCreate($vocab);
            if (!$vocab->transaltion) {
                $vocab->translation = [config('app.locale') => $vocab['term']];
                $vocab->save();
            }
            if (
                config('trans-helper.translation.mode') == 'auto'
                && !in_array(app()->getLocale(), array_keys($vocab->translation))
            ) {
                Translation::dispatch($vocab, [app()->getLocale()])->onQueue('tranlsation');
            }
            $cite['file'] = preg_replace(
                '/^' . addcslashes(base_path(), '\/') . '/',
                '',
                $cite['file']
            );
            $cite = config('trans-helper.model.cite')::firstOrCreate($cite);
            $vocab->cites()->sync([$cite->id], false);
            if (!$cite->code) {
                $lines = explode("\n", file_get_contents(base_path() . $cite->file));
                $cite->code = $lines[$cite->line - 1];
                if (substr($cite->file, -10) != '.blade.php') {
                    for ($start = $cite->line - 2; $start > -1; $start--) {
                        $char = substr(rtrim($lines[$start]), -1);
                        if ($char == ';' || $char == '}' || $char == '{') {
                            $start++;
                            break;
                        }
                    }
                    $count = count($lines);
                    for ($end = $cite->line - 1; $end < $count; $end++) {
                        $char = substr(rtrim($lines[$end]), -1);
                        if ($char == ';') {
                            break;
                        }
                    }
                    $code = array_filter(array_slice($lines, $start, $end - $start + 1, true), 'trim');
                    $max = strlen($end);
                    $cite->code = implode("\n", array_map(function ($u, $v) use ($max) {
                        return sprintf("%{$max}d    %s", $u + 1, rtrim($v));
                    }, array_keys($code), $code));
                    $cite->save();
                }
            }

            return localize($vocab->translation, $vocab->term);
        }

        return $failback;
    }
}

if (!function_exists('sweep')) {
    function sweep()
    {
        array_map(
            function ($u) {
                $u->sweep();
            },
            array_merge(
                config('trans-helper.model.cite')::get()->all(),
                config('trans-helper.model.term')::get()->all()
            )
        );
    }
}

if (!function_exists('translate')) {
    function translate($locales = [])
    {
        Translation::dispatch(null, $locales)->onQueue('tranlsation');
    }
}
