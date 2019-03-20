<?php

namespace TopviewDigital\TranslationHelper\Service;

use TopviewDigital\TranslationHelper\Interfaces\AsyncBrokerInterface;
use TopviewDigital\TranslationHelper\Model\VocabCite;
use TopviewDigital\TranslationHelper\Model\VocabTerm;

class CiteUpdater implements AsyncBrokerInterface
{
    protected $cite;
    protected $term;

    public function __construct(VocabTerm $vocab, array $tracer)
    {
        $cite = [];
        //Clean Tail Text
        $cite['file'] = preg_replace('/(\.php)(.+)$/', '${1}', $tracer[0]['file']);
        //Remove Head of Base_Path
        $cite['file'] = str_replace(base_path(), '', $cite['file']);
        $cite['line'] = $tracer[0]['line'];
        array_shift($tracer);
        $cite['function'] = $tracer[0]['function'] ?? '';
        $cite['class'] = $tracer[0]['class'] ?? '';
        $cite = VocabCite::firstOrCreate($cite);
        $cite->refresh();
        $vocab->cites()->sync([$cite->id], false);
        $cite->save();
        $this->cite = $cite->id;
        $this->term = $vocab->term;
    }

    private function initVocabCite(VocabCite $cite)
    {
        if (empty($cite->code)) {
            $lines = explode("\n", file_get_contents(base_path().$cite->file));
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
                    return sprintf("%{$max}d    %s  ", $u + 1, rtrim($v));
                }, array_keys($code), $code));
                $cite->save();
            }
        }
    }

    public function handle()
    {
        $this->initVocabCite(VocabCite::find($this->cite));
    }
}
