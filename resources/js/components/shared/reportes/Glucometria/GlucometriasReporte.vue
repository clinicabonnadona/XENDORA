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
                                              @click="refreshData"
                                              class="my-1"
                                              title="Buscar"
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
<!--                                <b-col xl="1" class="my-1">
                                    <b-button variant="info"
                                              @click="refreshData"
                                              size="sm"
                                              title="recargar"
                                    >
                                        <i class="fas fa-sync-alt"></i>
                                    </b-button>
                                </b-col>-->
                            </b-row>
                        </template>

                        <b-table
                            :items="activeGlucometries"
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
                            hover
                            bordered
                            :busy="activeGlucometriesIsLoading"
                            @filtered="onFiltered"
                            responsive
                        >

                            <template #table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Loading...</strong>
                                </div>
                            </template>

                            <template #cell(name)="row">
                                {{ row.value.first }} {{ row.value.last }}
                            </template>

                            <template #cell(actions)="row">
                                <b-button size="sm" @click="info(row.item, row.index, $event.target)" class="mr-1">
                                    Info modal
                                </b-button>
                                <b-button size="sm" @click="row.toggleDetails">
                                    {{ row.detailsShowing ? 'Hide' : 'Show' }} Details
                                </b-button>
                            </template>

                            <template #row-details="row">
                                <b-card>
                                    <ul>
                                        <li v-for="(value, key) in row.item" :key="key">{{ key }}: {{ value }}</li>
                                    </ul>
                                </b-card>
                            </template>
                        </b-table>

                        <template #footer>
                            <b-row>
                                <b-col xl="6" offset-xl="3" class="my-1">
                                    <b-pagination
                                        v-model="currentPage"
                                        :total-rows="activeGlucometriesCount"
                                        :per-page="perPage"
                                        align="fill"
                                        size="sm"
                                        class="my-0"
                                    ></b-pagination>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-col cols="12" class="text-center">
                                    total de registros: {{ activeGlucometriesCount }}
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
    name: "GlucometriasReporte",
    components: {
        NoConnectionAlert
    },
    data() {
        const todayDate = moment().format('YYYY-MM-DD')
        return {
            onLine: navigator.onLine,
            showBackOnline: false,
            payload: {
                initDate: this.$store.state.rg.initDateRG,
            },
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
                { key: 'docPac', label: 'Cédula', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'docTypePac', label: 'Doc', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'admCon', label: 'Ingreso', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'pacName', label: 'Nombre Paciente', sortable: true, sortDirection: 'desc', class: 'text-left'},
                { key: 'atteType', label: 'T. Atención', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'pav', label: 'Pabellón', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'hab', label: 'Habitación', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'eps', label: 'EPS', sortable: true, sortDirection: 'desc', class: 'text-center'},
                //{ key: 'codPro', label: 'Procedimiento', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'proName', label: 'Nombre Proc', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'quantity', label: 'Cantidad', sortable: true, sortDirection: 'desc', class: 'text-center'},
                { key: 'orderDate', label: 'Fecha Orden', sortable: true, sortDirection: 'desc', class: 'text-center'},
            ]
        }
    },
    computed: {
        activeGlucometries() {
            return this.$store.state.rg.activeGlucometries
        },
        activeGlucometriesIsLoading() {
            return this.$store.state.rg.isLoadingGlucometries
        },
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        },
        activeGlucometriesCount() {
            return this.$store.getters.activeGlucometriesCount
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
        refreshData() {
            this.$store.commit('SET_INIT_DATE', this.payload.initDate)
            this.$store.dispatch('getActiveGlucometries', this.payload)
        }
    },
    mounted() {
        window.addEventListener('online', this.updateOnlineStatus)
        window.addEventListener('offline', this.updateOnlineStatus)
    },
    created() {
        this.$store.commit('SET_INIT_DATE', moment().format('YYYY-MM-DD'))
        this.$store.commit('SET_END_DATE', moment().format('YYYY-MM-DD'))
        this.payload.initDate = this.$store.state.rg.initDateRG
        this.$store.dispatch('getActiveGlucometries', this.payload)
    },
     beforeDestroy() {
        window.removeEventListener('online', this.updateOnlineStatus)
        window.removeEventListener('offline', this.updateOnlineStatus)
    }
}
</script>

<style scoped>
/*.card-container {
    max-height: 80vh !important;
}*/
table#table-transition-example .flip-list-move {
    transition: transform 1s;
}
</style>
