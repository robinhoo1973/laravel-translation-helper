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
        $this->setTable('_tans_helper_cites');
        parent::__construct($attributes);
    }

    public function terms()
    {
        return $this->belongsToMany(
            VocabTerm,
            '_trans_helper_links',
            'cited',
            'vocab',
            'id',
            'id'
        );
    }

    public function sweep()
    {
        $line = explode("\n", file_get_contents(base_path() . $this->file));
        $line = $line[$this->line - 1] ?? '';
        $count = 0;
        if ($line) {
            foreach ($this->terms()->get() as $term) {
                $keywords = ["localize('{$term->term}')", 'localize("' . $term->term . '")'];
                $matched = array_filter(array_map(function($u) use ($line) {
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
