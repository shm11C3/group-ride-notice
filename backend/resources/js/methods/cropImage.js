import Cropper from 'cropperjs';

export default class CropImage{
    constructor(name) {
        this.name = name;
    }

    /**
     * Cropperライブラリの呼び出し
     *
     * @param {string} image_name
     */
    loadCropper (image_name) {
        const image_crop_data = document.getElementById(image_name);

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





}
