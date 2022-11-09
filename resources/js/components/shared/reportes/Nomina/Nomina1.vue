<template>
    <b-container class="mt-5">
        <b-row>
            <b-col cols="12">
                <b-card>
                    <b-row>
                        <b-col cols="12">
                            <b-form enctype="multipart/form-data">
                                <b-row>
                                    <b-col md="5" sm="12">
                                        <b-form-group
                                            id="fieldset-1"
                                            label="Fecha Inicial:"
                                            label-for="input-1"
                                        >
                                            <b-form-datepicker
                                                id="input-1"
                                                v-model="dataToSearch.field1"
                                                placeholder="Seleccionar Fecha Inicial"
                                                class="mb-2"
                                                reset-button
                                                label-reset-button="Limpiar"
                                            >
                                            </b-form-datepicker>
                                        </b-form-group>
                                    </b-col>

                                    <b-col md="5" sm="12">
                                        <b-form-group
                                            id="fieldset-2"
                                            label="Fecha Final:"
                                            label-for="input-2"
                                        >
                                            <b-form-datepicker
                                                id="input-2"
                                                v-model="dataToSearch.field2"
                                                placeholder="Seleccionar Fecha Final"
                                                class="mb-2"
                                                reset-button
                                                label-reset-button="Limpiar"
                                            >
                                            </b-form-datepicker>
                                        </b-form-group>
                                    </b-col>

                                    <b-col md="2" sm="12">
                                        <b-form-group id="fieldset-3">
                                            <b-button
                                                block
                                                variant="primary"
                                                class="mt-4"
                                                type="button"
                                                @click.prevent="fetchData()"
                                            >
                                                <i class="fa fa-search"></i>
                                                Buscar
                                            </b-button>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <div
                                        class="text-center"
                                        v-show="loading"
                                        role="status"
                                    >
                                        <b-spinner
                                            variant="primary"
                                            type="grow"
                                            label="Spinning"
                                        ></b-spinner>
                                    </div>

                                    <b-col
                                        md="2"
                                        sm="12"
                                        v-if="dataExists"
                                        role="status"
                                    >
                                        <b-form-group id="fieldset-4">
                                            <export-excel
                                                :data="rows"
                                                :fields="fields"
                                                class="btn btn-success"
                                                worksheet="My Worksheet"
                                                :title="title"
                                                name="ReporteNomina.xls"
                                            >
                                                <i class="fa fa-download"></i>
                                                Descargar Data
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
    name: "nomina1-component",
    data() {
        return {
            fields: {
                Cedula: "Cedula",
                Nombres: "Nombres",
                Apellidos: "Apellidos",
                Cargo: "Cargo",
                Centro_de_Costos: "Centro_de_Costos",
                Sueldo_Basico: {
                    field: "Sueldo_Basico",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Laborados: {
                    field: "Dias_Laborados",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Incap_menor_2_dias: {
                    field: "Dias_Incap_menor_2_dias",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Incap_mayor_2_dias: {
                    field: "Dias_Incap_mayor_2_dias",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Incap_total: {
                    field: "Dias_Incap_total",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Incap_menor_2_dias: {
                    field: "Incap_menor_2_dias",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Incap_mayor_2_dias: {
                    field: "Incap_mayor_2_dias",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Incap_total_Valor: {
                    field: "Incap_total_Valor",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Incapacidad_ARL: {
                    field: "Incapacidad_ARL",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Dias_Incap_ARL: {
                    field: "Dias_Incap_ARL",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Lic_Mat_y_Pat: {
                    field: "Dias_Lic_Mat_y_Pat",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Lic_Remunerada: {
                    field: "Dias_Lic_Remunerada",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Lic_No_Remunerada: {
                    field: "Dias_Lic_No_Remunerada",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Dias_Vacaciones: {
                    field: "Dias_Vacaciones",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Salario: {
                    field: "Salario",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Ajuste_Salario: {
                    field: "Ajuste_Salario",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Auxilio_Alimentacion: {
                    field: "Auxilio_Alimentacion",
                    callback: value => {
                        return Math.trunc(value);
                    }
                },
                Permisos_remunerados: {
                    field: "Permisos_remunerados",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Reliquidacion_Horas: {
                    field: "Reliquidacion_Horas",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Ausencias: {
                    field: "Ausencias",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Suspension: {
                    field: "Suspension",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Reliquidacion: {
                    field: "Reliquidacion",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Auxilio_Transporte: {
                    field: "Auxilio_Transporte",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Recargos: {
                    field: "Recargos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Horas_Extras: {
                    field: "Horas_Extras",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Retroactivo_Recargos: {
                    field: "Retroactivo_Recargos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Retroactivo_Horas_Extras: {
                    field: "Retroactivo_Horas_Extras",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Lic_Remunerada: {
                    field: "Lic_Remunerada",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Licencia_Mat_y_Pat: {
                    field: "Licencia_Mat_y_Pat",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Vacaciones_Tiempo: {
                    field: "Vacaciones_Tiempo",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Vacaciones_Compensadas: {
                    field: "Vacaciones_Compensadas",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Intereses_Cesantias: {
                    field: "Intereses_Cesantias",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Anticipo: {
                    field: "Anticipo",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Otros_Ingresos: {
                    field: "Otros_Ingresos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Total_Devengado: {
                    field: "Total_Devengado",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Salud: {
                    field: "Salud",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Pension: {
                    field: "Pension",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Fondo_Solidaridad: {
                    field: "Fondo_Solidaridad",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Pensiones_Voluntarias: {
                    field: "Pensiones_Voluntarias",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Retefuente: {
                    field: "Retefuente",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Mayor_Valor_Horas_Extras: {
                    field: "Mayor_Valor_Horas_Extras",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Mayor_Valor_Subsidio_Transp: {
                    field: "Mayor_Valor_Subsidio_Transp",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Mayor_Valor_Vacaciones: {
                    field: "Mayor_Valor_Vacaciones",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Permisos_No_remunerados: {
                    field: "Permisos_No_remunerados",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Descuento_Incentivos: {
                    field: "Descuento_Incentivos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Prestamos: {
                    field: "Prestamos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Mayor_Valor_Salario: {
                    field: "Mayor_Valor_Salario",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Libranza: {
                    field: "Libranza",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Multas_y_Huellero: {
                    field: "Multas_y_Huellero",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                AMI: {
                    field: "AMI",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Casino: {
                    field: "Casino",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Celular: {
                    field: "Celular",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Colseguros: {
                    field: "Colseguros",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                El_Cid: {
                    field: "El_Cid",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Embargos: {
                    field: "Embargos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Fundacion: {
                    field: "Fundacion",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Los_Olivos: {
                    field: "Los_Olivos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Parqueadero: {
                    field: "Parqueadero",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Poliza_Scare: {
                    field: "Poliza_Scare",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Preverriso: {
                    field: "Preverriso",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Vacuna: {
                    field: "Vacuna",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Otros_descuentos: {
                    field: "Otros_descuentos",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Total_Deducido: {
                    field: "Total_Deducido",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
                Neto_Pagado: {
                    field: "Neto_Pagado",
                    callback: value => {
                        return Math.trunc(value)
                    }
                },
            },
            rows: [],
            title: [
                'ORGANIZACIÓN CLÍNICA BONNADONA PREVENIR S.A.S',
                'NIT: 800194798-2',
                `REPORTE DE NOMINA - Periodo: fecha1 - fecha2`
            ],
            dataToSearch: {
                field1: "",
                field2: ""
            },
            loading: false,
            dataExists: false,
            errors: []
        };
    },
    methods: {
        fetchData() {
            let me = this;
            let url =
                "report1getdata/" +
                me.dataToSearch.field1 +
                "/fin/" +
                me.dataToSearch.field2;

            me.loading = true;

            if (this.validateForm()) {
                axios
                    .get(url)
                    .then(response => {
                        if (response.status == 200) {
                            this.$bvToast.toast(
                                "Datos cargados Correctamente",
                                {
                                    title: "Notificación",
                                    variant: "success",
                                    toaster: "b-toaster-top-right",
                                    solid: true,
                                    appendToast: false
                                }
                            );

                            //const incap_total = response.data[0].["Dias Incap < 2 dias"];
                            console.log(response.data.data);

                            me.rows = response.data.data;
                            me.dataExists = true;
                            me.loading = false;
                        } else if (response.status == 204) {
                            this.$bvToast.toast(
                                "No se han encontrados datos para este rango de fechas",
                                {
                                    title: "Notificación",
                                    variant: "info",
                                    toaster: "b-toaster-top-right",
                                    solid: true,
                                    appendToast: false
                                }
                            );

                            me.loading = false;
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            } else {
                this.$bvToast.toast(`${this.errors}`, {
                    title: "Notificación",
                    variant: "danger",
                    toaster: "b-toaster-top-right",
                    solid: true,
                    appendToast: false
                });
                me.loading = false;
                this.dataExists = false;
            }
        }
        ,
        validateForm() {
            this.errors = [];

            if (this.dataToSearch.field1 && this.dataToSearch.field2) {
                let fec1 = new Date(this.dataToSearch.field1).getTime();
                let fec2 = new Date(this.dataToSearch.field2).getTime();

                let diff = fec2 - fec1;
                let diffInt = diff / (1000 * 60 * 60 * 24);

                if (diffInt < 10) {
                    this.errors.push(
                        "El Rango Entre Las Fechas Debe Ser Menor a 10 Días"
                    );
                } else {
                    return true;
                }
            }

            if (!this.dataToSearch.field1) {
                this.errors.push("Seleccione la Fecha Inicial");
            }

            if (!this.dataToSearch.field2) {
                this.errors.push("Seleccione la Fecha Final");
            }
        }
    }
}
;
</script>

<style scoped></style>
