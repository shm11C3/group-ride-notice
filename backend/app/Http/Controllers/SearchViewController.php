<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchViewController extends Controller
{
    /**
     * 検索画面を表示
     * 
     * @return view
     */
    public function showSearch()
    {
        return view('search.form');
    }
}
