const state = {
    activeTotalReturns: [],
    isLoadingTotalReturns: false,
    docPacNum: null,
}

const getters = {
    activeTotalReturns: state => state.activeTotalReturns,
    activeTotalReturnsCount: (state, getters) => getters.activeTotalReturns.length,
    isLoadingTotalReturns: state => state.isLoadingTotalReturns,
    docPacNum: state => state.docPacNum,
}

const actions = {
    async getActiveTotalReturns({commit, state}, payload) {

        commit('SET_ACTIVE_TOTAL_RETURNS_IS_LOADING', true)

        const result = await axios.get('reportes/get-total-returns/' + payload.docPacNum)

        commit('SET_ACTIVE_TOTAL_RETURNS', result.data.data)

        commit('SET_ACTIVE_TOTAL_RETURNS_IS_LOADING', false)

    }
}

const mutations = {
    SET_ACTIVE_TOTAL_RETURNS_IS_LOADING(state, isLoadingTotalReturns) {
        state.isLoadingTotalReturns = isLoadingTotalReturns
    },
    SET_ACTIVE_TOTAL_RETURNS(state, activeTotalReturns) {
        state.activeTotalReturns = activeTotalReturns
    },
    SET_DOC_PAC_NUM(state, docPacNum) {
        state.docPacNum = docPacNum
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
