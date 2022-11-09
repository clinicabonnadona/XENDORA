const state = {
    activeRoles: [],
    role: null,
    isLoadingRoles: false,
    isCreatingRole: false,
    isDesactivatingRole: false,
    rolesErrors: []
}

const getters = {
    activeRoles: (state) => state.activeRoles,
    activeRolesCount: (state, getters) => getters.activeRoles.length,
    isLoadingRoles: (state) => this.state.isLoadingRoles,
    isCreatingRole: (state) => this.state.isCreatingRole,
    isDesactivatingRole: (state) => this.state.isDesactivatingRole,
    rolesErrors: (state) => state.rolesErrors
}

const actions = {
    async getActiveRoles({commit}) {
        commit('SET_IS_LOADING_ROLES', true)
        const result = await axios.get('admin/roles')
        commit('SET_ACTIVE_ROLES', result.data.data)
        commit('SET_IS_LOADING_ROLES', false)
    },
    async createNewRol({dispatch, commit, state}, payload) {
        commit('SET_IS_CREATING_ROLE', true)
        const result = await axios.post('admin/roles', payload)

        if (result.data.status === 400) {
            commit('SET_ERRORS', result.data.errors.name)
        } else {
            commit('SET_IS_CREATING_ROLE', false)
            dispatch('getActiveRoles')
        }

        commit('SET_IS_CREATING_ROLE', false)
    },
    async editRole({dispatch, commit, state}, payload) {
        commit('SET_IS_CREATING_ROLE', true)
        const result = await axios.post('admin/roles/edit', payload)
        if (result.data.status !== 400) {
            commit('SET_IS_CREATING_ROLE', false)
            dispatch('getActiveRoles')
        }
        commit('SET_IS_CREATING_ROLE', false)
    },
    async deleteRole({dispatch, commit, state}, payload) {
        commit('SET_IS_DESACTIVATING_ROLE', true)
        const result = await axios.post('admin/roles/delete', payload)
        if (result.data.status !== 400) {
            commit('SET_IS_CREATING_ROLE', false)
            dispatch('getActiveRoles')
        }
        commit('SET_IS_DESACTIVATING_ROLE', false)
    }
}

const mutations = {
    SET_ACTIVE_ROLES(state, activeRoles) {
        state.activeRoles = activeRoles
    },
    SET_ROLE(state, role) {
        state.role = role
    },
    SET_IS_LOADING_ROLES(state, isLoadingRoles) {
        state.isLoadingRoles = isLoadingRoles
    },
    SET_IS_CREATING_ROLE(state, isCreatingRole) {
        state.isCreatingRole = isCreatingRole
    },
    SET_IS_DESACTIVATING_ROLE(state, isDesactivatingRole) {
        state.isDesactivatingRole = isDesactivatingRole
    },
    SET_ERRORS(state, rolesErrors) {
        state.rolesErrors = rolesErrors
    }
}


export default {
    state,
    getters,
    actions,
    mutations
}
