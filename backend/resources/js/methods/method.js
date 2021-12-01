
/**
 * パスの最後のディレクトリ名を取得
 */
export const getLastPass = () =>{
    return location.pathname.split("/").pop();
}

/**
 * URIのクエリパラメータを分割・デコードする
 *
 * @return {object}
 */
export const getQueryParam = () =>{
    return decodeURIComponent(location.search).split("&");
}
