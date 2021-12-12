import Cropper from 'cropperjs';

export default class CropImage{
    //Cropper = new Cropper();
    constructor(name) {
        this.name = name;
    }

    /**
     * クロップ処理呼び出し
     *
     * @param {*} img
     */
    cropper(img){
        console.log(img);


    }
}
