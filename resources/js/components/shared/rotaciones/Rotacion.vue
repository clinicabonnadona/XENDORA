<template>
    <b-container fluid>
        <b-row>
            <b-col xl="12" sm="12">
                <b-card no-footer>
                    <b-card-title>
                        <b-row>
                            <b-col>
                                <h3
                                    class="card-table-title text-center mt-2 mb-2"
                                >
                                    Registro de Rotación de Productos
                                </h3>
                            </b-col>
                        </b-row>
                        <hr />
                        <b-row>
                            <b-col xl="7" offset-xl="2" sm="12" class="mt-2">
                                <b-form-group
                                    label-for="filterInput"
                                    class="mb-3 text-center"
                                >
                                    <b-input-group>
                                        <b-form-input
                                            v-model="filter"
                                            type="search"
                                            id="filterInput"
                                            placeholder="Escribe Aquí para Buscar"
                                            class="rotation-search-input"
                                        ></b-form-input>
                                        <b-input-group-append>
                                            <b-button
                                                :disabled="!filter"
                                                @click="filter = ''"
                                                class="login-search-input-append"
                                            >
                                                <i
                                                    class="fas fa-paint-brush"
                                                ></i>
                                            </b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>
                            </b-col>
                            <b-col xl="1" sm="12" class="mt-3 text-center">
                                <b-button
                                    variant="warning"
                                    pill
                                    bl
                                    class="border-dark pl-5 pr-5"
                                    @click="fetchData"
                                    :disabled="showOverlay"
                                >
                                    <i class="fas fa-redo"></i>
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-card-title>

                    <!-- Begin of table component -->
                    <b-overlay
                        spinner-variant="primary"
                        spinner-type="grow"
                        spinner-small
                        rounded="sm"
                        :show="this.$store.state.r.isLoadingSuministros"
                    >
                        <b-row class="p-0">
                            <b-col
                                sm="12"
                                lg="12"
                                md="12"
                                class="overflow-auto"
                            >
                                <!-- Medicamentos Table -->
                                <b-table
                                    show-empty
                                    ref="selectableTable"
                                    selectable
                                    bordered
                                    responsive
                                    :select-mode="selectMode"
                                    :items="getSumistros"
                                    :fields="fields"
                                    :current-page="currentPage"
                                    :per-page="perPage"
                                    :filter="filter"
                                    :filter-included-fields="filterOn"
                                    :sort-by.sync="sortBy"
                                    :sort-desc.sync="sortDesc"
                                    :sort-direction="sortDirection"
                                    :head-variant="headVariant"
                                    @filtered="onFiltered"
                                    @row-selected="onRowSelected"
                                >
                                    <template #cell(actions)="row">
                                        <b-button
                                            size="sm"
                                            @click="
                                                info(
                                                    row.item,
                                                    row.index,
                                                    $event.target
                                                )
                                            "
                                            class="mr-1"
                                        >
                                            Info modal
                                        </b-button>
                                        <b-button
                                            size="sm"
                                            @click="row.toggleDetails"
                                        >
                                            {{
                                                row.detailsShowing
                                                    ? "Hide"
                                                    : "Show"
                                            }}
                                            Details
                                        </b-button>
                                    </template>

                                    <template #row-details="row">
                                        <b-card>
                                            <ul>
                                                <li
                                                    v-for="(value,
                                                    key) in row.item"
                                                    :key="key"
                                                >
                                                    {{ key }}: {{ value }}
                                                </li>
                                            </ul>
                                        </b-card>
                                    </template>
                                </b-table>

                                <!-- Pagination Medicamentos Table -->
                                <b-col cols="8" offset="2" class="mt-5">
                                    <b-pagination
                                        v-model="currentPage"
                                        :total-rows="suministrosCount"
                                        :per-page="perPage"
                                        align="fill"
                                        size="sm"
                                        class="my-0"
                                    ></b-pagination>
                                    <!-- End Pagination Medicamentos Table -->
                                </b-col>

                                <!-- Modal Detalle Rotación -->
                                <b-modal
                                    ref="bv-modal-detail-rot"
                                    title="Detalle Rotación"
                                    no-close-on-backdrop
                                    no-close-on-esc
                                    centered
                                    size="xl"
                                >
                                    <template #modal-title>
                                        Rotación Año y Facturación Farmacia -
                                        {{ selected.sumDescription }}
                                    </template>

                                    <b-row>
                                        <b-col>
                                            <b-card no-body class="border-0">
                                                <b-tabs
                                                    pills
                                                    card
                                                    vertical
                                                    end
                                                    lazy
                                                >
                                                    <b-tab active>
                                                        <template #title>
                                                            <i
                                                                class="fas fa-sync"
                                                            ></i>
                                                            Rotaciones
                                                        </template>
                                                        <b-row>
                                                            <b-col>
                                                                <RotacionDetail
                                                                    :sumcod="
                                                                        selected.sumCod
                                                                    "
                                                                    :sumnomc="
                                                                        selected.sumDescription
                                                                    "
                                                                ></RotacionDetail>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col>
                                                                <EntradasDetail
                                                                    :sumcod="
                                                                        selected.sumCod
                                                                    "
                                                                    :sumnomc="
                                                                        selected.sumDescription
                                                                    "
                                                                ></EntradasDetail>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col>
                                                                <Despachos
                                                                    :sumcod="
                                                                        selected.sumCod
                                                                    "
                                                                    :sumnomc="
                                                                        selected.sumDescription
                                                                    "
                                                                ></Despachos>
                                                            </b-col>
                                                        </b-row>
                                                    </b-tab>

                                                    <b-tab>
                                                        <template #title>
                                                            <i
                                                                class="fas fa-sign-out-alt"
                                                            ></i>
                                                            Despachos AC
                                                        </template>
                                                        <b-card-text>
                                                            <DespachosAC
                                                                :sumcod="
                                                                    selected.sumCod
                                                                "
                                                            >
                                                            </DespachosAC>
                                                        </b-card-text>
                                                    </b-tab>
                                                </b-tabs>
                                            </b-card>
                                        </b-col>
                                    </b-row>

                                    <template #modal-footer>
                                        <b-button
                                            variant="danger"
                                            @click="hideModal"
                                        >
                                            Cerrar
                                        </b-button>
                                    </template>
                                </b-modal>
                            </b-col>
                        </b-row>
                    </b-overlay>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
