<template>
    <b-container fluid no-gutters>
        <b-overlay :show="showOverlay" rounded="sm">
            <b-row>
                <b-col>
                    <b-pagination
                        :total-rows="getItemsLength"
                        v-model="currentPage"
                        :per-page="perPage"
                        aria-controls="my-table"
                    ></b-pagination>
                </b-col>
                <b-col>
                    <b-col>
                        <b-form-group
                            label-for="filter-input"
                            label-align-sm="right"
                            class="mb-0"
                        >
                            <b-input-group>
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="Escribe acá para buscar"
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
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-table-simple
                        id="my-table"
                        hover
                        responsive
                        bordered
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :filter-function="customTableFilter"
                        show-empty
                        small
                        :tbody-transition-props="transProps"
                        empty-text="No hay registro de Despachos para este Medicamento"
                    >
                        <b-thead>
                            <b-tr>
                                <b-th
                                    colspan="4"
                                    class="text-center"
                                    variant="success"
                                >
                                    INFORMACIÓN BÁSICA DEL PACIENTE
                                </b-th>
                                <b-th
                                    colspan="13"
                                    class="text-center"
                                    variant="primary"
                                    >MESES {{ new Date().getFullYear() }}
                                </b-th>
                            </b-tr>
                            <b-tr>
                                <b-th
                                    class="text-center align-middle"
                                    variant="success"
                                    >PACIENTE</b-th
                                >
                                <b-th
                                    class="text-center align-middle"
                                    variant="success"
                                    >DOCUMENTO</b-th
                                >
                                <b-th
                                    class="text-center align-middle"
                                    variant="success"
                                    >PRIMER DESPACHO</b-th
                                >
                                <b-th
                                    class="text-center align-middle"
                                    variant="success"
                                    >ÚLTIMO CONTRATO</b-th
                                >
                                <b-th
                                    variant="danger"
                                    v-for="(m, index) in monthsArray"
                                    :key="index"
                                    class="text-center"
                                    >{{ m.toUpperCase() }}
                                </b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr
                                v-for="(re, index) in itemsForPagination"
                                :key="index"
                            >
                                <b-th
                                    :class="
                                        `text-left ${
                                            re.patientAliveStatus ===
                                            'FALLECIDO'
                                                ? 'deceased-patient'
                                                : ''
                                        }`
                                    "
                                >
                                    {{ re.patientName }}
                                </b-th>
                                <b-th class="text-center align-middle">
                                    {{ re.patientDocument }}
                                </b-th>
                                <b-th class="text-center align-middle">
                                    {{ re.patientFirstDispatch }}
                                </b-th>
                                <b-th class="text-center align-middle">
                                    {{ re.patientContract }}
                                </b-th>
                                <b-td
                                    class="text-center align-middle"
                                    v-for="(dispatch, index_two) in re.dispatch"
                                    :key="index_two"
                                    :variant="dispatch.__variant"
                                >
                                    {{
                                        dispatch.quantity === 0
                                            ? ""
                                            : dispatch.quantity
                                    }}
                                </b-td>
                            </b-tr>
                        </b-tbody>
                        <b-tfoot foot-variant="dark" class="table-footer">
                            <b-tr>
                                <b-td colspan="17" class="text-right">
                                    Cantidad de pacientes:
                                    <b>{{ getItemsLength }}</b>
                                </b-td>
                            </b-tr>
                        </b-tfoot>
                    </b-table-simple>
                </b-col>
            </b-row>

            <hr />

            <b-row>
                <b-col>
                    <b-table-simple small caption-top hover responsive bordered>
                        <caption style="font-weight: bold; color: black;">
                            CANTIDAD DE PACIENTES MENSUALES CON DESPACHOS:
                        </caption>
                        <colgroup>
                            <col />
                            <col />
                        </colgroup>
                        <colgroup>
                            <col />
                            <col />
                            <col />
                        </colgroup>
                        <colgroup>
                            <col />
                            <col />
                        </colgroup>
                        <b-thead head-variant="dark">
                            <b-tr>
                                <b-th
                                    variant="danger"
                                    v-for="(m, index) in despachosAcPerMonths"
                                    :key="index"
                                    class="text-center"
                                    >{{ getMonthsName(m.month) }} {{ m.year }}
                                </b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr class="text-center">
                                <b-th
                                    v-for="(ds, index) in despachosAcPerMonths"
                                    :key="index"
                                    >{{ ds.quantity }}
                                </b-th>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
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
            despachosAc: [],
            despachosAcPerMonths: [],
            monthsArray: [],
            transProps: {
                // Transition name
                name: "flip-list"
            },
            fields: [
                { key: "patientName", label: "PACIENTE" },
                {
                    key: "patientDocument",
                    label: "DOCUMENTO",
                    class: "text-center"
                },
                {
                    key: "patientFirstDispatch",
                    label: "PRIMER DESPACHO",
                    class: "text-center"
                },
                { key: "Dispatch", label: "DESPACHOS" }
                /* { key: "months" } */
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            filter: "",
            filterOn: [],
            emptyTabletext: "No hay registro de Despachos para este Medicamento"
        };
    },
    mounted() {
        //this.getDespachos();
        this.getDespachosTwo();
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
        customElementsWithFilter() {
            return this.filter
                ? this.despachosAc.filter(
                      item =>
                          item.patientName.includes(
                              this.filter
                          ) /* ||
                          item.patientDocument.includes(this.filter) */
                  )
                : this.despachosAc;
        },
        getItemsLength() {
            return this.despachosAc.length;
        },
        itemsForPagination() {
            return this.customElementsWithFilter.slice(
                (this.currentPage - 1) * this.perPage,
                this.currentPage * this.perPage
            );
        }
    },
    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        getDespachosTwo() {
            if (this.sumcod != null) {
                let url =
                    "admin/suministros/rotacion/despachos/super-alto-costo-two/" +
                    this.sumcod;

                this.showOverlay = true;
                axios
                    .get(url)
                    .then(response => {
                        //@ts-ignoreconsole.log(response.data.data);

                        if (response.status === 200) {
                            this.despachosAc = response.data.data;
                            this.despachosAcPerMonths =
                                response.data.dataPerMonth;
                            //console.log(response.data.data);
                            //this.totalRows = this.despachosAc.length;

                            // Aquí se crea un array con todos los meses
                            var d = new Date();
                            var arrayMonths = [];
                            var monthName = new Array(
                                "ENERO",
                                "FEBRERO",
                                "MARZO",
                                "ABRIL",
                                "MAYO",
                                "JUNIO",
                                "JULIO",
                                "AGOSTO",
                                "SEPTIEMBRE",
                                "OCTUBRE",
                                "NOVIEMBRE",
                                "DICIEMBRE"
                            );
                            d.setDate(1);
                            for (var i = 0; i <= 12; i++) {
                                arrayMonths.push(
                                    monthName[d.getMonth()] +
                                        " " +
                                        d.getFullYear()
                                );
                                //d.setMonth(d.getMonth() - 1);
                                d.setMonth(d.getMonth() - 1);
                            }

                            this.monthsArray = arrayMonths.reverse();

                            this.showOverlay = false;
                            //
                        } else if (response.status === 204) {
                            this.despachosAc = [];
                            this.emptyTabletext =
                                "No hay registros de Despachos Alto Costo para este Medicamento";
                        }
                    })
                    .catch(error => {
                        this.showOverlay = false;
                        console.log(error);
                    });
            }
        },
        customTableFilter(row, filter) {
            return this;
        },
        getMonthsName(month) {
            var monthNameArray = new Array(
                "ENERO",
                "FEBRERO",
                "MARZO",
                "ABRIL",
                "MAYO",
                "JUNIO",
                "JULIO",
                "AGOSTO",
                "SEPTIEMBRE",
                "OCTUBRE",
                "NOVIEMBRE",
                "DICIEMBRE"
            );

            return monthNameArray[month - 1];
        }
    }
};
</script>

<style>
.table-secondary {
    background-color: black !important;
}
/* .table thead th:nth-child(4) {
    padding: 0 !important;
} */
th[aria-colindex="4"] {
    padding: 0 !important;
    border: none !important;
}
.table-footer {
    background-color: #343a40 !important;
    color: #fff;
}

.deceased-patient {
    background-color: #cc99ff;
}
tbody tr th {
    font-size: 0.8rem !important;
}
</style>
