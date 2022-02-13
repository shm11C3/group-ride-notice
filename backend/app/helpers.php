<?php declare(strict_types=1);

use Illuminate\Support\HtmlString;

if (! function_exists('html')) {
    /**
     * \nの改行タグをHTMLの<br>タグに変換
     *
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

if(!function_exists('get_nonStrict_domain_by_hostname')){
  /**
   * ホスト名から厳密ではないドメインを返す
   *
   * @param string  $hostname
   * @param int     $domainLength     サブドメインを含めないドメインの数 ex: www.example.com => 2, example.ne.jp => 3
   * @return string $nonStrict_domain
   */
  function get_nonStrict_domain_by_hostname(string $hostname, ?int $domainLength = null)
  {
    if($domainLength === null){
      $domainLength = 2;
    }

    $host_arr = explode('.', $hostname); // ホスト名を`.`で分割
    $host_arr_count = count($host_arr); // ホスト名の長さ

    if ($domainLength < 2 || $domainLength > $host_arr_count) {
      throw new \RuntimeException('Invalid domainLength');
    }

    $nonStrict_domain = '';
    for ($i = 0; $i < $domainLength; $i++) {
      $nonStrict_domain = $nonStrict_domain.'.'.$host_arr[$host_arr_count - $domainLength + $i];
    }

    return mb_substr($nonStrict_domain, 1);;
  }
}
