<template>
    <div>
        <b-overlay
            spinner-variant="primary"
            spinner-type="grow"
            spinner-small
            rounded="sm"
            :show="this.$store.state.cs.isLoadingTransaccionesCompras"
        >
            <b-container>
                <b-row>
                    <b-col xl="3" sm="12" class="mb-2">
                        <b-card
                            class="card-rounded"
                            bg-variant="danger"
                            text-variant="white"
                        >
                            <b-card-body>
                                <div class="row">
                                    <div class="col">
                                        <h5
                                            class="card-title text-uppercase mb-0"
                                        >
                                            PAPELERIA
                                        </h5>
                                        <span class="h2 font-weight-bold mb-0"
                                            >{{ countPapeleria.cantidad }}
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <div
                                            class="icon icon-shape rounded-circle shadow bg-gradient-red"
                                        >
                                            <i
                                                class="fa fa-paperclip fa-3x"
                                                aria-hidden="true"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-xl">
                                    <span class="mr-2">{{
                                        countPapeleria.valor | currency("$", 0)
                                    }}</span>
                                </p>
                            </b-card-body>
                        </b-card>
                    </b-col>

                    <b-col xl="3" sm="12" class="mb-2">
                        <b-card
                            class="card-rounded"
                            bg-variant="success"
                            text-variant="white"
                        >
                            <b-card-body>
                                <div class="row">
                                    <div class="col">
                                        <h5
                                            class="card-title text-uppercase mb-0"
                                        >
                                            FERRETERIA
                                        </h5>
                                        <span
                                            class="h2 font-weight-bold mb-0"
                                            >{{
                                                countFerreteria.cantidad
                                            }}</span
                                        >
                                    </div>
                                    <div class="col-auto">
                                        <div
                                            class="icon icon-shape rounded-circle shadow bg-gradient-red"
                                        >
                                            <i
                                                class="fa fa-cogs fa-3x"
                                                aria-hidden="true"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="mr-2">{{
                                        countFerreteria.valor | currency("$", 0)
                                    }}</span>
                                </p>
                            </b-card-body>
                        </b-card>
                    </b-col>

                    <b-col xl="3" sm="12" class="mb-2">
                        <b-card
                            class="card-rounded"
                            bg-variant="dark"
                            text-variant="white"
                        >
                            <b-card-body>
                                <div class="row">
                                    <div class="col">
                                        <h5
                                            class="card-title text-uppercase mb-0"
                                        >
                                            COLCHONETAS
                                        </h5>
                                        <span
                                            class="h2 font-weight-bold mb-0"
                                            >{{
                                                countColchonetas.cantidad
                                            }}</span
                                        >
                                    </div>
                                    <div class="col-auto">
                                        <div
                                            class="icon icon-shape rounded-circle shadow bg-gradient-red"
                                        >
                                            <i
                                                class="fa fa-bed fa-3x"
                                                aria-hidden="true"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="mr-2">{{
                                        countColchonetas.valor
                                            | currency("$", 0)
                                    }}</span>
                                </p>
                            </b-card-body>
                        </b-card>
                    </b-col>

                    <b-col xl="3" sm="12" class="mb-2">
                        <b-card
                            class="card-rounded"
                            bg-variant="primary"
                            text-variant="white"
                        >
                            <b-card-body>
                                <div class="row">
                                    <div class="col">
                                        <h5
                                            class="card-title text-uppercase mb-0"
                                        >
                                            CONSTRUCCIÓN
                                        </h5>
                                        <span
                                            class="h2 font-weight-bold mb-0"
                                            >{{
                                                countConstruccion.cantidad
                                            }}</span
                                        >
                                    </div>
                                    <div class="col-auto">
                                        <div
                                            class="icon icon-shape rounded-circle shadow bg-gradient-red"
                                        >
                                            <i
                                                class="fa fa-building fa-3x"
                                                aria-hidden="true"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="mr-2">{{
                                        countConstruccion.valor
                                            | currency("$", 0)
                                    }}</span>
                                </p>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </b-container>
        </b-overlay>

        <b-container fluid>
            <b-row class="mt-4">
                <b-col>
                    <b-card no-footer>
                        <b-card-title>
                            <!-- <b-row>
                                <b-col>
                                    <h3
                                        class="card-table-title text-center mt-2 mb-2"
                                    >
                                        Registro de Rotación de Productos
                                    </h3>
                                </b-col>
                            </b-row>
                            <hr /> -->
                            <b-row>
                                <b-col
                                    xl="7"
                                    offset-xl="2"
                                    sm="12"
                                    class="mt-2"
                                >
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
                                            >
                                            </b-form-input>
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
                                        @click="fetchData()"
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
                            :show="
                                this.$store.state.cs
                                    .isLoadingTransaccionesCompras
                            "
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
                                        <template #cell(sumTotalValue)="row">
                                            {{
                                                row.item.sumTotalValue
                                                    | currency("$", 0)
                                            }}
                                        </template>

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
                                        size="xl"
                                    >
                                        <template #modal-title>
                                            Rotación Año y Facturación Farmacia
                                            -
                                            {{ selected.SUMNOMC }}
                                        </template>

                                        <b-row>
                                            <b-col>
                                                <b-card
                                                    no-body
                                                    class="border-0"
                                                >
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
                                                                            selected.MSRESO
                                                                        "
                                                                        :sumnomc="
                                                                            selected.SUMNOMC
                                                                        "
                                                                    ></RotacionDetail>
                                                                </b-col>
                                                            </b-row>
                                                            <b-row>
                                                                <b-col>
                                                                    <EntradasDetail
                                                                        :sumcod="
                                                                            selected.MSRESO
                                                                        "
                                                                        :sumnomc="
                                                                            selected.SUMNOMC
                                                                        "
                                                                    ></EntradasDetail>
                                                                </b-col>
                                                            </b-row>
                                                            <b-row>
                                                                <b-col>
                                                                    <Despachos
                                                                        :sumcod="
                                                                            selected.MSRESO
                                                                        "
                                                                        :sumnomc="
                                                                            selected.SUMNOMC
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
                                                                        selected.MSRESO
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
    </div>
