<template>
    <div>
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <b-col xl="12" sm="12">
                    <b-card>
                        <b-card-title>
                            <b-row>
                                <b-col>
                                    <h3
                                        class="card-table-title text-center mt-2 mb-5"
                                    >
                                        Registro de Envío de Ordenes de Compra a
                                        Proveedores
                                    </h3>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-col xl="3" sm="12">
                                    <b-form-group label-for="input-date-init">
                                        <b-form-datepicker
                                            id="input-date-init"
                                            placeholder="Fecha de Inicio"
                                            v-model="payload.initDate"
                                            :date-format-options="{
                                                year: 'numeric',
                                                month: 'numeric',
                                                day: 'numeric'
                                            }"
                                            :max="maxDate"
                                            today-button
                                            class="login-date-input input-with-centered-placeholder"
                                        ></b-form-datepicker>
                                    </b-form-group>
                                </b-col>
                                <b-col xl="3" sm="12">
                                    <b-form-group label-for="input-date-end">
                                        <b-form-datepicker
                                            id="input-date-end"
                                            placeholder="Fecha de Fin"
                                            v-model="payload.endDate"
                                            :date-format-options="{
                                                year: 'numeric',
                                                month: 'numeric',
                                                day: 'numeric'
                                            }"
                                            :max="maxDate"
                                            today-button
                                            class="login-date-input input-with-centered-placeholder"
                                        ></b-form-datepicker>
                                    </b-form-group>
                                </b-col>
                                <b-col xl="2" sm="12" class="text-center">
                                    <b-button
                                        pill
                                        variant="success"
                                        @click="getShippingPurchaseOrders"
                                    >
                                        <i class="fas fa-search mr-2"></i>
                                        Buscar</b-button
                                    >

                                    <!-- <b-button
                                        pill
                                        variant="success"
                                        @click="getShippingPurchaseOrders"
                                    >
                                        <i class="fas fa-file-excel mr-2"></i>
                                        Informe</b-button
                                    > -->
                                    <export-excel
                                        class="btn btn-success ml-2 rounded-pill"
                                        title="REPORTE ENVÍO DE ORDENES DE COMPRAS"
                                        :data="getItemsToReport"
                                        worksheet="Ordenes"
                                        name="OrdenesEnviadas.xls"
                                        pill
                                    >
                                        Informe
                                        <i class="fas fa-file-excel fa-3x"></i>
                                    </export-excel>
                                </b-col>
                                <b-col xl="4" sm="12" class="mt-2">
                                    <b-input-group>
                                        <b-form-input
                                            id="filter-input"
                                            v-model="filter"
                                            type="search"
                                            placeholder="# Orden"
                                            class="login-search-input"
                                        ></b-form-input>

                                        <b-input-group-append>
                                            <b-button
                                                :disabled="!filter"
                                                @click="filter = ''"
                                                class="login-search-input-append"
                                            >
                                                <i class="fas fa-brush"></i>
                                            </b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-col>
                            </b-row>
                        </b-card-title>
                        <b-overlay
                            :show="this.$store.state.so.isLoadingShippingOrders"
                            spinner-variant="primary"
                            spinner-type="grow"
                            spinner-small
                            rounded="sm"
                        >
                            <b-table
                                responsive
                                hover
                                :current-page="currentPage"
                                :per-page="perPage"
                                @filtered="onFiltered"
                                :filter="filter"
                                :filter-included-fields="filterOn"
                                :select-mode="selectMode"
                                :items="getItems"
                                :fields="fields"
                                selectable
                                show-empty
                                @row-selected="onRowSelected"
                            >
                                <template #cell(Status)="row">
                                    <i
                                        :class="
                                            row.item.Status != 1
                                                ? 'fas fa-circle text-danger'
                                                : 'fas fa-circle text-success'
                                        "
                                        :title="
                                            row.item.Status != 1
                                                ? 'Orden A espera de Fecha de Envío'
                                                : 'Orden Enviada'
                                        "
                                    ></i>
                                </template>

                                <template #cell(ManagementShippingDate)="row">
                                    <p>
                                        {{
                                            row.item.ManagementShippingDate !=
                                            null
                                                ? moment(
                                                      row.item
                                                          .ManagementShippingDate
                                                  ).format("YYYY-MM-DD hh:mm")
                                                : ""
                                        }}
                                    </p>
                                </template>

                                <template #cell(ManagementDevolutionDate)="row">
                                    <p>
                                        {{
                                            row.item.ManagementDevolutionDate !=
                                            null
                                                ? moment(
                                                      row.item
                                                          .ManagementDevolutionDate
                                                  ).format("YYYY-MM-DD hh:mm")
                                                : ""
                                        }}
                                    </p>
                                </template>

                                <template #cell(FechaEnvio)="row">
                                    <p>
                                        {{
                                            row.item.ShippingDate != null
                                                ? moment(
                                                      row.item.ShippingDate
                                                  ).format("YYYY-MM-DD hh:mm")
                                                : ""
                                        }}
                                    </p>
                                </template>

                                <template #cell(Comments)="row">
                                    <b-button
                                        variant="outlines-ligth"
                                        :id="
                                            `tooltip-target-${row.item.NroOrden}`
                                        "
                                        v-if="row.item.Comments"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </b-button>
                                    <b-tooltip
                                        v-if="row.item.Comments"
                                        :target="
                                            `tooltip-target-${row.item.NroOrden}`
                                        "
                                        triggers="hover"
                                    >
                                        {{ row.item.Comments }}
                                    </b-tooltip>
                                </template>

                                <template #cell(Actions)="row">
                                    <b-button
                                        :disabled="selectItemsCount"
                                        :variant="
                                            row.item.Status != 1
                                                ? 'primary'
                                                : 'warning'
                                        "
                                        pill
                                        class="button-times-icon"
                                        @click="
                                            row.item.Status != 1
                                                ? showModal(
                                                      'add-date-modal',
                                                      []
                                                  )
                                                : showModal(
                                                      'edit-date-modal',
                                                      row.item
                                                  )
                                        "
                                    >
                                        <i
                                            :class="
                                                row.item.Status != 1
                                                    ? 'fas fa-plus times-icon'
                                                    : 'fas fa-pen times-icon'
                                            "
                                        ></i>
                                        {{
                                            row.item.Status != 1
                                                ? "Add Date"
                                                : "Edit Date"
                                        }}</b-button
                                    >
                                </template>
                            </b-table>

                            <!-- Pagination Section -->
                            <b-pagination
                                v-model="currentPage"
                                :total-rows="activeOrderCount"
                                :per-page="perPage"
                                align="fill"
                                size="md"
                                class="my-0"
                            ></b-pagination>
                            <!-- End Pagination Section -->
                        </b-overlay>
                    </b-card>

                    <!-- Modal Add Date -->
                    <b-modal
                        ref="add-date-modal"
                        class="card-table-title"
                        ok-only
                        no-close-on-backdrop
                        hide-header-close
                    >
                        <template #modal-title>
                            AGREGAR FECHA DE ENVÍO
                        </template>
                        <b-form role="form" class="mt-3">
                            <b-container fluid>
                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-2"
                                            label="Fecha y Hora de Envío a Gerencia"
                                            label-for="input-2"
                                        >
                                            <b-form-input
                                                id="input-2"
                                                type="datetime-local"
                                                v-model="
                                                    payload.ManagementShippingDate
                                                "
                                                :max="maxDate2"
                                                class="login-date-input"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-3"
                                            label="Fecha y Hora de Devolución Gerencia"
                                            label-for="input-3"
                                        >
                                            <b-form-input
                                                id="input-3"
                                                type="datetime-local"
                                                v-model="
                                                    payload.ManagementDevolutionDate
                                                "
                                                :max="maxDate2"
                                                class="login-date-input"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <hr />

                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-1"
                                            label="Fecha y Hora de Envío a Proveedor"
                                            label-for="input-1"
                                        >
                                            <b-form-input
                                                id="input-1"
                                                type="datetime-local"
                                                v-model="payload.shippingDate"
                                                :max="maxDate2"
                                                class="login-date-input"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-4"
                                            label="Observación del envío"
                                            label-for="input-4"
                                        >
                                            <b-form-textarea
                                                id="input-4"
                                                v-model="payload.Comments"
                                                placeholder="Comentarios para este envío"
                                                rows="3"
                                                max-rows="6"
                                                :disabled="selected.length > 1"
                                            ></b-form-textarea>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-container>
                        </b-form>
                        <template #modal-footer>
                            <b-button
                                variant="danger"
                                pill
                                @click="hideModal('add-date-modal')"
                            >
                                <i class="fas fa-times"></i>
                                Cerrar
                            </b-button>
                            <b-button
                                variant="warning"
                                pill
                                @click="clearFields"
                            >
                                <i class="fas fa-brush"></i>
                                Limpiar
                            </b-button>
                            <b-button
                                variant="success"
                                pill
                                @click="saveShippingPurchaseOrders"
                            >
                                <i class="fas fa-save"></i>
                                Guardar
                            </b-button>
                        </template>
                    </b-modal>

                    <!-- Modal Edit Date -->
                    <b-modal
                        ref="edit-date-modal"
                        class="card-table-title"
                        hide-header-close
                        no-close-on-backdrop
                    >
                        <template #modal-title>
                            EDITAR FECHA DE ENVÍO
                        </template>
                        <b-form role="form" class="mt-3">
                            <b-container fluid>
                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-6"
                                            label="Fecha y Hora de Envío a Gerencia"
                                            label-for="input-6"
                                        >
                                            <b-form-input
                                                id="input-6"
                                                type="datetime-local"
                                                v-model="
                                                    payload.ManagementShippingDate
                                                "
                                                class="login-date-input"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-7"
                                            label="Fecha y Hora de Devolución Gerencia"
                                            label-for="input-7"
                                        >
                                            <b-form-input
                                                id="input-7"
                                                type="datetime-local"
                                                v-model="
                                                    payload.ManagementDevolutionDate
                                                "
                                                class="login-date-input"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <hr />

                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-5"
                                            label="Fecha y Hora de Envío a Proveedor"
                                            label-for="input-5"
                                        >
                                            <b-form-input
                                                id="input-5"
                                                type="datetime-local"
                                                v-model="payload.shippingDate"
                                                class="login-date-input"
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col>
                                        <b-form-group
                                            id="fieldset-8"
                                            label="Observación del envío"
                                            label-for="input-8"
                                        >
                                            <b-form-textarea
                                                id="input-8"
                                                v-model="payload.Comments"
                                                placeholder="Comentarios para este envío"
                                                rows="3"
                                                max-rows="6"
                                            ></b-form-textarea>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-container>
                        </b-form>
                        <template #modal-footer>
                            <b-button
                                variant="danger"
                                pill
                                @click="hideModal('edit-date-modal')"
                            >
                                <i class="fas fa-times"></i>
                                Cerrar
                            </b-button>
                            <b-button
                                variant="primary"
                                pill
                                @click="clearFields"
                            >
                                <i class="fas fa-brush"></i>
                                Limpiar
                            </b-button>
                            <b-button
                                variant="warning"
                                pill
                                :disabled="
                                    payload.ManagementDevolutionDate === null
                                "
                                @click="updatedShippingPurchaseOrders"
                            >
                                <i class="fas fa-pen"></i>
                                Guardar
                            </b-button>
                        </template>
                    </b-modal>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import moment from "moment";
