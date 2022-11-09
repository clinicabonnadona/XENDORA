<template>
    <div>
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <b-col>
                    <b-card
                        header-tag="header"
                        no-footer
                        border-variant="secondary"
                        header-border-variant="secondary"
                    >
                        <template #header>
                            <b-row>
                                <b-col
                                    sm="12"
                                    md="12"
                                    lg="12"
                                    xl="4"
                                    offset-xl="2"
                                >
                                    <b-row class="mt-2">
                                        <b-col sm="12" lg="5">
                                            <b-form-group>
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
                                                    class="login-date-input"
                                                ></b-form-datepicker>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" lg="5">
                                            <b-form-group>
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
                                                    class="login-date-input"
                                                ></b-form-datepicker>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" lg="2">
                                            <b-button
                                                variant="success"
                                                block
                                                @click="getActiveOrdersByDate"
                                                pill
                                            >
                                                <i class="fas fa-search"></i>
                                            </b-button>
                                        </b-col>
                                    </b-row>
                                </b-col>

                                <b-col
                                    sm="12"
                                    md="12"
                                    lg="12"
                                    xl="3"
                                    class="mt-sm-2 mt-xl-2 mt-xl-0 mt-md-0"
                                >
                                    <b-form-group
                                        content-cols-xl="3"
                                        class="mb-0"
                                    >
                                        <b-input-group>
                                            <b-form-input
                                                id="filter-input"
                                                v-model="filter"
                                                type="search"
                                                placeholder="# Orden - Entrada"
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
                                    </b-form-group>
                                </b-col>

                                <b-col sm="12" md="12" lg="12" xl="2">
                                    <export-excel
                                        class="btn btn-success mt-2 rounded-pill"
                                        title="REPORTE RECEPCIÓN DE MEDICAMENTOS"
                                        :data="getReceivedOrdersReport"
                                        worksheet="Entradas"
                                        name="Entradas.xls"
                                        v-can="'reporte-recepcion-medicamentos'"
                                        pill
                                    >
                                        Descargar
                                        <i class="fas fa-file-excel fa-3x"></i>
                                    </export-excel>
                                </b-col>
                            </b-row>
                        </template>

                        <!-- Begin of TABLE COMPONENT -->
                        <b-overlay
                            :show="showOverlay"
                            spinner-variant="primary"
                            spinner-type="grow"
                            spinner-small
                            rounded="sm"
                        >
                            <b-row class="p-0">
                                <b-col
                                    sm="12"
                                    lg="12"
                                    md="12"
                                    class="overflow-auto"
                                >
                                    <b-table
                                        :items="items"
                                        :fields="fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :filter="filter"
                                        :filter-included-fields="filterOn"
                                        :sort-by.sync="sortBy"
                                        :sort-desc.sync="sortDesc"
                                        :sort-direction="sortDirection"
                                        stacked="md"
                                        show-empty
                                        empty-text="Aún no hay registros de entradas el día de hoy"
                                        small
                                        @filtered="onFiltered"
                                        headVariant="light"
                                        bordered="bordered"
                                        hover
                                        responsive
                                    >
                                        <template #cell(fecEntrada)="row">
                                            {{
                                                moment(
                                                    row.item.fecEntrada
                                                ).format("YYYY-MM-DD")
                                            }}
                                        </template>

                                        <template #cell(provider)="row">
                                            {{
                                                row.item.provider.substring(
                                                    0,
                                                    25
                                                ) + "..."
                                            }}
                                        </template>

                                        <template #cell(dueDate)="row">
                                            {{
                                                row.item.dueDate != null
                                                    ? moment(
                                                          row.item.dueDate
                                                      ).format("YYYY-MM-DD")
                                                    : ""
                                            }}
                                        </template>

                                        <template #cell(actions)="row">
                                            <b-button
                                                size="sm"
                                                v-if="row.item._rowVariant"
                                                @click="
                                                    editShowModal(
                                                        row.item,
                                                        row.index,
                                                        $event.target
                                                    )
                                                "
                                                variant="warning"
                                                class="mr-1"
                                                pill
                                                v-can="
                                                    'recepcion-medicamento-editar'
                                                "
                                            >
                                                <i class="fas fa-pen"></i>
                                            </b-button>

                                            <b-button
                                                size="sm"
                                                @click="
                                                    info(
                                                        row.item,
                                                        row.index,
                                                        $event.target
                                                    )
                                                "
                                                variant="info"
                                                class="mr-1"
                                                pill
                                            >
                                                <i class="fas fa-eye"></i>
                                            </b-button>
                                        </template>
                                    </b-table>

                                    <b-col
                                        class="my-1 col-md-6 offset-md-3 col-xl-6 offset-xl-3 col-sm-12"
                                    >
                                        <b-pagination
                                            v-model="currentPage"
                                            :total-rows="activeOrderCount"
                                            :per-page="perPage"
                                            align="fill"
                                            size="md"
                                            class="my-0"
                                        ></b-pagination>
                                    </b-col>
                                </b-col>
                            </b-row>
                        </b-overlay>
                        <!-- End of TABLE COMPONENT -->
                    </b-card>
                </b-col>
            </b-row>
        </b-container>

        <!-- Info modal -->
        <b-modal
            ref="info-modal"
            ok-only
            no-close-on-backdrop
            size="xl"
            :title="infoModal.title"
        >
            <b-container fluid>
                <b-row>
                    <b-col cols="12">
                        <b-form>
                            <b-row class="mt-3">
                                <!-- Begin First Column -->
                                <b-col cols="9">
                                    <!-- Begin First Row -->
                                    <b-row>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-1"
                                                label="Código Producto:"
                                                label-for="input-1"
                                            >
                                                <b-form-input
                                                    id="input-1"
                                                    v-model="activeOrder.SumCod"
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="6">
                                            <b-form-group
                                                id="input-group-2"
                                                label="Nombre Producto:"
                                                label-for="input-2"
                                            >
                                                <b-form-input
                                                    id="input-2"
                                                    v-model="
                                                        activeOrder.SumDesc
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-3"
                                                label="Fecha de Vencimiento:"
                                                label-for="input-3"
                                                label-align="left"
                                            >
                                                <b-form-input
                                                    id="input-3"
                                                    v-model="
                                                        activeOrder.FechaVencimiento
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End First Row -->

                                    <!-- Begin Second Row -->
                                    <b-row>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-6"
                                                label="Concentración:"
                                                label-for="input-6"
                                            >
                                                <b-form-input
                                                    id="input-6"
                                                    v-model="
                                                        activeOrder.Concentracion
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="4">
                                            <b-form-group
                                                id="input-group-7"
                                                label="Forma Farmaceutica:"
                                                label-for="input-7"
                                            >
                                                <b-form-input
                                                    id="input-7"
                                                    v-model="
                                                        activeOrder.ForFarmaceutica
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-8"
                                                label="Cantidad:"
                                                label-for="input-8"
                                            >
                                                <b-form-input
                                                    id="input-8"
                                                    v-model="
                                                        activeOrder.Cantidad
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-9"
                                                label="Tamaño Muestra:"
                                                label-for="input-9"
                                            >
                                                <b-form-input
                                                    id="input-9"
                                                    v-model="
                                                        activeOrder.TamMuestra
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-10"
                                                label="Lote:"
                                                label-for="input-10"
                                            >
                                                <b-form-input
                                                    id="input-10"
                                                    v-model="activeOrder.Lote"
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Second Row -->

                                    <!-- Begin Third Row -->
                                    <b-row>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-11"
                                                label="Nro. Orden:"
                                                label-for="input-11"
                                            >
                                                <b-form-input
                                                    id="input-11"
                                                    v-model="
                                                        activeOrder.NroOrden
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-12"
                                                label="Nit Proveedor:"
                                                label-for="input-12"
                                            >
                                                <b-form-input
                                                    id="input-12"
                                                    v-model="
                                                        activeOrder.ProveedorNit
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="6">
                                            <b-form-group
                                                id="input-group-13"
                                                label="Nombre Proveedor:"
                                                label-for="input-13"
                                            >
                                                <b-form-input
                                                    id="input-13"
                                                    v-model="
                                                        activeOrder.NombreProveedor
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Third Row -->

                                    <hr />

                                    <!-- Begin Fourth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-18"
                                                label="Registro Sanitario:"
                                                label-for="input-18"
                                            >
                                                <b-form-input
                                                    id="input-18"
                                                    v-model="
                                                        activeOrder.RegSanitario
                                                    "
                                                    type="text"
                                                    placeholder="Registro Sanitario"
                                                    required
                                                >
                                                </b-form-input>
                                                <span
                                                    class="text-danger"
                                                    v-if="getErrorCount"
                                                >
                                                    {{ errors.RegSanitario }}
                                                </span>
                                            </b-form-group>
                                        </b-col>

                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-21"
                                                label="Tipo Proveedor:"
                                                label-for="input-21"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.TipoProveedor
                                                    "
                                                    :options="
                                                        optionsTipoProveedor
                                                    "
                                                    required
                                                ></b-form-select>
                                                <span
                                                    class="text-danger"
                                                    v-if="getErrorCount"
                                                >
                                                    {{ errors.TipoProveedor }}
                                                </span>
                                            </b-form-group>
                                        </b-col>

                                        <b-col
                                            sm="12"
                                            xl="4"
                                            v-if="
                                                activeOrder.TipoProveedor ===
                                                    'DISTRIBUIDOR'
                                            "
                                        >
                                            <b-form-group
                                                id="input-group-20"
                                                label="Estado Registro Sanitario:"
                                                label-for="input-20"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.EstadoRegSan
                                                    "
                                                    :options="
                                                        optionsEstadoRegSan
                                                    "
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Fourth Row -->

                                    <b-row
                                        v-if="
                                            activeOrder.TipoProveedor ===
                                                'DISTRIBUIDOR'
                                        "
                                    >
                                        <b-col sm="12" xl="12">
                                            <b-form-group
                                                id="input-group-29"
                                                label="Proveedor Titular del Registro:"
                                                label-for="input-29"
                                            >
                                                <b-form-input
                                                    id="input-28"
                                                    v-model="
                                                        activeOrder.ProveedorDesc
                                                    "
                                                    type="text"
                                                    placeholder="Proveedor Titular del Registro"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>

                                    <!-- Begin Fifth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-19"
                                                label="Presentación Comercial:"
                                                label-for="input-19"
                                            >
                                                <b-form-input
                                                    id="input-19"
                                                    v-model="
                                                        activeOrder.PresComercial
                                                    "
                                                    type="text"
                                                    placeholder="Presentación Comercial"
                                                >
                                                </b-form-input>
                                                <span
                                                    class="text-danger"
                                                    v-if="getErrorCount"
                                                >
                                                    {{ errors.PresComercial }}
                                                </span>
                                            </b-form-group>
                                        </b-col>

                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-22"
                                                label="Marca Dispotivo:"
                                                label-for="input-22"
                                            >
                                                <b-form-input
                                                    id="input-22"
                                                    v-model="
                                                        activeOrder.MarcaDispositivo
                                                    "
                                                    type="text"
                                                    placeholder="Marca Dispotivo"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-23"
                                                label="Clasificación del Riesgo:"
                                                label-for="input-23"
                                            >
                                                <b-form-input
                                                    id="input-23"
                                                    v-model="
                                                        activeOrder.ClasiRiesgo
                                                    "
                                                    type="text"
                                                    placeholder="Clasificación del Riesgo"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Fifth Row -->

                                    <!-- Begin Sixth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-24"
                                                label="Embalaje:"
                                                label-for="input-24"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.Embalaje
                                                    "
                                                    :options="optionsEmbalaje"
                                                ></b-form-select>
                                                <span
                                                    class="text-danger"
                                                    v-if="getErrorCount"
                                                >
                                                    {{ errors.Embalaje }}
                                                </span>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-25"
                                                label="Inspeccion de Defecto:"
                                                label-for="input-25"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.InspDefecto
                                                    "
                                                    :options="
                                                        optionsInspeccionDefecto
                                                    "
                                                ></b-form-select>
                                                <span
                                                    class="text-danger"
                                                    v-if="getErrorCount"
                                                >
                                                    {{ errors.InspDefecto }}
                                                </span>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-26"
                                                label="Tipo de Producto:"
                                                label-for="input-26"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.TipoProducto
                                                    "
                                                    :options="
                                                        optionsTipoProducto
                                                    "
                                                ></b-form-select>
                                                <span
                                                    class="text-danger"
                                                    v-if="getErrorCount"
                                                >
                                                    {{ errors.TipoProducto }}
                                                </span>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Sixth Row -->

                                    <!-- Begin Eigth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-30"
                                                label="Temperatura:"
                                                label-for="input-30"
                                            >
                                                <b-form-input
                                                    id="input-30"
                                                    v-model="
                                                        activeOrder.Temperatura
                                                    "
                                                    type="text"
                                                    placeholder="Temperatura"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-31"
                                                label="Serie Dispositivo:"
                                                label-for="input-31"
                                            >
                                                <b-form-input
                                                    id="input-31"
                                                    v-model="
                                                        activeOrder.SerieDispositivo
                                                    "
                                                    type="text"
                                                    placeholder="Serie dispositivo"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-32"
                                                label="Vida Util:"
                                                label-for="input-32"
                                            >
                                                <b-form-input
                                                    id="input-32"
                                                    v-model="
                                                        activeOrder.VidaUtil
                                                    "
                                                    type="text"
                                                    placeholder="Vida Util"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Eigth Row -->

                                    <hr />

                                    <!-- Begin Seventh Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-27"
                                                label="Desviación:"
                                                label-for="input-27"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.Desviacion
                                                    "
                                                    :options="optionsDesviacion"
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                        <b-col
                                            sm="12"
                                            xl="8"
                                            v-if="
                                                activeOrder.Desviacion === 'SI'
                                            "
                                        >
                                            <b-form-group
                                                id="input-group-28"
                                                label="Observación de Desviación:"
                                                label-for="input-28"
                                            >
                                                <b-form-textarea
                                                    v-model="
                                                        activeOrder.ObsDesviacion
                                                    "
                                                    id="input-28"
                                                    placeholder="Observación de Desviación"
                                                    rows="3"
                                                    no-resize
                                                ></b-form-textarea>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Seventh Row -->
                                </b-col>
                                <!-- End First Column -->

                                <!-- Begin Second Column -->
                                <b-col cols="3">
                                    <!-- Begin First Row -->
                                    <b-row>
                                        <b-col sm="12" xl="12">
                                            <b-card
                                                header="Datos de la Entrada"
                                                header-bg-variant="secondary"
                                                header-text-variant="white"
                                                align="center"
                                            >
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-14"
                                                            label="Nro Entrada:"
                                                            label-for="input-14"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-14"
                                                                v-model="
                                                                    activeOrder.NroEntrada
                                                                "
                                                                type="text"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-15"
                                                            label="Fecha Entrada:"
                                                            label-for="input-15"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-15"
                                                                v-model="
                                                                    activeOrder.FechaEntrada
                                                                "
                                                                type="date"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                            </b-card>
                                        </b-col>
                                    </b-row>
                                    <!-- End First Row -->

                                    <!-- Begin Second Row -->
                                    <b-row class="mt-2">
                                        <b-col sm="12" xl="12">
                                            <b-card
                                                header="Facturación - Remisión"
                                                header-bg-variant="secondary"
                                                header-text-variant="white"
                                                align="center"
                                            >
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-16"
                                                            label="Nro Factura:"
                                                            label-for="input-16"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-16"
                                                                v-model="
                                                                    activeOrder.NroFactura
                                                                "
                                                                type="text"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-17"
                                                            label="Nro Remisión:"
                                                            label-for="input-17"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-17"
                                                                v-model="
                                                                    activeOrder.NroRemision
                                                                "
                                                                type="text"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                            </b-card>
                                        </b-col>
                                    </b-row>
                                    <!-- End Second Row -->
                                </b-col>
                                <!-- End First Column -->
                            </b-row>
                        </b-form>
                    </b-col>
                </b-row>
            </b-container>

            <template #modal-footer>
                <b-button
                    variant="warning"
                    @click="clearFields"
                    :disabled="
                        infoModal._rowVariant === 'success' ||
                            getIsCreatedOrderState
                    "
                >
                    <i class="fas fa-brush"></i> LIMPIAR
                </b-button>

                <b-button variant="danger" @click="hideModal('info-modal')">
                    <i class="fas fa-times"></i> CERRAR
                </b-button>

                <b-button
                    variant="success"
                    @click="saveActiveOrder()"
                    v-if="!getIsUpdatingState"
                    :disabled="
                        infoModal._rowVariant === 'success' ||
                            getIsCreatedOrderState
                    "
                >
                    <i class="fas fa-save"></i> GUARDAR
                </b-button>
                <b-button
                    variant="success"
                    @click="saveActiveOrder()"
                    v-if="getIsUpdatingState"
                    :disabled="!getIsUpdatingState"
                >
                    <i class="fas fa-save"></i> EDITAR
                </b-button>
            </template>
        </b-modal>

        <!-- EDIT ORDER MADE -->
        <b-modal
            ref="edit-info-modal"
            ok-only
            no-close-on-backdrop
            size="xl"
            :title="infoModal.title"
        >
            <b-container fluid>
                <b-row>
                    <b-col cols="12">
                        <b-form method="PUT">
                            <b-row>
                                <!-- Begin First Column -->
                                <b-col cols="9">
                                    <!-- Begin First Row -->
                                    <b-row>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-1"
                                                label="Código Producto:"
                                                label-for="input-1"
                                            >
                                                <b-form-input
                                                    id="input-1"
                                                    v-model="activeOrder.SumCod"
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="6">
                                            <b-form-group
                                                id="input-group-2"
                                                label="Nombre Producto:"
                                                label-for="input-2"
                                            >
                                                <b-form-input
                                                    id="input-2"
                                                    v-model="
                                                        activeOrder.SumDesc
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-3"
                                                label="Fecha de Vencimiento:"
                                                label-for="input-3"
                                                label-align="left"
                                            >
                                                <b-form-input
                                                    id="input-3"
                                                    v-model="
                                                        activeOrder.FechaVencimiento
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End First Row -->

                                    <!-- Begin Second Row -->
                                    <b-row>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-6"
                                                label="Concentración:"
                                                label-for="input-6"
                                            >
                                                <b-form-input
                                                    id="input-6"
                                                    v-model="
                                                        activeOrder.Concentracion
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="4">
                                            <b-form-group
                                                id="input-group-7"
                                                label="Forma Farmaceutica:"
                                                label-for="input-7"
                                            >
                                                <b-form-input
                                                    id="input-7"
                                                    v-model="
                                                        activeOrder.ForFarmaceutica
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-8"
                                                label="Cantidad:"
                                                label-for="input-8"
                                            >
                                                <b-form-input
                                                    id="input-8"
                                                    v-model="
                                                        activeOrder.Cantidad
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-9"
                                                label="Tamaño Muestra:"
                                                label-for="input-9"
                                            >
                                                <b-form-input
                                                    id="input-9"
                                                    v-model="
                                                        activeOrder.TamMuestra
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="6" xl="2">
                                            <b-form-group
                                                id="input-group-10"
                                                label="Lote:"
                                                label-for="input-10"
                                            >
                                                <b-form-input
                                                    id="input-10"
                                                    v-model="activeOrder.Lote"
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Second Row -->

                                    <!-- Begin Third Row -->
                                    <b-row>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-11"
                                                label="Nro. Orden:"
                                                label-for="input-11"
                                            >
                                                <b-form-input
                                                    id="input-11"
                                                    v-model="
                                                        activeOrder.NroOrden
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="3">
                                            <b-form-group
                                                id="input-group-12"
                                                label="Nit Proveedor:"
                                                label-for="input-12"
                                            >
                                                <b-form-input
                                                    id="input-12"
                                                    v-model="
                                                        activeOrder.ProveedorNit
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="6">
                                            <b-form-group
                                                id="input-group-13"
                                                label="Nombre Proveedor:"
                                                label-for="input-13"
                                            >
                                                <b-form-input
                                                    id="input-13"
                                                    v-model="
                                                        activeOrder.NombreProveedor
                                                    "
                                                    type="text"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Third Row -->

                                    <hr />

                                    <!-- Begin Fourth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-18"
                                                label="Registro Sanitario:"
                                                label-for="input-18"
                                            >
                                                <b-form-input
                                                    id="input-18"
                                                    v-model="
                                                        activeOrder.RegSanitario
                                                    "
                                                    type="text"
                                                    placeholder="Registro Sanitario"
                                                    required
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>

                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-21"
                                                label="Tipo Proveedor:"
                                                label-for="input-21"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.TipoProveedor
                                                    "
                                                    :options="
                                                        optionsTipoProveedor
                                                    "
                                                    required
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>

                                        <b-col
                                            sm="12"
                                            xl="4"
                                            v-if="
                                                activeOrder.TipoProveedor ==
                                                    'DISTRIBUIDOR'
                                            "
                                        >
                                            <b-form-group
                                                id="input-group-20"
                                                label="Estado Registro Sanitario:"
                                                label-for="input-20"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.EstadoRegSan
                                                    "
                                                    :options="
                                                        optionsEstadoRegSan
                                                    "
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Fourth Row -->

                                    <b-row
                                        v-if="
                                            activeOrder.TipoProveedor ==
                                                'DISTRIBUIDOR'
                                        "
                                    >
                                        <b-col sm="12" xl="12">
                                            <b-form-group
                                                id="input-group-29"
                                                label="Proveedor Titular del Registro:"
                                                label-for="input-29"
                                            >
                                                <b-form-input
                                                    id="input-28"
                                                    v-model="
                                                        activeOrder.ProveedorDesc
                                                    "
                                                    type="text"
                                                    placeholder="Proveedor Titular del Registro"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>

                                    <!-- Begin Fifth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-19"
                                                label="Presentación Comercial:"
                                                label-for="input-19"
                                            >
                                                <b-form-input
                                                    id="input-19"
                                                    v-model="
                                                        activeOrder.PresComercial
                                                    "
                                                    type="text"
                                                    placeholder="Presentación Comercial"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>

                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-22"
                                                label="Marca Dispotivo:"
                                                label-for="input-22"
                                            >
                                                <b-form-input
                                                    id="input-22"
                                                    v-model="
                                                        activeOrder.MarcaDispositivo
                                                    "
                                                    type="text"
                                                    placeholder="Marca Dispotivo"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-23"
                                                label="Clasificación del Riesgo:"
                                                label-for="input-23"
                                            >
                                                <b-form-input
                                                    id="input-23"
                                                    v-model="
                                                        activeOrder.ClasiRiesgo
                                                    "
                                                    type="text"
                                                    placeholder="Clasificación del Riesgo"
                                                    readonly
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Fifth Row -->

                                    <!-- Begin Sixth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-24"
                                                label="Embalaje:"
                                                label-for="input-24"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.Embalaje
                                                    "
                                                    :options="optionsEmbalaje"
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-25"
                                                label="Inspeccion de Defecto:"
                                                label-for="input-25"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.InspDefecto
                                                    "
                                                    :options="
                                                        optionsInspeccionDefecto
                                                    "
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-26"
                                                label="Tipo de Producto:"
                                                label-for="input-26"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.TipoProducto
                                                    "
                                                    :options="
                                                        optionsTipoProducto
                                                    "
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Sixth Row -->

                                    <!-- Begin Eigth Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-30"
                                                label="Temperatura:"
                                                label-for="input-30"
                                            >
                                                <b-form-input
                                                    id="input-30"
                                                    v-model="
                                                        activeOrder.Temperatura
                                                    "
                                                    type="text"
                                                    placeholder="Temperatura"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-31"
                                                label="Serie Dispositivo:"
                                                label-for="input-31"
                                            >
                                                <b-form-input
                                                    id="input-31"
                                                    v-model="
                                                        activeOrder.SerieDispositivo
                                                    "
                                                    type="text"
                                                    placeholder="Serie dispositivo"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-32"
                                                label="Vida Util:"
                                                label-for="input-32"
                                            >
                                                <b-form-input
                                                    id="input-32"
                                                    v-model="
                                                        activeOrder.VidaUtil
                                                    "
                                                    type="text"
                                                    placeholder="Vida Util"
                                                >
                                                </b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Eigth Row -->

                                    <hr />

                                    <!-- Begin Seventh Row -->
                                    <b-row>
                                        <b-col sm="12" xl="4">
                                            <b-form-group
                                                id="input-group-27"
                                                label="Desviación:"
                                                label-for="input-27"
                                            >
                                                <b-form-select
                                                    v-model="
                                                        activeOrder.Desviacion
                                                    "
                                                    :options="optionsDesviacion"
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>
                                        <b-col
                                            sm="12"
                                            xl="8"
                                            v-if="
                                                activeOrder.Desviacion === 'SI'
                                            "
                                        >
                                            <b-form-group
                                                id="input-group-28"
                                                label="Observación de Desviación:"
                                                label-for="input-28"
                                            >
                                                <b-form-textarea
                                                    v-model="
                                                        activeOrder.ObsDesviacion
                                                    "
                                                    id="input-28"
                                                    placeholder="Observación de Desviación"
                                                    rows="3"
                                                    no-resize
                                                ></b-form-textarea>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <!-- End Seventh Row -->
                                </b-col>
                                <!-- End First Column -->

                                <!-- Begin Second Column -->
                                <b-col cols="3">
                                    <!-- Begin First Row -->
                                    <b-row>
                                        <b-col sm="12" xl="12">
                                            <b-card
                                                header="Datos de la Entrada"
                                                header-bg-variant="secondary"
                                                header-text-variant="white"
                                                align="center"
                                            >
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-14"
                                                            label="Nro Entrada:"
                                                            label-for="input-14"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-14"
                                                                v-model="
                                                                    activeOrder.NroEntrada
                                                                "
                                                                type="text"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-15"
                                                            label="Fecha Entrada:"
                                                            label-for="input-15"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-15"
                                                                v-model="
                                                                    activeOrder.FechaEntrada
                                                                "
                                                                type="date"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                            </b-card>
                                        </b-col>
                                    </b-row>
                                    <!-- End First Row -->

                                    <!-- Begin Second Row -->
                                    <b-row class="mt-2">
                                        <b-col sm="12" xl="12">
                                            <b-card
                                                header="Facturación - Remisión"
                                                header-bg-variant="secondary"
                                                header-text-variant="white"
                                                align="center"
                                            >
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-16"
                                                            label="Nro Factura:"
                                                            label-for="input-16"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-16"
                                                                v-model="
                                                                    activeOrder.NroFactura
                                                                "
                                                                type="text"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                                <b-row>
                                                    <b-col cols="12">
                                                        <b-form-group
                                                            id="input-group-17"
                                                            label="Nro Remisión:"
                                                            label-for="input-17"
                                                            label-align="left"
                                                        >
                                                            <b-form-input
                                                                id="input-17"
                                                                v-model="
                                                                    activeOrder.NroRemision
                                                                "
                                                                type="text"
                                                                readonly
                                                            >
                                                            </b-form-input>
                                                        </b-form-group>
                                                    </b-col>
                                                </b-row>
                                            </b-card>
                                        </b-col>
                                    </b-row>
                                    <!-- End Second Row -->
                                </b-col>
                                <!-- End First Column -->
                            </b-row>
                        </b-form>
                    </b-col>
                </b-row>
            </b-container>

            <template #modal-footer>
                <b-button
                    variant="warning"
                    @click="clearFields"
                    :disabled="infoModal._rowVariant === 'success'"
                >
                    <i class="fas fa-brush"></i> LIMPIAR
                </b-button>

                <b-button
                    variant="danger"
                    @click="hideModal('edit-info-modal')"
                >
                    <i class="fas fa-times"></i> CANCELAR
                </b-button>

                <b-button
                    variant="info"
                    v-if="getIsUpdatingState"
                    :disabled="!activeOrder._rowVariant"
                    @click="editingActiveOrder"
                >
                    <i class="fas fa-pen"></i> EDITAR
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import moment from "moment";
import NoConnectionAlert from "../alerts/NoConnectionAlert";