</template>

<script>
import moment from "moment";
import NoConnectionAlert from "../alerts/NoConnectionAlert.vue";

export default {
    name: "ComprasSeguimientos",
    components: {
        NoConnectionAlert
    },
    data() {
        return {
            showOverlay: false,
            payload: {
                initDate: this.$store.state.cs.initDate,
                endDate: this.$store.state.cs.endDate
            },
            fields: [
                {
                    key: "sumCode",
                    label: "CODIGO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumDescription",
                    label: "DESCRIPCIÓN PRODUCTO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumGroupCode",
                    label: "CODIGO GRUPO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumGroupDescription",
                    label: "DESCRIPCIÓN GRUPO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumQuantity",
                    label: "SALDO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sumTotalValue",
                    label: "VALOR",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                }
            ],
            currentPage: 1,
            perPage: 15,
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            sortBy: "",
            sortDesc: false,
            sortDirection: "asc",
            filter: "",
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
            return this.$store.state.cs.transaccionesCompras;
        },
        suministrosCount() {
            return this.$store.state.cs.transaccionesCompras.length;
        },
        countPapeleria() {
            const result = this.$store.state.cs.transaccionesCompras.filter(
                item =>
                    item.sumGroupDescription === "UTILES Y PAPELERIA -GASTOS"
            );
            var quantities = result.map(element => element.sumQuantity);
            var priceAll = result.map(element => element.sumTotalValue);

            var quantity = quantities.reduce((a, b) => a + b, 0);
            var prices = priceAll.reduce((a, b) => a + b, 0);

            return { cantidad: quantity, valor: prices };
        },
        countColchonetas() {
            const result = this.$store.state.cs.transaccionesCompras.filter(
                item => item.sumGroupDescription === "LENCERIA"
            );
            var quantities = result.map(element => element.sumQuantity);
            var priceAll = result.map(element => element.sumTotalValue);

            var quantity = quantities.reduce((a, b) => a + b, 0);
            var prices = priceAll.reduce((a, b) => a + b, 0);

            return { cantidad: quantity, valor: prices };
        },
        countConstruccion() {
            const result = this.$store.state.cs.transaccionesCompras.filter(
                item =>
                    item.sumGroupDescription ===
                    "CONSTRUCCION Y EDIFICACIONES-GASTO"
            );
            var quantities = result.map(element => element.sumQuantity);
            var priceAll = result.map(element => element.sumTotalValue);

            var quantity = quantities.reduce((a, b) => a + b, 0);
            var prices = priceAll.reduce((a, b) => a + b, 0);

            return { cantidad: quantity, valor: prices };
        },
        countFerreteria() {
            const result = this.$store.state.cs.transaccionesCompras.filter(
                item =>
                    item.sumGroupDescription ===
                    "MANTENIMIENTO DE MAQUINARIA Y EQUIPO-COSTO"
            );
            var quantities = result.map(element => element.sumQuantity);
            var priceAll = result.map(element => element.sumTotalValue);

            var quantity = quantities.reduce((a, b) => a + b, 0);
            var prices = priceAll.reduce((a, b) => a + b, 0);

            return { cantidad: quantity, valor: prices };
        }
    },
    methods: {
        fetchData() {
            this.$store.commit("SET_INIT_DATE", moment().format("YYYY-MM-DD"));
            this.$store.commit("SET_END_DATE", moment().format("YYYY-MM-DD"));
            this.payload.initDate = this.$store.state.cs.initDate;
            this.payload.endDate = this.$store.state.cs.endDate;
            this.$store.dispatch("getTransaccionesCompras");
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
        this.payload.initDate = this.$store.state.cs.initDate;
        this.payload.endDate = this.$store.state.cs.endDate;
        this.$store.dispatch("getTransaccionesCompras");
    },
    beforeDestroy() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    }
};
</script>

<style scoped>
.card-rounded {
    border-radius: 0.5rem;
}
</style>
