<template>
    <b-overlay :show="showOverlay" rounded="sm">
        <b-table-simple hover small caption-top responsive>
            <caption> <i class="fas fa-bars"></i>
                COMPRAS MENSUALES PARA - {{ sumnomc !== '' || sumnomc !== null || sumnomc !== undefined ?  sumnomc : '' }}
            </caption>
            <b-thead head-variant="dark">
                <b-tr>
                    <b-th colspan="12" class="text-center border-right">MESES {{ new Date().getFullYear() }}</b-th>
                </b-tr>
                <b-tr>
                    <b-th v-for="(m, index) in monthsArray" :key="index"
                          :class="index === 11 ? 'border-right text-center' : ''">
                        {{ m.toUpperCase() }}
                    </b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr>
                    <b-td v-for="(re, index) in entradasEnd" :key="index" class="text-center">
                        <!-- {{ re != null ? re : 0 }} -->
                        <b-button   id="show-btn"
                                    size="sm"
                                    @click="showModal('bv-my-modal', re.mes, re.anio, re.sumcod)"
                                    :disabled="re === 0"
                        >
                            {{ re.compras != null ? re.compras : 0 }}
                        </b-button>
                    </b-td>
                    <b-modal    id="bv-my-modal"
                                size="lg"
                                header-bg-variant="success"
                                header-text-variant="light"
                                centered
                                title="Detalle de Factura"
                                ok-only
                                no-close-on-backdrop
                                no-close-on-esc
                    >

                        <b-overlay  id="overlay-background"
                                    :show="this.$store.state.r.isLoadingInvoices"
                                    variant="secondary"
                                    rounded="sm"
                        >
                            <b-row v-if="!this.$store.state.r.isLoadingInvoices">
                                <b-col>
                                    <b-table striped hover :items="getInvoiceDetailsComp" :fields="fields">
                                        <template #cell(compras)="row" class="text-center">
                                            {{ new Intl.NumberFormat({ maximumSignificantDigits: 0 }).format(row.value) }}
                                        </template>

                                        <template #cell(valor_factura)="row" class="text-center">
                                            {{ new Intl.NumberFormat("es-CO", { style: "currency", currency: "COP" }).format(row.value) }}
                                        </template>

                                    </b-table>
                                </b-col>
                            </b-row>
                        </b-overlay>

                        <template #modal-footer>
                                <b-button   variant="danger"
                                            @click="hideModal('bv-my-modal')"
                                >
                                    Cerrar
                                </b-button>
                        </template>

                    </b-modal>
                </b-tr>
            </b-tbody>
        </b-table-simple>
    </b-overlay>
</template>

<script>
export default {
    name: "EntradasDetail",
    props: {
        sumcod: String,
        sumnomc: String
    },
    data() {
        return {
            showOverlay: true,
            monthsArray: [],
            entradas: [],
            entradasEnd: [],
            payload: {
                sumcod: '',
                mes: '',
                anio: '',
            },
            fields: [
                { key: 'factura', label: 'FACTURA'},
                { key: 'compras', label: 'CANTIDAD'},
                { key: 'proveedor', label: 'PROVEEDOR'},
                { key: 'fec_factura', label: 'FECHA FACTURA'},
                { key: 'valor_factura', label: 'VALOR EN FACTURA'},
            ]
        }
    },
    computed: {
        getInvoiceDetailsComp() {
            return this.$store.state.r.invoiceDetails
        },
        invoiceDetailsCount() {
            return this.$store.getters.suministrosCount
        }
    },
    mounted() {
        this.fetchData()
    },
    methods: {
        fetchData() {
            if (this.sumcod != null) {
                let url = 'admin/suministros/rotacion/entradas/' + this.sumcod

                axios.get(url)
                .then(response => {
                    //console.log(response.data)

                    if (response.status === 200)
                    {
                        this.entradas = response.data
                        var arrayAllMoths = []
                        var arrayAllEntradas = []

                        //Aquí Inicia el display de los utlimos 12 meses rotativos
                        var d = new Date()
                        var arrayMonths = [];
                        var monthName = new Array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO",
                            "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE")
                        d.setDate(1)
                        for (var i=0; i<=11; i++) {
                            arrayMonths.push(monthName[d.getMonth()] + ' ' + d.getFullYear())
                            d.setMonth(d.getMonth() - 1)
                        }
                        this.monthsArray = arrayMonths.reverse()
                        //Aquí finaliza el display de los utlimos 12 meses rotativos


                        arrayAllMoths = this.monthsArray

                        //Ciclo para transponer datos, validacion entre array meses y array rotacion
                        //Se asigna 0 donde no haya rotación
                        for (var x = 0; x < arrayAllMoths.length; x++) {
                            var match = false
                            for (var y = 0; y < this.entradas.length; y++) {
                                if (arrayAllMoths[x] === ((monthName[this.entradas[y].MES - 1]) + ' ' + this.entradas[y].ANIO)) {
                                    match = true;
                                    arrayAllEntradas[x] = {
                                        compras: parseInt(this.entradas[y].COMPRAS),
                                        mes: parseInt(this.entradas[y].MES),
                                        anio: parseInt(this.entradas[y].ANIO),
                                        sumcod: this.entradas[y].COD_SUM,
                                    }
                                    break
                                }
                            }
                            if (!match) {
                                arrayAllEntradas.push(0)
                            }
                        }

                        this.entradasEnd = arrayAllEntradas
                        //console.log(arrayAllEntradas)
                        //End Ciclo de Transposición

                    } else {
                        console.log("No hay datos que mostrar")
                    }
                    this.showOverlay = false
                })
                .catch(error => {
                    console.log(error)
                })
            }
        },
        showModal(modal, mes, anio, sumcod) {
            this.$bvModal.show(`${modal}`)
            this.payload = {
                sumcod,
                anio,
                mes,
            }
            this.$store.dispatch('getInvoiceDetails', this.payload)
            // console.log('mes:' + mes + ' ' + 'anio:' + anio + ' ' + 'sumcod:' + sumcod)
        },
        hideModal(modal) {
            this.$bvModal.hide(`${modal}`)
        },
    }
}
</script>

<style scoped>

</style>
