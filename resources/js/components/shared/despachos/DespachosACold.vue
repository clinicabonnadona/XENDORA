<template>
    <b-container fluid>
        <b-overlay :show="showOverlay" rounded="sm">
            <b-row class="mb-3">
                <b-col class="my-1">
                    <b-form-group class="mb-0">
                        <b-input-group>
                            <b-form-input
                                v-model="filter"
                                type="search"
                                id="filterInput"
                                placeholder="Escribe Aquí para Buscar"
                                class="login-search-input"
                            ></b-form-input>
                            <b-input-group-append>
                                <b-button
                                    :disabled="!filter"
                                    @click="filter = ''"
                                    class="login-search-input-append"
                                >
                                    <i class="fas fa-paint-brush"></i>
                                </b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-col>
                <b-col class="my-1">
                    <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        align="fill"
                        size="sm"
                        class="my-0"
                    ></b-pagination>
                </b-col>
            </b-row>

            <div></div>

            <b-row>
                <b-col cols="12">
                    <b-table
                        id="table-transition-example"
                        :items="despachos"
                        :fields="fields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        striped
                        bordered
                        small
                        show-empty
                        primary-key="a"
                        :tbody-transition-props="transProps"
                        empty-text="No hay registro de Despachos para este Medicamento"
                    >
                        <template #custom-foot>
                            <b-td colspan="7" variant="dark" class="text-right">
                                Total Pacientes:
                                <b>{{ totalRows }}</b>
                            </b-td>
                        </template>
                    </b-table>
                </b-col>
            </b-row>
        </b-overlay>
    </b-container>
</template>

<script>
export default {
    name: "DespachosAC",
    props: {
        sumcod: String
    },
    data() {
        return {
            showOverlay: true,
            despachos: [],
            transProps: {
                // Transition name
                name: "flip-list"
            },
            fields: [
                {
                    key: "patient",
                    label: "PACIENTE",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "lastDispatch",
                    label: "ULTIMO DESPACHO",
                    sortable: true,
                    class: "text-center"
                },
                // {key: 'dispatchProduct', label: 'PRODUCTO', sortable: true},
                {
                    key: "dispatchQuantity",
                    label: "CANTIDAD DESPACHADA",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "dispatchDevolution",
                    label: "CANTIDAD DEVUELTA",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "dispatchTotalQuantity",
                    label: "CANTIDAD TOTAL",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "dispatchPbsNoPbs",
                    label: "PBS / NOPBS",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "dispatchContract",
                    label: "CONTRATO",
                    sortable: true,
                    class: "text-center"
                }
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            filter: null,
            filterOn: []
        };
    },
    mounted() {
        this.getDespachos();
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key };
                });
        }
    },
    methods: {
        getDespachos() {
            if (this.sumcod != null) {
                let url =
                    "admin/suministros/rotacion/despachos/super-alto-costo/" +
                    this.sumcod;

                this.showOverlay = true;
                axios
                    .get(url)
                    .then(response => {
                        this.despachos = response.data.data;
                        this.totalRows = response.data.data.length;
                        this.showOverlay = false;
                    })
                    .catch(error => {
                        this.showOverlay = false;
                        console.log(error);
                    });
            }
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        }
    }
};
</script>

<style scoped></style>
