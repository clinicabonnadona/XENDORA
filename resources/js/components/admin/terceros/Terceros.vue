<template>
    <div>
        <b-card header-tag="header" no-footer>

            <template v-slot:header>
                <div class="float-sm-right">
                    <b-button size="sm" variant="light" title="Recargar" @click="fetchTerceros()">
                        <i class="fas fa-redo"></i>
                    </b-button>
                    <b-dropdown size="sm" dropleft variant="link" toggle-class="text-decoration-none" no-caret
                                title="Acciones">
                        <template v-slot:button-content>
                            <i class="fas fa-ellipsis-v"></i><span class="sr-only">Search</span>
                        </template>
                        <b-dropdown-item href="#" @click="showModalCreatePermission('create-tercero-modal')"><i
                            class="fas fa-plus-circle"></i>
                            Crear Suministro
                        </b-dropdown-item>
                    </b-dropdown>
                </div>
            </template>

            <b-overlay :show="showOverlay" rounded="sm">
                <v-client-table :data="items" :columns="columns" :options="options">

                    <span slot="TerActNeg" slot-scope="{row}">
                        <p v-if="row.TerActNeg == 1">Si</p>
                        <p v-if="row.TerActNeg == 0">No</p>
                    </span>

                    <span slot="TerEst" slot-scope="{row}">
                        <i :class="row.TerEst == 1 ? 'fas fa-circle text-success' : ' fas fa-circle text-danger'"></i>
                    </span>

                    <span slot="acciones" slot-scope="{row}">
                        <b-dropdown size="sm" dropleft variant="link" toggle-class="text-decoration-none" no-caret>
                            <template v-slot:button-content>
                              <i class="fas fa-ellipsis-v"></i><span class="sr-only">Search</span>
                            </template>
                            <b-dropdown-item href="#" @click="showModalEditSuministro('edit-tercero-modal', row)">
                                <i class="fas fa-pen" style="color: dodgerblue"></i> Editar
                            </b-dropdown-item>
                            <b-dropdown-item href="#" @click="destroyTercero(form.id = row.id)">
                                <i class="fas fa-trash" style="color: red"></i> Desactivar
                            </b-dropdown-item>
                        </b-dropdown>
                    </span>
                </v-client-table>
            </b-overlay>
        </b-card>


        <!-- MODAL CREATE TERCERO -->
        <b-modal ref="create-tercero-modal" size="lg" id="create-tercero-modal" title="Creando Tercero...">

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Tipo de Documento:</label>
                    <b-form-select v-model="form.tipdoc_id">
                        <b-form-select-option :value="null">Seleccione el Tipo de Documento</b-form-select-option>
                        <b-form-select-option v-for="(tdocs, index) in tipdocs" :key="index" :value="tdocs.id">
                            {{ tdocs.TDocDesc }}
                        </b-form-select-option>
                    </b-form-select>
                </b-col>
                <b-col>
                    <label for="">Número de Documento:</label>
                    <b-form-input
                        id="input-2" v-model="form.TerCod" required placeholder="Número de Documento"
                    ></b-form-input>
                </b-col>
            </b-form-row>

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Razón Social:</label>
                    <b-form-input v-model="form.TerRazSoc" placeholder="Razón Social"></b-form-input>
                </b-col>
            </b-form-row>

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Dirección:</label>
                    <b-form-input v-model="form.TerDir" placeholder="Dirección"></b-form-input>
                </b-col>
                <b-col>
                    <label for="">Télefono:</label>
                    <b-form-input v-model="form.TerTel" placeholder="Télefono"></b-form-input>
                </b-col>
            </b-form-row>


            <template v-slot:modal-footer>
                <b-button class="mt-3" variant="outline-danger" @click="closeModals('create-tercero-modal')">
                    Cerrar
                </b-button>
                <b-button class="mt-3" variant="outline-success" @click="saveTercero()">
                    Guardar
                </b-button>
            </template>
        </b-modal>
        <!-- END MODAL CREATE TERCERO -->

        <!-- MODAL EDIT TERCERO -->
        <b-modal ref="edit-tercero-modal" size="lg" id="edit-tercero-modal" title="Creando Tercero...">

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Tipo de Documento:</label>
                    <b-form-input class="d-none" v-model="form.id"></b-form-input>
                    <b-form-select v-model="form.tipdoc_id">
                        <b-form-select-option :value="null">Seleccione el Tipo de Documento</b-form-select-option>
                        <b-form-select-option v-for="(tdocs, index) in tipdocs" :key="index" :value="tdocs.id">
                            {{ tdocs.TDocDesc }}
                        </b-form-select-option>
                    </b-form-select>
                </b-col>
                <b-col>
                    <label for="">Número de Documento:</label>
                    <b-form-input
                        id="input-2" v-model="form.TerCod" required placeholder="Número de Documento"
                    ></b-form-input>
                </b-col>
            </b-form-row>

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Razón Social:</label>
                    <b-form-input v-model="form.TerRazSoc" placeholder="Razón Social"></b-form-input>
                </b-col>
            </b-form-row>

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Dirección:</label>
                    <b-form-input v-model="form.TerDir" placeholder="Dirección"></b-form-input>
                </b-col>
                <b-col>
                    <label for="">Télefono:</label>
                    <b-form-input v-model="form.TerTel" placeholder="Télefono"></b-form-input>
                </b-col>
            </b-form-row>

            <b-form-row class="mb-3">
                <b-col>
                    <label for="">Aprobar para Negociar:</label>
                    <b-form-select v-model="form.TerActNeg">
                        <b-form-select-option :value="null">Seleccione una opción</b-form-select-option>
                        <b-form-select-option value="1">Si</b-form-select-option>
                        <b-form-select-option value="0">No</b-form-select-option>
                    </b-form-select>
                </b-col>
            </b-form-row>


            <template v-slot:modal-footer>
                <b-button class="mt-3" variant="outline-danger" @click="closeModals('edit-tercero-modal')">
                    Cerrar
                </b-button>
                <b-button class="mt-3" variant="outline-success" @click="editTercero()">
                    Guardar
                </b-button>
            </template>
        </b-modal>
        <!-- END MODAL CREATE TERCERO -->

    </div>
