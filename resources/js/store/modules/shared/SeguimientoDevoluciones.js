const state = {
    devolutionsOrders: [],
    isLoadingDevolutionsOrders: false,
    initDate: null,
    endDate: null
};
const getters = {};

const actions = {
    async getDevolutionsOrders({ commit, state }) {
        commit("SET_IS_LOADING_DEVOLUTIONS_ORDERS", true);

        const result = await axios.get(
            "reportes/seg-evolutions-fact/" +
                state.initDate +
                "/to/" +
                state.endDate
        );

        commit("SET_DEVOLUTIONS_ORDERS", result.data.data);

        commit("SET_IS_LOADING_DEVOLUTIONS_ORDERS", false);
    }
};
const mutations = {
    SET_IS_LOADING_DEVOLUTIONS_ORDERS(state, isLoadingDevolutionsOrders) {
        state.isLoadingDevolutionsOrders = isLoadingDevolutionsOrders;
    },
    SET_DEVOLUTIONS_ORDERS(state, devolutionsOrders) {
        state.devolutionsOrders = devolutionsOrders;
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
