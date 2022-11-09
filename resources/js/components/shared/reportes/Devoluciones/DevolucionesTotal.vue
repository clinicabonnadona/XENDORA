<template>
    <div>
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <b-col cols="12" class="my-1">
                    <b-card
                        border-variant="dark"
                        align="center"
                        header-border-variant="secondary"
                        class="card-container"
                    >
                        <template #header>
                            <b-row>
                                <b-col xl="12" class="my-1">
                                    <b-form-group
                                        label="Número de Documento:"
                                        label-size="sm"
                                        label-cols-xl="5"
                                        label-cols-xs="12"
                                        content-cols-xl="7"
                                        content-cols-xs="12"
                                        label-align-xs="center"
                                        label-align-xl="center"
                                        class="mb-0 float-xs-right float-xl-right"
                                    >
                                        <b-input-group>
                                            <b-form-input
                                                size="sm"
                                                v-model="payload.docPacNum"
                                                placeholder="Escribe Aquí"
                                            >
                                            </b-form-input>

                                            <b-input-group-append>
                                                <b-button
                                                    variant="primary"
                                                    size="sm"
                                                    @click="getTotalReturns()"
                                                >
                                                    <i
                                                        class="fas fa-search"
                                                    ></i>
                                                </b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                        </template>

                        <b-table
                            :items="getReturns"
                            :fields="fields"
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            :filter-included-fields="filterOn"
                            head-variant="dark"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc"
                            :sort-direction="sortDirection"
                            show-empty
                            small
                            :busy="activeReturnsIsLoading"
                            @filtered="onFiltered"
                            responsive
                            empty-text="No hay registros de Devoluciones para este paciente"
                        >
                            <template #table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Loading...</strong>
                                </div>
                            </template>

                            <template #cell(reqStatus)="row">
                                <b-badge pill variant="success">
                                    {{ row.value }}
                                </b-badge>
                            </template>
                        </b-table>

                        <template #footer>
                            <b-row>
                                <b-col xl="6" offset-xl="3" class="my-1">
                                    <b-pagination
                                        v-model="currentPage"
                                        :total-rows="activeReturnsCount"
                                        :per-page="perPage"
                                        align="fill"
                                        size="sm"
                                        class="my-0"
                                    ></b-pagination>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-col cols="12" class="text-center">
                                    total de registros: {{ activeReturnsCount }}
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import NoConnectionAlert from "../../alerts/NoConnectionAlert";
import moment from "moment";

export default {
    name: "DevolucionesTotal",
    components: {
        NoConnectionAlert
    },
    data() {
        const todayDate = moment().format("YYYY-MM-DD");
        return {
            onLine: navigator.onLine,
            payload: {
                docPacNum: this.$store.state.rdt.docPacNum
            },
            showBackOnline: false,
            maxDate: todayDate,
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            filter: null,
            filterOn: [],
            sortBy: "",
            sortDesc: false,
            sortDirection: "asc",
            isBusy: false,
            fields: []
        };
    },
    computed: {
        getReturns() {
            return this.$store.state.rdt.activeTotalReturns;
        },
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key };
                });
        },
        activeReturnsIsLoading() {
            return this.$store.state.rdt.isLoadingTotalReturns;
        },
        activeReturnsCount() {
            return this.$store.getters.activeTotalReturnsCount;
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
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        getTotalReturns() {
            if (this.payload.docPacNum) {
                this.$store.dispatch("getActiveTotalReturns", this.payload);
            } else {
                window.alert(
                    "El campo Número de documento no puede estar vacio"
                );
            }
        }
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    beforeDestroy() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    }
};
</script>

<style scoped></style>
