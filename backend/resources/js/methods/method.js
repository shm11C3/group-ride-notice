
/**
 * パスの最後のディレクトリ名を取得
 */
export const getLastPass = () =>{
    return location.pathname.split("/").pop();
}
