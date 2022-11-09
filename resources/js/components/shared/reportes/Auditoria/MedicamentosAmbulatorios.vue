<template>
    <b-container fluid>
        <b-row>
            <b-col md="12" sm="12" xl="12" lg="12">
                <b-card-group deck>
                    <b-card
                        header="Auditoria de Medicamentos Ambulatorios"
                        header-tag="header"
                    >
                        <div class="row">
                            <b-col cols="12">
                                <b-form-group
                                    id="fieldset-1"
                                    label="Ingrese el Número de Documento"
                                    label-for="input-1"
                                >
                                    <b-form-input id="input-1"
                                                  v-model="form.documento"
                                                  trim
                                                  @keypress.enter.prevent="getAuditoriaMedAmb"
                                    >
                                    </b-form-input>
                                </b-form-group>
                            </b-col>
                        </div>
                        <div class="row" v-if="dataExistAud">
                            <b-col cols="12">
                                <b-table
                                    :items="dataAudiMedAmb != [] ? dataAudiMedAmb : []"
                                    :fields="fieldsAudMedAmb"
                                    head-variant="dark"
                                    :current-page="currentPage"
                                    :per-page="perPage"
                                    :sort-by.sync="sortBy"
                                    :sort-desc.sync="sortDesc"
                                    :sort-direction="sortDirection"
                                    stacked="md"
                                    show-empty
                                    small
                                >
                                    <template #cell(CANTIDAD)="row" class="text-center">
                                        {{ new Intl.NumberFormat({maximumSignificantDigits: 0}).format(row.value) }}
                                    </template>
                                </b-table>
                            </b-col>
                        </div>
                    </b-card>
                </b-card-group>
            </b-col>
        </b-row>
        {{ clearTable }}
    </b-container>
</template>

<script>
export default {
    name: "MedicamentosAmbulatorios",
    data() {
        return {
            fieldsAudMedAmb: [
                { key: 'HISCKEY', label: 'DOCUMENTO', sortable: true },
                { key: 'HISTIPDOC', label: 'TIP DOC', sortable: true },
                { key: 'NOMBRE_PACIENTE', label: 'PACIENTE', sortable: true },
                { key: 'INGRESO', label: 'INGRESO', sortable: true },
                { key: 'FOLIO', label: 'FOLIO', sortable: true },
                { key: 'TIPO_ATENCION', label: 'TIP ATENCIÓN', sortable: true },
                { key: 'COD_SUM', label: 'COD SUM', sortable: true },
                { key: 'NOMBRE_SUMINISTRO', label: 'SUMINISTRO', sortable: true },
                { key: 'CANTIDAD', label: 'CANTIDAD', sortable: true },
                { key: 'FECHA', label: 'FECHA', sortable: true },
                { key: 'USUARIO', label: 'COLABORADOR', sortable: true },
            ],
            form: {
                documento: ''
            },
            dataExistAud: false,
            dataAudiMedAmb: [],
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
        }
    },
    computed: {
        clearTable () {
            if (this.form.documento == '' || this.form.documento == null)
            {
                return this.dataAudiMedAmb = []
            }
        }
    },
    methods: {
        getAuditoriaMedAmb() {
            let url = 'reportes/get-auditoria-med-amb/' + this.form.documento
            this.dataExistAud = false

            axios.get(url)
                .then(response => {

                    this.dataAudiMedAmb = response.data
                    this.dataExistAud = true
                    //console.log(response.data)
                })
                .catch(error => {
                    console.log(error)
                })

        }
    }
}
</script>

<style scoped>

</style>
