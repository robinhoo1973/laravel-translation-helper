<?php

namespace TopviewDigital\TranslationHelper\Model;

use Illuminate\Database\Eloquent\Model;

class VocabTerm extends Model
{
    protected $casts = [
        'translation' => 'json',
    ];
    protected $fillable = [
        'term', 'translation', 'namespace',
    ];

    public function __construct(array $attributes = [])
    {
        $connection = config('trans-helper.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('trans-helper.database.table.term'));
        parent::__construct($attributes);
    }

    public function cites()
    {
        return $this->belongsToMany(
            config('trans-helper.model.cite'),
            config('trans-helper.database.table.link'),
            'vocab',
            'cited',
            'id',
            'id'
        );
    }

    public function sweep()
    {
        if ($this->cites()->count() < 1) {
            $this->delete();
        }
    }
}
