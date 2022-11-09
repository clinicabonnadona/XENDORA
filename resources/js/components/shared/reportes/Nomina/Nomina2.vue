<template>
    <b-container class="mt-5">
        <b-row>
            <b-col md="12">
                <b-card>
                    <b-row>
                        <b-col md="12">
                            <b-form enctype="multipart/form-data">

                                <b-row>

                                    <b-col md="10" sm="12">
                                        <b-form-group
                                            id="fieldset-1"
                                            label="Fecha Inicial:"
                                            label-for="input-1"
                                        >
                                            <b-form-datepicker
                                                id="input-1"
                                                v-model="dataToSearch.field1"
                                                placeholder="Seleccionar Fecha"
                                                class="mb-2"
                                                reset-button
                                                label-reset-button="Limpiar"
                                            >
                                            </b-form-datepicker>

                                        </b-form-group>
                                    </b-col>

                                    <b-col md="2" sm="12">
                                        <b-button variant="primary"
                                                  class="mt-4"
                                                  type="button"
                                                  @click.prevent="fetchData"
                                        >
                                            <i class="fa fa-search"></i> Buscar
                                        </b-button>
                                    </b-col>

                                </b-row>

                                <b-row>
                                    <b-col md="2" sm="12" v-if="loading">
                                        <div v-if="loading" role="status">
                                            <b-spinner variant="primary" type="grow" label="Spinning"></b-spinner>
                                        </div>
                                    </b-col>

                                    <b-col md="2" sm="12" v-if="dataExists" role="status">
                                        <b-form-group
                                            id="fieldset-4"
                                        >
                                            <export-excel :data="rows" :fields="fields" class="btn btn-success"
                                                          worksheet="My Worksheet" name="ReporteConceDuplicado.xls">
                                                <i class="fa fa-download"></i> Descargar Data
                                            </export-excel>
                                        </b-form-group>
                                    </b-col>

                                </b-row>

                            </b-form>
                        </b-col>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
    </b-container>

</template>

<script>
export default {
    name: "Nomina2",
    data() {
        return {
            fields: {
                'Codigo Empleado': "cod_empl",
                'Nombres': "nom_empl",
                'Apellidos': "ape_empl",
                'Cod Cargo': "cod_carg",
                'Cargo': "nom_carg",
                'Cod Ccos': "cod_ccos",
                'Nombre Ccos': "nom_ccos",
                'Sueldo Basico': {
                    field: "sue_basi",
                    callback: (value) => {
                        return Math.trunc(value);
                    }
                },
                'Cod Conc': "cod_conc",
                'Nombre Conc': "nom_conc",
                'Fecha Acum': "fec_acum",
                'Cantidad': "cantidad"
            },
            rows: [],
            dataToSearch: {
                field1: ''
            },
            dataExists: false,
            loading: false,
            errors: []
        }
    },
    methods: {
        fetchData() {
            let me = this;
            let url = "report2getdata/" + me.dataToSearch.field1;

            me.loading = true

            if (this.validateForm()) {
                axios.get(url)
                    .then(response => {
                        //console.log(res.data);
                        me.rows = response.data.data
                        me.dataExists = true
                        me.loading = false
                    })
                    .catch(err => {
                        console.log(err);
                    })
            } else {
                this.$bvToast.toast(`${this.errors}`, {
                    title: 'Notificación',
                    variant: 'danger',
                    toaster: 'b-toaster-top-right',
                    solid: true,
                    appendToast: false
                })
                me.loading = false
                this.dataExists = false
            }

        },
        validateForm() {
            this.errors = []

            if (this.dataToSearch.field1) {
                return true
            }

            if (!this.dataToSearch.field1) {
                this.errors.push("Por Favor Seleccione una Fecha")
            }
        }
    }
}
</script>

<style scoped>

</style>
