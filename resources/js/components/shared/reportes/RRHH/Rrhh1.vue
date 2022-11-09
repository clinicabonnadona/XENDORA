<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Seleccionar Mes:</label>
                                                <select v-model="dataToSearch.field1" class="form-control form-control-sm">
                                                    <option value="null" readonly disabled>Seleccione un Mes</option>
                                                    <option value="1">Enero</option>
                                                    <option value="2">Febrero</option>
                                                    <option value="3">Marzo</option>
                                                    <option value="4">Abril</option>
                                                    <option value="5">Mayo</option>
                                                    <option value="6">Junio</option>
                                                    <option value="7">Julio</option>
                                                    <option value="8">Agosto</option>
                                                    <option value="9">Septiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group pt-4">
                                                <button type="button" class="btn btn-primary" @click="fetchData()">
                                                    <i class="fa fa-search"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div v-show="loading" class="spinner-grow text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>

                                        <div class="col-md-2" v-if="dataExists" role="status">
                                            <div class="form-group pt-4">
                                                <export-excel :data="rows" :fields="fields" class="btn btn-success" worksheet = "My Worksheet" name="ReporteCumpleaños.xls">
                                                    <i class="fa fa-download"></i> Descargar Data
                                                </export-excel>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "rrhh1-component",
        data() {
            return {
                fields: {
                    'Documento': "DOC",
                    'Nombres': "NOMBRES",
                    'Apellidos': "APELLIDOS",
                    'Cargo': "CARGO",
                    'Telefono Movil': "tel_movi",
                    'Telefono Residencial': "tel_resi",
                    'Centro de Costo': "CENTRO_COSTO",
                    'Fecha Cumpleaños': "FECHA_CUMPL",
                },
                rows: [],
                dataToSearch: {
                    field1: null
                },
                dataExists: false,
                loading: false
            }
        },
        methods: {
            fetchData() {
                let me = this;
                let url = "/reportes/get-cumpleanios/" + me.dataToSearch.field1

                me.loading = true

                axios.get(url)
                    .then(res => {
                        me.rows = res.data.data
                        me.dataExists = true
                        me.loading = false
                    })
                    .catch(err => {
                        console.log(err)
                    })
            }
        }

    }
</script>

<style scoped>

</style>
