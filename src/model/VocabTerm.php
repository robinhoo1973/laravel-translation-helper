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
        $this->setTable('_trans_helper_terms');
        parent::__construct($attributes);
    }

    public function cites()
    {
        return $this->belongsToMany(
            VocabCite::class,
            '_trans_helper_links',
            'vocab',
            'cited',
            'id',
            'id'
        );
    }

    public function getSlugAttribute($attr)
    {
        $key = slugify($this->translation['en']);

        return $key;
    }

    public function sweep()
    {
        if ($this->cites()->count() < 1) {
            $this->delete();
        }
    }

    public static function namespaces()
    {
        return self::distinct('namespace')->pluck('namespace')->toArray();
    }

    public static function locales()
    {
        return array_unique(
            call_user_func_array(
                'array_merge',
                array_map(
                    function ($u) {
                        return array_keys($u->translation);
                    },
                    self::get()->all()
                )
            )
        );
    }
}
