<template>
    <b-container>
        <b-row>
            <b-col>
                <b-card>
                    <b-card-body>
                        <b-form @submit="onSubmit" @reset="onReset" v-if="show">
                            <b-overlay :show="dataExists" rounded="sm">
                                <b-form-group
                                    id="input-group-1"
                                    label="fecha Inicio:"
                                    label-for="example-datepicker-1"
                                >
                                    <b-form-datepicker
                                        id="example-datepicker-1"
                                        v-model="form.initDate"
                                        class="mb-2"
                                        placeholder="Seleccione la fecha de Inicio"
                                        required
                                        aria-required="true"
                                        today-button
                                        reset-button
                                        locale="es"
                                    ></b-form-datepicker>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-2"
                                    label="Fecha Fin:"
                                    label-for="example-datepicker-2"
                                >
                                    <b-form-datepicker
                                        id="example-datepicker-2"
                                        v-model="form.endDate"
                                        class="mb-2"
                                        placeholder="Seleccione la fecha de Fin"
                                        required
                                        today-button
                                        reset-button
                                        locale="es"
                                    ></b-form-datepicker>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-3"
                                    label="Informe:"
                                    label-for="input-3"
                                >
                                    <b-form-select
                                        id="input-3"
                                        v-model="form.selected"
                                        :options="audits"
                                        required
                                    ></b-form-select>
                                </b-form-group>

                                <b-form-group>
                                    <b-button type="submit" variant="primary">
                                        <i class="fas fa-search"></i>
                                        Consultar</b-button
                                    >
                                    <b-button type="reset" variant="danger">
                                        <i class="fas fa-times"></i>
                                        Cancelar</b-button
                                    >
                                    <export-excel
                                        :data="auditsData"
                                        class="btn btn-success"
                                        worksheet="Auditorias"
                                        name="ReporteAuditoria.xls"
                                        v-if="showDownloadButton"
                                    >
                                        <i class="fa fa-download"></i> Descargar
                                        Data
                                    </export-excel>
                                </b-form-group>
                            </b-overlay>
                        </b-form>
                    </b-card-body>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: "Calidad",
    components: {},
    data() {
        return {
            auditsData: [],
            form: {
                initDate: "",
                endDate: "",
                selected: null
            },
            audits: [
                { text: "SELECCIONE UN TIPO DE REPORTE", value: null },
                { text: "INFORME AUDITORIA", value: 1 },
                { text: "INFORME COMITÉS", value: 2 }
            ],
            show: true,
            dataExists: false,
            showDownloadButton: false
        };
    },
    methods: {
        onSubmit(event) {
            event.preventDefault();
            var url = "";
            this.dataExists = true;

            if (this.form.selected === 1) {
                url =
                    "reportes/get/auditoria/" +
                    this.form.initDate +
                    "/" +
                    this.form.endDate;
            }
            if (this.form.selected === 2) {
                url =
                    "reportes/get/comites/" +
                    this.form.initDate +
                    "/" +
                    this.form.endDate;
            }

            axios
                .get(url)
                .then(response => {
                    if (response.data.status === 200) {
                        //console.log(response.data.data);
                        this.$bvToast.toast(
                            `Reporte de ${response.data.msg} Generado`,
                            {
                                title: "Notificación",
                                autoHideDelay: 5000,
                                solid: true,
                                variant: "success",
                                toaster: "b-toaster-top-right"
                            }
                        );
                        this.dataExists = false;
                        this.showDownloadButton = true;
                        this.auditsData = response.data.data;
                    }
                })
                .catch(error => {
                    console.log(error);
                    this.$bvToast.toast(
                        "Error al generar el reporte, debe Seleccionar el rango de fechas",
                        {
                            title: "Notificación",
                            autoHideDelay: 5000,
                            solid: true,
                            variant: "danger",
                            toaster: "b-toaster-top-right"
                        }
                    );
                    this.dataExists = false;
                });
        },
        onReset(event) {
            event.preventDefault();
            // Reset our form values
            this.form.initDate = "";
            this.form.endDate = "";
            this.form.selected = null;
            // Trick to reset/clear native browser form validation state
            this.show = false;
            this.showDownloadButton = false;
            this.$nextTick(() => {
                this.show = true;
            });
        }
    }
};
</script>

<style></style>
