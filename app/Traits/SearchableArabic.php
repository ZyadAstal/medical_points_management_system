<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait SearchableArabic
{
    /**
     * Scope a query to search with Arabic normalization.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $columns
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchArabic(Builder $query, $columns, $value)
    {
        if (empty($value)) {
            return $query;
        }

        $columns = (array) $columns;
        $words = explode(' ', $value);
        $words = array_filter($words); // Remove empty strings

        return $query->where(function ($q) use ($columns, $words) {
            foreach ($words as $word) {
                $normalizedArabicWord = $this->normalizeArabic($word);
                $lowerWord = strtolower($word); // للبحث في الأعمدة الإنجليزية

                $q->where(function ($innerQuery) use ($columns, $normalizedArabicWord, $lowerWord) {
                    foreach ($columns as $column) {
                        // عمود الاسم الإنجليزي: نبحث بالـ lowercase
                        if ($column === 'name_en') {
                            $innerQuery->orWhereRaw(
                                "LOWER(`{$column}`) LIKE ?",
                                ["%{$lowerWord}%"]
                            );
                        } else {
                            // الأعمدة العربية: نبحث بالتطبيع العربي
                            $innerQuery->orWhereRaw(
                                $this->getNormalizedColumnSql($column) . " LIKE ?",
                                ["%{$normalizedArabicWord}%"]
                            );
                        }
                    }
                });
            }
        });
    }

    /**
     * Normalize Arabic string.
     *
     * @param string $text
     * @return string
     */
    protected function normalizeArabic($text)
    {
        $search = ['أ', 'إ', 'آ', 'ة', 'ى'];
        $replace = ['ا', 'ا', 'ا', 'ه', 'ي'];
        return str_replace($search, $replace, $text);
    }

    /**
     * Get SQL for normalizing a column.
     *
     * @param string $column
     * @return string
     */
    protected function getNormalizedColumnSql($column)
    {
        // For security, wrap column name. Assuming standard column names.
        $wrappedColumn = "`{$column}`";

        // English columns don't need Arabic normalization — just use LOWER for case-insensitive match
        if ($column === 'name_en') {
            return "LOWER({$wrappedColumn})";
        }

        // Nested REPLACE for Alif variations, Ta Marbuta, and Alef Maksura
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$wrappedColumn}, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ة', 'ه'), 'ى', 'ي')";
    }

    /**
     * Detect if a string is primarily English/Latin.
     */
    protected function isEnglish($text)
    {
        return preg_match('/^[\x00-\x7F]+$/', trim($text));
    }
}
