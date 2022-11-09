<template>
    <div>
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-card header-tag="header"
                no-footer
                border-variant="secondary"
                header-border-variant="secondary"
        >

            <template #header>
                <b-row>

                    <b-col lg="6" class="my-1">
                        <b-form-group
                            label="Búsqueda"
                            label-for="filter-input"
                            label-cols-sm="12"
                            label-cols-xl="3"
                            label-cols-md="12"
                            label-align-sm="center"
                            class="mb-0"
                        >
                            <b-input-group>
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="# Orden - Entrada"
                                ></b-form-input>

                                <b-input-group-append>
                                    <b-button :disabled="!filter" @click="filter = ''">
                                        <i class="fas fa-brush"></i>
                                    </b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </b-form-group>
                    </b-col>

                    <b-col lg="4" offset-lg="2" class="my-1">
                        <div class="float-sm-right">
                            <b-button size="sm"
                                      variant="warning"
                                      title="Recargar"
                                      pill
                                      class="button-times-icon"
                                      @click="fetchSuministros()">
                                <i class="fas fa-sync-alt times-icon"></i>
                            </b-button>
                            <b-button   size="sm"
                                        variant="primary"
                                        title="Crear Suministro"
                                        pill
                                        class="button-times-icon"
                                        @click="showModalCreatePermission('create-suministro-modal')">
                                <i class="fas fa-plus times-icon"></i> Crear Suministro
                            </b-button>
                        </div>
                    </b-col>
                </b-row>
            </template>

            <b-card-body>
                <b-row>
                    <b-col xl="12" class="my-1">
                        <b-table
                            :items="items"
                            :fields="fields"
                            :busy="showOverlay"
                            :per-page="perPage"
                            :current-page="currentPage"
                            :filter="filter"
                            :filter-included-fields="filterOn"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc"
                            :sort-direction="sortDirection"
                             responsive
                             hover
                             bordered
                            head-variant="dark"
                            small
                            show-empty
                            @filtered="onFiltered"
                        >
                            <template #table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Loading...</strong>
                                </div>
                            </template>

                            <template #cell(SumReg)="row">
                                <i class="fas fa-circle fa-2x"
                                            v-if="row.value == 1"
                                            :style="row.value == 1 ? 'color: green' : ''"></i>
                            </template>

                            <template   #cell(SumAltCos)="row">
                                        <i  class="fas fa-circle"
                                            v-if="row.value == 1"
                                            :style="row.value == 1 && row.value < 10000000 ?
                                                            'color: red' : row.value > 10000000 ? 'color: blue' : ''"
                                        ></i>
                            </template>

                            <template #cell(SumPreReg)="row">
                                <p v-if="row.value != 0">{{ new Intl.NumberFormat("es-CO", {
                                                                        minimumSignificantDigits: 1,
                                                                        style: "currency",
                                                                        currency:"COP"
                                                                    }
                                                            ).format(row.value) }}</p>

                            </template>

                            <template #cell(SumEst)="row">
                                <i class="fas fa-user-circle fa-2x"
                                    :style="row.value === 0 ? 'color: red' : 'color: green'"></i>
                            </template>

                            <template #cell(acciones)="row">
                                <b-dropdown size="sm" dropleft variant="link" toggle-class="text-decoration-none" no-caret>
                                    <template v-slot:button-content>
                                    <i class="fas fa-ellipsis-v"></i><span class="sr-only">Search</span>
                                    </template>
                                    <b-dropdown-item href="#" @click="showModalEditSuministro('edit-suministro-modal', row.item)">
                                        <i class="fas fa-pen" style="color: dodgerblue"></i> Editar
                                    </b-dropdown-item>
                                    <b-dropdown-item href="#" @click="destroySuministro(form.id = row.id)">
                                        <i class="fas fa-trash" style="color: red"></i> Desactivar
                                    </b-dropdown-item>
                                </b-dropdown>
                            </template>
                        </b-table>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col xl="4" offset-xl="4" class="my-1">
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="totalRow"
                            :per-page="perPage"
                            align="fill"
                            size="sm"
                            class="my-0"
                        ></b-pagination>
                    </b-col>
                </b-row>

            </b-card-body>
        </b-card>


        <!-- MODAL CREATE SUMINISTRO -->
        <b-modal ref="create-suministro-modal" size="xl" id="create-suministro-modal" title="Creando Suministro..." no-close-on-backdrop>

            <template #modal-header>
                <p class="m-0">CREAR SUMINISTRO</p>
            </template>

            <template #default>
                <b-container fluid class="mt-2">
                    <b-row>
                        <b-col cols="12">

                            <b-form autocomplete="off">
                                <b-row>

                                    <b-col>
                                        <b-form-group label="Código Suministro:">
                                            <b-form-input
                                                id="input-1"
                                                autofocus
                                                v-model="form.SumCod"
                                                @keyup="upper($event)"
                                                @keydown.tab.exact="validateSumCod($event)"
                                                placeholder="Código del Suministro" autocomplete="off"
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>

                                    <b-col>
                                        <b-form-group label="Nombre Generico:">
                                            <b-form-input
                                                id="input-2"
                                                v-model="form.SumNomG"
                                                @keyup="upper($event)"
                                                :disabled="!createMode"
                                                placeholder="Nombre Generico del Suministro"
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>

                                    <b-col>
                                        <b-form-group label="Nombre Comercial:">
                                            <b-form-input
                                                id="input-3"
                                                v-model="form.SumNomC"
                                                :disabled="!createMode"
                                                @keyup="upper($event)"
                                                placeholder="Nombre Comercial del Suministro"
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>

                                </b-row>

                                <b-row>
                                    <b-col>
                                        <b-form-group label="Forma Farmaceutica:">
                                            <v-select
                                                    v-model="form.formaFar"
                                                    :reduce="formasf => formasf.id"
                                                    :options="formasf"
                                                    :disabled="!createMode"
                                                    label="FormaFDesc"
                                                    placeholder="Seleccione la Forma para este Suministro"
                                            />
                                        </b-form-group>
                                    </b-col>

                                    <b-col>
                                        <b-form-group label="Presentación:">
                                            <v-select
                                                    v-model.trim="form.formaPre"
                                                    :reduce="formaspre => formaspre.id"
                                                    :options="formaspre"
                                                    :disabled="!createMode"
                                                    label="FormaPDesc"
                                                    placeholder="Seleccione la Presentación"
                                            />
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="4">
                                        <b-form-group label="Pos No Pos:">
                                            <b-form-select v-model="form.SumPosNoPos" :disabled="!createMode">
                                                <b-form-select-option :value="null" disabled>-- Seleccione una Opción --</b-form-select-option>
                                                <b-form-select-option value="POS">POS</b-form-select-option>
                                                <b-form-select-option value="NoPos">NoPOS</b-form-select-option>
                                            </b-form-select>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="4">
                                        <b-form-group label="Regulado?:">
                                            <b-form-select v-model="form.SumReg" :disabled="!createMode">
                                                <b-form-select-option :value="null" disabled>-- Seleccione una Opción --</b-form-select-option>
                                                <b-form-select-option value="1">Si</b-form-select-option>
                                                <b-form-select-option value="0">No</b-form-select-option>
                                            </b-form-select>
                                        </b-form-group>
                                    </b-col>

                                    <b-col v-if="form.SumReg == 1" :disabled="!createMode">
                                        <b-form-group label="$ Precio Regulado:">
                                            <currency-input class="form-control form-control-sm"
                                                        v-model="form.SumPreReg"
                                                        required
                                                        placeholder="$ Precio Regulado"
                                            >
                                            </currency-input>
                                        </b-form-group>
                                    </b-col>

                                </b-row>

                                <hr v-if="errors.length > 0">

                            </b-form>

                        </b-col>

                    </b-row>
                </b-container>
            </template>



            <b-form-row>
                <h6 v-if="errors.length > 0">Valide Los Siguientes Errores:</h6>
                <ul>
                    <li v-for="(error, indice) in errors" :key="indice">
                        {{ error }}
                    </li>
                </ul>
            </b-form-row>



            <template v-slot:modal-footer>
                <b-button class="mt-3" variant="danger" @click="closeModals('create-suministro-modal')">
                    Cerrar
                </b-button>
                <b-button class="mt-3" variant="success" @click="saveSuministro()" :disabled="!createMode">
                    Guardar
                </b-button>
            </template>
        </b-modal>
        <!-- END MODAL CREATE SUMINISTRO -->


        <!-- MODEL EDIT SUMINISTRO -->
        <b-modal ref="edit-suministro-modal" size="xl" id="edit-suministro-modal" title="Editando Suministro..." no-close-on-backdrop>

            <b-form autocomplete="off" class="p-3">
                <b-form-row class="mb-3">
                    <b-col>
                        <label>Código Suministro:</label>
                        <b-form-input
                            id="input-1" v-model="form.SumCod" required placeholder="Código del Suministro" autocomplete="off"
                        ></b-form-input>
                    </b-col>
                    <b-col>
                        <label>Nombre Generico:</label>
                        <b-form-input
                            id="input-2" v-model="form.SumNomG" required placeholder="Nombre Generico del Suministro"
                        ></b-form-input>
                    </b-col>
                    <b-col>
                        <label>Nombre Comercial:</label>
                        <b-form-input
                            id="input-3" v-model="form.SumNomC" required placeholder="Nombre Comercial del Suministro"
                        ></b-form-input>
                    </b-col>
                </b-form-row>

                <b-form-row class="mb-3">
                    <b-col>
                        <label>Forma Farmaceutica:</label>
                        <v-select v-model="form.formaFar" :reduce="formasf => formasf.id"
                                  :options="formasf" label="FormaFDesc" placeholder="Seleccione la Forma para este Suministro"/>
                    </b-col>
                    <b-col>
                        <label>Presentación:</label>
                        <v-select v-model="form.formaPre" :reduce="formaspre => formaspre.id"
                                  :options="formaspre" label="FormaPDesc" placeholder="Seleccione la Presentación"/>
                    </b-col>
                </b-form-row>

                <b-form-row class="mb-3">
                    <b-col>
                        <label>Pos No Pos:</label>
                        <b-form-select v-model="form.SumPosNoPos">
                            <b-form-select-option :value="null" disabled>-- Seleccione una Opción --</b-form-select-option>
                            <b-form-select-option value="POS">POS</b-form-select-option>
                            <b-form-select-option value="NoPos">NoPOS</b-form-select-option>
                        </b-form-select>
                    </b-col>
                    <b-col>
                        <label>Regulado?:</label>
                        <b-form-select v-model="form.SumReg" @change="form.SumReg == 0 ? form.SumPreReg = '' : ''">
                            <b-form-select-option :value="null" disabled>-- Seleccione una Opción --</b-form-select-option>
                            <b-form-select-option value="1">Si</b-form-select-option>
                            <b-form-select-option value="0">No</b-form-select-option>
                        </b-form-select>
                    </b-col>
                    <b-col v-if="form.SumReg == 1">
                        <label>$ Precio Regulado:</label>
                        <currency-input class="form-control form-control-sm"
                                        v-model="form.SumPreReg"
                                        required
                                        placeholder="$ Precio Regulado"
                        >
                        </currency-input>
                        <!-- <b-form-input
                            id="input-4" v-model="form.SumPreReg" required placeholder="$ Precio Regulado"
                        ></b-form-input> -->
                    </b-col>
                </b-form-row>
            </b-form>


            <template v-slot:modal-footer>
                <b-button class="mt-3" variant="danger" @click="closeModals('edit-suministro-modal')">
                    Cerrar
                </b-button>
                <b-button class="mt-3" variant="warning" @click="editSuministro()">
                    Editar
                </b-button>
            </template>
        </b-modal>
        <!-- END MODEL EDIT SUMINISTRO -->

    </div>
