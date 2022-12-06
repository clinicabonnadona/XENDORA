const state = {
    providers: [],
    providersType: [],
    isLoadingProviders: false,
    isLoadingProvidersType: false
};

const getters = {
    providers: state => state.providers,
    providersType: state => state.providersType,
    isLoadingProviders: state => state.isLoadingProviders,
    isLoadingProvidersType: state => state.isLoadingProvidersType
};

const actions = {
    async getProvidersTypes({ commit }) {
        commit("SET_IS_LOADING_PROVIDERS_TYPE", true);

        const result = await axios.get(
            "provider-causation/get/providers-types/from-hosvital"
        );

        if (result.data.status === 200) {
            commit("SET_PROVIDERS_TYPE", result.data.data);
        } else {
            commit("SET_PROVIDERS_TYPE", []);
        }

        commit("SET_IS_LOADING_PROVIDERS_TYPE", false);
    },
    async getProvidersList({ commit }, payload) {
        commit("SET_IS_LOADING_PROVIDERS", true);

        const result = await axios.get("provider-causation/get/all-providers/from-hosvital/"+ payload);

        if (result.data.status === 200) {
            commit("SET_PROVIDERS", result.data.data);
        } else {
            commit("SET_PROVIDERS", []);
        }

        commit("SET_IS_LOADING_PROVIDERS", false);

    }
};
const mutations = {
    SET_PROVIDERS(state, providers) {
        state.providers = providers;
    },
    SET_PROVIDERS_TYPE(state, providersType) {
        state.providersType = providersType;
    },
    SET_IS_LOADING_PROVIDERS(state, isLoadingProviders) {
        state.isLoadingProviders = isLoadingProviders;
    },
    SET_IS_LOADING_PROVIDERS_TYPE(state, isLoadingProvidersType) {
        state.isLoadingProvidersType = isLoadingProvidersType;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
