<template>
    <div>
        <b-container fluid>
            <b-row>
                <b-col cols="10" offset="1">
                    <b-card class="text-center">
                        <b-row class="mb-3">
                            <b-col class="text-right">
                                <b-button
                                    pill
                                    variant="primary"
                                    class="button-times-icon"
                                    @click="showModal"
                                >
                                    <i class="fas fa-plus times-icon"></i>
                                    Agregar Proveedor
                                </b-button>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col>
                                <b-overlay
                                    :show="getShowProvidersItemsStatus"
                                    rounded="sm"
                                >
                                    <b-table
                                        :items="getPharmProvidersInfo"
                                        :fields="providerTableFields"
                                        show-empty
                                        responsive
                                    >
                                        <template #empty="scope">
                                            <h4>{{ scope.emptyText }}</h4>
                                        </template>
                                        <template #cell(_providerStatus)="row">
                                            <i
                                                class="fas fa-user-circle"
                                                :style="
                                                    row.value == 0
                                                        ? 'color: red'
                                                        : 'color: green'
                                                "
                                            ></i>
                                        </template>

                                        <template #cell(details)="row">
                                            <b-button
                                                size="sm"
                                                @click="
                                                    printProviderInfo(
                                                        row,
                                                        'provider-agents-template'
                                                    )
                                                "
                                            >
                                                <i class="fas fa-eye"></i>
                                            </b-button>
                                        </template>

                                        <template #cell(actions)="row">
                                            <b-button
                                                size="sm"
                                                variant="info"
                                                @click="
                                                    printProviderInfo(
                                                        row,
                                                        'provider-evaluation-template'
                                                    )
                                                "
                                                title="Evaluar Proveedor"
                                            >
                                                <i
                                                    class="fas fa-file"
                                                    aria-hidden="true"
                                                ></i>
                                            </b-button>

                                            <b-button
                                                size="sm"
                                                variant="danger"
                                                @click="
                                                    printProviderInfo(
                                                        row,
                                                        'provider-agents-template'
                                                    )
                                                "
                                                title="Desactivar Proveedor"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </b-button>
                                        </template>
                                    </b-table>
                                </b-overlay>
                            </b-col>
                        </b-row>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>

        <modalTemplate
            :showModalComponent="showModalComponent"
            @close="closeProviderInfo"
            :modalTemplateName="modalTemplate"
            :providerIdToShowModalComponent="providerIdToShowModalComponent"
        ></modalTemplate>

        <!-- MODAL TO ADD PROVIDER -->
        <b-modal
            ref="my-modal"
            title="AGREGAR PROVEEDOR"
            no-close-on-backdrop
            no-close-on-esc
            size="xl"
        >
            <b-row class="p-4">
                <b-col>
                    <b-form>
                        <b-row>
                            <b-col>
                                <b-form-group>
                                    <v-select
                                        class="mb-2 input-select-custom"
                                        placeholder="Seleccione un Proveedor"
                                        :options="getPharmProvidersList"
                                        v-model="provider.selected"
                                        label="providerName"
                                    ></v-select>
                                </b-form-group>
                            </b-col>
                        </b-row>

                        <hr />

                        <b-row v-if="provider.selected != null">
                            <b-col>
                                <b-row>
                                    <b-col>
                                        <b-form-group>
                                            <b-form-input
                                                id="input-1"
                                                v-model="
                                                    provider.selected
                                                        .providerName
                                                "
                                                type="text"
                                                disabled
                                                readonly
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col>
                                        <b-form-group>
                                            <b-form-input
                                                id="input-1"
                                                v-model="
                                                    provider.selected
                                                        .providerNit
                                                "
                                                type="text"
                                                disabled
                                                readonly
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col cols="4">
                                        <b-form-group>
                                            <b-form-input
                                                id="input-1"
                                                v-model="
                                                    provider.selected
                                                        .providerAddress
                                                "
                                                type="text"
                                                disabled
                                                readonly
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="3">
                                        <b-form-group>
                                            <b-form-input
                                                id="input-1"
                                                v-model="
                                                    provider.selected
                                                        .providerCity
                                                "
                                                type="text"
                                                disabled
                                                readonly
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col>
                                        <b-form-group>
                                            <b-form-select
                                                v-model="
                                                    provider.providerTypesOptions
                                                "
                                                :options="
                                                    getPharmProvidersTypes
                                                "
                                                text-field="xendoraProviderTypeDescription"
                                                value-field="xendoraProviderTypeId"
                                            >
                                                <template #first>
                                                    <b-form-select-option
                                                        :value="null"
                                                        disabled
                                                        >-- Please select an
                                                        option
                                                        --</b-form-select-option
                                                    >
                                                </template>
                                            </b-form-select>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-col>
            </b-row>

            <template #modal-footer>
                <b-button
                    variant="primary"
                    pill
                    size="sm"
                    class="float-right"
                    @click="hideModal()"
                >
                    <i class="fas fa-times"></i>
                    Close
                </b-button>

                <b-button
                    variant="success"
                    pill
                    size="sm"
                    class="float-right"
                    @click="savePharmProviders()"
                >
                    <i class="fas fa-save"></i>
                    Save
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import modalTemplate from "../modal/modalTemplate.vue";
export default {
    components: { modalTemplate },
    name: "ProviderEvaluation",
    data() {
        return {
            provider: {
                selected: null,
                providerTypesOptions: null
            },
            providersRegistered: [],
            providerTableFields: [
                { key: "xendoraProviderNit", sortable: true, label: "Nit" },
                {
                    key: "xendoraProviderName",
                    sortable: true,
                    label: "Proveedor",
                    class: "text-left"
                },
                {
                    key: "xendoraProviderAddress",
                    sortable: true,
                    label: "Dirección",
                    class: "text-left"
                },
                { key: "xendoraProviderCity", sortable: true, label: "Ciudad" },
                {
                    key: "xendoraProviderCreateDate",
                    sortable: true,
                    label: "Creado",
                    class: "ds-none"
                },
                /* { key: "providerPhone", sortable: true, label: "Télefono" }, */
                /* { key: "providerEmail", sortable: true, label: "Email" }, */
                {
                    key: "xendoraProviderType",
                    sortable: true,
                    label: "Tipo",
                    class: "text-left"
                },
                { key: "_providerStatus", sortable: true, label: "Estado" },
                { key: "details", label: "Detalle" },
                { key: "actions", label: "Acciones" }
            ],
            showModalComponent: false,
            providerIdToShowModalComponent: 0,
            modalTemplate: ""
        };
    },
    computed: {
        getPharmProvidersList() {
            return this.$store.state.proe.providersList;
        },
        getPharmProvidersInfo() {
            return this.$store.state.proe.providersInfoFromXendora;
        },
        getPharmProvidersTypes() {
            return this.$store.state.proe.providersTypesFromXendora;
        },
        getShowProvidersItemsStatus() {
            return this.$store.state.proe.providersIsLoading;
        }
    },
    methods: {
        showModal() {
            this.$refs["my-modal"].show();
        },
        hideModal() {
            this.$refs["my-modal"].hide();
            this.provider.selected = null;
        },
        toggleModal() {
            // We pass the ID of the button that we want to return focus to
            // when the modal has hidden
            this.$refs["my-modal"].toggle("#toggle-btn");
        },
        getPharmProvidersFromHosvital() {
            this.$store.dispatch("getPharmProvidersFromHosvital");
        },
        getPharmProvidersFromXendora() {
            this.$store.dispatch("getPharmProvidersFromXendora");
        },
        getPharmProvidersTypesFromXendora() {
            this.$store.dispatch("getPharmProvidersTypesFromXendora");
        },
        savePharmProviders() {
            let url = "provider-evaluation/post/provider-to-evaluate";

            let json_to_send = {
                nit: this.provider.selected.providerNit,
                razon_social: this.provider.selected.providerName,
                direccion: this.provider.selected.providerAddress,
                ciudad: this.provider.selected.providerCity,
                provider_type_id: this.provider.providerTypesOptions
            };

            this.$store.commit("SET_PROVIDERS_IS_LOADING", true);

            axios
                .post(url, json_to_send)
                .then(result => {
                    if (result.data.status === 201) {
                        /* console.log("ok", result.data.data); */
                        this.getPharmProvidersFromXendora();
                        this.$bvToast.toast(`${result.data.msg}`, {
                            title: `CALIFICACIÓN DE PROVEEDORES`,
                            variant: `success`,
                            solid: true
                        });
                        /* this.errors = []; */
                        this.$store.commit("SET_PROVIDERS_IS_LOADING", false);
                    } else {
                        console.log(result.data);
                    }
                })
                .catch(err => {
                    if (
                        err.response.status === 400 ||
                        err.response.status === 422
                    ) {
                        console.log(err.response);
                        /* this.errors = err.response.data.errors; */
                        this.$bvToast.toast(`${err.response.data.msg}`, {
                            title: `CALIFICACIÓN DE PROVEEDORES`,
                            variant: `danger`,
                            solid: true
                        });
                        this.$store.commit("SET_PROVIDERS_IS_LOADING", false);
                    }
                    this.$store.commit("SET_PROVIDERS_IS_LOADING", false);
                    console.log(err);
                });
        },
        closeProviderInfo() {
            this.showModalComponent = !this.showModalComponent;
            this.providerIdToShowModalComponent = 0;
        },
        printProviderInfo(provider, modalTemplate) {
            this.modalTemplate = modalTemplate;
            this.showModalComponent = !this.showModalComponent;
            this.providerIdToShowModalComponent =
                provider.item.xendoraProviderId;
        }
    },
    created() {
        this.getPharmProvidersFromHosvital();
        this.getPharmProvidersFromXendora();
        this.getPharmProvidersTypesFromXendora();
    }
};
</script>

<style></style>
