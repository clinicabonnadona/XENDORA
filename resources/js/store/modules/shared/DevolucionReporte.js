const state = {
    activeReturns: [],
    isLoadingReturns: false,
    initDateRD: null,
}

const getters = {
    activeReturns: state => state.activeReturns,
    activeReturnsCount: (state, getters) => getters.activeReturns.length,
    isLoadingReturns: state => state.isLoadingReturns,
    initDateRD: state => state.initDateRD,
}

const actions = {
    async getActiveReturns({commit, state}, payload) {

        commit('SET_ACTIVE_RETURNS_IS_LOADING', true)

        const result = await axios.get('reportes/get-returns/'+ state.initDateRD )

        if (result.status === 200) {

            commit('SET_ACTIVE_RETURNS', result.data.data)
            commit('SET_ACTIVE_RETURNS_IS_LOADING', false)

        } else if (result.status === 400) {

            commit('SET_ACTIVE_RETURNS_IS_LOADING', false)

        }       

    }
}

const mutations = {
    SET_ACTIVE_RETURNS_IS_LOADING(state, isLoadingReturns) {
        state.isLoadingReturns = isLoadingReturns
    },
    SET_ACTIVE_RETURNS(state, activeReturns) {
        state.activeReturns = activeReturns
    },
    SET_INIT_DATE(state, initDateRD) {
        state.initDateRD = initDateRD
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
