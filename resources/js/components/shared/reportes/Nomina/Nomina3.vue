<template>
    <b-container class="mt-5">
        <b-row>
            <b-col cols="12">
                <b-card>
                    <b-row class="mt-4">
                        <b-col md="10" offset-md="1">
                            <b-form enctype="multipart/form-data">
                                <b-row>
                                    <b-col md="10" offset-md="1">
                                        <b-form-group id="fieldset-5">
                                            <b-form-input
                                                id="input-5"
                                                size="lg"
                                                placeholder="Documeto del colaborador"
                                                class="mb-2 login-date-input input-with-centered-placeholder text-center"
                                                v-model="dataToSearch.document"
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col md="10" offset-md="1">
                                        <b-row no-gutters>
                                            <b-col class="p-2">
                                                <b-form-group
                                                    id="fieldset-1"
                                                    label="Fecha Inicial:"
                                                    label-for="input-1"
                                                >
                                                    <b-form-datepicker
                                                        id="input-1"
                                                        v-model="
                                                            dataToSearch.field1
                                                        "
                                                        placeholder="Seleccionar Fecha Inicial"
                                                        class="mb-2 login-date-input"
                                                        reset-button
                                                        label-reset-button="Limpiar"
                                                    >
                                                    </b-form-datepicker>
                                                </b-form-group>
                                            </b-col>

                                            <b-col class="p-2">
                                                <b-form-group
                                                    id="fieldset-2"
                                                    label="Fecha Final:"
                                                    label-for="input-2"
                                                >
                                                    <b-form-datepicker
                                                        id="input-2"
                                                        v-model="
                                                            dataToSearch.field2
                                                        "
                                                        placeholder="Seleccionar Fecha Final"
                                                        class="mb-2 login-date-input"
                                                        reset-button
                                                        label-reset-button="Limpiar"
                                                    >
                                                    </b-form-datepicker>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col md="10" offset-md="1">
                                        <b-form-group id="fieldset-3">
                                            <b-button
                                                block
                                                variant="primary"
                                                class="mt-4"
                                                pill
                                                type="button"
                                                @click.prevent="fetchData()"
                                            >
                                                <i class="fa fa-search"></i>
                                                Buscar
                                            </b-button>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <hr />

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
                                        md="6"
                                        offset-md="3"
                                        v-if="dataExists"
                                        role="status"
                                    >
                                        <b-form-group id="fieldset-4">
                                            <export-excel
                                                :data="rows"
                                                :fields="fields"
                                                class="btn btn-success btn-block login-form-button"
                                                worksheet="My Worksheet"
                                                :title="title"
                                                name="ReporteHuellero.xls"
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
    name: "nomina3-component",
    data() {
        return {
            fields: {
                Documento: "employeeDocument",
                Nombre: "employeeName",
                Apellido: "employeeLastName",
                "Fecha Marcación": "employeeMarkDate",
                "Hora Marcación": "employeeMarkHour",
                "Tipo Marcación": "employeeMarkType"
            },
            rows: [],
            title: [
                "ORGANIZACIÓN CLÍNICA BONNADONA PREVENIR S.A.S",
                "NIT: 800194798-2",
                "REPORTE DE HUELLERO"
            ],
            dataToSearch: {
                document: "",
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
                "report3getdata/" +
                me.dataToSearch.field1 +
                "/fin/" +
                me.dataToSearch.field2 +
                "/" +
                me.dataToSearch.document;

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
        },
        validateForm() {
            this.errors = [];

            if (!this.dataToSearch.field1) {
                this.errors.push("Seleccione la Fecha Inicial");
            } else {
                return true;
            }

            if (!this.dataToSearch.field2) {
                this.errors.push("Seleccione la Fecha Final");
            } else {
                return true;
            }
        }
    }
};
</script>

<style scoped></style>
