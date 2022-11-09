<template>
    <div>
        <b-row no-gutters>
            <b-col id="create-agent-section">
                <b-card no-body>
                    <b-tabs pills card vertical lazy end>
                        <!---->
                        <b-tab title="Dashboard" active>
                            <b-row>
                                <b-col>
                                    <b-card
                                        bg-variant="primary"
                                        text-variant="white"
                                    >
                                        <b-card-title class="text-center"
                                            >CANTIDAD DE
                                            REPRESENTANTES</b-card-title
                                        >
                                        <h1
                                            class="text-center"
                                            style="font-size: 2rem !important"
                                        >
                                            {{ getProvidersAgentsCounter }}
                                        </h1>
                                    </b-card>
                                </b-col>
                            </b-row>
                        </b-tab>
                        <!---->
                        <b-tab title="Representantes">
                            <b-row>
                                <b-col cols="3">
                                    <b-row>
                                        <b-col>
                                            <b-form>
                                                <b-form-group>
                                                    <b-form-input
                                                        id="input-1"
                                                        type="text"
                                                        placeholder="Nombres"
                                                        v-model="
                                                            agentsInfo.name
                                                        "
                                                        class="login-form-input"
                                                    >
                                                    </b-form-input>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-form-input
                                                        id="input-2"
                                                        type="text"
                                                        placeholder="Apellidos"
                                                        v-model="
                                                            agentsInfo.lastName
                                                        "
                                                        class="login-form-input"
                                                    >
                                                    </b-form-input>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-form-input
                                                        id="input-3"
                                                        type="number"
                                                        placeholder="telefono"
                                                        v-model="
                                                            agentsInfo.phone
                                                        "
                                                        class="login-form-input"
                                                    >
                                                    </b-form-input>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-form-input
                                                        id="input-4"
                                                        type="email"
                                                        placeholder="email"
                                                        v-model="
                                                            agentsInfo.email
                                                        "
                                                        class="login-form-input"
                                                    >
                                                    </b-form-input>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-form-select
                                                        v-model="
                                                            agentsInfo.selected
                                                        "
                                                        :options="
                                                            getProvidersAgentLines
                                                        "
                                                        text-field="xendoraProviderAgentLineName"
                                                        value-field="xendoraProviderAgentLineId"
                                                        class="login-form-input"
                                                    >
                                                        <template #first>
                                                            <b-form-select-option
                                                                :value="null"
                                                                disabled
                                                                >-- Please
                                                                select an option
                                                                --</b-form-select-option
                                                            >
                                                        </template>
                                                    </b-form-select>
                                                </b-form-group>
                                            </b-form>
                                        </b-col>
                                    </b-row>
                                    <hr />
                                    <b-row>
                                        <b-col>
                                            <b-button
                                                pill
                                                variant="info"
                                                class="button-times-icon"
                                                @click="
                                                    saveProviderAgentToXendora()
                                                "
                                                block
                                            >
                                                <i
                                                    class="fas fa-plus times-icon"
                                                ></i>
                                                Agregar Agente</b-button
                                            >
                                        </b-col>
                                    </b-row>
                                </b-col>
                                <b-col
                                    cols="9"
                                    style="max-height: 500px; overflow-y: scroll"
                                >
                                    <b-overlay :show="isLoadingProviders">
                                        <b-row
                                            v-for="(agent,
                                            index) in getProvidersAgents"
                                            :key="index"
                                            class="mb-2"
                                        >
                                            <b-col>
                                                <b-card>
                                                    <!-- Card body -->
                                                    <b-row align-v="center">
                                                        <b-col md="auto">
                                                            <!-- Avatar -->
                                                            <b-avatar
                                                                variant="primary"
                                                                :text="
                                                                    `${agent.xendoraProviderAgentLetters}`
                                                                "
                                                                class="mr-3"
                                                            ></b-avatar>
                                                        </b-col>
                                                        <b-col class="ml--2">
                                                            <h4 class="mb-1">
                                                                {{
                                                                    agent.xendoraProviderAgentName
                                                                }}
                                                                {{
                                                                    agent.xendoraProviderAgentLastName
                                                                }}
                                                            </h4>
                                                            <p
                                                                class="text-sm text-muted mb-1"
                                                            >
                                                                <b-badge
                                                                    pill
                                                                    variant="primary"
                                                                    >Email:</b-badge
                                                                >
                                                                {{
                                                                    agent.xendoraProviderAgentEmail
                                                                }}
                                                            </p>
                                                            <p
                                                                class="text-sm text-muted mb-1"
                                                            >
                                                                <b-badge
                                                                    pill
                                                                    variant="primary"
                                                                    >Teléfono:</b-badge
                                                                >
                                                                {{
                                                                    agent.xendoraProviderAgentPhone
                                                                }}
                                                            </p>
                                                            <span
                                                                :class="
                                                                    agent._providerAgentStatus ===
                                                                    '1'
                                                                        ? 'text-success'
                                                                        : 'text-danger'
                                                                "
                                                                >●
                                                            </span>
                                                            <small
                                                                >Activo</small
                                                            >
                                                        </b-col>
                                                        <b-col md="auto">
                                                            <b-button
                                                                variant="danger"
                                                                size="sm"
                                                                pill
                                                                >Desactivar</b-button
                                                            >
                                                            <b-button
                                                                variant="warning"
                                                                size="sm"
                                                                pill
                                                                >Editar</b-button
                                                            >
                                                        </b-col>
                                                    </b-row>
                                                </b-card>
                                            </b-col>
                                        </b-row>
                                    </b-overlay>
                                </b-col>
                            </b-row>
                        </b-tab>
                        <b-tab title="Evaluaciones">
                            <b-card-text>Tab contents 3</b-card-text>
                        </b-tab>
                    </b-tabs>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
