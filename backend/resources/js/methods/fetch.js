
/**
 * App\Http\Controllers\Api\User\FollowController
 * follow()
 *
 * @param {*} user_to
 */
export const postFollow = (user_to)=>{
    const url = `${window.location.protocol}//${window.location.hostname}/api/post/follow`
    const data = {
        "user_to" : user_to
    }

    return fetch(url,
        {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            return response.json();
        })
        .then(res => {
            return res;
        })
        .catch(e => {
            console.error(e);
            return e;
        });
}
