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

if(!function_exists('floor_plus')){
    /**
     * 桁数を指定して切り捨て
     *
     * @param float $value     切り捨てる値
     * @param int   $precision 切り捨てる桁数
     */
    function floor_plus(float $value, ?int $precision = null): float
    {
      if (null === $precision) {
        return (float)floor($value);
      }
      if ($precision < 0) {
        throw new \RuntimeException('Invalid precision');
      }

      $reg = $value - 0.5 / (10 ** $precision);
      return round($reg, $precision, $reg > 0 ? PHP_ROUND_HALF_UP : PHP_ROUND_HALF_DOWN);
    }
}
