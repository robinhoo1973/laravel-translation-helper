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
        $this->setTable(config('trans-helper.database.table.cite'));
        parent::__construct($attributes);
    }

    public function terms()
    {
        return $this->belongsToMany(
            config('trans-helper.model.term'),
            config('trans-helper.database.table.link'),
            'cited',
            'vocab',
            'id',
            'id'
        );
    }

    public function sweep()
    {
        $line = explode("\n", file_get_contents($this->file));
        $line = $line[$this->line] ?? '';
        if ($line) {
            $terms = call_user_func_array(
                'array_merge',
                array_map(
                    function($u) {
                        return ["localize('{$u->term}')", 'localize("' . $u->term . '")'];
                    },
                    $this->terms()->get()->all()
                )
            );
            $matched = array_filter(array_map(function($u) use ($line) {
                return strpos($line, $u);
            }, $terms), 'strlen');

            if (!empty($matched)) {
                return;
            }
        }
        $this->delete();
    }
}
