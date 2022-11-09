<template>
    <div>
        <div class="card" style="width: 18rem;" v-can="'censo-access'">
            <div class="card-body text-center">
                <h3 class="card-title">Censo Diario</h3>
                <hr/>
                <p class="card-text">
                    Aquí puedes descargar el censo diario PDF.
                </p>
                <button
                    class="btn btn-primary btn-sm btn-block"
                    @click="downloadPDF()"
                    v-can="'censo-access'"
                >
                    <i class="fas fa-file-pdf"></i> Descargar Censo Diario Pdf
                </button>
                <hr/>
                <p class="card-text">
                    Ahora puedes descargar el censo diario en EXCEL.
                </p>
                <export-excel
                    :data="items"
                    :fields="fields"
                    type="xls"
                    class="btn btn-success btn-sm mt-2"
                    worksheet="My Worksheet"
                    :name="name"
                >
                    <i class="fas fa-file-excel"></i> Descargar Censo Excel
                </export-excel>
            </div>
        </div>
        <div id="content" style="display: none">
            <h1
                style="text-align:center; margin: 0; padding: 0; font-size: 30px !important;"
            >
                Organizacion Clinica Bonnadona Prevenir
            </h1>
            <h2
                style="text-align:center; margin: 0; padding: 0; font-size: 20px !important;"
            >
                800194798-2
            </h2>
            <h1
                style="text-align:center; margin: 0; padding: 0; font-size: 30px !important;"
            >
                Censo Diario con Estancia Real
            </h1>
            <hr/>
            <table class="table table-bordered" id="censo-table">
                <thead class="thead-dark">
                <tr>
                    <th>PABELLON</th>
                    <th>CAMA</th>
                    <th>ESTADO</th>
                    <th>DOCUMENTO</th>
                    <th>PACIENTE</th>
                    <th>EDAD</th>
                    <th>SEXO</th>
                    <th>F.INGRESO</th>
                    <th>DIAGNOSTICO</th>
                    <th>ARS-EPS</th>
                    <th>CONTRATO</th>
                    <th>Estancia Real</th>
                    <th># CORTES</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                    v-if="item.ESTADO !== 'LIBRE'"
                >
                    <td>{{ item.PABELLON }}</td>
                    <td>{{ item.CAMA }}</td>
                    <td>{{ item.ESTADO }}</td>
                    <td
                        v-if="
                                item.ESTADO !== 'MANTENIMIENTO' &&
                                    item.ESTADO !== 'DESINFECCION'
                            "
                    >
                        {{ item.TI_DOC }}-{{ item.NUM_HISTORIA }}-{{
                        item.INGRESO
                        }}
                    </td>
                    <td v-else></td>
                    <td>{{ item.NOMBRE_PACIENTE }}</td>
                    <td
                        v-if="
                                item.ESTADO !== 'MANTENIMIENTO' &&
                                    item.ESTADO !== 'DESINFECCION'
                            "
                    >
                        {{ item.EDAD }} A
                    </td>
                    <td v-else></td>
                    <td>{{ item.SEXO }}</td>
                    <td>
                        {{
                        item.FECHA_INGRESO
                        ? moment(item.FECHA_INGRESO).format(
                        "DD/MM/YYYY"
                        )
                        : ""
                        }}
                    </td>
                    <td>{{ item.DX }}</td>
                    <td>{{ item.CONTRATO }}</td>
                    <td>{{ item.TIPO }}</td>
                    <td>{{ item.EstanciaReal }}</td>
                    <td>
                        {{ item.ESTADO === 'MANTENIMIENTO' || item.ESTADO === 'DESINFECCION' ? "" : item.CANTIDAD_CORTE
                        > 0 ? item.CANTIDAD_CORTE : 0 }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import moment from "moment";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

export default {
    name: "CensoDiario",
    data() {
        return {
            items: [],
            fields: {
                PABELLON: "PABELLON",
                CAMA: "CAMA",
                ESTADO: "ESTADO",
                "TIPO DOC": "TI_DOC",
                DOCUMENTO: "NUM_HISTORIA",
                INGRESO: {
                    field: "INGRESO",
                    callback: value => {
                        if (value == null || value === 0) {
                            return "";
                        } else {
                            return value;
                        }
                    }
                },
                PACIENTE: "NOMBRE_PACIENTE",
                EDAD: "EDAD",
                SEXO: "SEXO",
                "FECHA INGRESO": {
                    field: "FECHA_INGRESO",
                    callback: value => {
                        let dateStr = new Date(value);

                        if (dateStr == null || dateStr == "Invalid Date") {
                            return "";
                        } else {
                            return dateStr.toLocaleDateString();
                        }
                    }
                },
                DIAGNOSTICO: "DX",
                "ARS-EPS": "CONTRATO",
                CONTRATO: "TIPO",
                "ESTANCIA REAL": "EstanciaReal",
                "# CORTES": "CANTIDAD_CORTE"
            },
            name: "CensoDairio_" + moment().format("DD-MM-YYYY") + ".xls"
        };
    },
    created() {
        this.getData();
    },
    methods: {
        getData() {
            let url = "reportes/get-censo-real";

            axios
                .get(url)
                .then(response => {
                    if (response.status === 200) {
                        this.items = response.data;
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        downloadPDF() {
            const doc = new jsPDF({
                orientation: "landscape",
                unit: "mm",
                format: "legal",
                precision: 16
            });
            doc.setFontSize(12);
            doc.text(
                "Organizacion Clinica Bonnadona Prevenir",
                doc.internal.pageSize.width / 2,
                10,
                {
                    align: "center",
                    lineHeightFactor: "1.05"
                },
                null
            );
            doc.text("Nit: 800194798-2", doc.internal.pageSize.width / 2, 15, {
                align: "center",
                lineHeightFactor: "1.05"
            });
            doc.text(
                "Censo Diario con Estancias Reales / Corte a Fecha: " +
                `${moment().format("DD-MM-YYYY")}` +
                "  Hora: " +
                `${moment().format("hh:mm:ss")}`,
                doc.internal.pageSize.width / 2,
                20,
                {
                    align: "center",
                    lineHeightFactor: "1.05"
                }
            );

            autoTable(doc, {
                html: "#censo-table",
                margin: {top: 25},
                theme: "grid",
                headStyles: {
                    fillColor: "#e6f2ff",
                    halign: "center",
                    lineWidth: 0.5,
                    lineColor: [153, 204, 255]
                },
                styles: {
                    cellWidth: "auto",
                    fontSize: 7,
                    font: "helvetica",
                    textColor: "#000000",
                    cellPadding: 2,
                    overflow: "ellipsize",
                    pageBreak: "always"
                },
                columnStyles: {
                    2: {cellWidth: 16},
                    6: {halign: "center"},
                    7: {cellWidth: 20, halign: "center"},
                    8: {cellWidth: 65},
                    11: {halign: "center"},
                    12: {halign: "center"}
                }
            });

            doc.save(
                "CensoDiario-" + `${moment().format("DD-MM-YYYY")}` + ".pdf"
            );

            this.getData();
        }
    }
};
</script>

<style scoped>
* {
    font-size: 12px;
}
</style>
