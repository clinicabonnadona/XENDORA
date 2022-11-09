<template>
    <div>
        <b-container fluid>
            <b-row>
                <b-col cols="12">
                    <b-card>
                        <b-row class="mt-4">
                            <b-col md="6" offset-md="3">
                                <b-form enctype="multipart/form-data">
                                    <b-row>
                                        <b-col md="10" offset-md="1">
                                            <b-row no-gutters>
                                                <b-col class="p-2">
                                                    <b-form-group
                                                        id="fieldset-1"
                                                        label="Fecha Inicial:"
                                                        label-for="input-1"
                                                    >
                                                        <b-form-datepicker
                                                            id="input-1"
                                                            placeholder="Seleccionar Fecha Inicial"
                                                            :date-format-options="{
                                                                year: 'numeric',
                                                                month:
                                                                    'numeric',
                                                                day: 'numeric'
                                                            }"
                                                            :max="maxDate"
                                                            today-button
                                                            class="login-date-input input-with-centered-placeholder"
                                                            reset-button
                                                            label-reset-button="Limpiar"
                                                            v-model="
                                                                payload.initDate
                                                            "
                                                        >
                                                        </b-form-datepicker>
                                                    </b-form-group>
                                                </b-col>

                                                <b-col class="p-2">
                                                    <b-form-group
                                                        id="fieldset-2"
                                                        label="Fecha Final:"
                                                        label-for="input-2"
                                                    >
                                                        <b-form-datepicker
                                                            id="input-2"
                                                            placeholder="Seleccionar Fecha Final"
                                                            :date-format-options="{
                                                                year: 'numeric',
                                                                month:
                                                                    'numeric',
                                                                day: 'numeric'
                                                            }"
                                                            :max="maxDate"
                                                            today-button
                                                            class="login-date-input input-with-centered-placeholder"
                                                            reset-button
                                                            label-reset-button="Limpiar"
                                                            v-model="
                                                                payload.endDate
                                                            "
                                                        >
                                                        </b-form-datepicker>
                                                    </b-form-group>
                                                </b-col>
                                            </b-row>
                                        </b-col>
                                    </b-row>

                                    <b-row>
                                        <b-col md="10" offset-md="1">
                                            <b-form-group id="fieldset-3">
                                                <b-button
                                                    block
                                                    variant="primary"
                                                    class="mt-4"
                                                    pill
                                                    type="button"
                                                    @click="getEvolutionsFact"
                                                >
                                                    <i class="fa fa-search"></i>
                                                    Buscar
                                                </b-button>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                </b-form>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col>
                                <b-overlay
                                    :show="
                                        this.$store.state.sde
                                            .isLoadingDevolutionsOrders
                                    "
                                    spinner-variant="primary"
                                    spinner-type="grow"
                                    spinner-small
                                    rounded="sm"
                                >
                                    <b-table
                                        :items="getItems"
                                        :fields="fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :filter="filter"
                                        :filter-included-fields="filterOn"
                                        show-empty
                                        responsive
                                        bordered
                                        small
                                        @filtered="onFiltered"
                                        head-variant="dark"
                                    >
                                    </b-table>
                                </b-overlay>
                            </b-col>
                        </b-row>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import moment from "moment";
import NoConnectionAlert from "../../alerts/NoConnectionAlert.vue";

export default {
    name: "SeguimientoEvolucionesFact",
    components: {
        NoConnectionAlert
    },
    data() {
        const todayDate = moment().format("YYYY-MM-DD");
        return {
            payload: {
                initDate: this.$store.state.sde.initDate,
                endDate: this.$store.state.sde.endDate
            },
            onLine: navigator.onLine,
            showBackOnline: false,
            filter: null,
            filterOn: [],
            selectMode: "multi",
            selected: [],
            currentPage: 1,
            perPage: 10,
            maxDate: todayDate,
            json_meta: [
                [
                    {
                        key: "charset",
                        value: "utf-8"
                    }
                ]
            ],
            fields: [
                {
                    key: "doctorCode",
                    label: "Código Médico",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "doctorName",
                    label: "Médico",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "doctorSpeciality",
                    label: "Especialidad",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "patientDocument",
                    label: "Doc. Paciente",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "patientDocumentType",
                    label: "Tipo Doc.",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "patientName",
                    label: "Paciente",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "patientEvoDate",
                    label: "Fecha Evo",
                    sortable: true,
                    class: "text-center"
                }
            ]
        };
    },
    computed: {
        getItems() {
            return this.$store.state.sde.devolutionsOrders;
        }
    },
    watch: {
        onLine(v) {
            if (v) {
                this.showBackOnline = true;
                setTimeout(() => {
                    this.showBackOnline = false;
                }, 1000);
            }
        }
    },
    methods: {
        getEvolutionsFact() {
            this.$store.commit("SET_INIT_DATE", this.payload.initDate);
            this.$store.commit("SET_END_DATE", this.payload.endDate);
            this.$store.dispatch("getDevolutionsOrders", this.payload);
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        updateOnlineStatus(e) {
            const { type } = e;
            this.onLine = type === "online";
        }
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    created() {
        this.$store.commit("SET_INIT_DATE", moment().format("YYYY-MM-DD"));
        this.$store.commit("SET_END_DATE", moment().format("YYYY-MM-DD"));
        this.payload.initDate = this.$store.state.sde.initDate;
        this.payload.endDate = this.$store.state.sde.endDate;
        this.$store.dispatch("getDevolutionsOrders");
    },
    beforeDestroy() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    }
};
</script>

<style></style>
