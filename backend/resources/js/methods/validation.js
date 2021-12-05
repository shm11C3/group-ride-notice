import {nameRule} from '../constants/user'
import {prefecture} from '../constants/constant'

export default class Validation{
    constructor(name) {
        this.name = name;
    }

    /**
     * 名前存在かつ最大数以下の場合trueを返す
     *
     * @param {string} name
     * @return bool
     */
    isNameValid(name) {
        return (name.length !== 0 && name.length <= nameRule.max);
    }

    /**
     * 都道府県コードが正常な場合trueを返す
     *
     * @param {int} prefecture_code
     * @return bool
     */
    isPrefecture_codeValid(prefecture_code) {
        return (prefecture_code > 0 && prefecture_code <= prefecture.length)
    }
}
