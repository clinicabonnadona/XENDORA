const state = {
    providersList: [],
    providersInfoFromXendora: [],
    providersTypesFromXendora: [],
    providersAgentsFromXendora: [],
    providersAgentsLinesFromXendora: [],
    providersIsLoading: false
};

const getters = {};

const actions = {
    async getPharmProvidersFromHosvital({ commit }) {
        const result = await axios.get(
            "provider-evaluation/get/all-providers/from-hosvital"
        );

        if (result.data.status === 200) {
            if (result.data.data.length < 0) {
                commit("SET_PROVIDERS_LIST", []);
            }
            commit("SET_PROVIDERS_LIST", result.data.data);
        }
    },
    async getPharmProvidersFromXendora({ commit }) {
        commit("SET_PROVIDERS_IS_LOADING", true);

        const result = await axios.get(
            "provider-evaluation/get/all-providers/from-xendora"
        );

        if (result.data.status === 200) {
            if (result.data.data.length < 0) {
                commit("SET_PROVIDERS_INFO", []);
            }
            commit("SET_PROVIDERS_INFO", result.data.data);
        }

        commit("SET_PROVIDERS_IS_LOADING", false);
    },
    async getPharmProvidersTypesFromXendora({ commit }) {
        const result = await axios.get(
            "provider-evaluation/get/providers-types/from-xendora"
        );

        if (result.data.status === 200) {
            if (result.data.data.length < 0) {
                commit("SET_PROVIDERS_TYPES", []);
            }
            commit("SET_PROVIDERS_TYPES", result.data.data);
        }
    },
    async getProvidersAgentsFromXendora({ commit }, payload) {
        const result = await axios.get(
            "provider-evaluation/get/all-agents/from-xendora/" + payload
        );

        //console.log(result.data);
        if (result.data.status === 200) {
            if (result.data.data.length < 0) {
                commit("SET_PROVIDERS_AGENTS", []);
            }
            commit("SET_PROVIDERS_AGENTS", result.data.data);
        }
    },
    async getProvidersAgentsLinesFromXendora({ commit }) {
        const result = await axios.get(
            "provider-evaluation/get/all-agents-lines/from-xendora"
        );

        //console.log(result.data);
        if (result.data.status === 200) {
            if (result.data.data.length < 0) {
                commit("SET_PROVIDERS_AGENTS_LINES", []);
            }
            commit("SET_PROVIDERS_AGENTS_LINES", result.data.data);
        }
    }
};

const mutations = {
    SET_PROVIDERS_LIST(state, providersList) {
        state.providersList = providersList;
    },
    SET_PROVIDERS_INFO(state, providersInfoFromXendora) {
        state.providersInfoFromXendora = providersInfoFromXendora;
    },
    SET_PROVIDERS_TYPES(state, providersTypesFromXendora) {
        state.providersTypesFromXendora = providersTypesFromXendora;
    },
    SET_PROVIDERS_AGENTS(state, providersAgentsFromXendora) {
        state.providersAgentsFromXendora = providersAgentsFromXendora;
    },
    SET_PROVIDERS_AGENTS_LINES(state, providersAgentsLinesFromXendora) {
        state.providersAgentsLinesFromXendora = providersAgentsLinesFromXendora;
    },
    SET_PROVIDERS_IS_LOADING(state, providersIsLoading) {
        state.providersIsLoading = providersIsLoading;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
