<template>
    <div>
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <b-col cols="12" class="my-1">
                    <b-card border-variant="dark" align="center" header-border-variant="secondary" class="card-container">
                        <template #header>
                            <b-row>
                                <b-col xl="2" offset-xl="6">
                                    <b-form-group label="Fecha:"
                                                  label-size="sm"
                                                  label-cols-xl="3"
                                                  content-cols-xl="9"
                                                  label-align-sm="center"
                                                  class="mb-0"
                                    >
                                        <b-form-datepicker v-model="payload.initDate"
                                                           :max="maxDate"
                                                           :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                                           today-button
                                                           size="sm"
                                                           class="my-1"
                                        ></b-form-datepicker>
                                    </b-form-group>
                                </b-col>

                                <b-col xl="1">
                                    <b-button variant="primary"
                                              size="sm"
                                              class="my-1"
                                              title="Buscar"
                                              @click="searchActiveReturnByDate()"
                                    >
                                        <i class="fas fa-search"></i>
                                    </b-button>
                                </b-col>

                                <b-col xl="3" class="my-1">
                                    <b-form-group
                                        label="Búsqueda:"
                                        label-for="filter-input"
                                        label-cols-sm="12"
                                        label-cols-xl="3"
                                        label-align-sm="center"
                                        label-size="sm"
                                        class="mb-0"
                                    >
                                        <b-input-group size="sm">
                                            <b-form-input
                                                id="filter-input"
                                                v-model="filter"
                                                type="search"
                                                placeholder="Escribe para Buscar..."
                                            ></b-form-input>

                                            <b-input-group-append>
                                                <b-button :disabled="!filter" @click="filter = ''">
                                                    <i class="fas fa-brush"></i>
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
import NoConnectionAlert from "../../alerts/NoConnectionAlert"
import moment from 'moment'

export default {
    name: "DevolucionReporte",
    components: {
        NoConnectionAlert
    },
    data() {
        const todayDate = moment().format('YYYY-MM-DD')
        return {
            onLine: navigator.onLine,
            payload: {
                initDate: this.$store.state.rg.initDateRD,
            },
            showBackOnline: false,
            maxDate: todayDate,
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            filter: null,
            filterOn: [],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            isBusy: false,
            fields: [
                { key: 'reqNum', label: 'N. Solicitud'},
                { key: 'reqDate', label: 'Fecha Solicitud'},
                { key: 'bodega', label: 'Bodega'},
                { key: 'sumCod', label: 'Cod. Suministro'},
                { key: 'sumNomG', label: 'N. Suministro'},
                { key: 'quantity', label: 'Cantidad'},
                { key: 'docType', label: 'T. Documento', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'docNro', label: 'Documento'},
                { key: 'pacNam', label: 'N. Paciente'},
                { key: 'invoice', label: 'Folio'},
                { key: 'pav', label: 'Pavellón'},
                { key: 'reqStatus', label: 'Estado Solicitud'},
                { key: 'approved', label: 'Usario Autoriza/Niega'},
            ],
        }
    },
    computed: {
        getReturns() {
            return this.$store.state.rd.activeReturns
        },
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        },
        activeReturnsIsLoading() {
            return this.$store.state.rd.isLoadingReturns
        },
        activeReturnsCount() {
            return this.$store.getters.activeReturnsCount
        }
    },
    watch: {
        onLine(v) {
            if (v) {
                this.showBackOnline = true
                setTimeout(() => {
                    this.showBackOnline = false;
                }, 1000)
            }
        }
    },
    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        searchActiveReturnByDate() {
            this.$store.commit('SET_INIT_DATE', this.payload.initDate)
            this.$store.dispatch('getActiveReturns', this.payload)
        }
    },
    mounted() {
        window.addEventListener('online', this.updateOnlineStatus)
        window.addEventListener('offline', this.updateOnlineStatus)
    },
    created() {
        this.$store.commit('SET_INIT_DATE', moment().format('YYYY-MM-DD'))
        this.payload.initDate = this.$store.state.rd.initDateRD
        this.$store.dispatch('getActiveReturns', this.payload)
    },
    beforeDestroy() {
        window.removeEventListener('online', this.updateOnlineStatus)
        window.removeEventListener('offline', this.updateOnlineStatus)
    }
}
</script>

<style scoped>

</style>
