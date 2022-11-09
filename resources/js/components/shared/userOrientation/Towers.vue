<template>
    <b-col>
        <b-alert show variant="primary" class="text-center"
            ><a href="#" class="alert-link tower-name">{{
                towersstructure.towerDescription
            }}</a></b-alert
        >

        <b-card no-body>
            <b-card
                no-body
                class="overflow-hidden b-card-pavilion"
                v-for="(pavilion, pavilionIndex) in towersstructure.pavilions"
                :key="pavilionIndex"
                border-variant="light"
            >
                <b-row class="border">
                    <b-col
                        md="3"
                        class="text-center border-right bg-secondary text-white"
                        ><div
                            class="h-100 pav-name-section d-flex justify-content-center align-items-center p-2"
                        >
                            <p class="m-0 tower-name">
                                {{ pavilion.pavName }}
                            </p>
                        </div></b-col
                    >
                    <b-col md="9" class="border-right p-2">
                        <b-button
                            v-for="(bed, btnindex) in pavilion.habs"
                            :key="btnindex"
                            class="mr-2 mb-1"
                            :id="`popover-target-${bed.habitation}`"
                            pill
                            @click="showModal('my-modal', bed)"
                            :variant="
                                (bed.hab_status === 'OCUPADA') &
                                (bed.real_stay >= 0) &
                                (bed.real_stay <= 3)
                                    ? 'success'
                                    : (bed.hab_status === 'OCUPADA') &
                                      (bed.real_stay >= 4) &
                                      (bed.real_stay <= 6)
                                    ? 'info'
                                    : (bed.hab_status === 'OCUPADA') &
                                      (bed.real_stay >= 7) &
                                      (bed.real_stay <= 9)
                                    ? 'warning'
                                    : (bed.hab_status === 'OCUPADA') &
                                      (bed.real_stay >= 12)
                                    ? 'danger'
                                    : 'light'
                            "
                            >{{ bed.habitation }}
                            <b-popover
                                v-if="bed.hab_status === 'OCUPADA'"
                                :target="`popover-target-${bed.habitation}`"
                                triggers="hover"
                                placement="top"
                                variant="primary"
                            >
                                <template #title>{{ bed.habitation }}</template>
                                <b-list-group flush class="bg-none">
                                    <b-list-group-item
                                        >PACIENTE:
                                        {{
                                            bed.patient_name
                                        }}</b-list-group-item
                                    >
                                    <b-list-group-item
                                        >DOCUMENTO: {{ bed.patient_doc }} <br />
                                        TIPO:
                                        {{ bed.patient_doctype }}
                                    </b-list-group-item>
                                    <b-list-group-item
                                        >EPS:
                                        {{ bed.patient_eps }}</b-list-group-item
                                    >
                                    <b-list-group-item
                                        >INGRESO: {{ bed.admission_num }} <br />
                                        {{ bed.admission_date }}
                                    </b-list-group-item>
                                </b-list-group>
                            </b-popover>
                        </b-button>
                    </b-col>
                </b-row>
            </b-card>
            <b-modal
                ref="my-modal"
                hide-footer
                :title="infoModal.title"
                header-bg-variant="dark"
                header-text-variant="light"
                size="xl"
            >
                <b-row class="m-4">
                    <b-col>
                        <b-form>
                            <b-form-group id="fieldset-2" label-for="input-2">
                                <b-form-input
                                    id="input-2"
                                    v-model="family.patientVisitorDocument"
                                    trim
                                    placeholder="DOCUMENTO DEL FAMILIAR"
                                    class="login-form-input"
                                    autofocus
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group id="fieldset-1" label-for="input-1">
                                <b-form-input
                                    id="input-1"
                                    v-model="family.patientVisitorName"
                                    trim
                                    placeholder="NOMBRE DEL FAMILIAR"
                                    class="login-form-input"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group id="fieldset-3" label-for="input-3">
                                <b-form-select
                                    id="input-3"
                                    v-model="family.patientVisitorType"
                                    :options="options"
                                    class="login-form-input"
                                ></b-form-select>
                            </b-form-group>

                            <b-form-group id="fieldset-4" label-for="input-4">
                                <b-form-select
                                    id="input-4"
                                    v-model="family.patientVisitorRelationship"
                                    :options="visitiorRelationship"
                                    class="login-form-input"
                                ></b-form-select>
                            </b-form-group>

                            <hr />

                            <b-form-group id="fieldset-3">
                                <b-button
                                    variant="primary"
                                    block
                                    pill
                                    class="p-2"
                                    @click="saveFamilyIncomeRecord"
                                >
                                    <i class="fas fa-save"></i>
                                    GUARDAR</b-button
                                >
                            </b-form-group>
                        </b-form>
                    </b-col>
                </b-row>
                <hr />
                <b-row class="m-4">
                    <b-col>
                        <b-overlay
                            :show="getIsLoadingFamilyRecords"
                            rounded="sm"
                        >
                            <b-table
                                striped
                                hover
                                :items="getPatientVisitorsInfo"
                                :fields="fields"
                            >
                                <template #cell(actions)="row">
                                    <b-button
                                        variant="danger"
                                        pill
                                        @click="InactivateFamilyRecords(row)"
                                        title="Salida Visitante"
                                    >
                                        <i
                                            class="fa fa-arrow-circle-right"
                                            aria-hidden="true"
                                        ></i>
                                    </b-button>
                                </template>
                                <template
                                    #cell(patientVisitorHoursDuration)="row"
                                >
                                    <p>
                                        {{
                                            moment(
                                                row.item
                                                    .patientVisitorHoursDuration
                                            )
                                                .startOf("hour")
                                                .fromNow()
                                        }}
                                    </p>
                                </template>
                            </b-table>
                        </b-overlay>
                    </b-col>
                </b-row>
            </b-modal>
        </b-card>
    </b-col>
