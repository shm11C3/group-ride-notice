<?php declare(strict_types=1);

use Illuminate\Support\HtmlString;

if (! function_exists('html')) {
    /**
     * @param string $value
     * @return HtmlString
     */
    function html(string $value): HtmlString
    {
        return new HtmlString(nl2br(e($value)));
    }
}