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
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Fecha Inicial:</label>
                                                <input type="date" v-model="dataToSearch.field1" class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Fecha Inicial:</label>
                                                <input type="date" v-model="dataToSearch.field2" class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-4">
                                                <button type="button" class="btn btn-primary" @click.prevent="fetchData()">
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
                                                <export-excel :data="rows" :fields="fields" class="btn btn-success" worksheet = "My Worksheet" name="ReportePermisosAprobados.xls">
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
        name: "rrhh2-component",
        data() {
            return  {
                fields: {
                    'Documento': "DOC",
                    'Nombres': "NOMBRES",
                    'Apellidos': "APELLIDOS",
                    'Contrato': "CONTRATO",
                    'Tipo de Permiso': "TIPO_PERMISO",
                    'Cargo': "CARGO",
                    'Centro de Costo': "CENTRO_COSTO",
                    'Fecha Inicio': "FEC_INI",
                    'Hora Salida': "HOR_SALI",
                    'Fecha Fin': "FEC_FIN",
                    'Hora Entrada': "HOR_ENTR",
                    'Total Horas': {
                        field: "HORAS",
                        callback: (value) => {
                            return Math.trunc(value);
                        }
                    },
                    'Estado Permiso': "ESTADO_PERMISO",
                    'Usuario que Autoriza': "AUTORIZA",
                },
                rows: [],
                dataToSearch: {},
                dataExists: false,
                loading: false
            }
        },
        methods: {
            fetchData() {
                let me = this;
                let url = "reportes/permisos-aprobados/" + me.dataToSearch.field1 + "/fin/" + me.dataToSearch.field2;

                me.loading = true;

                axios.get(url)
                    .then(res => {
                        me.rows = res.data[0]
                        me.dataExists = true
                        me.loading = false
                    })
                    .catch(err => {
                        console.log(err);
                    })
            }
        }
    }
</script>

<style scoped>

</style>
