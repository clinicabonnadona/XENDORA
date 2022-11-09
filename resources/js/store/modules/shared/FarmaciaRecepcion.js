import moment from "moment";
/* To Handle State */
const state = {
    activeOrders: [],
    activeOrder: null,
    isLoadingOrders: false,
    isCreatingOrders: false,
    createdDataOrders: null,
    isUpdatingOrders: false,
    updatedDataOrders: null,
    isDeletingOrders: false,
    deletedDataOrders: null,
    connectionStatus: true,
    modalStatus: true,
    initDate: null,
    endDate: null,
    dataToReport: []
};

/* To Handle Getters */
const getters = {
    activeOrders: state => state.activeOrders,
    activeOrder: state => state.activeOrder,
    activeOrdersCount: (state, getters) => {
        return getters.activeOrders.length;
    },
    isLoadingOrders: state => state.isLoadingOrders,
    isCreatingOrders: state => state.isCreatingOrders,
    createdDataOrders: state => state.createdDataOrders,
    isUpdatingOrders: state => state.isUpdatingOrders,
    updatedDataOrders: state => state.updatedDataOrders,
    isDeletingOrders: state => state.isDeletingOrders,
    deletedDataOrders: state => state.deletedDataOrders,
    connectionStatus: state => state.connectionStatus,
    initDate: state => state.initDate,
    endDate: state => state.endDate,
    dataToReport: state => state.dataToReport
};

/* To Handle Actions */
const actions = {
    async getActiveOrder({ commit, state }) {
        commit("SET_ACTIVEORDERS_IS_LOADING", true);

        const result = await axios.get(
            "farmacia/get/active-orders/" +
                state.initDate +
                "/to/" +
                state.endDate
        );

        if (result.data.status === 200) {
            if (result.data.data.length > 0) {
                commit("SET_ACTIVEORDERS", result.data.data);
                commit("SET_ACTIVEORDERS_IS_LOADING", false);
            } else {
                console.log("sin Datos");
                commit("SET_ACTIVEORDERS", []);
                commit("SET_ACTIVEORDERS_IS_LOADING", false);
            }
        }
    },

    async saveActiveOrder({ dispatch, commit, state }, payload) {
        commit("SET_IS_CREATINGORDERS", true);

        const result = await axios.post("farmacia/post/active-order", payload);

        //commit('SET_ACTIVEORDERS', result.data.data)

        if (result.data.status === 201) {
            commit("SET_CREATEDORDER", true);
            dispatch("getActiveOrder");
        } else {
            console.log("An error Ocurred!");
        }

        //console.log(result.data)
    },
    async getReceivedOrdersReport({ dispatch, commit, state }, payload) {
        commit("SET_ACTIVEORDERS_IS_LOADING", true);

        const result = await axios.get(
            "farmacia/get/received-orders/" +
                state.initDate +
                "/to/" +
                state.endDate
        );

        if (result.status === 200)
            commit("SET_DATA_TO_REPORT", result.data.data);

        commit("SET_ACTIVEORDERS_IS_LOADING", false);
    },
    async editingActiveOrder({ dispatch, commit, state }, payload) {
        commit("SET_ACTIVEORDERS_IS_LOADING", true);

        const result = await axios.post("farmacia/put/active-order/" + payload.receptionId, payload);
        if (result.status === 200) {
            dispatch("getActiveOrder");
        } else {
            console.log("error");
            commit("SET_ACTIVEORDERS_IS_LOADING", false);
        }

        commit("SET_ACTIVEORDERS_IS_LOADING", false);
    }
};

/* To Handle Mutations */
const mutations = {
    SET_ACTIVEORDERS_IS_LOADING(state, isLoadingOrders) {
        state.isLoadingOrders = isLoadingOrders;
    },
    SET_ACTIVEORDERS(state, activeOrdersList) {
        state.activeOrders = activeOrdersList;
    },
    SET_CONNECTION_STATUS(state, connectionStatus) {
        state.connectionStatus = connectionStatus;
    },
    SET_IS_CREATINGORDERS(state, isCreatingOrders) {
        state.isCreatingOrders = isCreatingOrders;
    },
    SET_CREATEDORDER(state, createdDataOrders) {
        state.createdDataOrders = createdDataOrders;
    },
    SET_INIT_DATE(state, initDate) {
        state.initDate = initDate;
    },
    SET_END_DATE(state, endDate) {
        state.endDate = endDate;
    },
    SET_DATA_TO_REPORT(state, dataToReport) {
        state.dataToReport = dataToReport;
    },
    SET_IS_UPDATINGORDERS(state, isUpdatingOrders) {
        state.isUpdatingOrders = isUpdatingOrders;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
