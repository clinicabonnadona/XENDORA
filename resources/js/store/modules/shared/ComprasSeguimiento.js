const state = {
    transaccionesCompras: [],
    isLoadingTransaccionesCompras: false,
    initDate: null,
    endDate: null
};

const getters = {};

const actions = {
    async getTransaccionesCompras({ commit, state }) {
        commit("SET_IS_LOADING_TRANSACCIONES_COMPRAS", true);

        const result = await axios.get(
            "compras/get/inventory/" + state.initDate + "/" + state.endDate
        );

        if (result.data.status === 400 || result.data.status === 204)
            commit("SET_TRANSACCIONES_COMPRAS", result.data.data);

        if (result.data.status === 200)
            commit("SET_TRANSACCIONES_COMPRAS", result.data.data);

        commit("SET_IS_LOADING_TRANSACCIONES_COMPRAS", false);
    }
};

const mutations = {
    SET_IS_LOADING_TRANSACCIONES_COMPRAS(state, isLoadingTransaccionesCompras) {
        state.isLoadingTransaccionesCompras = isLoadingTransaccionesCompras;
    },
    SET_TRANSACCIONES_COMPRAS(state, transaccionesCompras) {
        state.transaccionesCompras = transaccionesCompras;
    },
    SET_INIT_DATE(state, initDate) {
        state.initDate = initDate;
    },
    SET_END_DATE(state, endDate) {
        state.endDate = endDate;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
