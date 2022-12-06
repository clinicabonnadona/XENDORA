<template>
    <b-container fluid no-gutters>
        <b-row>
            <b-col>
                <b-form>
                    <div class="row">
                        <b-col xl="4" offset-xl="2">
                            <b-form-input
                                v-model="filter"
                                id="filter-input"
                                type="search"
                                placeholder="Escribe acá para Buscar"
                                class="login-search-input"
                            ></b-form-input>
                        </b-col>
                        <b-col xl="4">
                            <b-row>
                                <b-col xl="9">
                                    <b-form-select v-model="selectedT">
                                        <b-form-select-option :value="null"
                                            >Seleccione un Tipo de
                                            Proveedor</b-form-select-option
                                        >
                                        <b-form-select-option
                                            v-for="(pt, index) in providersType"
                                            :key="index"
                                            :value="pt.typeName"
                                            >{{ pt.typeName }}</b-form-select-option
                                        >
                                    </b-form-select>
                                </b-col>
                                <b-col xl="3">
                                    <b-button variant="success" rounded @click="getProvidersList"> Buscar</b-button>
                                </b-col>
                            </b-row>
                        </b-col>
                    </div>
                </b-form>
            </b-col>
        </b-row>
        <hr />

        <b-row class="mt-3">
            <b-col>
                <b-pagination
                    :total-rows="itemsLength"
                    v-model="currentPage"
                    :per-page="perPage"
                    aria-controls="my-table"
                ></b-pagination>
            </b-col>
        </b-row>

        <b-row class="mt-3">
            <b-col>
                <b-overlay :show="isLoadingProviders">
                    <b-table-simple    striped hover 
                                :items="providers" 
                                :fields="fields"
                                :current-page="currentPage"
                                :per-page="perPage"
                                show-empty
                                empty-text="No hay registro de Despachos para este Medicamento"
                    >
                        <!-- A custom Empty Text -->
                        <template #empty="scope">
                            <h4 class="text-center">{{ scope.emptyText }}</h4>
                        </template>

                        <b-thead>
                            <b-tr>
                                <b-th
                                    colspan="3"
                                    class="text-center"
                                    variant="danger"
                                >
                                    INFORMACIÓN DEL PROVEEDOR
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
                                    >PROVEEDOR</b-th
                                >
                                <b-th
                                    class="text-center align-middle"
                                    variant="success"
                                    >NIT</b-th
                                >
                                <b-th
                                    class="text-center align-middle"
                                    variant="success"
                                    >TIPO PROVEEDOR</b-th
                                >
                                <b-th
                                    variant="danger"
                                    v-for="(m, index) in setMonthsToArray"
                                    :key="index"
                                    class="text-center"
                                    >{{ m.toUpperCase() }}
                                </b-th>
                            </b-tr>
                        </b-thead>

                        <b-tbody>
                            <b-tr v-for="(item, index) in itemsForPagination" :key="index">
                                <b-th class="text-center align-middle">
                                    {{ item.providerName }}
                                </b-th>
                                <b-th class="text-center align-middle">
                                    {{ item.providerNit }}
                                </b-th>
                                <b-th class="text-center align-middle">
                                    {{ item.providerType }}
                                </b-th>
                                <b-td   class="text-center align-middle"
                                        v-for="(movement, index_two) in item.movement"
                                        :key="index_two"
                                        :variant="movement._variant"
                                >
                                 {{
                                    movement.mount === 0 ? "" : movement.mount
                                 }}
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </b-overlay>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "ProveedoresCausacion",
    data() {
        return {
            selectedT: null,
            fields: [
                {
                    key: "providerName",
                    label: "PROVEEDOR",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "providerNit",
                    label: "NIT",
                    sortable: true,
                    class: "text-center"
                },
                {
                    key: "providerType",
                    label: "TIPO PROVEEDOR",
                    sortable: true,
                    sortByFormatted: true,
                    filterByFormatted: true,
                    class: "text-center"
                },
                { key: "actions", label: "Actions", class: "text-center" }
            ],
            currentPage: 1,
            perPage: 10,
            filter: "",
            filterOn: [],
        };
    },
    computed: {
        ...mapGetters(["providersType", "providers", "isLoadingProvidersType", "isLoadingProviders"]),
        setMonthsToArray() {
            return this.getMonths()
        },
        customElementsWithFilter() {
            return this.filter
                ? this.providers.filter(
                      item =>
                          item.providerName.includes(
                              this.filter
                          ) ||
                          item.providerNit.includes(this.filter)
                  )
                : this.providers;
        },
        itemsLength() {
            return this.providers.length
        },
        itemsForPagination() {
            return this.customElementsWithFilter.slice(
                (this.currentPage - 1) * this.perPage,
                this.currentPage * this.perPage
            )
        }
    },
    methods: {
        getProvidersList () {
            this.$store.dispatch("getProvidersList", this.selectedT);
        },
        getMonths() {
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
            for (var i = 0; i <= 11; i++) {
                arrayMonths.push(
                    monthName[d.getMonth()] +
                        " " +
                        d.getFullYear()
                );
                //d.setMonth(d.getMonth() - 1);
                d.setMonth(d.getMonth() - 1);
            }

            return arrayMonths.reverse();
        }
    },
    created() {
        this.$store.dispatch("getProvidersTypes");     
    }
};
</script>

<style></style>
