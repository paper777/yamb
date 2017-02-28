import auth from './modules/auth'

const profile = state => {
    //console.log(state.auth.user);
    return state.auth.user;
}

export {
    profile
}