</template>

<script>
import moment from "moment";
export default {
    name: "Towers",
    props: {
        towersstructure: Object
    },
    data() {
        const todayDate = moment().format("YYYY-MM-DD hh:mm:ss");
        return {
            family: {
                patientVisitorName: "",
                patientVisitorDocument: "",
                patientVisitorAdmDate: todayDate,
                patientName: "",
                patientDocument: "",
                patientDocumentType: "",
                patientVisitorType: null,
                patientVisitorRelationship: null,
                patientAdmConsecutive: 0,
                patientAdmDate: "",
                patientHabitation: ""
            },
            fields: [
                { key: "patientVisitorName", label: "NOMBRE VISITANTE" },
                { key: "patientVisitorType", label: "TIPO DE VISITANTE" },
                {
                    key: "patientVisitorRelationship",
                    label: "PARENTESCO"
                },
                { key: "patientVisitorAdmDate", label: "FECHA DE INGRESO" },
                { key: "patientVisitorOutDate", label: "FECHA DE SALIDA" },
                {
                    key: "patientVisitorHoursDuration",
                    label: "DURACIÓN EN HORAS",
                    class: "text-center"
                },
                { key: "actions", label: "ACCIONES", class: "text-center" }
            ],
            state: false,
            infoModal: {
                title: "",
                content: ""
            },
            options: [
                {
                    value: null,
                    text: "SELECCIONE UN TIPO PARA EL FAMILIAR"
                },
                { value: 2, text: "VISITANTE" },
                { value: 1, text: "ACOMPAÑANTE" }
            ],
            visitiorRelationship: [
                {
                    value: null,
                    text: "SELECCIONE EL PARENTESCO DEL FAMILIAR"
                },
                { value: "PADRES", text: "PADRES" },
                { value: "HERMANOS", text: "HERMANOS" },
                { value: "ABUELOS", text: "ABUELOS" },
                { value: "TIOS", text: "TIOS" },
                { value: "PRIMOS", text: "PRIMOS" },
                { value: "SOBRINOS", text: "SOBRINOS" },
                { value: "NIETOS", text: "NIETOS" }
            ]
        };
    },
    computed: {
        getPatientVisitorsInfo() {
            return this.$store.state.uor.familyIcomeRecords;
        },
        getIsLoadingFamilyRecords() {
            return this.$store.state.uor.isLoadingFamilyRecords;
        }
    },
    methods: {
        showModal(modal, item) {
            this.infoModal.title =
                item.patient_name +
                "  ---  " +
                item.patient_doc +
                "  ---  " +
                item.habitation;
            this.family.patientName = item.patient_name;
            this.family.patientDocument = item.patient_doc;
            this.family.patientDocumentType = item.patient_doctype;
            this.family.patientAdmConsecutive = item.admission_num;
            this.family.patientAdmDate = item.admission_date;
            this.family.patientHabitation = item.habitation;
            //
            this.$store.dispatch("getPatientsVisitors", item);
            //
            this.$refs[`${modal}`].show();
        },
        hideModal(modal) {
            this.$refs[`${modal}`].hide();
        },
        saveFamilyIncomeRecord() {
            //this.$store.dispatch("getPatientsVisitors", this.family);
            this.$store.dispatch("savePatientVisitors", this.family);
            /* var copyObject = Object.assign({}, this.family);
            this.items.push(copyObject);
            this.clearFormFields(); */
        },
        InactivateFamilyRecords(row) {
            //this.items.splice(row.index, 1);
            console.log(row);
        },
        clearFormFields() {
            this.family.name = "";
            this.family.document = "";
            this.family.age = "";
        }
    }
};
</script>

<style>
.tower-name {
    text-transform: uppercase;
}
</style>
