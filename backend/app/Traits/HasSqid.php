<?php

declare(strict_types=1);

namespace App\Traits;

use Sqids\Sqids;

trait HasSqid
{
    /**
     * Sqidインスタンスを取得
     */
    protected static function getSqids(): Sqids
    {
        $alphabet = config('app.sqids_alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $minLength = config('app.sqids_min_length', 8);

        return new Sqids(
            alphabet: $alphabet,
            minLength: $minLength
        );
    }

    /**
     * IDからSqidを生成
     */
    public function getSqidAttribute(): string
    {
        return static::getSqids()->encode([$this->id]);
    }

    /**
     * SqidからIDをデコード
     */
    public static function findBySqid(string $sqid): ?self
    {
        $ids = static::getSqids()->decode($sqid);

        if (empty($ids)) {
            return null;
        }

        return static::find($ids[0]);
    }

    /**
     * SqidからIDをデコード(失敗時は例外)
     */
    public static function findBySqidOrFail(string $sqid): self
    {
        $model = static::findBySqid($sqid);

        if ($model === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
        }

        return $model;
    }
}