</template>

<script>

import NoConnectionAlert from "../../shared/alerts/NoConnectionAlert";

export default {
    name: "Suministros",
    components: {
        NoConnectionAlert
    },
    data() {
        return {
            onLine: navigator.onLine,
            items: [],
            fields: [
                { key: "SumCod", label: "Cod. Sum", class: "text-center" },
                { key: "SumNomG", label: "Nombre Generico", class: "text-center" },
                { key: "SumNomC", label: "Nombre Comercial", class: "text-center" },
                { key: "formafar.FormaFDesc", label: "Forma Farmaceutica", class: "text-center" },
                { key: "formapre.FormaPDesc", label: "Presentación", class: "text-center" },
                { key: "SumPosNoPos", label: "Pos NoPos", class: "text-center" },
                { key: "SumReg", label: "Regulado?", class: "text-center" },
                { key: "SumPreReg", label: "Precio Regulado", class: "text-center" },
                { key: "SumAltCos", label: "A.Costo?", class: "text-center" },
                { key: "SumEst", label: "Estado", class: "text-center" },
                { key: "acciones", label: "Acciones", class: "text-center" },
            ],
            currentPage: 1,
            perPage: 10,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
            formasf: [],
            formaspre: [],
            totalRow: 1,
            form: {
                SumCod: '',
                SumNomG: '',
                SumNomC: '',
                formaFar: null,
                formaPre: null,
                SumPosNoPos: null,
                SumReg: null,
                SumPreReg: ''
            },
            errors: [],
            showOverlay: false,
            showPreRegInput: false,
            createMode: false
        }
    },
    created() {
        this.fetchSuministros()
        this.fetchFormaFarmaceutica()
        this.fetchFormaPresentacion()
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
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
        showModalCreatePermission(modal) {
            this.$refs[`${modal}`].show()
        },
        closeModals(modal) {
            this.$refs[`${modal}`].hide();
            this.clearSuministroForm()
            this.errors = []
        },
        showModalEditSuministro(modal, data) {
            this.$refs[`${modal}`].show();
            this.form.id = data.id;
            this.form.SumCod = data.SumCod;
            this.form.SumNomG = data.SumNomG;
            this.form.SumNomC = data.SumNomC;
            this.form.formaFar = data.formafar.id;
            this.form.formaPre = data.formapre.id;
            this.form.SumPosNoPos = data.SumPosNoPos;
            this.form.SumReg = data.SumReg;
            this.form.SumPreReg = data.SumPreReg;
            console.log(data)
        },
        fetchSuministros() {
            let url = 'admin/suministros';

            this.showOverlay = true;

            axios.get(url).then(response => {
                this.items = response.data;
                this.totalRow = response.data.length
                this.errors = [];
                this.showOverlay = false;
            }).catch(error => {
                console.log(error);
            });
        },
        fetchFormaFarmaceutica() {
            let url = 'admin/suministros/getformaf';

            axios.get(url).then(response => {
                this.formasf = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        fetchFormaPresentacion() {
            let url = 'admin/suministros/getformapre';

            axios.get(url).then(response => {
                this.formaspre = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        validateSumCod(e) {
            let url = 'admin/suministros/validate/exists/suministro/'+ e.target.value;

            axios.get(url)
            .then(response => {
                if (response.status == 200) {
                    this.createMode = false
                    this.$bvToast.toast(`${response.data.msg}`, {
                        title: 'Suministro',
                        variant: 'danger',
                        solid: true,
                        toaster: 'b-toaster-bottom-right'
                    })
                } else {
                    this.createMode = true
                    console.log('ok')
                }
            })
        },
        saveSuministro() {
            let url = 'admin/suministros/savesuministro';


            if (this.validateForm()) {
                axios.post(url, this.form)
                    .then(response => {
                        if (response.status == 201) {
                            this.$bvToast.toast(`${response.data.msg}`, {
                                title: 'Suministro',
                                variant: 'success',
                                solid: true,
                                toaster: 'b-toaster-bottom-right'
                            })
                            this.fetchSuministros()
                            this.clearSuministroForm()
                        } else if (response.status == 200) {
                            this.$bvToast.toast(`${response.data.msg}`, {
                                title: 'Suministro',
                                variant: 'danger',
                                solid: true,
                                toaster: 'b-toaster-bottom-right'
                            });
                        }

                    }).catch(error => {
                    console.log(error);
                })
            }
        },
        editSuministro() {
            let url = 'admin/suministros/updatesuministro';

            axios.post(url, this.form).then(response => {
                //console.log(response.data);
                this.fetchSuministros();
                this.clearSuministroForm()
            }).catch(error => {
                console.log(error);
            })
        },
        destroySuministro() {
            let url = 'admin/suministros/delete';

            axios.post(url, this.form).then(response => {
                //console.log(response.data);
                this.fetchSuministros();
            }).catch(error => {
                console.log(error);
            });
        },
        upper(e) {
            return e.target.value = e.target.value.toUpperCase()
        },
        validateForm() {

            if (this.form.SumNomC && this.form.SumNomC && this.form.SumNomG && this.form.formaFar) {
                return true
            }

            this.errors = []

            if(!this.form.SumCod) {
                this.errors.push('Por Favor Ingrese el Código del Suministro')
            }
            if(!this.form.SumNomC) {
                this.errors.push('Por Favor Ingrese el Nombre Genérico')
            }
            if(!this.form.SumNomG) {
                this.errors.push('Por Favor Ingrese el Nombre Comercial')
            }
            if(!this.form.formaFar) {
                this.errors.push('Por Favor Seleccione la Forma Farmacéutica')
            }

        },
        clearSuministroForm() {
            this.form.id = '';
            this.form.SumCod = '';
            this.form.SumNomG = '';
            this.form.SumNomC = '';
            this.form.formaFar = null;
            this.form.formaPre = null;
            this.form.SumPosNoPos = null;
            this.form.SumReg = null;
            this.form.SumPreReg = 0;
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRow = filteredItems.length
            this.currentPage = 1
        },
        updateOnlineStatus(e) {
            const {type} = e
            this.onLine = type === 'online'
        },
    }
}
</script>

<style scoped>

</style>
