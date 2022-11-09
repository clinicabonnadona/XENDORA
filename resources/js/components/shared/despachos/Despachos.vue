<template>
    <b-overlay :show="showOverlay" rounded="sm">
        <b-table-simple hover small caption-top responsive>
            <caption>
                <i class="fas fa-bars"></i>
                DESPACHOS MENSUALES PARA -
                {{
                    sumnomc != "" || sumnomc != null || sumnomc != undefined
                        ? sumnomc
                        : ""
                }}
            </caption>
            <b-thead head-variant="dark">
                <b-tr>
                    <b-th colspan="12" class="text-center border-right"
                        >MESES {{ new Date().getFullYear() }}</b-th
                    >
                </b-tr>
                <b-tr>
                    <b-th
                        v-for="(m, index) in monthsArray"
                        :key="index"
                        :class="index == 11 ? 'border-right text-center' : ''"
                    >
                        {{ m.toUpperCase() }}
                    </b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr>
                    <b-td
                        v-for="(re, index) in despachosEnd"
                        :key="index"
                        class="text-center"
                    >
                        {{ re != null ? re : 0 }}
                    </b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>
    </b-overlay>
</template>

<script>
export default {
    name: "Despachos",
    props: {
        sumcod: String,
        sumnomc: String
    },
    data() {
        return {
            showOverlay: true,
            monthsArray: [],
            despachos: [],
            despachosEnd: []
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            if (this.sumcod != null) {
                let url = "admin/suministros/rotacion/despachos/" + this.sumcod;

                axios
                    .get(url)
                    .then(response => {
                        //console.log(response.data)

                        if (response.status == 200) {
                            this.despachos = response.data;
                            var arrayAllMoths = [];
                            var arrayAllDespachos = [];

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
                            for (var i = 0; i <= 11; i++) {
                                arrayMonths.push(
                                    monthName[d.getMonth()] +
                                        " " +
                                        d.getFullYear()
                                );
                                d.setMonth(d.getMonth() - 1);
                            }
                            this.monthsArray = arrayMonths.reverse();
                            //Aquí finaliza el display de los utlimos 12 meses rotativos

                            arrayAllMoths = this.monthsArray;

                            //Ciclo para transponer datos, validacion entre array meses y array rotacion
                            //Se asigna 0 donde no haya rotación
                            for (var x = 0; x < arrayAllMoths.length; x++) {
                                var match = false;
                                for (
                                    var y = 0;
                                    y < this.despachos.length;
                                    y++
                                ) {
                                    if (
                                        arrayAllMoths[x] ===
                                        monthName[this.despachos[y].MES - 1] +
                                            " " +
                                            this.despachos[y].ANIO
                                    ) {
                                        match = true;
                                        arrayAllDespachos[x] = parseInt(
                                            this.despachos[y].COMPRAS
                                        );
                                        break;
                                    }
                                }
                                if (!match) {
                                    arrayAllDespachos.push(0);
                                }
                            }

                            this.despachosEnd = arrayAllDespachos;
                            //console.log(arrayAllEntradas)
                            //End Ciclo de Transposición
                        } else {
                            console.log("No hay datos que mostrar");
                        }
                        this.showOverlay = false;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        }
    }
};
</script>

<style scoped></style>