import NoConnectionAlert from "../alerts/NoConnectionAlert.vue";

export default {
    name: "ShippingPurchaseOrders",
    components: {
        NoConnectionAlert
    },
    props: {
        userdata: Object
    },
    data() {
        const todayDate = moment().format("YYYY-MM-DD");
        const todayDateLocal = new Date(
            new Date().getTime() - new Date().getTimezoneOffset() * 60000
        )
            .toISOString()
            .split(".")[0];
        return {
            payload: {
                initDate: this.$store.state.so.initDate,
                endDate: this.$store.state.so.endDate,
                shippingDate: "",
                ManagementShippingDate: "",
                ManagementDevolutionDate: "",
                Comments: ""
            },
            json_meta: [
                [
                    {
                        key: "charset",
                        value: "utf-8"
                    }
                ]
            ],
            maxDate: todayDate,
            maxDate2: todayDateLocal,
            infoModal: {
                id: "",
                title: "",
                content: ""
            },
            onLine: navigator.onLine,
            showBackOnline: false,
            filter: null,
            filterOn: [],
            selectMode: "multi",
            selected: [],
            currentPage: 1,
            perPage: 10,
            fields: [
                { key: "NroOrden", label: "Nro. Orden", sortable: true },
                { key: "ProveedorNit", label: "Nit Proveedor", sortable: true },
                {
                    key: "ProveedorName",
                    label: "Nombre Proveedor",
                    sortable: true
                },
                { key: "FechaOrden", label: "Fecha Orden", sortable: true },
                {
                    key: "ManagementShippingDate",
                    label: "Fecha Envio Gerencia",
                    sortable: true
                },
                {
                    key: "ManagementDevolutionDate",
                    label: "Fecha Devo. Gerencia",
                    sortable: true
                },
                { key: "FechaEnvio", label: "Fecha Envio", sortable: true },
                {
                    key: "Status",
                    label: "Estado",
                    sortable: true,
                    class: "text-center align-middle"
                },
                {
                    key: "Comments",
                    label: "Observaciones",
                    class: "text-center"
                },
                { key: "Actions", label: "Acciones", sortable: false }
            ],
            disableButtons: true
        };
    },
    computed: {
        getItems() {
            return this.$store.state.so.shippingPurchaseOrders;
        },
        getItemsToReport() {
            return this.$store.state.so.shippingPurchaseOrdersReport;
        },
        activeOrderCount() {
            return this.$store.state.so.shippingPurchaseOrders.length;
        },
        selectItemsCount: {
            get: function() {
                if (this.selected.length > 0) {
                    return false;
                } else {
                    return true;
                }
            },
            set: function(newValue) {
                if (newValue) {
                    this.disableButtons = newValue;
                    return true;
                } else {
                    return false;
                }
            }
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
        onRowSelected(items) {
            const selectedItemsCount = items.length;

            if (selectedItemsCount > 1) {
                for (var i = 0; i < items.length - 1; i++) {
                    if (items[i].Status != items[i + 1].Status) {
                        this.$bvToast.toast(
                            "No puede Seleccionar Ordenes ya registradas y Ordenes por Registrar",
                            {
                                title: "Alerta de Error Selección",
                                variant: "danger",
                                solid: true
                            }
                        );
                        this.selectItemsCount = true;
                    } else {
                        this.selected = items;
                    }
                }
            } else {
                this.selected = items;
            }
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        updateOnlineStatus(e) {
            const { type } = e;
            this.onLine = type === "online";
        },
        getShippingPurchaseOrders() {
            this.$store.commit("SET_INIT_DATE", this.payload.initDate);
            this.$store.commit("SET_END_DATE", this.payload.endDate);
            this.$store.dispatch("getShippingPurchaseOrders", this.payload);
            this.$store.dispatch("getDataToReport", this.payload);
        },
        showModal(modal, item) {
            if (modal === "edit-date-modal") {
                this.payload.shippingDate = item.ShippingDate;
                this.payload.ManagementShippingDate =
                    item.ManagementShippingDate;
                this.payload.ManagementDevolutionDate =
                    item.ManagementDevolutionDate;
                this.payload.Comments = item.Comments;
            }
            this.$refs[`${modal}`].show();
        },
        hideModal(modal) {
            this.clearFields();
            this.$refs[`${modal}`].hide();
        },
        clearFields() {
            this.payload.shippingDate = "";
            this.payload.Comments = "";
            this.payload.ManagementDevolutionDate = "";
            this.payload.ManagementShippingDate = "";
        },
        saveShippingPurchaseOrders() {
            /* if (!this.payload.ManagementShippingDate) {
                this.$bvToast.toast(
                    "Debe Registrar una fecha de Envio a Grencia",
                    {
                        title:
                            "Alerta de Error Campo Fecha Envio a Gerencia Vacio",
                        variant: "danger",
                        solid: true
                    }
                );
            } */
            this.payload.selectedOrders = this.selected;
            this.$store.dispatch("saveShippingPurchaseOrders", this.payload);
            //this.clearFields();
        },
        updatedShippingPurchaseOrders() {
            this.payload.selectedOrders = this.selected;
            this.$store.dispatch("updateShippingPurchaseOrders", this.payload);
        }
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    created() {
        this.$store.commit("SET_INIT_DATE", moment().format("YYYY-MM-DD"));
        this.$store.commit("SET_END_DATE", moment().format("YYYY-MM-DD"));
        this.payload.initDate = this.$store.state.so.initDate;
        this.payload.endDate = this.$store.state.so.endDate;
        this.$store.dispatch("getShippingPurchaseOrders");
    },
    beforeDestroy() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    }
};
</script>

<style>
.card .card-table-title {
    color: #000000;
    text-transform: uppercase;
    font-size: 0.875rem;
    font-weight: 500;
}
</style>