import RotacionDetail from "./RotacionDetail";
import EntradasDetail from "../entradas/EntradasDetail";
import Despachos from "../despachos/Despachos";
import RotacionVariables from "./RotacionVariables";
import DespachosAC from "../despachos/DespachosAC.vue";

export default {
    name: "Rotacion-Component",
    components: {
        Despachos,
        EntradasDetail,
        RotacionDetail,
        RotacionVariables,
        DespachosAC
    },
    data() {
        return {
            showOverlay: false,
            monthsArray: [],
            suministros: [],
            rotacion: [],
            fields: [
                {
                    key: "sumCod",
                    label: "CÓDIGO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumDescription",
                    label: "DESCRIPCIÓN",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumCommercialDescription",
                    label: "DESCRIPCIÓN COMERCIAL",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumBalance",
                    label: "SALDO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                }
            ],
            currentPage: 1,
            perPage: 11,
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            sortBy: "",
            sortDesc: false,
            sortDirection: "asc",
            filter: "MO000265",
            customFilters: [
                {
                    name: "alphabet",
                    callback: (row, query) => {
                        return row.name[1] == query;
                    }
                }
            ],
            filterOn: [],
            headVariant: "dark",
            selected: [],
            selectMode: "single",
            onLine: navigator.onLine,
            showBackOnline: false
        };
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key };
                });
        },
        getSumistros() {
            return this.$store.state.r.suministros;
        },
        suministrosCount() {
            return this.$store.getters.suministros.length;
        }
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            this.$store.dispatch("getSumistros");
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        onRowSelected(items) {
            if (items.length != 0) {
                this.selected = items[0];
                this.$refs["bv-modal-detail-rot"].show();
            }
        },
        hideModal() {
            this.$refs.selectableTable.clearSelected();
            this.$refs["bv-modal-detail-rot"].hide();
        }
    }
};
</script>

<style>
.modal-body {
    padding: 0 !important;
}
.modal-dialog {
    max-width: 95% !important;
    /* min-height: 100% !important; */
}

.col-auto {
    border-left: 1px dashed grey;
}
.card .card-table-title {
    color: #000000;
    text-transform: uppercase;
    font-size: 0.875rem;
    font-weight: 500;
}
.rotation-search-input {
    border-bottom-left-radius: 20.5px;
    border-top-left-radius: 20.5px;
    padding: 10px;
    height: 3rem;
}
</style>
