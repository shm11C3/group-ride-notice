<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    /**
     * キーワードのメタ文字をエスケープ
     * @param array $searchWords_arr
     * @return array $replaced_searchWord_arr
     */
    public function escapeMetaCharacters_byArr(array $searchWords_arr)
    {
        $replaced_SearchWords_arr = [];
        foreach($searchWords_arr as $searchWord){
            array_push($replaced_SearchWords_arr, '%' . addcslashes($searchWord, '%_\\') . '%');
        }
        return $replaced_SearchWords_arr;
    }

    /**
     * キーワードのメタ文字をエスケープ
     * @param string $searchWord
     * @return string
     */
    public function escapeMetaCharacters_byStr(string $searchWord)
    {
        return '%' . addcslashes($searchWord, '%_\\') . '%';
    }

    /**
     * スペースを置換
     *
     * @param string $request
     * @return array
     */
    public function spaceSubstitute(string $request)
    {
        $substitutedSpace = mb_convert_kana($request, 's');

        return preg_split('/[\s,]+/', $substitutedSpace, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * 受け取った検索文字列をエスケープ済みの配列に変換
     *
     * @param string $request
     * @return array
     */
    public function generateSearchArray(string $keyword)
    {
        return $this->escapeMetaCharacters_byArr($this->spaceSubstitute($keyword));
    }
}
