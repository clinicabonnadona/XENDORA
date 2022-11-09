const state = {
    activeUsers: [],
    activeDoctypes: [],
    user: null,
    isLoadingUsers: false,
    isCreatingUsers: false,
    isEditingUsers: false,
    isDesactivatingUser: false,
    usersErrors: [],
}

const getters = {
    activeUsers: (state) => state.activeUsers,
    activeUsersCount: (state, getters) => getters.activeUsers.length,
    activeDoctypes: (state) => state.activeDoctypes,
    isLoadingUsers: (state) => state.isLoadingUsers,
    isCreatingUsers: (state) => state.isCreatingUsers,
    isEditingUsers: (state) => state.isEditingUsers,
    isDesactivatingUser: (state) => state.isDesactivatingUser,
    usersErrors : (state) => state.usersErrors
}

const actions = {
    async getActiveUsers({commit}) {
        commit('SET_IS_LOADING_USER', true)
        const result = await axios.get('admin/users')

        if (result.status === 200) {
            commit('SET_IS_LOADING_USER', false)
            commit('SET_ACTIVE_USERS', result.data.data)
            //console.log(result.data.data)
        }

        commit('SET_IS_LOADING_USER', false)
    },
    async getActiveDoctype({commit}) {
        const result = await axios.get('admin/doctypes')
        if (result.status === 200) {
            commit('SET_ACTIVE_DOCTYPES', result.data.data)
            //console.log(result.data.data)
        }
    },
    async saveUsers({dispatch, commit, state}, payload) {

        commit('SET_IS_CREATING_USER', true)
        
        const result = await axios.post('admin/users', payload)

        if (result.data.status === 201) {

            //console.log(result.data)
            commit('SET_USER_ERRORS', [])
            dispatch('getActiveUsers')
            commit('SET_IS_CREATING_USER', false)


        } else if (result.data.status === 400) {

            console.log('error')
            commit('SET_USER_ERRORS', result.data.errors)

        }
    },
    async editUser({dispatch, commit, state}, payload) {

        commit('SET_IS_EDITING_USER', true)

        const result = await axios.post('admin/users/update', payload)

        if (result.data.status === 200) {

            //console.log(result.data.data)
            commit('SET_USER_ERRORS', [])
            dispatch('getActiveUsers')
            commit('SET_IS_EDITING_USER', false)

        } else if (result.data.status === 400) {

            commit('SET_USER_ERRORS', result.data.errors)
            commit('SET_IS_EDITING_USER', false)

        }

    }

}

const mutations = {
    SET_ACTIVE_USERS(state, activeUsers) {
        state.activeUsers = activeUsers
    },
    SET_USER(state, user) {
        state.user = user
    },
    SET_ACTIVE_DOCTYPES(state, activeDoctypes) {
        state.activeDoctypes = activeDoctypes
    },
    SET_IS_LOADING_USER(state, isLoadingUsers) {
        state.isLoadingUsers = isLoadingUsers
    },
    SET_IS_CREATING_USER(state, isCreatingUsers) {
        state.isCreatingUsers = isCreatingUsers
    },
    SET_IS_EDITING_USER(state, isEditingUsers) {
        state.isEditingUsers = isEditingUsers
    },
    SET_IS_DESACTIVATING_USER(state, isDesactivatingUser) {
        state.isDesactivatingUser = isDesactivatingUser
    },
    SET_USER_ERRORS(state, usersErrors) {
        state.usersErrors = usersErrors
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
