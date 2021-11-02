export const passwordRule = {
    min:8,
    max:64
}

export const emailRule = {
    reg : new RegExp(/^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/),
    max: 255
};

export const nameRule = {
    max : 32
}
