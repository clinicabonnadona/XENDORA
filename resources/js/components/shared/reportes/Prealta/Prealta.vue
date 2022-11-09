<template>
    <b-container fluid>

        <b-row>
            <b-col md="12" sm="12" xl="12" lg="12">
                <b-row class="mb-3">
                    <b-col md="12" xl="12" lg="12">
                        <b-button variant="warning" @click="getPacientesConPrealta" title="Recargar">
                            <i class="fas fa-redo"></i>
                        </b-button>
                    </b-col>
                </b-row>
                <b-row class="mb-3">
                    <b-col md="12" xl="12" lg="12" v-if="dataExist">
                        <export-excel :data="pacientesPrealta" :fields="fields" class="btn btn-success"
                                      worksheet="My Worksheet" name="ReportePrealta.xls">
                            <i class="fa fa-download"></i> Descargar Reporte
                        </export-excel>
                    </b-col>

                    <b-col md="12" xl="3" lg="3" v-if="!dataExist">
                        <h5>No Se Encontraron Pacientes con Pre-Alta Médica</h5>
                    </b-col>
                </b-row>

                <hr>

                <b-row v-if="dataExist">
                    <b-col md="4" xl="4" lg="4" sm="12" v-for="(item, index) in pacientesPrealta" :key="index">
                        <b-list-group>
                            <b-list-group-item>
                                <p>
                                    NÚMERO DE DOCUMENTO: <strong>{{ item.CODIGO }}</strong>
                                </p>
                                <ul>
                                    <li>Fecha de Ingreso: {{ item.FECHA }}</li>
                                    <li>Consecutivo de Ingreso: {{ item.INGRESO }}</li>
                                    <li>Cama: {{ item.CAMA }}</li>
                                    <li>Pabellón: {{ item.PABELLON }}</li>
                                    <li>Torre: {{ item.TORRE }}</li>
                                </ul>
                            </b-list-group-item>
                        </b-list-group>
                    </b-col>
                </b-row>
            </b-col>

        </b-row>

    </b-container>
</template>

<script>
export default {
    name: "Prealta",
    data() {
        return {
            pacientesPrealta: [],
            dataExist: false,
        }
    },
    created() {
        this.getPacientesConPrealta()
    },
    methods: {
        getPacientesConPrealta() {

            let apiURL = 'reportes/get-pre-alta'
            this.dataExist = false

            axios.get(apiURL)
            .then(response => {
                if (response.status == 200)
                {
                    this.pacientesPrealta = response.data[0]
                    this.dataExist = true
                } else if (response.status == 204) {
                    this.dataExist = false
                }
            })
            .catch(error => {
                console.log(error)
            })
        },
    }
}
</script>

<style scoped>

</style>
