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
        }).toDataURL('image/jpeg');
    }

    /**
     * 文字列のdata URIをfileに変換
     *
     * @param {string} cropped_image_uri
     * @return {file} file
     */
    dataUriToFile(cropped_image_uri){
        const byteString = atob(cropped_image_uri.split( "," )[1] ) ;
        const mimeType = cropped_image_uri.match( /(:)([a-z\/]+)(;)/ )[2] ;

        let content= '';
        for(let i=0, l=byteString.length, content=new Uint8Array( l ); l>i; i++) {
            content[i] = byteString.charCodeAt(i);
        }

        const blob = new Blob( [ content ], {
            type: mimeType ,
        });

	    const file = new File([blob], "cropped_file.jpeg", { type: "image/jpeg" });

        return blob;
    }
}
