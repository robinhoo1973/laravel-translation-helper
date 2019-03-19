<?php
use Illuminate\Support\Str;
use TopviewDigital\TranslationHelper\Model\VocabTerm;
use TopviewDigital\TranslationHelper\Model\VocabCite;
use TopviewDigital\TranslationHelper\Service\Translation;

if (!function_exists('array_sort_value')) {
    function array_sort_value($array, $mode = SORT_LOCALE_STRING)
    {
        // SORT_REGULAR - compare items normally (don't change types)
        // SORT_NUMERIC - compare items numerically
        // SORT_STRING - compare items as strings
        // SORT_LOCALE_STRING - compare items as strings, based on the current locale.
        // It uses the locale, which can be changed using setlocale()
        // SORT_NATURAL - compare items as strings using "natural ordering" like natsort()
        // SORT_FLAG_CASE

        if (!is_array($array)) {
            $array = method_exists($array, 'toArray') ? $array->toArray() : (array)$array;
        }
        // \Locale::setDefault(str_replace('-', '_', \App::getLocale()));
        $keys = array_keys($array);
        $vals = array_values($array);
        array_multisort($vals, $mode, $keys);

        return array_combine($keys, $vals);
    }
}

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

if (!function_exists('auto_trans_able')) {
    function auto_trans_able()
    {
        return config('trans-helper.translation.mode') == 'auto'
            && config('queue.default') != 'sync';
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
            $vocab = VocabTerm::firstOrCreate($vocab);
            if (!$vocab->transaltion) {
                $vocab->translation = [config('app.locale') => $vocab['term']];
                $vocab->save();
                if (auto_trans_able()) {
                    dispatch(new Translation($vocab));
                }
            }
            if (auto_trans_able() && !in_array(app()->getLocale(), array_keys($vocab->translation))) {
                dispatch(new Translation($vocab, [app()->getLocale()]));
            }
            $cite['file'] = preg_replace(
                '/^' . addcslashes(base_path(), '\/') . '/',
                '',
                $cite['file']
            );
            $cite = VocabCite::firstOrCreate($cite);
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
                    $cite->code = implode("\n", array_map(function($u, $v) use ($max) {
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
            function($u) {
                $u->sweep();
            },
            array_merge(
                VocabCite::get()->all(),
                VocabTerm::get()->all()
            )
        );
    }
}

if (!function_exists('translate')) {
    function translate($locales = [])
    {
        dispatch(new Translation(null, $locales));
    }
}
if (!function_exists('slugify')) {
    function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicated - symbols
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

if (!function_exists('unique_slugs')) {
    function unique_slugs($slugs)
    {
        $keys = array_keys($slugs);
        $slugs = array_values($slugs);
        $slugs = array_map(function($k, $v) use ($slugs) {
            if ($k > 0 && in_array($v, array_slice($slugs, 0, $k - 1))) {
                $v .= '-' . uniqid();
            }
            return $v;
        }, array_keys($slugs), $slugs);
        return array_combine($keys, $slugs);
    }
}

if (!function_exists('lang_file_name')) {
    function lang_file_name($path, $locale, $namespace)
    {
        $lang_file = str_replace('/', '.', strtolower(ltrim(Str::snake($namespace), '/')));
        $lang_file = str_replace('//', '/', implode('/', [$path, $locale, $lang_file]));
        $lang_file = str_replace('._', '.', $lang_file) . '.php';
        return $lang_file;
    }
}

if (!function_exists('export')) {
    function export($path = null, $locales = null)
    {
        $path = $path ?? config('trans-helper.export.path');
        $locales = $locales ?? VocabTerm::locales();
        $namespaces = VocabTerm::namespaces();
        foreach ($namespaces as $namespace) {
            foreach ($locales as $locale) {
                $lang_file = lang_file_name($path, $locale, $namespace);
                $lang_dir = dirname($lang_file);
                if (file_exists($lang_dir) && !is_dir($lang_dir)) {
                    unlink($lang_dir);
                }
                if (!file_exists($lang_dir)) {
                    mkdir($lang_dir, 0777, true);
                }

                $slugs = [];
                $terms = [];
                foreach (VocabTerm::where('namespace', $namespace)->get() as $term) {
                    $slugs[] = $term->slug;
                    $terms[] = $term->translation[$locale] ?? $term->translation[config('app.locale')];
                }
                $slugs = unique_slugs($slugs);
                $max = intdiv(max(array_map('strlen', $slugs)) + 3, 4) * 4;
                $lines = array_map(function($u, $v) use ($max) {
                    $u = "'{$u}'";
                    return sprintf("    %-{$max}s => '%s',", $u, $v);
                }, $slugs, $terms);
                $lines[] = "];\n";
                array_unshift($lines, "\nreturn [");
                array_unshift($lines, '<?php');
                file_put_contents($lang_file, implode("\n", $lines));
            }
        }
    }
}
