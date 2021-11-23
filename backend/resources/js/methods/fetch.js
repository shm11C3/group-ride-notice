/**
 * App\Http\Controllers\Api\User\FollowController
 * -> follow()
 *
 * @param {string} user_to
 */
export const postFollow = (user_to)=>{
    const url = `${window.location.protocol}//${window.location.hostname}/api/post/follow`;
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

/**
 * App\Http\Controllers\Api\Ride\RideController
 * -> getUserRides()
 *
 * @param {string} user_uuid
 * @param {int} page
 * @returns
 */
export const fetchUserRides = (user_uuid, page) =>{
    const url = `/api/get/userRides/${user_uuid}?page=${page}`;

    return fetch (url)
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

/**
 * App\Http\Controllers\Api\Ride\FollowController
 * -> getFollows()
 *
 * @param {string} user_by
 * @param {int} page
 * @returns
 */
export const fetchFollows = (user_by, page) =>{
    const url = `${window.location.protocol}//${window.location.hostname}/api/get/follows/${user_by}?page=${page}`;

    return fetch (url)
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
/**
 * App\Http\Controllers\Api\Ride\FollowController
 * -> getFollowers()
 *
 * @param {string} user_to
 * @param {int} page
 * @returns
 */
export const fetchFollowers = (user_to, page) =>{
    const url = `${window.location.protocol}//${window.location.hostname}/api/get/followers/${user_to}?page=${page}`;

    return fetch (url)
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
