const state = {
    shippingPurchaseOrders: [],
    shippingPurchaseOrdersReport: [],
    isLoadingShippingOrders: false,
    initDate: null,
    endDate: null
};

const getters = {};

const actions = {
    async getShippingPurchaseOrders({ commit, state }) {
        commit("SET_PURCHASE_ORDERS_IS_LOADING", true);

        const result = await axios.get(
            "farmacia/get/active-orders-to-shipping/" +
                state.initDate +
                "/to/" +
                state.endDate
        );

        commit("SET_SHIPPING_PURCHASE_ORDERS", result.data.data);

        commit("SET_PURCHASE_ORDERS_IS_LOADING", false);
    },
    async saveShippingPurchaseOrders({ commit, state, dispatch }, payload) {
        commit("SET_PURCHASE_ORDERS_IS_LOADING", true);

        const result = await axios.post(
            "farmacia/post/shipping-orders",
            payload
        );

        dispatch("getShippingPurchaseOrders");

        commit("SET_PURCHASE_ORDERS_IS_LOADING", false);

        return result;
    },
    async updateShippingPurchaseOrders({ commit, state, dispatch }, payload) {
        commit("SET_PURCHASE_ORDERS_IS_LOADING", true);

        const result = await axios.post(
            "farmacia/update/shipping-orders",
            payload
        );

        dispatch("getShippingPurchaseOrders");

        commit("SET_PURCHASE_ORDERS_IS_LOADING", false);

        return result;
    },
    async getDataToReport({ commit }, payload) {
        commit("SET_PURCHASE_ORDERS_IS_LOADING", true);

        const result = await axios.get(
            "farmacia/get/active-orders-to-shipping-report/" +
                payload.initDate +
                "/to/" +
                payload.endDate
        );

        commit("SET_SHIPPING_PURCHASE_ORDERS_REPORT", result.data.data);

        commit("SET_PURCHASE_ORDERS_IS_LOADING", false);
    }
};

const mutations = {
    SET_PURCHASE_ORDERS_IS_LOADING(state, isLoadingShippingOrders) {
        state.isLoadingShippingOrders = isLoadingShippingOrders;
    },
    SET_SHIPPING_PURCHASE_ORDERS(state, shippingPurchaseOrders) {
        state.shippingPurchaseOrders = shippingPurchaseOrders;
    },
    SET_SHIPPING_PURCHASE_ORDERS_REPORT(state, shippingPurchaseOrdersReport) {
        state.shippingPurchaseOrdersReport = shippingPurchaseOrdersReport;
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
