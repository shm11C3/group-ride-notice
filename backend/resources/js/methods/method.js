
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

export default class WindowHelper {
    constructor(name) {
        this.name = name;
    }

    /**
     * 改行コード '\r\n', '\n'ごとの配列に分割する
     *
     * @param {string} string
     * @return {array}
     */
    splitByLineFeed(string){
        if(string){
            return string.split(/\r\n|\n/);
        }
        return [];
    }

    /**
     * y-m-d h:m:sフォーマットの日付を[y年, m月d日, h時m分, s秒]に変換
     *
     * @param {string} date
     * @return {array}
     */
    replaceDate(date){
        return [
            `${date.substring(0,4)}年`,
            `${date.substring(5,7)}月${date.substring(8,10)}日`,
            `${date.substring(10,12)}時${date.substring(14,16)}分`,
            `${date.substring(18,20)}秒`
        ]
    }
}


