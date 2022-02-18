export default class LoadImage{
    constructor(name) {
        this.name = name;
    }

    /**
     * CSS操作用のクラス名
     * 画像読み込みが完了するまで<img>に貼る
     */
    default_ride_opacity = 'load_img_transparent';
}
