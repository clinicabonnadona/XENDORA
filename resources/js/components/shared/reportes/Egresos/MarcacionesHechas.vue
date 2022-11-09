<template>
    <b-container fluid>
        <b-row>
            <b-col cols="8" offset="2">
                <b-card header="Marcaciones Egreso">
                    <b-form @submit.prevent="getMarcacionesInfo(form.pctDoc)">
                        <b-input-group id="input-search" label="Ingresar Número de Documento:">
                            <b-form-input id="input-search"
                                     v-model="form.pctDoc"
                                     placeholder="Número de Documento"
                                     type="number"
                            ></b-form-input>
                            <b-input-group-append>
                                <b-button variant="info"
                                          @click="getMarcacionesInfo(form.pctDoc)"
                                >
                                    <i class="fas fa-search"></i>
                                    Buscar
                                </b-button>
                            </b-input-group-append>
                        </b-input-group>
                        <b-form-text id="input-search" class="mt-3">
                            El Documento debe Contener entre 5 y 20 Caracteres
                        </b-form-text>
                    </b-form>

                </b-card>
            </b-col>
        </b-row>

        <b-row class="mt-3">
            <b-col cols="8" offset="2">

                <b-form-textarea
                    id="textarea"
                    v-model="marcacionesEgresosArray.length > 0 ? marcacionesEgresosArray : ''"
                    placeholder="Marcaciones Aquí"
                    rows="8"
                    max-rows="9"
                    disabled
                >

                </b-form-textarea>
                <b-overlay :show="showOverlay" no-wrap></b-overlay>

            </b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: "MarcacionesHechas",
    data() {
        return {
            showOverlay: false,
            form: {
                pctDoc: ''
            },
            errors: [],
            marcacionesEgresosArray: []
        }
    },
    methods: {
        getMarcacionesInfo(pcte) {

            let url = 'reportes/get-marcaciones-egresos/'+ pcte +'/paciente'

            this.showOverlay = true

            if (this.validationForm(pcte))
            {

                var marcaciones = []
                var arrayMarcaciones = []

                axios.get(url)
                .then(response => {
                    //console.log(response.data)
                    if (response.status === 200)
                    {
                        arrayMarcaciones = response.data[0]

                        for (var i = 0; i < arrayMarcaciones.length; i++) {
                            marcaciones.push(arrayMarcaciones[i].disp + '. ' + arrayMarcaciones[i].observacion + '  -  ')
                        }

                        this.marcacionesEgresosArray = marcaciones.length > 0 ? marcaciones.join("").toString() : ''

                        this.showOverlay = false

                    } else {

                        this.$bvToast.toast(`No Se Han Encontrado Datos Con Este Documento`, {
                            title: 'Notificación',
                            autoHideDelay: 5000,
                            variant: 'info'
                        })
                        this.showOverlay = false
                    }

                })
                .catch(error => {
                    console.log(error)
                })

            } else {

                this.$bvToast.toast(`${this.errors}`, {
                    title: 'Error',
                    autoHideDelay: 5000,
                    variant: 'danger'
                })
                //console.log('error')
                this.marcacionesEgresosArray = []
                this.showOverlay = false

            }


        },
        validationForm(pcteDcto) {

            this.errors = []

            if (pcteDcto)
            {

                if (pcteDcto.length < 5 || pcteDcto.length > 20)
                {
                    this.errors.push('Verifique que el número de documento')
                } else {
                    return true
                }

            } else {
                this.errors.push('No hay Ingresado Ningún Número de Documento')
            }

        }
    }

}
</script>

<style scoped>

</style>