export default {
    name: "providerAgentsModalTemplate",
    props: {
        providerIdToShowModalComponent: Number
    },
    data() {
        return {
            showCreateAgentForm: false,
            agentsInfo: {
                name: "",
                lastName: "",
                phone: "",
                email: "",
                selected: null
            },
            agentLinesOptions: [
                { value: null, text: "Please select an option" },
                { value: "a", text: "This is First option" },
                { value: "b", text: "Selected Option" },
                { value: "c", text: "This one is disabled" }
            ],
            isLoadingProviders: false
        };
    },
    computed: {
        getProvidersAgents() {
            return this.$store.state.proe.providersAgentsFromXendora;
        },
        getProvidersAgentLines() {
            return this.$store.state.proe.providersAgentsLinesFromXendora;
        },
        getProvidersAgentsCounter() {
            return this.$store.state.proe.providersAgentsFromXendora.length;
        }
    },
    methods: {
        getProvidersAgentsFromXendora() {
            this.$store.dispatch(
                "getProvidersAgentsFromXendora",
                this.providerIdToShowModalComponent
            );
        },
        getProvidersAgentsLinesFromXendora() {
            this.$store.dispatch("getProvidersAgentsLinesFromXendora");
        },
        saveProviderAgentToXendora() {
            let url = "provider-evaluation/post/provider-agent/to-xendora";

            this.isLoadingProviders = true;

            let json_to_send = {
                nombres: this.agentsInfo.name,
                apellidos: this.agentsInfo.lastName,
                telefono: this.agentsInfo.phone,
                email: this.agentsInfo.email,
                selected: this.agentsInfo.selected,
                provider_id: this.providerIdToShowModalComponent
            };

            axios
                .post(url, json_to_send)
                .then(result => {
                    if (result.data.status === 201) {
                        this.getProvidersAgentsFromXendora();
                        this.isLoadingProviders = false;
                        this.clearFields();
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        },
        clearFields() {
            this.agentsInfo.name = "";
            this.agentsInfo.lastName = "";
            this.agentsInfo.phone = "";
            this.agentsInfo.email = "";
            this.agentsInfo.selected = null;
        }
    },
    created() {
        this.getProvidersAgentsFromXendora();
        this.getProvidersAgentsLinesFromXendora();
    }
};
</script>

<style></style>