export default {
    name: "RecepcionMedicamentos",
    components: {
        NoConnectionAlert
    },
    props: {
        userdata: Number
    },
    data() {
        const todayDate = moment().format("YYYY-MM-DD");
        return {
            payload: {
                initDate: "",
                endDate: ""
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
            showOverlay: false,
            items: [],
            fields: [
                {
                    key: "nroOrden",
                    label: "#ORDEN",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "nroEntrada",
                    label: "#ENTRADA",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "fecEntrada",
                    label: "FEC. ENTRADA",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "provider",
                    label: "PROVEEDOR",
                    sortable: true,
                    sortDirection: "desc"
                },
                {
                    key: "invoice",
                    label: "#FACTURA",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "remission",
                    label: "#REMISIÓN",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sum_cod",
                    label: "COD.PRODUCTO",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sum_desc",
                    label: "PRODUCTO",
                    sortable: true,
                    sortDirection: "desc"
                },
                {
                    key: "concentration",
                    label: "CONCENTRACIÒN",
                    sortable: true,
                    sortDirection: "desc"
                },
                {
                    key: "pForm",
                    label: "FORM.FARMA",
                    sortable: true,
                    sortDirection: "desc"
                },
                {
                    key: "quantity",
                    label: "CANTIDAD",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "lot",
                    label: "LOTE",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "dueDate",
                    label: "FEC.VENC",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                {
                    key: "sampleSize",
                    label: "T.MUESTRA",
                    sortable: true,
                    sortDirection: "desc",
                    class: "text-center"
                },
                { key: "actions", label: "Actions", class: "text-center" }
            ],
            currentPage: 1,
            perPage: 15,
            totalRows: 0,
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            sortBy: "",
            sortDesc: false,
            sortDirection: "asc",
            filter: null,
            filterOn: [],
            infoModal: {
                id: "",
                title: "",
                content: "",
                index: ""
            },
            onLine: navigator.onLine,
            showBackOnline: false,
            activeOrder: {
                SumCod: "",
                SumDesc: "",
                NroOrden: "",
                NroEntrada: "",
                FechaEntrada: "",
                ProveedorNit: "",
                NombreProveedor: "",
                NroFactura: "",
                NroRemision: "",
                Concentracion: "",
                CodPForm: "",
                ForFarmaceutica: "",
                Cantidad: "",
                TamMuestra: "",
                Lote: "",
                FechaVencimiento: "",
                RegSanitario: "",
                PresComercial: "",
                TipoProveedor: null,
                EstadoRegSan: null,
                ProveedorDesc: "",
                MarcaDispositivo: "",
                ClasiRiesgo: "",
                Embalaje: null,
                InspDefecto: null,
                TipoProducto: null,
                Desviacion: null,
                ObsDesviacion: "",
                Temperatura: "",
                SerieDispositivo: "",
                VidaUtil: "",
                user_id: this.userdata
            },
            optionsTipoProveedor: [
                { value: null, text: "Por Favor Seleccione una Opción" },
                { value: "DISTRIBUIDOR", text: "DISTRIBUIDOR" },
                { value: "LABORATORIO", text: "LABORATORIO" }
            ],
            optionsEstadoRegSan: [
                { value: null, text: "Por Favor Seleccione una Opción" },
                { value: "CANCELADO", text: "CANCELADO" },
                { value: "DESCONTINUADO", text: "DESCONTINUADO" },
                {
                    value: "EN_TRAMITE_DE_RENOVACION",
                    text: "EN TRAMITE DE RENOVACION"
                },
                { value: "NEGADO", text: "NEGADO" },
                {
                    value: "PERDIDA_DE_FUERZA_EJECUTORIA",
                    text: "PERDIDA DE FUERZA EJECUTORIA"
                },
                { value: "SUSPENDIDO", text: "SUSPENDIDO" },
                { value: "VENCIDO", text: "VENCIDO" },
                { value: "VIGENTE", text: "VIGENTE" }
            ],
            optionsEmbalaje: [
                { value: null, text: "Por Favor Seleccione una Opción" },
                { value: "CUMPLE", text: "CUMPLE" },
                { value: "NO_CUMPLE", text: "NO CUMPLE" }
            ],
            optionsInspeccionDefecto: [
                { value: null, text: "Por Favor Seleccione una Opción" },
                { value: "CUMPLE", text: "CUMPLE" },
                { value: "NO_CUMPLE", text: "NO CUMPLE" }
            ],
            optionsTipoProducto: [
                { value: null, text: "Por Favor Seleccione una Opción" },
                { value: "PROPIEDAD", text: "PROPIEDAD" },
                { value: "CONSIGNACION", text: "CONSIGNACIÓN" }
            ],
            optionsDesviacion: [
                { value: null, text: "Por Favor Seleccione una Opción" },
                { value: "SI", text: "SI" },
                { value: "NO", text: "NO" }
            ],
            errors: []
        };
    },
    computed: {
        getItems() {
            return this.$store.state.f.activeOrders;
        },
        activeOrderCount() {
            return this.totalRows;
        },
        getReceivedOrdersReport() {
            return this.$store.state.f.dataToReport;
        },
        getIsCreatedOrderState() {
            return this.$store.state.f.createdDataOrders;
        },
        getIsUpdatingState() {
            return this.$store.state.f.isUpdatingOrders;
        },
        getErrorCount() {
            if (this.errors.length > 0) return false;
            return true;
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
        info(item, index) {
            this.infoModal.title = `${item.sum_cod} -- ${item.sum_desc}`;
            this.infoModal.content = JSON.stringify(item, null, 2);
            this.infoModal._rowVariant = item._rowVariant;
            this.infoModal.index = index;
            this.activeOrder.SumCod = item.sum_cod;
            this.activeOrder.SumDesc = item.sum_desc;
            this.activeOrder.NroOrden = item.nroOrden;
            this.activeOrder.NroEntrada = item.nroEntrada;
            this.activeOrder.FechaEntrada = moment(item.fecEntrada).format(
                "YYYY-MM-DD"
            );
            this.activeOrder.ProveedorNit = item.nitProvider;
            this.activeOrder.NombreProveedor = item.provider;
            this.activeOrder.NroFactura =
                item.invoice != null ? item.invoice : null;
            this.activeOrder.NroRemision =
                item.remission != null ? item.remission : null;
            this.activeOrder.Concentracion = item.concentration;
            this.activeOrder.CodPForm = item.codPForm;
            this.activeOrder.ForFarmaceutica = item.pForm;
            this.activeOrder.Cantidad = item.quantity;
            this.activeOrder.TamMuestra = item.sampleSize;
            this.activeOrder.Lote = item.lot != null ? item.lot : null;
            this.activeOrder.FechaVencimiento =
                item.dueDate != null
                    ? moment(item.dueDate).format("YYYY-MM-DD")
                    : "";
            this.activeOrder.RegSanitario =
                item.healthRegister != null ? item.healthRegister : "";
            this.activeOrder.EstadoRegSan =
                item.healthRegisterState != null
                    ? item.healthRegisterState
                    : null;
            this.activeOrder.PresComercial =
                item.pcomercial != null ? item.pcomercial : "";
            this.activeOrder.ProveedorDesc =
                item.sanitaryRegHolder != null ? item.sanitaryRegHolder : "";
            this.activeOrder.TipoProveedor =
                item.providerType != null ? item.providerType : null;
            this.activeOrder.MarcaDispositivo =
                item.deviceBrand != null ? item.deviceBrand : null;
            this.activeOrder.ClasiRiesgo = item.riskClasi;
            this.activeOrder.Embalaje =
                item.packaging != null ? item.packaging : null;
            this.activeOrder.InspDefecto =
                item.defectInspection != null ? item.defectInspection : null;
            this.activeOrder.TipoProducto =
                item.kindProduct != null ? item.kindProduct : null;
            this.activeOrder.Desviacion =
                item.deviation != null ? item.deviation : null;
            this.activeOrder.ObsDesviacion =
                item.deviationObs != null ? item.deviationObs : "";
            this.activeOrder.Temperatura =
                item.temperatura != null ? item.temperatura : "";
            this.activeOrder.SerieDispositivo =
                item.serieDispositivo != null ? item.serieDispositivo : "";
            this.activeOrder.VidaUtil =
                item.vidaUtil != null ? item.vidaUtil : "";

            this.$refs["info-modal"].show();
            //this.$root.$emit('bv::show::modal', this.infoModal.id, button)
        },
        hideModal(modal) {
            this.$store.commit("SET_CREATEDORDER", false);
            this.clearFields();
            this.$refs[`${modal}`].hide();
        },
        editShowModal(item, index) {
            this.$store.commit("SET_IS_UPDATINGORDERS", true);
            this.infoModal.title = `EDITANDO ${item.sum_cod} -- ${item.sum_desc}`;
            this.infoModal.content = JSON.stringify(item, null, 2);
            this.infoModal._rowVariant = item._rowVariant;
            this.infoModal.index = index;
            this.activeOrder.receptionId = item.receptionId;
            this.activeOrder.SumCod = item.sum_cod;
            this.activeOrder.SumDesc = item.sum_desc;
            this.activeOrder.NroOrden = item.nroOrden;
            this.activeOrder.NroEntrada = item.nroEntrada;
            this.activeOrder.FechaEntrada = moment(item.fecEntrada).format(
                "YYYY-MM-DD"
            );
            this.activeOrder.ProveedorNit = item.nitProvider;
            this.activeOrder.NombreProveedor = item.provider;
            this.activeOrder.NroFactura =
                item.invoice != null ? item.invoice : null;
            this.activeOrder.NroRemision =
                item.remission != null ? item.remission : null;
            this.activeOrder.Concentracion = item.concentration;
            this.activeOrder.CodPForm = item.codPForm;
            this.activeOrder.ForFarmaceutica = item.pForm;
            this.activeOrder.Cantidad = item.quantity;
            this.activeOrder.TamMuestra = item.sampleSize;
            this.activeOrder.Lote = item.lot != null ? item.lot : null;
            this.activeOrder.FechaVencimiento =
                item.dueDate != null
                    ? moment(item.dueDate).format("YYYY-MM-DD")
                    : "";
            this.activeOrder.RegSanitario =
                item.healthRegister != null ? item.healthRegister : "";
            this.activeOrder.EstadoRegSan =
                item.healthRegisterState != null
                    ? item.healthRegisterState
                    : null;
            this.activeOrder.PresComercial =
                item.pcomercial != null ? item.pcomercial : "";
            this.activeOrder.ProveedorDesc =
                item.sanitaryRegHolder != null ? item.sanitaryRegHolder : "";
            this.activeOrder.TipoProveedor =
                item.providerType != null ? item.providerType : null;
            this.activeOrder.MarcaDispositivo =
                item.deviceBrand != null ? item.deviceBrand : null;
            this.activeOrder.ClasiRiesgo = item.riskClasi;
            this.activeOrder.Embalaje =
                item.packaging != null ? item.packaging : null;
            this.activeOrder.InspDefecto =
                item.defectInspection != null ? item.defectInspection : null;
            this.activeOrder.TipoProducto =
                item.kindProduct != null ? item.kindProduct : null;
            this.activeOrder.Desviacion =
                item.deviation != null ? item.deviation : null;
            this.activeOrder.ObsDesviacion =
                item.deviationObs != null ? item.deviationObs : "";
            this.activeOrder.Temperatura =
                item.temperatura != null ? item.temperatura : "";
            this.activeOrder.SerieDispositivo =
                item.serieDispositivo != null ? item.serieDispositivo : "";
            this.activeOrder.VidaUtil =
                item.vidaUtil != null ? item.vidaUtil : "";
            this.activeOrder._rowVariant = item._rowVariant;

            this.$refs["edit-info-modal"].show();
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
        getActiveOrdersByDate() {
            let url = "farmacia/get/active-orders/";

            this.showOverlay = true;

            axios
                .get(
                    url + this.payload.initDate + "/to/" + this.payload.endDate
                )
                .then(result => {
                    if (result.data.status === 200) {
                        /* if (result.data.data.length > 0) { */
                        this.items = result.data.data;
                        this.totalRows = result.data.data.length;
                        this.showOverlay = false;
                        /* } */
                    } else {
                        this.showOverlay = true;
                    }
                })
                .catch(err => {
                    console.log(err + "Un error ha ocurrido");
                    this.showOverlay = false;
                });
        },
        saveActiveOrder() {
            let url = "farmacia/post/active-order";

            this.showOverlay = true;
            axios
                .post(url, this.activeOrder)
                .then(result => {
                    if (result.data.status === 201) {
                        this.$store.commit("SET_CREATEDORDER", true);
                        console.log("ok", result.data.data);
                        this.getActiveOrdersByDate();
                        this.$bvToast.toast("Orden Registrada", {
                            title: `RECEPCIÓN DE MEDICAMENTOS`,
                            variant: `success`,
                            solid: true
                        });
                        this.errors = [];
                        this.showOverlay = false;
                    }
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        this.errors = err.response.data.errors;
                        this.$bvToast.toast("Error Al Crear la Orden", {
                            title: `RECEPCIÓN DE MEDICAMENTOS`,
                            variant: `danger`,
                            solid: true
                        });
                        this.showOverlay = false;
                    }
                    this.showOverlay = false;
                });
        },
        editingActiveOrder() {
            this.$store.dispatch("editingActiveOrder", this.activeOrder);
        },
        clearFields() {
            this.activeOrder.RegSanitario = "";
            this.activeOrder.EstadoRegSan = null;
            this.activeOrder.PresComercial = "";
            this.activeOrder.MarcaDispositivo = "";
            this.activeOrder.ClasiRiesgo = "";
            this.activeOrder.TipoProveedor = null;
            this.activeOrder.Embalaje = null;
            this.activeOrder.InspDefecto = null;
            this.activeOrder.TipoProducto = null;
            this.activeOrder.Desviacion = null;
            this.activeOrder.ObsDesviacion = "";
            this.errors = [];
        }
    },
    created() {
        this.$store.commit("SET_INIT_DATE", moment().format("YYYY-MM-DD"));
        this.$store.commit("SET_END_DATE", moment().format("YYYY-MM-DD"));
        this.payload.initDate = this.$store.state.f.initDate;
        this.payload.endDate = this.$store.state.f.endDate;
    },
    beforeMount() {
        this.getActiveOrdersByDate();
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

<style>
.table tr td div {
    font-size: 12px !important;
}
</style>
