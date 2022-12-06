<template>
    <b-overlay :show="showOverlay" rounded="sm">
        <b-table-simple hoverhover small responsive>
            <b-thead>
                <b-tr>
                    <b-th variant="dark" colspan="12" class="text-center">
                        VARIABLES DE ROTACIÓN
                    </b-th>
                </b-tr>
                <b-tr>
                    <b-th variant="dark" class="text-center"
                        >SALDOS FARMACIAS</b-th
                    >
                    <b-th variant="dark" class="text-center"
                        >SALDO CENTRAL MEZCLAS</b-th
                    >
                    <b-th variant="dark" class="text-center"
                        >SALDOS CARROS DE PARO</b-th
                    >
                    <b-th variant="dark" class="text-center"
                        >PROMEDIO ROTACIÓN</b-th
                    >
                    <b-th variant="success" class="text-center">MIN</b-th>
                    <b-th variant="danger" class="text-center">MAX</b-th>
                    <b-th variant="dark" class="text-center"
                        >$ ÚLTIMO VALOR COMPRA</b-th
                    >
                    <b-th variant="dark" class="text-center"
                        >ALERTA SOBRE STOCK</b-th
                    >
                    <b-th variant="dark" class="text-center"
                        >DIAS DE INVENTARIO</b-th
                    >
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr>
                    <b-td class="text-center">{{ rotacion.sumBalance }}</b-td>
                    <b-td class="text-center">{{
                        rotacion.sumBalanceCentralM
                    }}</b-td>
                    <b-td class="text-center">{{
                        rotacion.sumBalanceCrashCart
                    }}</b-td>
                    <b-td class="text-center">{{ rotacion.averageValue }}</b-td>
                    <b-td variant="success" class="text-center">{{
                        rotacion.minValue
                    }}</b-td>
                    <b-td variant="danger" class="text-center">{{
                        rotacion.maxValue
                    }}</b-td>
                    <b-td class="text-center">{{
                        rotacion.sumLastPrice | currency("$", 0)
                    }}</b-td>
                    <b-td
                        :variant="
                            rotacion.stockAlert == 1
                                ? 'warning'
                                : rotacion.stockAlert == 0
                                ? 'danger'
                                : ''
                        "
                        class="text-center"
                    >
                        {{
                            rotacion.stockAlert == 1
                                ? "SOBRE STOCK"
                                : rotacion.stockAlert == 0
                                ? "BAJO ESTOCK"
                                : ""
                        }}
                    </b-td>
                    <b-td class="text-center">
                        {{ rotacion.inventoryDays }}
                    </b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>

        <hr />

        <b-table-simple hover small caption-top responsive>
            <caption>
                <i class="fas fa-bars"></i>
                ROTACIÓN MENSUAL PARA -
                {{
                    sumnomc != "" || sumnomc != null || sumnomc != undefined
                        ? sumnomc
                        : ""
                }}
            </caption>
            <colgroup>
                <col />
                <col />
            </colgroup>
            <colgroup>
                <col />
                <col />
                <col />
            </colgroup>
            <colgroup>
                <col />
                <col />
            </colgroup>
            <b-thead head-variant="dark">
                <b-tr>
                    <b-th colspan="13" class="text-center border-right"
                        >MESES {{ new Date().getFullYear() }}</b-th
                    >
                    <!--                    <b-th colspan="6"></b-th>-->
                </b-tr>
                <b-tr>
                    <b-th
                        v-for="(m, index) in monthsArray"
                        :key="index"
                        :class="index == 12 ? 'border-right text-center' : ''"
                    >
                        {{ m.toUpperCase() }}
                    </b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr>
                    <b-td
                        v-for="(re, index) in rotacion.rotations"
                        :key="index"
                        class="text-center"
                    >
                        {{ re.quantity != null ? re.quantity : 0 }}
                    </b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>
    </b-overlay>
</template>

<script>
import RotacionVariables from "./RotacionVariables";
export default {
    name: "RotacionDetail",
    components: { RotacionVariables },
    props: {
        sumcod: String,
        sumnomc: String
    },
    data() {
        return {
            showOverlay: true,
            monthsArray: [],
            rotacion: [],
            rotacionEnd: [],
            oRotacion: [],
            otherRotacion: {
                saldo: 0,
                promedio: 0,
                min: 0,
                max: 0,
                valUltMes: 0,
                stockAlert: 0
            }
            //testValue: this.$store.state.saldo
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            if (this.sumcod != null) {
                let url =
                    "admin/suministros/rotacion/meses/msreso/" + this.sumcod;
                this.showOverlay = true;

                axios
                    .get(url)
                    .then(response => {
                        if (response.status == 200) {
                            this.rotacion = response.data.data[0];

                            //Aquí Inicia el display de los utlimos 12 meses rotativos
                            var d = new Date();
                            var arrayMonths = [];
                            var monthName = new Array(
                                "ENERO",
                                "FEBRERO",
                                "MARZO",
                                "ABRIL",
                                "MAYO",
                                "JUNIO",
                                "JULIO",
                                "AGOSTO",
                                "SEPTIEMBRE",
                                "OCTUBRE",
                                "NOVIEMBRE",
                                "DICIEMBRE"
                            );
                            d.setDate(1);
                            for (var i = 0; i <= 12; i++) {
                                arrayMonths.push(
                                    monthName[d.getMonth()] +
                                        " " +
                                        d.getFullYear()
                                );
                                d.setMonth(d.getMonth() - 1);
                            }
                            this.monthsArray = arrayMonths.reverse();
                            //Aquí finaliza el display de los utlimos 12 meses rotativos

                            this.showOverlay = false;
                        } else if (response.status == 204) {
                            this.showTableRotacion = false;
                            this.showOverlay = false;
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
        }
    }
};
</script>

<style scoped></style>