</template>

<script>
export default {
    name: "Terceros",
    data() {
        return {
            items: [],
            columns: ['TerCod', 'tipdoc.TDocDesc', 'TerRazSoc', 'TerDir', 'TerTel', 'TerActNeg', 'TerEst', 'acciones'],
            options: {
                headings: {
                    TerCod: 'Cod. Tercero',
                    "tipdoc.TDocDesc": 'Tipo Documento',
                    TerRazSoc: 'Razón Social',
                    TerDir: 'Dirección',
                    TerTel: 'Telefono',
                    TerActNeg: 'Activo para Negociar?',
                    TerEst: 'Estado',
                    acciones: 'Acciones'
                },
                sortable: ['TerCod', 'tipdoc.TDocDesc', 'TerRazSoc', 'TerDir', 'TerTel', 'TerActNeg',],
                filterable: ['TerCod', 'tipdoc.TDocDesc', 'TerRazSoc', 'TerDir', 'TerTel', 'TerActNeg',],
                perPage: 10,
            },
            tipdocs: [],
            form: {
                id: '',
                TerCod: '',
                tipdoc_id: null,
                TerRazSoc: '',
                tipterc_id: null,
                TerDir: '',
                TerTel: '',
                TerActNeg: null,
                TerEst: ''
            },
            errors: [],
            showOverlay: false
        }
    },
    created() {
        this.fetchTerceros();
        this.fetchTipDocs();
    },
    methods: {
        showModalCreatePermission(modal) {
            this.$refs[`${modal}`].show();
        },
        closeModals(modal) {
            this.$refs[`${modal}`].hide();
            this.form.id = '';
            this.form.tipdoc_id = null;
            this.form.TerCod = '';
            this.form.TerRazSoc = '';
            this.form.TerDir = '';
            this.form.TerTel = '';
            this.form.TerActNeg = '';
        },
        showModalEditSuministro(modal, data) {
            this.$refs[`${modal}`].show();
            this.form.id = data.id;
            this.form.tipdoc_id = data.tipdoc_id;
            this.form.TerCod = data.TerCod;
            this.form.TerRazSoc = data.TerRazSoc;
            this.form.TerDir = data.TerDir;
            this.form.TerTel = data.TerTel;
            this.form.TerActNeg = data.TerActNeg;
        },
        fetchTerceros() {
            let url = 'admin/terceros';

            this.showOverlay = true;

            axios.get(url).then(response => {
                this.items = response.data;
                this.errors = [];
                this.showOverlay = false;
            }).catch(error => {
                console.log(error);
            });
        },
        fetchTipDocs() {
            let url = 'admin/terceros/tdocs';

            axios.get(url).then(response => {
                this.tipdocs = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        saveTercero() {
            let url = 'admin/terceros/savetercero';

            axios.post(url, this.form).then(response => {
                this.fetchTerceros();
                this.form = {};
            }).catch(error => {
                console.log(error);
            });
        },
        editTercero() {
            let url = 'admin/terceros/updatetercero';

            axios.post(url, this.form).then(response => {
                this.fetchTerceros();
                this.form = {};
                //console.log(response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        destroyTercero() {
            let url = 'admin/terceros/delete';

            axios.post(url, this.form).then(response => {
                this.fetchTerceros();
                //console.log(response.data);
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
