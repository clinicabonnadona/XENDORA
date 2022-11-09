/* To handle Rotación State*/
const state = {
    suministros: [],
    isLoadingSuministros: false,
    invoiceDetails: [],
    isLoadingInvoices: false
};

const getters = {
    suministros: state => state.suministros,
    //suministrosCount: (state, getters) => getters.suministros.length,
    isLoadingSuministros: state => state.isLoadingSuministros,
    invoiceDetails: state => state.invoiceDetails,
    invoiceDetailsCount: (state, getters) => getters.invoiceDetails.length,
    isLoadingInvoices: state => state.isLoadingInvoices
};

const actions = {
    async getSumistros({ commit }) {
        commit("SET_SUMINISTROS_IS_LOADING", true);

        const result = await axios.get("admin/suministros/rotacion");

        //console.log(result.data.data);
        commit("SET_SUMINISTROS", result.data.data);

        commit("SET_SUMINISTROS_IS_LOADING", false);
    },
    async getInvoiceDetails({ dispatch, commit, state }, payload) {
        commit("SET_INVOICE_DETAILS_IS_LOADING", true);

        const result = await axios.get(
            "admin/suministros/rotacion/invoice-details/med/" +
                payload.sumcod.trim() +
                "/month/" +
                payload.mes +
                "/year/" +
                payload.anio
        );

        commit("SET_INVOICE_DETAILS", result.data.data);

        commit("SET_INVOICE_DETAILS_IS_LOADING", false);
    }
};

const mutations = {
    SET_SUMINISTROS_IS_LOADING(state, isLoadingSuministros) {
        state.isLoadingSuministros = isLoadingSuministros;
    },
    SET_SUMINISTROS(state, suministros) {
        state.suministros = suministros;
    },
    SET_INVOICE_DETAILS_IS_LOADING(state, isLoadingInvoices) {
        state.isLoadingInvoices = isLoadingInvoices;
    },
    SET_INVOICE_DETAILS(state, invoiceDetails) {
        state.invoiceDetails = invoiceDetails;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
