<?php

namespace TopviewDigital\TranslationHelper\Model;

use Illuminate\Database\Eloquent\Model;

class VocabCite extends Model
{
    protected $fillable = [
        'file', 'line', 'function', 'class', 'code',
    ];

    public function __construct(array $attributes = [])
    {
        $connection = config('trans-helper.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable('_trans_helper_cites');
        parent::__construct($attributes);
    }

    public function terms()
    {
        return $this->belongsToMany(
            VocabTerm::class,
            '_trans_helper_links',
            'cited',
            'vocab',
            'id',
            'id'
        );
    }

    public function sweep()
    {
        $filePath = realpath(base_path() . $this->file);
        $basePath = realpath(base_path());
        if ($filePath === false || $basePath === false
            || strpos($filePath . DIRECTORY_SEPARATOR, $basePath . DIRECTORY_SEPARATOR) !== 0) {
            return;
        }
        $line = explode("\n", file_get_contents($filePath));
        $line = $line[$this->line - 1] ?? '';
        $count = 0;
        if ($line) {
            foreach ($this->terms()->get() as $term) {
                $keywords = ["localize('{$term->term}')", 'localize("'.$term->term.'")'];
                $matched = array_filter(array_map(function ($u) use ($line) {
                    return strpos($line, $u);
                }, $keywords), 'strlen');
                if (empty($matched)) {
                    $this->terms()->detach($term->id);
                }
                $count += count($matched);
            }
        }
        if ($count == 0) {
            $this->delete();
        }
    }
}
