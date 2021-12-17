import Cropper from 'cropperjs';

export default class CropImage{
    constructor(name) {
        this.name = name;
    }

    /**
     * Cropperライブラリの呼び出し
     *
     * @param {string} image_id
     */
    loadCropper (image_id) {
        const image_crop_data = document.getElementById(image_id);

        if(image_crop_data){
            if(this.cropper){
                // 既にcropperが呼び出されている場合削除する
                this.cropper.destroy();
            }

            this.cropper = new Cropper(image_crop_data, {
                viewMode: 1,
                dragMode: 'move',
                aspectRatio: 1 / 1,
                autoCropArea: 0.65,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
            });
        }
    }

    /**
     * 切り取り処理
     *
     * @string cropped_image_uri
     */
    getCroppedImage(){
        return this.cropper.getCroppedCanvas({
            width:  400,
            height: 400,
        }).toDataURL();
    }

    /**
     * base64形式の文字列をpng形式のファイルに変換
     *
     * @param {string} cropped_image_uri
     * @return {file} file
     */
    base64ToFile(cropped_image_base64){
        const bin = atob(cropped_image_base64.replace(/^.*,/, ''));

        let buffer = new Uint8Array(bin.length);
        for (let i = 0; i < bin.length; i++) {
            buffer[i] = bin.charCodeAt(i);
        }
        return new File([buffer.buffer], 'cropped.png', {type: "image/png"});
    }
}
