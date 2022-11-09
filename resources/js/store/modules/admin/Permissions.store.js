/* HANDLE STATE AND OTHERS
* */

const state = {
    activePermissions: [],
    permission: null,
    isLoadingPermissions: false,
    isCreatingPermission: false,
    isDesactivatingPermission: false,
    permissionErrors: []
}

const getters = {
    activePermissions: (state) => state.activePermissions,
    activePermissionsCount: (state, getters) => getters.activePermissions.length,
    isLoadingPermissions: (state) => this.state.isLoadingPermissions,
    isCreatingPermission: (state) => this.state.isCreatingPermission,
    isDesactivatingPermission: (state) => this.state.isDesactivatingPermission,
    permissionErrors: (state) => state.permissionErrors
}

const actions = {
    async getActivePermissions({commit}) {
        commit('SET_IS_LOADING_PERMISSIONS', true)
        const result = await axios.get('admin/permissions')
        commit('SET_ACTIVE_PERMISSIONS', result.data)
        commit('SET_IS_LOADING_PERMISSIONS', false)
    },
    async savePermission({dispatch, commit, state}, payload) {
        commit('SET_IS_CREATING_PERMISSIONS', true)
        const result = await axios.post('admin/permissions', payload)

        if (result.data.status === 400) {
            commit('SET_ERRORS', result.data.errors.name)
        } else {
            commit('SET_IS_CREATING_PERMISSIONS', false)
            dispatch('getActivePermissions')
        }
    },
    async desactivatePermission({dispatch, commit, state}, payload) {
        commit('SET_IS_DESACTIVATING_PERMISSIONS', true)
        const result = await axios.post('admin/permissions/delete', payload)
        if (result.data.status === 200) {
            commit('SET_IS_DESACTIVATING_PERMISSIONS', false)
            dispatch('getActivePermissions')
        }
    }
}

const mutations = {
    SET_ACTIVE_PERMISSIONS(state, activePermissions) {
        state.activePermissions = activePermissions
    },
    SET_PERMISSION(state, permission) {
        state.permission = permission
    },
    SET_IS_LOADING_PERMISSIONS(state, isLoadingPermissions) {
        state.isLoadingPermissions = isLoadingPermissions
    },
    SET_IS_CREATING_PERMISSIONS(state, isCreatingPermission) {
        state.isCreatingPermission = isCreatingPermission
    },
    SET_IS_DESACTIVATING_PERMISSIONS(state, isDesactivatingPermission) {
        state.isDesactivatingPermission = isDesactivatingPermission
    },
    SET_ERRORS(state, permissionErrors) {
        state.permissionErrors = permissionErrors
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
