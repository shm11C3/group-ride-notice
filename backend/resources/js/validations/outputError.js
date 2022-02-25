export default class OutputError{
    constructor(name) {
        this.name = name;
    }

    /**
     * Web APIから受け取ったエラーメッセージを配列に変換
     *
     * @param {object} errors
     * @return {Array} error_arr
     */
    convert_to_array(errors){
        let error_arr = [];
        Object.keys(errors).forEach(function(key) {
            error_arr.push(errors[key][0]);
        });
        return error_arr;
    }
}
