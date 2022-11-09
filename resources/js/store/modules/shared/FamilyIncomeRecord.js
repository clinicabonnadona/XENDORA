const state = {
    censoInfo: [],
    familyIcomeRecords: [],
    isLoadingCenso: false,
    isLoadingFamilyRecords: false
};
const getters = {};
const actions = {
    async getCensoInfo({ commit, state }) {
        commit("SET_CENSO_IS_LOADING", true);

        const result = await axios.get("/orientacionusuario/get/censo");
        var structureBefore = result.data.data;
        if (result.data.status === 200) {
            structureBefore.push(structureBefore.shift(structureBefore));
        } else {
            structureBefore = [];
        }

        commit("SET_CENSO_INFO", structureBefore);
        commit("SET_CENSO_IS_LOADING", false);
        /* .then(response => {
                if (response.data.status == 200) {
                    var structureBefore = response.data.data;
                    structureBefore.push(
                        structureBefore.shift(structureBefore)
                    );
                    this.structure = structureBefore;
                    this.showOverlay = false;
                } else {
                    this.structure = [];
                    this.showOverlay = false;
                }
            }); */
    },
    async getPatientsVisitors({ commit }, payload) {
        commit("SET_FAMILY_RECORDS_IS_LOADING", true);

        const result = await axios.get(
            "/orientacionusuario/get/patient-visitors/" +
                payload.patient_doc +
                "/" +
                payload.patient_doctype +
                "/" +
                payload.admission_num
        );

        if (result.data.status === 200) {
            commit("SET_PATIENT_VISITORS_INFO", result.data.data);
            //console.log(result.data.data);
        }

        commit("SET_FAMILY_RECORDS_IS_LOADING", false);
    },
    async savePatientVisitors({ commit, state, dispatch }, payload) {
        commit("SET_FAMILY_RECORDS_IS_LOADING", true);

        const result = await axios.post(
            "/orientacionusuario/post/patient-visitor",
            payload
        );

        if (result.data.status === 200) {
            dispatch("getPatientsVisitors");
        }

        commit("SET_FAMILY_RECORDS_IS_LOADING", false);
    }
};
const mutations = {
    SET_CENSO_IS_LOADING(state, isLoadingCenso) {
        state.isLoadingCenso = isLoadingCenso;
    },
    SET_FAMILY_RECORDS_IS_LOADING(state, isLoadingFamilyRecords) {
        state.isLoadingFamilyRecords = isLoadingFamilyRecords;
    },
    SET_CENSO_INFO(state, censoInfo) {
        state.censoInfo = censoInfo;
    },
    SET_PATIENT_VISITORS_INFO(state, familyIcomeRecords) {
        state.familyIcomeRecords = familyIcomeRecords;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
