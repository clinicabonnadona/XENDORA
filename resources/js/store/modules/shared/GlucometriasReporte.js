const state = {
    activeGlucometries: [],
    isLoadingGlucometries: false,
    initDateRG: null,
}

const getters = {
    activeGlucometries: state => state.activeGlucometries,
    activeGlucometriesCount: (state, getters) => getters.activeGlucometries.length,
    isLoadingGlucometries: state => state.isLoadingGlucometries,
    initDateRG: state => state.initDateRG,
}

const actions = {
    async getActiveGlucometries({commit, state}, payload) {

        commit('SET_ACTIVE_GLUCOMETRIES_IS_LOADING', true)

        const result = await axios.get('reportes/get-gluco/'+ state.initDateRG )

        commit('SET_ACTIVE_GLUCOMETRIES', result.data.data)

        commit('SET_ACTIVE_GLUCOMETRIES_IS_LOADING', false)

    }
}

const mutations = {
    SET_ACTIVE_GLUCOMETRIES_IS_LOADING(state, isLoadingGlucometries) {
        state.isLoadingGlucometries = isLoadingGlucometries
    },
    SET_ACTIVE_GLUCOMETRIES(state, activeGlucometries) {
        state.activeGlucometries = activeGlucometries
    },
    SET_INIT_DATE(state, initDateRG) {
        state.initDateRG = initDateRG
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
