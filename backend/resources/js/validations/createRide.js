export default class CreateRideValidation {
    constructor(name){
        this.name = name;
    }

    /**
     * meetingPlaceのバリデーション処理
     *
     * @param {object} inputData WeB APIに送信したい入力データ
     * @return {Array} error_arr
     */
    validationMeetingPlace(inputData){
        let error_arr = [];
        if(!inputData.name){
            error_arr.push('名前を入力してください');
        }
        if(!inputData.prefecture_code){
            error_arr.push('都道府県を選択してください');
        }
        if(!inputData.address){
            error_arr.push('場所の詳細を入力してください');
        }
        if(inputData.publish_status === ''){
            error_arr.push('公開設定を選択してください');
        }

        return　error_arr;
    }

    /**
     * rideRouteのバリデーション処理
     *
     * @param {object} inputData WeB APIに送信したい入力データ　
     * @return {Array} error_arr
     */
    validationRideRoute(inputData){
        let error_arr = [];
        if(!inputData.name){
            error_arr.push('名前を入力してください');
        }
        if(!inputData.elevation){
            error_arr.push('獲得標高を入力してください');
        }
        if(!inputData.distance){
            error_arr.push('距離を入力してください');
        }
        if(!inputData.comment){
            error_arr.push('ルートの説明を入力してください');
        }
        if(inputData.publish_status === ''){
            error_arr.push('公開設定を選択してください');
        }

        return error_arr;
    }


}
