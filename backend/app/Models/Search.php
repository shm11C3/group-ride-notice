<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    /**
     * キーワードのメタ文字をエスケープ
     * @param array $searchWord
     * @return array $$searchWordArr
     */
    public function escapeMetaCharacters_byArr(array $searchWordArr)
    {
        foreach($searchWordArr as $i => $searchWord){
            $searchWordArr[$i] = '%' . addcslashes($searchWord, '%_\\') . '%';
        }
        
        return $searchWordArr;
    }

    /**
     * キーワードのメタ文字をエスケープ
     * @param string $$searchWord
     * @return string $$searchWord
     */
    public function escapeMetaCharacters_byStr(string $searchWord)
    {
        $searchWord = '%' . addcslashes($searchWord, '%_\\') . '%';
        
        return $searchWord;
    }

    /**
     * スペースを置換
     */
    public function spaceSubstitute($request)
    {
        $substitutedSpace = mb_convert_kana($request, 's');

        //$searchWord = "'".preg_replace("/( |　)+/u", "','", $request)."'";

        //$searchWordArr = preg_split('/[\s,]+/', $substitutedSpace, "");

        //dd($searchWord);

        $searchWordArr = preg_split('/[\s,]+/', $substitutedSpace, -1, PREG_SPLIT_NO_EMPTY);

        //dd($searchWordArr);

        return $searchWordArr;
    }
}
